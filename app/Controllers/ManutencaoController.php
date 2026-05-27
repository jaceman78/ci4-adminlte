<?php

namespace App\Controllers;

use App\Models\ManutencaoModel;
use CodeIgniter\Controller;

class ManutencaoController extends BaseController
{
    protected $manutencaoModel;

    public function __construct()
    {
        $this->manutencaoModel = new ManutencaoModel();
        helper('logs'); // Carregar helper de logs (se existir)
    }

    /**
     * Verificar se o utilizador tem nível 9
     */
    private function checkAccess()
    {
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel != 9) {
            return redirect()->to('/')->with('error', 'Acesso negado. Apenas administradores nível 9 podem aceder.');
        }
        return null;
    }

    /**
     * Página de administração do modo de manutenção (apenas nível 9)
     */
    public function admin()
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        // Obter configuração atual
        $config = $this->manutencaoModel->getConfiguracao();

        $data = [
            'title' => 'Modo de Manutenção',
            'config' => $config
        ];

        return view('manutencao/admin', $data);
    }

    /**
     * Página pública de aviso de manutenção
     */
    public function aviso()
    {
        // Obter configuração atual
        $config = $this->manutencaoModel->getConfiguracao();

        // Se não está em manutenção, redirecionar para página inicial
        if (!$config || $config['ativo'] == 0) {
            return redirect()->to('/');
        }

        $data = [
            'title' => 'Site em Manutenção',
            'config' => $config
        ];

        return view('manutencao/aviso', $data);
    }

    /**
     * Ativar modo de manutenção (AJAX)
     */
    public function ativar()
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado.'
            ]);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $mensagem = $this->request->getPost('mensagem') ?? 'O site está temporariamente em manutenção. Por favor, volte mais tarde.';
        $dataFimPrevista = $this->request->getPost('data_fim_prevista');

        // Validar data se fornecida
        if ($dataFimPrevista) {
            $dataFimPrevista = date('Y-m-d H:i:s', strtotime($dataFimPrevista));
        } else {
            $dataFimPrevista = null;
        }

        $userData = session()->get('LoggedUserData');
        $userNif = (int)($userData['NIF'] ?? 0);

        if (!$userNif) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'NIF do utilizador não encontrado.'
            ]);
        }

        $success = $this->manutencaoModel->ativarManutencao($mensagem, $dataFimPrevista, $userNif);

        if ($success) {
            // Log de ativação
            if (function_exists('log_activity') && $userNif) {
                log_activity(
                    'manutencao',
                    'ativar',
                    null,
                    "Ativou o modo de manutenção do site"
                );
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Modo de manutenção ativado com sucesso.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao ativar o modo de manutenção.'
        ]);
    }

    /**
     * Desativar modo de manutenção (AJAX)
     */
    public function desativar()
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado.'
            ]);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $success = $this->manutencaoModel->desativarManutencao();

        if ($success) {
            // Log de desativação
            $userData = session()->get('LoggedUserData');
            $userNif = (int)($userData['NIF'] ?? 0);
            
            if (function_exists('log_activity') && $userNif) {
                log_activity(
                    'manutencao',
                    'desativar',
                    null,
                    "Desativou o modo de manutenção do site"
                );
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Modo de manutenção desativado com sucesso.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao desativar o modo de manutenção.'
        ]);
    }

    /**
     * Atualizar mensagem e data prevista (AJAX)
     */
    public function atualizar()
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado.'
            ]);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $mensagem = $this->request->getPost('mensagem');
        $dataFimPrevista = $this->request->getPost('data_fim_prevista');

        // Validar data se fornecida
        if ($dataFimPrevista) {
            $dataFimPrevista = date('Y-m-d H:i:s', strtotime($dataFimPrevista));
        } else {
            $dataFimPrevista = null;
        }

        $data = [
            'mensagem' => $mensagem,
            'data_fim_prevista' => $dataFimPrevista
        ];

        $success = $this->manutencaoModel->update(1, $data);

        if ($success) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Configuração atualizada com sucesso.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar a configuração.'
        ]);
    }
}
