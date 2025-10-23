<?php

namespace App\Controllers;

use App\Models\AnoLetivoModel;

class AnosLetivosController extends BaseController
{
    protected $anoLetivoModel;

    public function __construct()
    {
        $this->anoLetivoModel = new AnoLetivoModel();
    }

    public function index()
    {
        // Verificar nível de acesso
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel < 6) {
            return redirect()->to('/')->with('error', 'Acesso negado');
        }

        $data = [
            'title' => 'Gestão de Anos Letivos',
            'page_title' => 'Gestão de Anos Letivos',
            'page_subtitle' => 'Listagem e gestão de anos letivos'
        ];

        return view('gestao_letiva/anos_letivos_index', $data);
    }

    public function getDataTable()
    {
        $anos = $this->anoLetivoModel->orderBy('anoletivo', 'DESC')->findAll();
        
        return $this->response->setJSON(['data' => $anos]);
    }

    public function create()
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        $data = $this->request->getPost();
        
        $anoId = $this->anoLetivoModel->insert($data);
        
        if ($anoId) {
            // LOG: Registar criação
            try {
                $anoletivo = $data['anoletivo'] ?? '';
                $descricao = $data['descricao'] ?? '';
                
                log_activity(
                    $userId,
                    'anos_letivos',
                    'create',
                    "Criou ano letivo '{$anoletivo}' - {$descricao}",
                    $anoId,
                    null,
                    $data
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de criação de ano letivo: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Ano letivo criado com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao criar ano letivo',
            'errors' => $this->anoLetivoModel->errors()
        ]);
    }

    public function update($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        
        // Buscar dados anteriores
        $dadosAnteriores = $this->anoLetivoModel->find($id);
        
        $data = $this->request->getPost();
        
        if ($this->anoLetivoModel->update($id, $data)) {
            // LOG: Registar atualização
            try {
                $anoletivo = $data['anoletivo'] ?? $dadosAnteriores['anoletivo'] ?? '';
                $descricao = $data['descricao'] ?? $dadosAnteriores['descricao'] ?? '';
                
                log_activity(
                    $userId,
                    'anos_letivos',
                    'update',
                    "Atualizou ano letivo '{$anoletivo}' - {$descricao}",
                    $id,
                    $dadosAnteriores,
                    $data
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de atualização de ano letivo: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Ano letivo atualizado com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar ano letivo',
            'errors' => $this->anoLetivoModel->errors()
        ]);
    }

    public function delete($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        
        // Buscar dados antes de eliminar
        $ano = $this->anoLetivoModel->find($id);
        
        if (!$ano) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ano letivo não encontrado'
            ]);
        }
        
        if ($this->anoLetivoModel->delete($id)) {
            // LOG: Registar eliminação
            try {
                $anoletivo = $ano['anoletivo'] ?? '';
                $descricao = $ano['descricao'] ?? '';
                
                log_activity(
                    $userId,
                    'anos_letivos',
                    'delete',
                    "Eliminou ano letivo '{$anoletivo}' - {$descricao}",
                    $id,
                    $ano,
                    null
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de eliminação de ano letivo: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Ano letivo excluído com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao excluir ano letivo'
        ]);
    }

    public function get($id)
    {
        $ano = $this->anoLetivoModel->find($id);
        
        if ($ano) {
            return $this->response->setJSON($ano);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Ano letivo não encontrado'
        ]);
    }

    public function ativar($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        
        // Buscar dados antes de ativar
        $ano = $this->anoLetivoModel->find($id);
        
        if ($this->anoLetivoModel->ativarAno($id)) {
            // LOG: Registar ativação
            try {
                $anoletivo = $ano['anoletivo'] ?? '';
                $descricao = $ano['descricao'] ?? '';
                
                log_activity(
                    $userId,
                    'anos_letivos',
                    'activate',
                    "Ativou ano letivo '{$anoletivo}' - {$descricao}",
                    $id,
                    $ano,
                    ['ativo' => 1]
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de ativação de ano letivo: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Ano letivo ativado com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao ativar ano letivo'
        ]);
    }
}
