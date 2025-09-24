<?php

namespace App\Controllers;

use App\Models\SalasModel;
use App\Models\EscolasModel;
use CodeIgniter\HTTP\ResponseInterface;

class SalaController extends BaseController
{
    protected $salasModel;
    protected $escolasModel;
    protected $validation;

    public function __construct()
    {
        $this->salasModel = new SalasModel();
        $this->escolasModel = new EscolasModel();
        $this->validation = \Config\Services::validation();
    }

    /**
     * Página principal de salas
     */
    public function index()
    {
        $data = [
            'title' => 'Gestão de Salas',
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => base_url()],
                ['name' => 'Salas', 'url' => '']
            ]
        ];

        return view('salas/salas_index', $data);
    }

    /**
     * Obter dados para DataTable via AJAX (filtrado por escola)
     */
    public function getDataTable()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $request = $this->request->getPost();
        
        $escolaId = $request['escola_id'] ?? null;
        
        if (!$escolaId) {
            return $this->response->setJSON([
                'draw' => intval($request['draw'] ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }
        
        $start = $request['start'] ?? 0;
        $length = $request['length'] ?? 10;
        $search = $request['search']['value'] ?? '';
        
        // Configurar ordenação
        $orderColumn = 'id';
        $orderDir = 'asc';
        
        if (isset($request['order'][0])) {
            $columns = ['id', 'codigo_sala', 'escola_nome', 'created_at'];
            $orderColumnIndex = $request['order'][0]['column'];
            $orderColumn = $columns[$orderColumnIndex] ?? 'id';
            $orderDir = $request['order'][0]['dir'] ?? 'asc';
        }

        $result = $this->salasModel->getDataTableData($escolaId, $start, $length, $search, $orderColumn, $orderDir);
        
        // Formatar dados para DataTable
        $data = [];
        foreach ($result['data'] as $sala) {
            $actions = '
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-primary" onclick="editSala(' . $sala['id'] . ')" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-info" onclick="viewSala(' . $sala['id'] . ')" title="Ver">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteSala(' . $sala['id'] . ')" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>';
            
            $data[] = [
                $sala['id'],
                $sala['codigo_sala'],
                $sala['escola_nome'],
                date('d/m/Y H:i', strtotime($sala['created_at'])),
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
     * Obter dados de uma sala específica
     */
    public function getSala($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        $sala = $this->salasModel->getSalaWithEscola($id);
        
        if (!$sala) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Sala não encontrada']);
        }

        return $this->response->setJSON(['success' => true, 'data' => $sala]);
    }

    /**
     * Criar nova sala
     */
    public function create()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $data = $this->request->getPost();
        
        // Validar dados
        $validation = $this->salasModel->validateSalaData($data);
        
        if (!$validation['success']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validation['errors']
            ]);
        }

        // Verificar se escola existe
        if (!$this->salasModel->escolaExists($data['escola_id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Escola não encontrada',
                'errors' => ['escola_id' => 'A escola selecionada não existe.']
            ]);
        }

        // Preparar dados para inserção
        $salaData = [
            'escola_id' => $data['escola_id'],
            'codigo_sala' => $data['codigo_sala']
        ];

        $salaId = $this->salasModel->insert($salaData);
        
        if ($salaId) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sala criada com sucesso!',
                'data' => ['id' => $salaId]
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao criar sala',
                'errors' => $this->salasModel->errors()
            ]);
        }
    }

    /**
     * Atualizar sala existente
     */
    public function update($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        // Verificar se sala existe
        $existingSala = $this->salasModel->find($id);
        if (!$existingSala) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Sala não encontrada']);
        }

        $data = $this->request->getPost();
        $data['id'] = $id; // Adicionar ID aos dados para validação
        
        // Validar dados
        $validation = $this->salasModel->validateSalaData($data, $id);
        
        if (!$validation['success']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validation['errors']
            ]);
        }

        // Verificar se escola existe
        if (!$this->salasModel->escolaExists($data['escola_id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Escola não encontrada',
                'errors' => ['escola_id' => 'A escola selecionada não existe.']
            ]);
        }

        // Preparar dados para atualização
        $salaData = [
            'escola_id' => $data['escola_id'],
            'codigo_sala' => $data['codigo_sala']
        ];

        $result = $this->salasModel->update($id, $salaData);
        
        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sala atualizada com sucesso!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao atualizar sala',
                'errors' => $this->salasModel->errors()
            ]);
        }
    }

    /**
     * Eliminar sala
     */
    public function delete($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        // Verificar se sala existe
        $sala = $this->salasModel->find($id);
        if (!$sala) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Sala não encontrada']);
        }

        $result = $this->salasModel->delete($id);
        
        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sala eliminada com sucesso!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao eliminar sala'
            ]);
        }
    }

    /**
     * Obter estatísticas das salas
     */
    public function getStats()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $escolaId = $this->request->getGet('escola_id');
        $stats = $this->salasModel->getSalasStats($escolaId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Pesquisar salas
     */
    public function search()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $search = $this->request->getGet('q');
        $escolaId = $this->request->getGet('escola_id');
        
        if (empty($search)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Termo de pesquisa não fornecido'
            ]);
        }

        $salas = $this->salasModel->searchSalas($search, $escolaId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $salas
        ]);
    }

    /**
     * Exportar salas para CSV
     */
    public function exportCSV()
    {
        $escolaId = $this->request->getGet('escola_id');
        
        if ($escolaId) {
            $salas = $this->salasModel->getSalasByEscola($escolaId);
            $filename = 'salas_escola_' . $escolaId . '_' . date('Y-m-d_H-i-s') . '.csv';
        } else {
            $salas = $this->salasModel->getAllSalasWithEscola();
            $filename = 'salas_' . date('Y-m-d_H-i-s') . '.csv';
        }
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        
        // Cabeçalhos
        fputcsv($output, ['ID', 'Código da Sala', 'Escola', 'Data Criação']);
        
        // Dados
        foreach ($salas as $sala) {
            fputcsv($output, [
                $sala['id'],
                $sala['codigo_sala'],
                $sala['escola_nome'] ?? '',
                $sala['created_at']
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Obter lista de escolas para dropdown
     */
    public function getEscolasDropdown()
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
     * Obter lista de salas para dropdown (por escola)
     */
    public function getSalasDropdown()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $escolaId = $this->request->getGet('escola_id');
        
        if (!$escolaId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID da escola não fornecido'
            ]);
        }

        $salas = $this->salasModel->getSalasForDropdown($escolaId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $salas
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
        
        $salas = $this->salasModel->advancedSearch($filters);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $salas
        ]);
    }

    /**
     * Eliminar múltiplas salas
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
                'message' => 'Nenhuma sala selecionada'
            ]);
        }

        $result = $this->salasModel->deleteMultipleSalas($ids);
        
        if ($result) {
            $count = count($ids);
            return $this->response->setJSON([
                'success' => true,
                'message' => "{$count} sala(s) eliminada(s) com sucesso!"
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao eliminar salas'
            ]);
        }
    }

    /**
     * Obter salas recentes
     */
    public function getRecent()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $days = $this->request->getGet('days') ?? 30;
        $escolaId = $this->request->getGet('escola_id');
        $salas = $this->salasModel->getRecentSalas($days, $escolaId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $salas
        ]);
    }

    /**
     * Verificar se código da sala existe numa escola
     */
    public function checkCodigo()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $codigoSala = $this->request->getPost('codigo_sala');
        $escolaId = $this->request->getPost('escola_id');
        $excludeId = $this->request->getPost('exclude_id');
        
        if (empty($codigoSala) || empty($escolaId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Código da sala e ID da escola são obrigatórios'
            ]);
        }

        $exists = $this->salasModel->codigoExists($codigoSala, $escolaId, $excludeId);
        
        return $this->response->setJSON([
            'success' => true,
            'exists' => $exists
        ]);
    }

    /**
     * Obter informações da escola selecionada
     */
    public function getEscolaInfo($id = null)
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

        // Obter estatísticas das salas desta escola
        $stats = $this->salasModel->getSalasStats($id);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'escola' => $escola,
                'stats' => $stats
            ]
        ]);
    }
}