<?php

namespace App\Controllers;

use App\Models\SugestaoModel;
use App\Models\UserModel;

class SugestoesController extends BaseController
{
    protected $sugestaoModel;
    protected $userModel;

    public function __construct()
    {
        $this->sugestaoModel = new SugestaoModel();
        $this->userModel = new UserModel();
    }

    /**
     * Listar todas as sugestões (admin)
     */
    public function index()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 6) {
            return redirect()->to('/')->with('error', 'Acesso negado');
        }

        $data = [
            'title' => 'Caixa de Sugestões',
            'page_title' => 'Gestão de Sugestões',
            'page_subtitle' => 'Visualizar e responder sugestões dos utilizadores'
        ];

        return view('sugestoes/index', $data);
    }

    /**
     * DataTable JSON
     */
    public function getDataTable()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 6) {
            return $this->response->setJSON(['data' => []]);
        }

        $sugestoes = $this->sugestaoModel->getSugestoesComUsuario();

        return $this->response->setJSON(['data' => $sugestoes]);
    }

    /**
     * Salvar nova sugestão
     */
    public function salvar()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData) {
            log_message('error', 'Tentativa de enviar sugestão sem sessão');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sessão expirada'
            ]);
        }

        $userNif = $userData['NIF'] ?? null;
        if (!$userNif) {
            log_message('error', 'Tentativa de enviar sugestão sem NIF no perfil');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'NIF não encontrado no perfil'
            ]);
        }

        $data = [
            'user_nif' => $userNif,
            'categoria' => $this->request->getPost('categoria'),
            'titulo' => $this->request->getPost('titulo'),
            'descricao' => $this->request->getPost('descricao'),
            'prioridade' => $this->request->getPost('prioridade') ?? 'media'
        ];

        log_message('info', 'Tentando salvar sugestão: ' . json_encode($data));

        if ($this->sugestaoModel->insert($data)) {
            $sugestaoId = $this->sugestaoModel->getInsertID();
            
            // Enviar email de notificação
            try {
                $this->enviarEmailNotificacao($sugestaoId, $userData);
            } catch (\Exception $e) {
                log_message('error', 'Erro ao enviar email de sugestão: ' . $e->getMessage());
            }

            // Log da atividade
            if (function_exists('log_activity')) {
                try {
                    log_activity(
                        $userData['id'],
                        'sugestoes',
                        'create',
                        "Enviou sugestão: {$data['titulo']}",
                        $sugestaoId,
                        null,
                        $data
                    );
                } catch (\Exception $e) {
                    log_message('error', 'Erro ao registar log de sugestão: ' . $e->getMessage());
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sugestão enviada com sucesso!'
            ]);
        }

        $errors = $this->sugestaoModel->errors();
        log_message('error', 'Falha ao inserir sugestão. Erros: ' . json_encode($errors));
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao enviar sugestão',
            'errors' => $errors
        ]);
    }

    /**
     * Responder a uma sugestão (admin)
     */
    public function responder($id)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 6) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        $resposta = $this->request->getPost('resposta');
        $novoEstado = $this->request->getPost('estado');

        if (empty($resposta)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'A resposta é obrigatória'
            ]);
        }

        if ($this->sugestaoModel->responderSugestao($id, $userData['id'], $resposta, $novoEstado)) {
            // Log da atividade
            if (function_exists('log_activity')) {
                try {
                    log_activity(
                        $userData['id'],
                        'sugestoes',
                        'update',
                        "Respondeu à sugestão #{$id}",
                        $id,
                        null,
                        ['resposta' => $resposta, 'estado' => $novoEstado]
                    );
                } catch (\Exception $e) {
                    log_message('error', 'Erro ao registar log de resposta: ' . $e->getMessage());
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Resposta enviada com sucesso'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao enviar resposta'
        ]);
    }

    /**
     * Alterar estado de uma sugestão
     */
    public function alterarEstado($id)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 6) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        $novoEstado = $this->request->getPost('estado');

        if ($this->sugestaoModel->update($id, ['estado' => $novoEstado])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Estado alterado com sucesso'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao alterar estado'
        ]);
    }

    /**
     * Excluir sugestão (admin)
     */
    public function excluir($id)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 6) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        if ($this->sugestaoModel->delete($id)) {
            // Log da atividade
            if (function_exists('log_activity')) {
                try {
                    log_activity(
                        $userData['id'],
                        'sugestoes',
                        'delete',
                        "Excluiu sugestão #{$id}",
                        $id,
                        null,
                        null
                    );
                } catch (\Exception $e) {
                    log_message('error', 'Erro ao registar log de exclusão: ' . $e->getMessage());
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sugestão excluída com sucesso'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao excluir sugestão'
        ]);
    }

    /**
     * Enviar email de notificação
     */
    private function enviarEmailNotificacao($sugestaoId, $userData)
    {
        $sugestao = $this->sugestaoModel->find($sugestaoId);
        if (!$sugestao) {
            return;
        }

        $email = \Config\Services::email();
        
        try {
            $email->setTo('escoladigitaljb@aejoaodebarros.pt');
            $email->setSubject('Nova Sugestão Recebida - #' . $sugestaoId);
            $email->setMailType('html');
            
            $emailBody = view('emails/nova_sugestao', [
                'sugestao' => $sugestao,
                'usuario' => $userData
            ]);
            
            $email->setMessage($emailBody);
            
            if ($email->send()) {
                log_message('info', "Email de notificação de sugestão enviado - ID: {$sugestaoId}");
            } else {
                log_message('error', "Erro ao enviar email de notificação - ID: {$sugestaoId}: " . $email->printDebugger(['headers']));
            }
        } catch (\Exception $e) {
            log_message('error', "Exceção ao enviar email de notificação: " . $e->getMessage());
        }
    }
}
