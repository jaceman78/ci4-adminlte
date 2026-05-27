<?php

namespace App\Controllers;

use App\Models\UserModel;

class ImpersonationController extends BaseController
{
    /**
     * Iniciar impersonificação de um utilizador.
     * Apenas nível >= 7 pode usar. Não pode impersonar nível >= 8 nem a si próprio.
     */
    public function start($userId)
    {
        $adminData = session()->get('LoggedUserData');
        $adminLevel = $adminData['level'] ?? 0;

        // Verificar permissão de admin
        if ($adminLevel < 7) {
            log_activity('impersonation', 'start_denied', $userId,
                "Tentativa de impersonificação sem permissão (nível {$adminLevel})",
                null, null, 'critical');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado.'
            ])->setStatusCode(403);
        }

        $userModel = new UserModel();
        $target = $userModel->find($userId);

        if (!$target) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Utilizador não encontrado.'
            ])->setStatusCode(404);
        }

        // Não pode impersonar a si próprio
        $adminId = $adminData['id'] ?? $adminData['ID'] ?? null;
        if ((int)$userId === (int)$adminId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Não pode impersonar a sua própria conta.'
            ]);
        }

        // Não pode impersonar utilizadores de nível >= 8 (apenas levels 0–7)
        if ((int)($target['level'] ?? 0) >= 8) {
            log_activity('impersonation', 'start_denied', $userId,
                "Tentativa de impersonificar utilizador com nível elevado ({$target['level']})",
                null, null, 'warning');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Não é possível impersonar utilizadores com nível 8 ou superior.'
            ]);
        }

        // Verificar se conta está ativa
        if ((int)($target['status'] ?? 0) !== 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Não é possível impersonar uma conta inativa.'
            ]);
        }

        // Guardar utilizador alvo na sessão (nunca substitui LoggedUserData)
        session()->set('ImpersonatedUserData', $target);
        session()->set('ImpersonatedAt', date('Y-m-d H:i:s'));

        log_activity('impersonation', 'start', $userId,
            "Iniciou impersonificação do utilizador #{$userId} - {$target['name']} (nível {$target['level']})",
            null, ['target_id' => $userId, 'target_name' => $target['name'], 'target_level' => $target['level']],
            'warning');

        return $this->response->setJSON([
            'success' => true,
            'message' => "A ver como: {$target['name']}",
            'redirect' => base_url('dashboard')
        ]);
    }

    /**
     * Terminar impersonificação e voltar à identidade real.
     */
    public function stop()
    {
        $impersonated = session()->get('ImpersonatedUserData');

        if ($impersonated) {
            log_activity('impersonation', 'stop', $impersonated['id'] ?? null,
                "Terminou impersonificação do utilizador {$impersonated['name']} (nível {$impersonated['level']})",
                ['target_id' => $impersonated['id'], 'target_name' => $impersonated['name']],
                null, 'info');
        }

        session()->remove('ImpersonatedUserData');
        session()->remove('ImpersonatedAt');

        return redirect()->to('dashboard')->with('success', 'Impersonificação terminada. Regressou à sua conta.');
    }

    /**
     * Pesquisa AJAX de utilizadores para o modal (nome ou email, excluindo nível >= 8 e conta própria).
     */
    public function search()
    {
        $adminData = session()->get('LoggedUserData');
        $adminLevel = $adminData['level'] ?? 0;

        if ($adminLevel < 7) {
            return $this->response->setJSON(['error' => 'Acesso negado'])->setStatusCode(403);
        }

        $q = trim($this->request->getGet('q') ?? '');
        if (strlen($q) < 2) {
            return $this->response->setJSON([]);
        }

        $adminId = $adminData['id'] ?? $adminData['ID'] ?? 0;

        $db = \Config\Database::connect();
        $results = $db->table('user')
            ->select('id, name, email, level, profile_img')
            ->groupStart()
                ->like('name', $q)
                ->orLike('email', $q)
            ->groupEnd()
            ->where('status', 1)
            ->where('level <', 8)
            ->where('id !=', $adminId)
            ->orderBy('name', 'ASC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return $this->response->setJSON($results);
    }
}
