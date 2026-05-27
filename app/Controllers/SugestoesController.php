<?php

namespace App\Controllers;

use App\Models\SugestaoModel;
use App\Models\SugestaoAnexoModel;
use App\Models\UserModel;

class SugestoesController extends BaseController
{
    protected $sugestaoModel;
    protected $sugestaoAnexoModel;
    protected $userModel;

    public function __construct()
    {
        $this->sugestaoModel = new SugestaoModel();
        $this->sugestaoAnexoModel = new SugestaoAnexoModel();
        $this->userModel = new UserModel();
        helper('notification');
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
            
            // Processar anexos (se houver)
            $anexosProcessados = 0;
            $files = $this->request->getFiles();
            
            if (!empty($files['anexos'])) {
                // Criar diretório se não existir
                $uploadPath = WRITEPATH . 'uploads/sugestoes/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                foreach ($files['anexos'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        // Validar tamanho (máx 5MB)
                        if ($file->getSize() > 5 * 1024 * 1024) {
                            log_message('warning', 'Ficheiro muito grande ignorado: ' . $file->getName());
                            continue;
                        }
                        
                        // Obter informações do ficheiro ANTES de mover
                        $mimeType = $file->getMimeType();
                        $tamanho = $file->getSize();
                        $nomeOriginal = $file->getName();
                        $extensao = $file->getExtension();
                        
                        // Validar tipo (apenas imagens, PDFs e documentos)
                        $allowedMimes = [
                            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp',
                            'application/pdf',
                            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/plain'
                        ];
                        
                        if (!in_array($mimeType, $allowedMimes)) {
                            log_message('warning', 'Tipo de ficheiro não permitido: ' . $mimeType);
                            continue;
                        }
                        
                        // Gerar nome único
                        $nomeFicheiro = 'sugestao_' . $sugestaoId . '_' . time() . '_' . uniqid() . '.' . $extensao;
                        
                        // Mover ficheiro
                        if ($file->move($uploadPath, $nomeFicheiro)) {
                            // Salvar registro na BD
                            $anexoData = [
                                'sugestao_id' => $sugestaoId,
                                'nome_original' => $nomeOriginal,
                                'nome_ficheiro' => $nomeFicheiro,
                                'tipo_mime' => $mimeType,
                                'tamanho' => $tamanho,
                                'caminho' => $uploadPath . $nomeFicheiro
                            ];
                            
                            if ($this->sugestaoAnexoModel->insert($anexoData)) {
                                $anexosProcessados++;
                                log_message('info', 'Anexo salvo: ' . $nomeFicheiro);
                            }
                        }
                        
                        // Limitar a 5 anexos
                        if ($anexosProcessados >= 5) {
                            break;
                        }
                    }
                }
            }
            
            // Enviar email de notificação
            try {
                $this->enviarEmailNotificacao($sugestaoId, $userData);
            } catch (\Exception $e) {
                log_message('error', 'Erro ao enviar email de sugestão: ' . $e->getMessage());
            }

            // Notificar super administradores (level 9) sobre nova sugestão
            try {
                notificar_utilizadores_por_level(
                    [9],
                    'sugestoes',
                    'info',
                    'Nova sugestão recebida',
                    'Nova sugestão "' . mb_substr($data['titulo'], 0, 80) . '" submetida por ' . ($userData['name'] ?? $userData['NIF']) . '.',
                    base_url('sugestoes'),
                    (int) ($userData['id'] ?? 0)
                );
            } catch (\Exception $e) {
                log_message('warning', 'Erro ao notificar sobre nova sugestão: ' . $e->getMessage());
            }

            // Log da atividade
            if (function_exists('log_activity')) {
                try {
                    log_activity(
                        'sugestoes',
                        'create',
                        $sugestaoId,
                        "Enviou sugestão: {$data['titulo']}" . ($anexosProcessados > 0 ? " com {$anexosProcessados} anexo(s)" : ""),
                        null,
                        $data
                    );
                } catch (\Exception $e) {
                    log_message('error', 'Erro ao registar log de sugestão: ' . $e->getMessage());
                }
            }

            $mensagem = 'Sugestão enviada com sucesso!';
            if ($anexosProcessados > 0) {
                $mensagem .= " ({$anexosProcessados} anexo(s) enviado(s))";
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $mensagem
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
            // Enviar email de feedback ao autor da sugestão
            try {
                $this->enviarEmailFeedbackAutor($id, $resposta, $novoEstado, $userData);
            } catch (\Exception $e) {
                log_message('error', 'Erro ao enviar email de feedback de sugestão: ' . $e->getMessage());
            }

            // Log da atividade
            if (function_exists('log_activity')) {
                try {
                    log_activity(
                        'sugestoes',
                        'update',
                        $id,
                        "Respondeu à sugestão #{$id}",
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
                        'sugestoes',
                        'delete',
                        $id,
                        "Excluiu sugestão #{$id}",
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
     * Listar anexos de uma sugestão
     */
    public function getAnexos($sugestaoId)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sessão expirada'
            ]);
        }

        $anexos = $this->sugestaoAnexoModel->getAnexosSugestao($sugestaoId);

        return $this->response->setJSON([
            'success' => true,
            'data' => $anexos
        ]);
    }

    /**
     * Fazer download de um anexo
     */
    public function downloadAnexo($anexoId)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData) {
            return redirect()->to('/login')->with('error', 'Sessão expirada');
        }

        $anexo = $this->sugestaoAnexoModel->find($anexoId);

        if (!$anexo) {
            return redirect()->back()->with('error', 'Anexo não encontrado');
        }

        // Verificar se o ficheiro existe
        if (!file_exists($anexo['caminho'])) {
            return redirect()->back()->with('error', 'Ficheiro não encontrado');
        }

        // Forçar download
        return $this->response->download($anexo['caminho'], null)
                              ->setFileName($anexo['nome_original']);
    }

    /**
     * Deletar um anexo (admin apenas)
     */
    public function deleteAnexo($anexoId)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 6) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        if ($this->sugestaoAnexoModel->deleteAnexoComFicheiro($anexoId)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Anexo eliminado com sucesso'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao eliminar anexo'
        ]);
    }

    /**
     * Enviar email de feedback ao autor quando a sugestão é respondida
     */
    private function enviarEmailFeedbackAutor($sugestaoId, $resposta, $novoEstado, $adminData)
    {
        $sugestao = $this->sugestaoModel->find($sugestaoId);
        if (!$sugestao) {
            log_message('warning', "enviarEmailFeedbackAutor: sugestão #{$sugestaoId} não encontrada");
            return;
        }

        // Obter o utilizador autor via NIF
        $autor = $this->userModel->where('NIF', $sugestao['user_nif'])->first();
        if (!$autor || empty($autor['email'])) {
            log_message('warning', "enviarEmailFeedbackAutor: autor da sugestão #{$sugestaoId} sem email");
            return;
        }

        $email = \Config\Services::email();

        try {
            $email->setTo($autor['email']);
            $email->setSubject('Atualização da sua Sugestão #' . $sugestaoId);
            $email->setMailType('html');

            $emailBody = view('emails/sugestao_respondida', [
                'sugestao'  => $sugestao,
                'autor'     => $autor,
                'resposta'  => $resposta,
                'estado'    => $novoEstado,
                'adminNome' => $adminData['name'] ?? null,
            ]);

            $email->setMessage($emailBody);

            if ($email->send()) {
                log_message('info', "Email de feedback de sugestão enviado ao autor ({$autor['email']}) - ID: {$sugestaoId}");
            } else {
                log_message('error', "Erro ao enviar email de feedback de sugestão #{$sugestaoId}: " . $email->printDebugger(['headers']));
            }
        } catch (\Exception $e) {
            log_message('error', "Exceção ao enviar email de feedback de sugestão: " . $e->getMessage());
        }
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
