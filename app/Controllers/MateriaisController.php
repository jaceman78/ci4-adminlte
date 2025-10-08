<?php

namespace App\Controllers;

use App\Models\MateriaisModel;
use CodeIgniter\HTTP\ResponseInterface;

class MateriaisController extends BaseController
{
    public $modulo = 'materiais';
    protected $materiaisModel;
    protected $validation;

    public function __construct()
    {
        $this->materiaisModel = new MateriaisModel();
        $this->validation = \Config\Services::validation();
        helper("LogHelper"); // Carrega o helper de logs
        helper("SessionHelper"); // Carrega o helper de sessão
    }

    /**
     * Página principal de gestão de materiais
     */
    public function index()
    {
        // Log de acesso à página de materiais
        log_activity(
            get_current_user_id(),
            'materiais',
            'view_page',
            'Acedeu à página de gestão de materiais'
        );

        $data = [
            'title' => 'Gestão de Materiais',
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => base_url()],
                ['name' => 'Materiais', 'url' => '']
            ]
        ];

        return view('materiais/materiais_index', $data);
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
        
        $orderColumn = 'nome';
        $orderDir = 'asc';
        
        if (isset($request['order'][0])) {
            $columns = ['id', 'nome', 'referencia', 'stock_atual'];
            $orderColumnIndex = $request['order'][0]['column'];
            $orderColumn = $columns[$orderColumnIndex] ?? 'nome';
            $orderDir = $request['order'][0]['dir'] ?? 'asc';
        }

        $result = $this->materiaisModel->getMateriaisDataTable($start, $length, $search, $orderColumn, $orderDir);
        
        // Log da consulta de materiais
        $detalhes = [
            'search' => $search,
            'order_column' => $orderColumn,
            'order_dir' => $orderDir,
            'records_found' => $result['recordsFiltered']
        ];
        log_activity(
            get_current_user_id(),
            'materiais',
            'datatable_query',
            'Consultou materiais via DataTable',
            null,
            null,
            null,
            $detalhes
        );

        $data = [];
        foreach ($result['data'] as $material) {
            $actions = '
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-primary" onclick="editMaterial(' . $material['id'] . ')" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteMaterial(' . $material['id'] . ')" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>';
            
            $data[] = [
                $material['id'],
                $material['nome'],
                $material['referencia'] ?? 'N/A',
                $material['stock_atual'],
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
     * Obter um material específico para edição
     */
    public function getMaterial($id = null)
    {
        if (!$this->request->isAJAX()) {
           
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        $material = $this->materiaisModel->find($id);

        if (!$material) {
            log_activity(
                get_current_user_id(),
                'materiais',
                'view_failed',
                "Tentou visualizar material inexistente (ID: {$id})",
                $id
            );
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Material não encontrado']);
        }

        return $this->response->setJSON(['success' => true, 'data' => $material]);
    }

    /**
     * Guardar (criar ou atualizar) um material
     */
    public function saveMaterial()
    {
        if (!$this->request->isAJAX()) {
            
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $data = $this->request->getPost();
        $id = $data['id'] ?? null;

        if (!$this->validation->run($data, ($id ? 'materialUpdate' : 'materialCreate'))) {
   
            return $this->response->setJSON(['success' => false, 'errors' => $this->validation->getErrors()]);
        }

        $oldData = null;
        if ($id) {
            $oldData = $this->materiaisModel->find($id);
        }

        if ($id && $oldData) {
            // Atualizar
            $result = $this->materiaisModel->updateMaterial($id, $data);
            if ($result) {
         
                return $this->response->setJSON(['success' => true, 'message' => 'Material atualizado com sucesso!']);
            } else {
            
                return $this->response->setJSON(['success' => false, 'message' => 'Erro ao atualizar material.']);
            }
        } else {
            // Criar
            $newId = $this->materiaisModel->addMaterial($data);
            if ($newId) {
      
                return $this->response->setJSON(['success' => true, 'message' => 'Material adicionado com sucesso!']);
            } else {
       
                return $this->response->setJSON(['success' => false, 'message' => 'Erro ao adicionar material.']);
            }
        }
    }

    /**
     * Eliminar um material
     */
    public function deleteMaterial($id = null)
    {
        if (!$this->request->isAJAX()) {
            
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        $material = $this->materiaisModel->find($id);
        if (!$material) {
            log_activity(
                get_current_user_id(),
                'materiais',
                'delete_failed',
                "Tentou eliminar material inexistente (ID: {$id})",
                $id
            );
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Material não encontrado.']);
        }

        $result = $this->materiaisModel->deleteMaterial($id);

        if ($result) {
           log_activity(
                get_current_user_id(),
                'materiais',
                'delete',
                "Eliminou material (ID: {$id})",
                $id,
                $material
            );
            return $this->response->setJSON(['success' => true, 'message' => 'Material eliminado com sucesso!']);
        } else {
         log_activity(
                get_current_user_id(),
                'materiais',
                'delete_failed',
                "Erro ao eliminar material (ID: {$id})",
                $id,
                $material
            );
            return $this->response->setJSON(['success' => false, 'message' => 'Erro ao eliminar material.']);
        }
    }





    /**
     * Obter estatísticas dos materiais
     */
    public function getStats()
    {
        if (!$this->request->isAJAX()) {
            
            return $this->response->setStatusCode(403)->setJSON(["error" => "Acesso negado"]);
        }

        $stats = $this->materiaisModel->getMateriaisStats();
        
        log_activity(
            get_current_user_id(),
            "materiais",
            "view_stats",
            "Consultou estatísticas de materiais"
        );

        return $this->response->setJSON([
            "success" => true,
            "data" => $stats
        ]);
    }


}