<?php

namespace App\Controllers;

use App\Models\TipologiaModel;

class TipologiasController extends BaseController
{
    protected $tipologiaModel;

    public function __construct()
    {
        $this->tipologiaModel = new TipologiaModel();
    }

    public function index()
    {
        // Verificar nível de acesso
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel < 6) {
            return redirect()->to('/')->with('error', 'Acesso negado');
        }

        $data = [
            'title' => 'Gestão de Tipologias',
            'page_title' => 'Gestão de Tipologias',
            'page_subtitle' => 'Listagem e gestão de tipologias de curso'
        ];

        return view('gestao_letiva/tipologias_index', $data);
    }

    public function getDataTable()
    {
        $tipologias = $this->tipologiaModel->findAll();
        
        return $this->response->setJSON(['data' => $tipologias]);
    }

    public function create()
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        $data = $this->request->getPost();
        
        $tipologiaId = $this->tipologiaModel->insert($data);
        
        if ($tipologiaId) {
            // LOG: Registar criação
            try {
                $descricao_tip = $data['descricao_tip'] ?? '';
                $abrev_tip = $data['abrev_tip'] ?? '';
                
                log_activity(
                    $userId,
                    'tipologias',
                    'create',
                    "Criou tipologia '{$abrev_tip}' - {$descricao_tip}",
                    $tipologiaId,
                    null,
                    $data
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de criação de tipologia: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tipologia criada com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao criar tipologia',
            'errors' => $this->tipologiaModel->errors()
        ]);
    }

    public function update($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        
        // Buscar dados anteriores
        $dadosAnteriores = $this->tipologiaModel->find($id);
        
        $data = $this->request->getPost();
        
        if ($this->tipologiaModel->update($id, $data)) {
            // LOG: Registar atualização
            try {
                $abrev_tip = $data['abrev_tip'] ?? $dadosAnteriores['abrev_tip'] ?? '';
                $descricao_tip = $data['descricao_tip'] ?? $dadosAnteriores['descricao_tip'] ?? '';
                
                log_activity(
                    $userId,
                    'tipologias',
                    'update',
                    "Atualizou tipologia '{$abrev_tip}' - {$descricao_tip}",
                    $id,
                    $dadosAnteriores,
                    $data
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de atualização de tipologia: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tipologia atualizada com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar tipologia',
            'errors' => $this->tipologiaModel->errors()
        ]);
    }

    public function delete($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        
        // Buscar dados antes de eliminar
        $tipologia = $this->tipologiaModel->find($id);
        
        if (!$tipologia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tipologia não encontrada'
            ]);
        }
        
        if ($this->tipologiaModel->delete($id)) {
            // LOG: Registar eliminação
            try {
                $abrev_tip = $tipologia['abrev_tip'] ?? '';
                $descricao_tip = $tipologia['descricao_tip'] ?? '';
                
                log_activity(
                    $userId,
                    'tipologias',
                    'delete',
                    "Eliminou tipologia '{$abrev_tip}' - {$descricao_tip}",
                    $id,
                    $tipologia,
                    null
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de eliminação de tipologia: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tipologia excluída com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao excluir tipologia'
        ]);
    }

    public function get($id)
    {
        $tipologia = $this->tipologiaModel->find($id);
        
        if ($tipologia) {
            return $this->response->setJSON($tipologia);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Tipologia não encontrada'
        ]);
    }
}
