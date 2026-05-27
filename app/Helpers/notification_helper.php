<?php

if (!function_exists('criar_notificacao')) {
    /**
     * Cria uma notificação in-app para um utilizador.
     *
     * @param int    $userId   ID do destinatário (tabela user.id)
     * @param string $modulo   Módulo de origem: 'ferias', 'tickets', 'permutas', 'convocatorias', etc.
     * @param string $tipo     'info' | 'success' | 'warning' | 'danger'
     * @param string $titulo   Título curto da notificação
     * @param string $mensagem Descrição completa
     * @param string $url      URL para o registo afetado (base_url(...))
     * @return bool
     */
    function criar_notificacao(
        int    $userId,
        string $modulo,
        string $tipo,
        string $titulo,
        string $mensagem,
        string $url = ''
    ): bool {
        try {
            if (!in_array($tipo, ['info', 'success', 'warning', 'danger'])) {
                $tipo = 'info';
            }

            $model = new \App\Models\NotificationModel();
            return $model->insert([
                'user_id'   => $userId,
                'modulo'    => $modulo,
                'tipo'      => $tipo,
                'titulo'    => $titulo,
                'mensagem'  => $mensagem,
                'url'       => $url,
                'lida'      => 0,
                'criado_em' => date('Y-m-d H:i:s'),
            ]) !== false;
        } catch (\Exception $e) {
            log_message('error', 'criar_notificacao: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notificar_utilizadores_por_level')) {
    /**
     * Cria a mesma notificação para todos os utilizadores ativos com determinados levels.
     *
     * @param array  $levels  Ex: [3, 6, 7, 8, 9]
     * @param string $modulo
     * @param string $tipo
     * @param string $titulo
     * @param string $mensagem
     * @param string $url
     * @param int|null $excluirUserId  Não notificar este ID (ex: o próprio autor)
     */
    function notificar_utilizadores_por_level(
        array  $levels,
        string $modulo,
        string $tipo,
        string $titulo,
        string $mensagem,
        string $url = '',
        ?int   $excluirUserId = null
    ): void {
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('user')
                         ->select('id')
                         ->whereIn('level', $levels)
                         ->where('status', 1);

            if ($excluirUserId) {
                $builder->where('id !=', $excluirUserId);
            }

            $users = $builder->get()->getResultArray();

            $model = new \App\Models\NotificationModel();
            $agora = date('Y-m-d H:i:s');

            foreach ($users as $user) {
                $model->insert([
                    'user_id'   => (int) $user['id'],
                    'modulo'    => $modulo,
                    'tipo'      => $tipo,
                    'titulo'    => $titulo,
                    'mensagem'  => $mensagem,
                    'url'       => $url,
                    'lida'      => 0,
                    'criado_em' => $agora,
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'notificar_utilizadores_por_level: ' . $e->getMessage());
        }
    }
}
