<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;
use CodeIgniter\API\ResponseTrait;

// Certifique-se de que o helper é carregado (via Autoload ou manualmente)
// helper("LogHelper"); // Se não estiver no Autoload.php

class ActivityLogController extends BaseController
{
    use ResponseTrait;

    protected $activityLogModel;

    public function __construct()
    {
        $this->activityLogModel = new ActivityLogModel();
        // Carregar o helper se não estiver no Autoload.php
        helper("LogHelper"); 
    }

    /**
     * Página principal de logs de atividade
     */
    public function index()
    {
        // Log de acesso à página de logs
        log_activity(
            get_current_user_id(),
            'logs',
            'view_page',
            'Acedeu à página de logs de atividade'
        );

        $data = [
            'title' => 'Logs de Atividade',
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => base_url('dashboard')],
                ['name' => 'Logs de Atividade', 'url' => '']
            ],
        ];

        return view('logs/activity_log_index', $data);
    }

    /**
     * Retorna os dados para a DataTable de logs
     */
    public function getDataTable()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado');
        }

        $filters = [
            'user_id' => $this->request->getPost('filter_user_id'),
            'modulo' => $this->request->getPost('filter_modulo'),
            'acao' => $this->request->getPost('filter_acao'),
            'data_inicio' => $this->request->getPost('filter_data_inicio'),
            'data_fim' => $this->request->getPost('filter_data_fim'),
        ];

        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('search')['value'];
        $order = $this->request->getPost('order');
        $columns = $this->request->getPost('columns');

        $data = $this->activityLogModel->getDataTableData($draw, $start, $length, $search, $order, $columns, $filters);

        // Log de visualização de dados da DataTable
        log_activity(
            get_current_user_id(),
            'logs',
            'view_datatable',
            'Visualizou dados da DataTable de logs',
            null, null, null,
            ['filters' => $filters, 'search' => $search]
        );

        return $this->respond($data);
    }

    /**
     * Retorna os detalhes de um log específico
     */
    public function getLog($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado');
        }

        if ($id === null) {
            return $this->failValidationErrors('ID do log não fornecido.');
        }

        $log = $this->activityLogModel->getLogDetails($id);

        if (!$log) {
            return $this->failNotFound('Log não encontrado.');
        }

        // Log de visualização de log individual
        log_activity(
            get_current_user_id(),
            'logs',
            'view_details',
            'Visualizou detalhes do log (ID: ' . $id . ')',
            $id
        );

        return $this->respond(['success' => true, 'data' => $log]);
    }

    /**
     * Retorna dados para os filtros (utilizadores, módulos, ações)
     */
    public function getFilterData()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado');
        }

        $users = $this->activityLogModel->getUniqueUsers();
        $modules = $this->activityLogModel->getUniqueModules();
        $actions = $this->activityLogModel->getUniqueActions();

        return $this->respond(['success' => true, 'data' => compact('users', 'modules', 'actions')]);
    }

    /**
     * Retorna estatísticas dos logs
     */
    public function getStats()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado');
        }

        $stats = $this->activityLogModel->getLogStats();

        // Log de visualização de estatísticas
        log_activity(
            get_current_user_id(),
            'logs',
            'view_stats',
            'Visualizou estatísticas dos logs'
        );

        return $this->respond(['success' => true, 'data' => $stats]);
    }

    /**
     * Exporta logs para CSV
     */
    public function exportCSV()
    {
        // Apenas administradores podem exportar
        if (session()->get('level') < 9) {
            log_permission_denied('logs', 'export');
            return $this->failForbidden('Não tem permissão para exportar logs.');
        }

        $filters = [
            'user_id' => $this->request->getGet('user_id'),
            'modulo' => $this->request->getGet('modulo'),
            'acao' => $this->request->getGet('acao'),
            'data_inicio' => $this->request->getGet('data_inicio'),
            'data_fim' => $this->request->getGet('data_fim'),
        ];

        $logs = $this->activityLogModel->getFilteredLogs($filters);

        if (empty($logs)) {
            return $this->failNotFound('Nenhum log encontrado para exportar com os filtros aplicados.');
        }

        $filename = 'activity_logs_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Cabeçalho CSV
        fputcsv($output, [
            'ID', 'User ID', 'User Name', 'User Email', 'Modulo', 'Acao', 'Registro ID',
            'Descricao', 'Dados Anteriores', 'Dados Novos', 'IP Address', 'User Agent', 'Detalhes', 'Criado Em'
        ]);

        // Dados
        foreach ($logs as $log) {
            fputcsv($output, [
                $log->id,
                $log->user_id,
                $log->user_name,
                $log->user_email,
                $log->modulo,
                $log->acao,
                $log->registro_id,
                $log->descricao,
                $log->dados_anteriores,
                $log->dados_novos,
                $log->ip_address,
                $log->user_agent,
                $log->detalhes,
                $log->criado_em
            ]);
        }

        fclose($output);

        // Log de exportação
        log_export_activity('logs', 'CSV', count($logs));

        exit();
    }

    /**
     * Elimina um log específico
     */
    public function delete($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado');
        }

        // Apenas administradores podem eliminar logs
        if (session()->get('level') < 9) {
            log_permission_denied('logs', 'delete');
            return $this->failForbidden('Não tem permissão para eliminar logs.');
        }

        if ($id === null) {
            return $this->failValidationErrors('ID do log não fornecido.');
        }

        $log = $this->activityLogModel->find($id);
        if (!$log) {
            return $this->failNotFound('Log não encontrado.');
        }

        if ($this->activityLogModel->delete($id)) {
            // Log da própria eliminação do log
            log_activity(
                get_current_user_id(),
                'logs',
                'delete',
                'Eliminou log de atividade (ID: ' . $id . ')',
                $id, json_decode($log->detalhes, true)
            );
            return $this->respondDeleted(['success' => true, 'message' => 'Log eliminado com sucesso.']);
        } else {
            return $this->failServerError('Erro ao eliminar log.');
        }
    }

    /**
     * Limpa logs antigos
     */
    public function cleanOldLogs()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado');
        }

        // Apenas administradores podem limpar logs
        if (session()->get('level') < 9) {
            log_permission_denied('logs', 'clean');
            return $this->failForbidden('Não tem permissão para limpar logs antigos.');
        }

        $days = $this->request->getPost('days');

        if (!is_numeric($days) || $days <= 0) {
            return $this->failValidationErrors('Número de dias inválido.');
        }

        $count = $this->activityLogModel->cleanOldLogs($days);

        // Log da limpeza de logs
        log_activity(
            get_current_user_id(),
            'logs',
            'clean',
            'Limpeza de logs antigos (mais de ' . $days . ' dias). ' . $count . ' logs eliminados.',
            null, null, null,
            ['days' => $days, 'deleted_count' => $count]
        );

        return $this->respond(['success' => true, 'message' => $count . ' logs antigos eliminados com sucesso.']);
    }
}
