<?php 

namespace App\Controllers;

use App\Models\ActivityLogModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class ActivityLogController extends BaseController
{
    protected $activityLogModel;
    protected $userModel;
    protected $validation;

    public function __construct()
    {
        $this->activityLogModel = new ActivityLogModel();
        $this->userModel = new UserModel();
        $this->validation = \Config\Services::validation();
        helper("LogHelper"); // Carrega o helper de logs
    }

    /**
     * Página principal de logs de atividade
     */
    public function index()
    {
        // Só aqui, não em cada AJAX!
        // log_activity(
        //     session()->get('user_id'),
        //     'logs',
        //     'view_page',
        //     'Acedeu à página de logs de atividade'
        // );

        $data = [
            'title' => 'Logs de Atividade',
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => base_url()],
                ['name' => 'Logs de Atividade', 'url' => '']
            ]
        ];

        return view('logs/activity_log_index', $data);
    }

    /**
     * Obter dados para DataTable via AJAX
     */
    public function getDataTable()
    {
        if (!$this->request->isAJAX()) {
            log_permission_denied('logs/getDataTable', 'non_ajax_request');
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $request = $this->request->getPost();
        
        $start = $request['start'] ?? 0;
        $length = $request['length'] ?? 10;
        $search = $request['search']['value'] ?? '';
        
        // Configurar ordenação
        $orderColumn = 'criado_em';
        $orderDir = 'desc';
        
        if (isset($request['order'][0])) {
            $columns = ['id', 'user_name', 'modulo', 'acao', 'descricao', 'criado_em'];
            $orderColumnIndex = $request['order'][0]['column'];
            $orderColumn = $columns[$orderColumnIndex] ?? 'criado_em';
            $orderDir = $request['order'][0]['dir'] ?? 'desc';
        }

        // Obter filtros adicionais
        $filters = [
            'user_id' => $request['filter_user_id'] ?? null,
            'modulo' => $request['filter_modulo'] ?? null,
            'acao' => $request['filter_acao'] ?? null,
            'data_inicio' => $request['filter_data_inicio'] ?? null,
            'data_fim' => $request['filter_data_fim'] ?? null
        ];

        // Remover filtros vazios
        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        $result = $this->activityLogModel->getDataTableData($start, $length, $search, $orderColumn, $orderDir, $filters);
        
        // Log da consulta de logs
        $detalhes = [
            'search' => $search,
            'filters' => $filters,
            'order_column' => $orderColumn,
            'order_dir' => $orderDir,
            'records_found' => $result['recordsFiltered']
        ];
        log_activity(
            get_current_user_id(),
            'logs',
            'datatable_query',
            'Consultou logs de atividade via DataTable',
            null,
            null,
            null,
            $detalhes
        );

        // Formatar dados para DataTable
        $data = [];
        foreach ($result['data'] as $log) {
            // Badge do módulo
            $moduloBadges = [
                'users' => 'bg-primary',
                'escolas' => 'bg-success',
                'salas' => 'bg-info',
                'auth' => 'bg-warning',
                'system' => 'bg-secondary',
                'logs' => 'bg-dark'
            ];
            $moduloBadge = '<span class="badge ' . ($moduloBadges[$log['modulo']] ?? 'bg-light') . '">' . ucfirst($log['modulo']) . '</span>';

            // Badge da ação
            $acaoBadges = [
                'create' => 'bg-success',
                'update' => 'bg-primary',
                'delete' => 'bg-danger',
                'view' => 'bg-info',
                'login' => 'bg-success',
                'logout' => 'bg-warning',
                'export' => 'bg-secondary'
            ];
            $acaoBadge = '<span class="badge ' . ($acaoBadges[$log['acao']] ?? 'bg-light') . '">' . ucfirst($log['acao']) . '</span>';

            // Nome do utilizador
            $userName = $log['user_name'] ?? null;
            $oauthId = $log['oauth_id'] ?? null;

            if ($userName) {
                $displayUser = $userName;
            } elseif ($oauthId) {
                $displayUser = '<span class="text-info">OAuth: ' . htmlspecialchars($oauthId) . '</span>';
            } else {
                $displayUser = '<span class="text-muted">Sistema</span>';
            }

            // Descrição truncada
            $descricao = strlen($log['descricao']) > 80 
                ? substr($log['descricao'], 0, 80) . '...' 
                : $log['descricao'];

            // Ações
            $actions = '
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-info" onclick="viewLog(' . $log['id'] . ')" title="Ver Detalhes">
                        <i class="fas fa-eye"></i>
                    </button>';
            
            // Só mostrar botão de eliminar para administradores (level >= 9)
            $currentUserLevel = session()->get('level') ?? 0;
            if ($currentUserLevel >= 9) {
                $actions .= '
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteLog(' . $log['id'] . ')" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>';
            }
            
            $actions .= '</div>';
            
            $data[] = [
                $log['id'],
                $displayUser,
                $moduloBadge,
                $acaoBadge,
                $descricao,
                $log['ip_address'] ?? 'N/A',
                date('d/m/Y H:i:s', strtotime($log['criado_em'])),
                $actions
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($request['draw'] ?? 1),
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => $data
        ]);
    }

    /**
     * Obter dados de um log específico
     */
    public function getLog($id = null)
    {
        if (!$this->request->isAJAX()) {
           log_activity(
                get_current_user_id(),
                'logs',
                'view_log_failed',
                'Tentou aceder a detalhes de log sem ser AJAX'
            );
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        $log = $this->activityLogModel->select('logs_atividade.*, user.name as user_name, user.email as user_email')
                                     ->join('user', 'user.id = logs_atividade.user_id', 'left')
                                     ->where('logs_atividade.id', $id)
                                     ->first();
        
        if (!$log) {
            log_activity(
                get_current_user_id(),
                'logs',
                'view_failed',
                "Tentou visualizar log inexistente (ID: {$id})",
                $id
            );
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Log não encontrado']);
        }

        // Log de visualização de log específico
        log_activity(
            get_current_user_id(),
            'logs',
            'view',
            "Visualizou detalhes do log ID: {$id}",
            $id
        );

        // Decodificar dados JSON se existirem
        if ($log['dados_anteriores']) {
            $log['dados_anteriores'] = json_decode($log['dados_anteriores'], true);
        }
        if ($log['dados_novos']) {
            $log['dados_novos'] = json_decode($log['dados_novos'], true);
        }
        if ($log['detalhes']) {
            $log['detalhes'] = json_decode($log['detalhes'], true);
        }

        return $this->response->setJSON(['success' => true, 'data' => $log]);
    }

    /**
     * Eliminar log (apenas para administradores)
     */
    public function delete($id = null)
    {
        if (!$this->request->isAJAX()) {
            
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        // Verificar permissões (apenas administradores)
        $currentUserLevel = session()->get('level') ?? 0;
        if ($currentUserLevel < 9) {
          
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Permissões insuficientes']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        // Verificar se log existe
        $log = $this->activityLogModel->find($id);
        if (!$log) {
            log_activity(
                get_current_user_id(),
                'logs',
                'delete_failed',
                "Tentou eliminar log inexistente (ID: {$id})",
                $id
            );
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Log não encontrado']);
        }

        $result = $this->activityLogModel->delete($id);
        
        if ($result) {
            // Log de eliminação bem-sucedida
            log_activity(
                get_current_user_id(),
                'logs',
                'delete',
                "Eliminou log de atividade (ID: {$id})",
                $id,
                $log
            );
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Log eliminado com sucesso!'
            ]);
        } else {
            // Log de erro na eliminação
            log_activity(
                get_current_user_id(),
                'logs',
                'delete_failed',
                "Erro ao eliminar log de atividade (ID: {$id})",
                $id
            );
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao eliminar log'
            ]);
        }
    }

    /**
     * Obter estatísticas dos logs
     */
    public function getStats()
    {
        if (!$this->request->isAJAX()) {
            
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $stats = $this->activityLogModel->getLogStats();
        
        // Log de consulta de estatísticas
        log_activity(
            get_current_user_id(),
            'logs',
            'view_stats',
            'Consultou estatísticas de logs de atividade'
        );
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Obter dados para filtros (dropdowns)
     */
    public function getFilterData()
    {
        if (!$this->request->isAJAX()) {
           
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $data = [
            'modules' => $this->activityLogModel->getUniqueModules(),
            'actions' => $this->activityLogModel->getUniqueActions(),
            'users' => $this->activityLogModel->getUsersWithLogs()
        ];

        return $this->response->setJSON([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Exportar logs para CSV
     */
    public function exportCSV()
    {
        // Obter filtros da query string
        $filters = [
            'user_id' => $this->request->getGet('user_id'),
            'modulo' => $this->request->getGet('modulo'),
            'acao' => $this->request->getGet('acao'),
            'data_inicio' => $this->request->getGet('data_inicio'),
            'data_fim' => $this->request->getGet('data_fim')
        ];

        // Remover filtros vazios
        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        $logs = $this->activityLogModel->exportToCSV($filters);
        
        // Log de exportação
        log_activity(
            get_current_user_id(),
            'logs',
            'export_csv',
            'Exportou logs de atividade para CSV',
            null,
            null,
            null,
            ['filters' => $filters, 'exported_count' => count($logs)]
        );
        
        $filename = 'logs_atividade_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cabeçalhos
        fputcsv($output, [
            'ID',
            'Utilizador',
            'Email',
            'Módulo',
            'Ação',
            'Descrição',
            'Registo ID',
            'IP Address',
            'User Agent',
            'Data/Hora'
        ], ';');
        
        // Dados
        foreach ($logs as $log) {
            fputcsv($output, [
                $log['id'],
                $log['user_name'] ?? 'Sistema',
                $log['user_email'] ?? 'N/A',
                $log['modulo'],
                $log['acao'],
                $log['descricao'],
                $log['registro_id'] ?? 'N/A',
                $log['ip_address'] ?? 'N/A',
                $log['user_agent'] ?? 'N/A',
                $log['criado_em']
            ], ';');
        }
        
        fclose($output);
        exit;
    }

    /**
     * Limpar logs antigos (apenas para administradores)
     */
    public function cleanOldLogs()
    {
        if (!$this->request->isAJAX()) {
            log_activity(
                get_current_user_id(),
                'logs',
                'clean_old_logs_failed',
                'Tentou limpar logs antigos sem ser AJAX'
            );
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        // Verificar permissões (apenas administradores)
        $currentUserLevel = session()->get('level') ?? 0;
        if ($currentUserLevel < 9) {
            log_activity(
                get_current_user_id(),
                'logs',
                'clean_old_logs_failed',
                'Tentou limpar logs antigos sem permissões suficientes'
            );
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Permissões insuficientes']);
        }

        $data = $this->request->getPost();
        $days = $data['days'] ?? 90;

        // Validar número de dias
        if (!is_numeric($days) || $days < 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Número de dias inválido'
            ]);
        }

        // Contar logs que serão eliminados
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $logsToDelete = $this->activityLogModel->where('criado_em <', $cutoffDate)->countAllResults();

        if ($logsToDelete == 0) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Nenhum log antigo encontrado para eliminar',
                'deleted_count' => 0
            ]);
        }

        $result = $this->activityLogModel->cleanOldLogs($days);
        
        if ($result) {
            // Log da limpeza
            log_activity(
                get_current_user_id(),
                'logs',
                'clean_old',
                "Limpou logs antigos (>{$days} dias) - {$logsToDelete} registos eliminados",
                null,
                null,
                null,
                ['days' => $days, 'deleted_count' => $logsToDelete]
            );
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Logs antigos eliminados com sucesso! ({$logsToDelete} registos)",
                'deleted_count' => $logsToDelete
            ]);
        } else {
            // Log de erro na limpeza
            log_activity(
                get_current_user_id(),
                'logs',
                'clean_old_failed',
                "Erro ao limpar logs antigos (>{$days} dias)",
                null,
                null,
                null,
                ['days' => $days]
            );
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao eliminar logs antigos'
            ]);
        }
    }

    /**
     * Obter logs recentes para dashboard
     */
    public function getRecentLogs()
    {
        if (!$this->request->isAJAX()) {
            log_activity(
                get_current_user_id(),
                'logs',
                'get_recent_logs_failed',
                'Tentou aceder a logs recentes sem ser AJAX'
            );
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $limit = $this->request->getGet('limit') ?? 10;
        $logs = $this->activityLogModel->getRecentLogs($limit);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Pesquisar logs
     */
    public function search()
    {
        if (!$this->request->isAJAX()) {
            
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $search = $this->request->getGet('q');
        
        if (empty($search)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Termo de pesquisa não fornecido'
            ]);
        }

        $logs = $this->activityLogModel->searchLogs($search, 50);
        
        // Log de pesquisa
        log_activity(
            get_current_user_id(),
            'logs',
            'search',
            "Pesquisou logs com termo: {$search}",
            null,
            null,
            null,
            ['search_term' => $search, 'results_count' => count($logs)]
        );
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Obter logs de um registo específico
     */
    public function getLogsByRecord()
    {
        if (!$this->request->isAJAX()) {
            
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $modulo = $this->request->getGet('modulo');
        $registroId = $this->request->getGet('registro_id');

        if (empty($modulo) || empty($registroId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Módulo e ID do registo são obrigatórios'
            ]);
        }

        $logs = $this->activityLogModel->getLogsByRecord($modulo, $registroId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Dashboard de logs (página de estatísticas)
     */
    public function dashboard()
    {
        // Log de acesso ao dashboard
        log_activity(
            get_current_user_id(),
            'logs',
            'view_dashboard',
            'Acedeu ao dashboard de logs de atividade'
        );

        $data = [
            'title' => 'Dashboard - Logs de Atividade',
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => base_url()],
                ['name' => 'Logs de Atividade', 'url' => base_url('logs')],
                ['name' => 'Dashboard', 'url' => '']
            ]
        ];

        return view('logs/activity_log_dashboard', $data);
    }
}