<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    protected $notifModel;

    public function __construct()
    {
        $this->notifModel = new NotificationModel();
    }

    /**
     * GET /notificacoes/nao-lidas
     * Retorna as notificações não lidas para o utilizador autenticado (AJAX).
     */
    public function naoLidas()
    {
        $userId = $this->getAuthUserId();
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Não autenticado']);
        }

        $notifs  = $this->notifModel->getNaoLidas($userId, 10);
        $total   = $this->notifModel->contarNaoLidas($userId);

        return $this->response->setJSON([
            'total'         => $total,
            'notificacoes'  => $notifs,
        ]);
    }

    /**
     * POST /notificacoes/marcar-lida/{id}
     * Marca uma notificação como lida.
     */
    public function marcarLida(int $id)
    {
        $userId = $this->getAuthUserId();
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Não autenticado']);
        }

        $this->notifModel->marcarComoLida($id, $userId);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /notificacoes/marcar-todas-lidas
     * Marca todas as notificações do utilizador como lidas.
     */
    public function marcarTodasLidas()
    {
        $userId = $this->getAuthUserId();
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Não autenticado']);
        }

        $this->notifModel->marcarTodasComoLidas($userId);

        return $this->response->setJSON(['success' => true]);
    }

    // ------------------------------------------------------------------

    private function getAuthUserId(): ?int
    {
        $userData = session()->get('LoggedUserData');
        $id = $userData['id'] ?? $userData['ID'] ?? session()->get('user_id');
        return $id ? (int) $id : null;
    }
}
