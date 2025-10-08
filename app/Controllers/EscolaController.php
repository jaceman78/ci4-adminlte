<?php

namespace App\Controllers;

use App\Models\EscolasModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;

// Certifique-se de que o helper é carregado (via Autoload ou manualmente)
// helper("LogHelper"); // Se não estiver no Autoload.php

class EscolaController extends BaseController
{
    use ResponseTrait;
    public $modulo = 'escola';
    protected $escolasModel;
    protected $validation;
    
    public function __construct()
    {
        $this->escolasModel = new EscolasModel();
        $this->validation = \Config\Services::validation();
        
        helper("LogHelper"); // Carregar o helper
    }

    /**
     * Página principal de escolas
     */
    public function index()
    {
        // Log de acesso à página de escolas


        $data = [
            'title' => 'Gestão de Escolas',
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => base_url()],
                ['name' => 'Escolas', 'url' => '']
            ]
        ];


        return view('escolas/escolas_index', $data);
    }

    /**
     * Obter dados para DataTable via AJAX
     */
    public function getDataTable()
    {
        if (!$this->request->isAJAX()) {
            
            return $this->failUnauthorized('Acesso não autorizado');
        }

        $request = $this->request->getPost();
        
        $start = $request['start'] ?? 0;
        $length = $request['length'] ?? 10;
        $search = $request['search']['value'] ?? '';
        
        // Configurar ordenação
        $orderColumn = 'id';
        $orderDir = 'asc';
        
        if (isset($request['order'][0])) {
            $columns = ['id', 'nome', 'morada', 'created_at'];
            $orderColumnIndex = $request['order'][0]['column'];
            $orderColumn = $columns[$orderColumnIndex] ?? 'id';
            $orderDir = $request['order'][0]['dir'] ?? 'asc';
        }

        $result = $this->escolasModel->getDataTableData($start, $length, $search, $orderColumn, $orderDir);
        
        // Formatar dados para DataTable
        $data = [];
        foreach ($result['data'] as $escola) {
            $morada = !empty($escola['morada']) ? $escola['morada'] : '<span class="text-muted">Não definida</span>';
            
            $actions = '
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-primary" onclick="editEscola(' . $escola['id'] . ')" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-info" onclick="viewEscola(' . $escola['id'] . ')" title="Ver">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteEscola(' . $escola['id'] . ')" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>';
            
            $data[] = [
                $escola['id'],
                $escola['nome'],
                $morada,
                date('d/m/Y H:i', strtotime($escola['created_at'])),
                $actions
            ];
        }

        // Log de visualização de dados da DataTable
 

        return $this->response->setJSON([
            'draw' => intval($request['draw'] ?? 1),
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => $data
        ]);
    }

    /**
     * Obter dados de uma escola específica
     */
    public function getEscola($id = null)
    {
        if (!$this->request->isAJAX()) {
            
            return $this->failUnauthorized('Acesso não autorizado');
        }

        if (!$id) {
               return $this->failValidationErrors('ID não fornecido');
        }

        $escola = $this->escolasModel->find($id);
        
        if (!$escola) {
           
            return $this->failNotFound('Escola não encontrada');
        }

        // Log de visualização de escola
      

        return $this->respond(['success' => true, 'data' => $escola]);
    }

    /**
     * Criar nova escola
     */
    public function create()
    {
        if (!$this->request->isAJAX()) {
           
            return $this->failUnauthorized('Acesso não autorizado');
        }

        $data = $this->request->getPost();
        
        // Validar dados
        $validation = $this->escolasModel->validateEscolaData($data);
        
        if (!$validation['success']) {
            
                return $this->respond([
                'success' => false,
                'message' => 'Erro ao criar escola', 
                'errors' => $validation['errors']         
            ]);
        }

        // Preparar dados para inserção
        $escolaData = [
            'nome' => $data['nome'],
            'morada' => $data['morada'] ?? null
        ];

        $escolaId = $this->escolasModel->insert($escolaData);
        
        if ($escolaId) {
            log_activity(session()->get('user_id'),$this->modulo,'create', 'Escola criada com sucesso', $escolaId, null, $escolaData);
            return $this->respondCreated([
                'success' => true,
                'message' => 'Escola criada com sucesso!',
                'data' => ['id' => $escolaId]
            ]);
        } else {          
        
            
      
            return $this->failServerError('Erro ao criar escola', implode('; ', $this->escolasModel->errors()));
          //  return $this->failServerError('Erro ao criar escola', implode('; ', $this->escolasModel->errors()));

        }
    }

    /**
     * Atualizar escola existente
     */
    public function update($id = null)
    {
        if (!$this->request->isAJAX()) {
           
            return $this->failUnauthorized('Acesso não autorizado');
        }

        if (!$id) {
           
            return $this->failValidationErrors('ID não fornecido');
        }

        // Verificar se escola existe
        $existingEscola = $this->escolasModel->find($id);
        if (!$existingEscola) {
            log_activity(session()->get('user_id'),$this->modulo,'update_not_found', 'Tentativa de atualizar escola não encontrada', $id);
            return $this->failNotFound('Escola não encontrada');
        }

        $data = $this->request->getPost();
        $data['id'] = $id; // Adicionar ID aos dados para validação
        
        // Validar dados
        $validation = $this->escolasModel->validateEscolaData($data, $id);
        
        if (!$validation['success']) {
            log_activity(session()->get('user_id'),$this->modulo,'update_validation_fail', 'Erro de validação ao atualizar escola', $id, $existingEscola, $data, ['errors' => $validation['errors']]);
            return $this->failValidationErrors($validation['errors']);
        }

        // Preparar dados para atualização
        $escolaData = [
            'nome' => $data['nome'],
            'morada' => $data['morada'] ?? null
        ];

        $result = $this->escolasModel->update($id, $escolaData);
        
        if ($result) {
            log_activity(session()->get('user_id'),$this->modulo,'update', 'Escola atualizada com sucesso', $id, $existingEscola, $escolaData);
            return $this->respondUpdated([
                'success' => true,
                'message' => 'Escola atualizada com sucesso!'
            ]);
        } else {
            log_activity(session()->get('user_id'), $this->modulo, 'update_fail', 'Erro ao atualizar escola', $id, $existingEscola, $escolaData, ['db_errors' => $this->escolasModel->errors()]);
            return $this->failServerError('Erro ao atualizar escola', implode('; ', $this->escolasModel->errors()));
        }
    }

    /**
     * Eliminar escola
     */
    public function delete($id = null)
    {
        if (!$this->request->isAJAX()) {
           
            return $this->failUnauthorized('Acesso não autorizado');
        }

        if (!$id) {
            log_activity(session()->get('user_id'),$this->modulo,'error', 'Tentativa de eliminar escola sem ID', null, null, null, ['ip_address' => $this->request->getIPAddress()]);
            return $this->failValidationErrors('ID não fornecido');
        }

        // Verificar se escola existe
        $escola = $this->escolasModel->find($id);
        if (!$escola) {
            log_activity(session()->get('user_id'),$this->modulo,'delete_not_found', 'Tentativa de eliminar escola não encontrada', $id);
            return $this->failNotFound('Escola não encontrada');
        }

        $result = $this->escolasModel->delete($id);
        
        if ($result) {
            log_activity(session()->get('user_id'),$this->modulo,'delete', 'Escola eliminada com sucesso', $id, $escola);
            return $this->respondDeleted([
                'success' => true,
                'message' => 'Escola eliminada com sucesso!'
            ]);
        } else {
            log_activity(session()->get('user_id'),$this->modulo,'delete_fail', 'Erro ao eliminar escola', $id, $escola, null, ['db_errors' => $this->escolasModel->errors()]);
            return $this->failServerError('Erro ao eliminar escola');
        }
    }

    /**
     * Obter estatísticas das escolas
     */
    public function getStats()
    {
        if (!$this->request->isAJAX()) {
            
            return $this->failUnauthorized('Acesso não autorizado');
        }

        $stats = $this->escolasModel->getEscolasStats();
        
        log_activity(session()->get('user_id'),$this->modulo,'view_stats', 'Visualizou estatísticas das escolas', null, null, $stats);

        return $this->respond([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Pesquisar escolas
     */
    public function search()
    {
        if (!$this->request->isAJAX()) {
            log_activity(session()->get('user_id'),$this->modulo,'escolas', 'search');
            return $this->failUnauthorized('Acesso não autorizado');
        }

        $search = $this->request->getGet('q');
        
        if (empty($search)) {
            log_activity(session()->get('user_id'),$this->modulo,'search_empty', 'Tentativa de pesquisa de escolas com termo vazio');
            return $this->failValidationErrors('Termo de pesquisa não fornecido');
        }

        $escolas = $this->escolasModel->searchEscolas($search);
        
        log_activity(session()->get('user_id'),$this->modulo,'search', 'Pesquisou escolas', null, ['term' => $search], ['results_count' => count($escolas)]);

        return $this->respond([
            'success' => true,
            'data' => $escolas
        ]);
    }

    /**
     * Exportar escolas para CSV
     */
    public function exportCSV()
    {
        // Apenas administradores podem exportar
        if (session()->get('level') < 9) {
            log_activity(session()->get('user_id'),$this->modulo, 'export', 'Tentativa de exportação de escolas sem permissão');
            return $this->failForbidden('Não tem permissão para exportar escolas.');
        }

        $escolas = $this->escolasModel->getAllEscolas();
        
        $filename = 'escolas_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        
        // Cabeçalhos
        fputcsv($output, ['ID', 'Nome', 'Morada', 'Data Criação']);
        
        // Dados
        foreach ($escolas as $escola) {
            fputcsv($output, [
                $escola['id'],
                $escola['nome'],
                $escola['morada'] ?? '',
                $escola['created_at']
            ]);
        }
        
        fclose($output);

        log_activity(session()->get('user_id'),$this->modulo,'export_csv', 'Exportou escolas para CSV', null, null, ['exported_count' => count($escolas)]);
      

        exit;
    }

    /**
     * Obter lista de escolas para dropdown
     */
    public function getDropdownList()
    {
        if (!$this->request->isAJAX()) {
            
            return $this->failUnauthorized('Acesso não autorizado');
        }

        $escolas = $this->escolasModel->getEscolasForDropdown();
        
        log_activity(session()->get('user_id'),$this->modulo,'view_dropdown', 'Visualizou lista de escolas para dropdown', null, null, ['count' => count($escolas)]);

        return $this->respond([
            'success' => true,
            'data' => $escolas
        ]);
    }

    /**
     * Pesquisa avançada
     */
    public function advancedSearch()
    {
        if (!$this->request->isAJAX()) {
            
            return $this->failUnauthorized('Acesso não autorizado');
        }

        $filters = $this->request->getPost();
        
        $escolas = $this->escolasModel->advancedSearch($filters);
        
      

        return $this->respond([
            'success' => true,
            'data' => $escolas
        ]);
    }

    /**
     * Eliminar múltiplas escolas
     */
    public function deleteMultiple()
    {
        if (!$this->request->isAJAX()) {
            log_activity(session()->get('user_id'),$this->modulo,'delete_multiple_unauthorized', 'Tentativa de eliminar múltiplas escolas sem ser AJAX');
           
            return $this->failUnauthorized('Acesso não autorizado');
        }

        // Apenas administradores podem eliminar múltiplas escolas
        if (session()->get('level') < 9) {
            log_activity(session()->get('user_id'),$this->modulo,'delete_multiple_forbidden', 'Tentativa de eliminar múltiplas escolas sem permissões suficientes');
            
            return $this->failForbidden('Não tem permissão para eliminar múltiplas escolas.');
        }

        $ids = $this->request->getPost('ids');
        
        if (empty($ids) || !is_array($ids)) {
            log_activity(session()->get('user_id'),$this->modulo,'delete_multiple_no_ids', 'Tentativa de eliminar múltiplas escolas sem IDs selecionados');
            return $this->failValidationErrors('Nenhuma escola selecionada');
        }

        $deletedEscolas = [];
        foreach ($ids as $id) {
            $escola = $this->escolasModel->find($id);
            if ($escola) {
                $deletedEscolas[] = $escola;
            }
        }

        $result = $this->escolasModel->deleteMultipleEscolas($ids);
        
        if ($result) {
            $count = count($ids);
            log_activity(session()->get('user_id'),$this->modulo,'delete_multiple', 'Eliminou múltiplas escolas', null, ['ids' => $ids, 'deleted_data' => $deletedEscolas]);
            return $this->respondDeleted([
                'success' => true,
                'message' => "{$count} escola(s) eliminada(s) com sucesso!"
            ]);
        } else {
            log_activity(session()->get('user_id'),$this->modulo,'delete_multiple_fail', 'Erro ao eliminar múltiplas escolas', null, ['ids' => $ids], null, ['db_errors' => $this->escolasModel->errors()]);
            return $this->failServerError('Erro ao eliminar escolas');
        }
    }

    /**
     * Obter escolas recentes
     */
    public function getRecent()
    {
        if (!$this->request->isAJAX()) {
            log_activity(session()->get('user_id'),$this->modulo,'get_recent_unauthorized', 'Tentativa de aceder a escolas recentes sem ser AJAX');
          
            return $this->failUnauthorized('Acesso não autorizado');
        }

        $days = $this->request->getGet('days') ?? 30;
        $escolas = $this->escolasModel->getRecentEscolas($days);
        log_activity(session()->get('user_id'),$this->modulo,'view_recent', 'Visualizou escolas recentes', null, ['days' => $days], ['results_count' => count($escolas)]);

        return $this->respond([
            'success' => true,
            'data' => $escolas
        ]);
    }

    /**
     * Verificar se nome da escola existe
     */
    public function checkNome()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado');
        }

        $nome = $this->request->getPost('nome');
        $excludeId = $this->request->getPost('exclude_id');
        
        if (empty($nome)) {
             return $this->failValidationErrors('Nome não fornecido');
        }

        $exists = $this->escolasModel->nomeExists($nome, $excludeId);
       
        return $this->respond([
            'success' => true,
            'exists' => $exists
        ]);
    }
}


