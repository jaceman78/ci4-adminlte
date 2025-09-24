<?php

namespace App\Controllers;

use App\Models\EscolasModel;
use CodeIgniter\HTTP\ResponseInterface;

class EscolaController extends BaseController
{
    protected $escolasModel;
    protected $validation;

    public function __construct()
    {
        $this->escolasModel = new EscolasModel();
        $this->validation = \Config\Services::validation();
    }

    /**
     * Página principal de escolas
     */
    public function index()
    {
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
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
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
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        $escola = $this->escolasModel->find($id);
        
        if (!$escola) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Escola não encontrada']);
        }

        return $this->response->setJSON(['success' => true, 'data' => $escola]);
    }

    /**
     * Criar nova escola
     */
    public function create()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $data = $this->request->getPost();
        
        // Validar dados
        $validation = $this->escolasModel->validateEscolaData($data);
        
        if (!$validation['success']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
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
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Escola criada com sucesso!',
                'data' => ['id' => $escolaId]
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao criar escola',
                'errors' => $this->escolasModel->errors()
            ]);
        }
    }

    /**
     * Atualizar escola existente
     */
    public function update($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        // Verificar se escola existe
        $existingEscola = $this->escolasModel->find($id);
        if (!$existingEscola) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Escola não encontrada']);
        }

        $data = $this->request->getPost();
        $data['id'] = $id; // Adicionar ID aos dados para validação
        
        // Validar dados
        $validation = $this->escolasModel->validateEscolaData($data, $id);
        
        if (!$validation['success']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validation['errors']
            ]);
        }

        // Preparar dados para atualização
        $escolaData = [
            'nome' => $data['nome'],
            'morada' => $data['morada'] ?? null
        ];

        $result = $this->escolasModel->update($id, $escolaData);
        
        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Escola atualizada com sucesso!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao atualizar escola',
                'errors' => $this->escolasModel->errors()
            ]);
        }
    }

    /**
     * Eliminar escola
     */
    public function delete($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        // Verificar se escola existe
        $escola = $this->escolasModel->find($id);
        if (!$escola) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Escola não encontrada']);
        }

        $result = $this->escolasModel->delete($id);
        
        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Escola eliminada com sucesso!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao eliminar escola'
            ]);
        }
    }

    /**
     * Obter estatísticas das escolas
     */
    public function getStats()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $stats = $this->escolasModel->getEscolasStats();
        
        return $this->response->setJSON([
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
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $search = $this->request->getGet('q');
        
        if (empty($search)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Termo de pesquisa não fornecido'
            ]);
        }

        $escolas = $this->escolasModel->searchEscolas($search);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $escolas
        ]);
    }

    /**
     * Exportar escolas para CSV
     */
    public function exportCSV()
    {
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
        exit;
    }

    /**
     * Obter lista de escolas para dropdown
     */
    public function getDropdownList()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $escolas = $this->escolasModel->getEscolasForDropdown();
        
        return $this->response->setJSON([
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
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $filters = $this->request->getPost();
        
        $escolas = $this->escolasModel->advancedSearch($filters);
        
        return $this->response->setJSON([
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
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $ids = $this->request->getPost('ids');
        
        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nenhuma escola selecionada'
            ]);
        }

        $result = $this->escolasModel->deleteMultipleEscolas($ids);
        
        if ($result) {
            $count = count($ids);
            return $this->response->setJSON([
                'success' => true,
                'message' => "{$count} escola(s) eliminada(s) com sucesso!"
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao eliminar escolas'
            ]);
        }
    }

    /**
     * Obter escolas recentes
     */
    public function getRecent()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $days = $this->request->getGet('days') ?? 30;
        $escolas = $this->escolasModel->getRecentEscolas($days);
        
        return $this->response->setJSON([
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
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $nome = $this->request->getPost('nome');
        $excludeId = $this->request->getPost('exclude_id');
        
        if (empty($nome)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nome não fornecido'
            ]);
        }

        $exists = $this->escolasModel->nomeExists($nome, $excludeId);
        
        return $this->response->setJSON([
            'success' => true,
            'exists' => $exists
        ]);
    }
}
