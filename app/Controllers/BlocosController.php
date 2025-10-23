<?php

namespace App\Controllers;

use App\Models\BlocosHorariosModel;

class BlocosController extends BaseController
{
    protected $blocosModel;

    public function __construct()
    {
        $this->blocosModel = new BlocosHorariosModel();
    }

    public function index()
    {
        // Verificar nível de acesso
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel < 6) {
            return redirect()->to('/')->with('error', 'Acesso negado');
        }

        $data = [
            'title' => 'Gestão de Blocos Horários',
            'page_title' => 'Gestão de Blocos Horários',
            'page_subtitle' => 'Listagem e gestão de blocos horários'
        ];

        return view('gestao_letiva/blocos_index', $data);
    }

    public function getDataTable()
    {
        $blocos = $this->blocosModel->orderBy('dia_semana ASC, hora_inicio ASC')->findAll();
        
        // Traduzir dias da semana
        $diasSemana = [
            'Segunda_Feira' => 'Segunda-Feira',
            'Terca_Feira' => 'Terça-Feira',
            'Quarta_Feira' => 'Quarta-Feira',
            'Quinta_Feira' => 'Quinta-Feira',
            'Sexta_Feira' => 'Sexta-Feira',
            'Sabado' => 'Sábado'
        ];
        
        foreach ($blocos as &$bloco) {
            $bloco['dia_semana_formatado'] = $diasSemana[$bloco['dia_semana']] ?? $bloco['dia_semana'];
        }
        
        return $this->response->setJSON(['data' => $blocos]);
    }

    public function create()
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        $data = $this->request->getPost();
        
        $blocoId = $this->blocosModel->insert($data);
        
        if ($blocoId) {
            // LOG: Registar criação
            try {
                $diasSemana = [
                    'Segunda_Feira' => 'Segunda-Feira',
                    'Terca_Feira' => 'Terça-Feira',
                    'Quarta_Feira' => 'Quarta-Feira',
                    'Quinta_Feira' => 'Quinta-Feira',
                    'Sexta_Feira' => 'Sexta-Feira',
                    'Sabado' => 'Sábado'
                ];
                
                $dia = $diasSemana[$data['dia_semana']] ?? $data['dia_semana'];
                $inicio = $data['hora_inicio'] ?? '';
                $fim = $data['hora_fim'] ?? '';
                
                log_activity(
                    $userId,
                    'blocos',
                    'create',
                    "Criou bloco horário: {$dia} ({$inicio} - {$fim})",
                    $blocoId,
                    null,
                    $data
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de criação de bloco: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Bloco horário criado com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao criar bloco horário',
            'errors' => $this->blocosModel->errors()
        ]);
    }

    public function update($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        
        // Buscar dados anteriores
        $dadosAnteriores = $this->blocosModel->find($id);
        
        $data = $this->request->getPost();
        
        if ($this->blocosModel->update($id, $data)) {
            // LOG: Registar atualização
            try {
                $diasSemana = [
                    'Segunda_Feira' => 'Segunda-Feira',
                    'Terca_Feira' => 'Terça-Feira',
                    'Quarta_Feira' => 'Quarta-Feira',
                    'Quinta_Feira' => 'Quinta-Feira',
                    'Sexta_Feira' => 'Sexta-Feira',
                    'Sabado' => 'Sábado'
                ];
                
                $diaSemana = $data['dia_semana'] ?? $dadosAnteriores['dia_semana'] ?? '';
                $dia = $diasSemana[$diaSemana] ?? $diaSemana;
                $inicio = $data['hora_inicio'] ?? $dadosAnteriores['hora_inicio'] ?? '';
                $fim = $data['hora_fim'] ?? $dadosAnteriores['hora_fim'] ?? '';
                
                log_activity(
                    $userId,
                    'blocos',
                    'update',
                    "Atualizou bloco horário: {$dia} ({$inicio} - {$fim})",
                    $id,
                    $dadosAnteriores,
                    $data
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de atualização de bloco: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Bloco horário atualizado com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar bloco horário',
            'errors' => $this->blocosModel->errors()
        ]);
    }

    public function delete($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        
        // Buscar dados antes de eliminar
        $bloco = $this->blocosModel->find($id);
        
        if (!$bloco) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bloco horário não encontrado'
            ]);
        }
        
        if ($this->blocosModel->delete($id)) {
            // LOG: Registar eliminação
            try {
                $diasSemana = [
                    'Segunda_Feira' => 'Segunda-Feira',
                    'Terca_Feira' => 'Terça-Feira',
                    'Quarta_Feira' => 'Quarta-Feira',
                    'Quinta_Feira' => 'Quinta-Feira',
                    'Sexta_Feira' => 'Sexta-Feira',
                    'Sabado' => 'Sábado'
                ];
                
                $dia = $diasSemana[$bloco['dia_semana']] ?? $bloco['dia_semana'];
                $inicio = $bloco['hora_inicio'] ?? '';
                $fim = $bloco['hora_fim'] ?? '';
                
                log_activity(
                    $userId,
                    'blocos',
                    'delete',
                    "Eliminou bloco horário: {$dia} ({$inicio} - {$fim})",
                    $id,
                    $bloco,
                    null
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de eliminação de bloco: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Bloco horário excluído com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao excluir bloco horário'
        ]);
    }

    public function get($id)
    {
        $bloco = $this->blocosModel->find($id);
        
        if ($bloco) {
            return $this->response->setJSON($bloco);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Bloco horário não encontrado'
        ]);
    }
}
