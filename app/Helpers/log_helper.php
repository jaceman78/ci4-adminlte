<?php
use App\Models\ActivityLogModel;

if (!function_exists('log_activity')) {
    /**
     * Regista uma ação na tabela logs_atividade
     */
    function log_activity(
        ?int $userId,
        string $modulo,
        string $acao,
        string $descricao,
        ?int $registroId = null,
        ?array $dadosAnteriores = null,
        ?array $dadosNovos = null,
        ?array $detalhes = null
    ): bool {
        // Se não houver user_id, não registar o log (evita erros de FK)
        if (!$userId) {
            log_message('warning', "Tentativa de log sem user_id: {$modulo}/{$acao}");
            return false;
        }

        try {
            // Verificar se o utilizador existe antes de inserir
            $userModel = new \App\Models\UserModel();
            $userExists = $userModel->find($userId);
            
            if (!$userExists) {
                log_message('warning', "Tentativa de log com user_id inexistente: {$userId}");
                return false;
            }

            $model = new ActivityLogModel();

            $data = [
                'user_id'         => $userId,
                'modulo'          => $modulo,
                'acao'            => $acao,
                'descricao'       => $descricao,
                'registro_id'     => $registroId,
                'dados_anteriores'=> $dadosAnteriores ? json_encode($dadosAnteriores) : null,
                'dados_novos'     => $dadosNovos ? json_encode($dadosNovos) : null,
                'ip_address'      => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent'      => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'detalhes'        => $detalhes ? json_encode($detalhes) : null,
                'criado_em'       => date('Y-m-d H:i:s')
            ];

            return $model->insert($data);
        } catch (\Exception $e) {
            log_message('error', "Erro ao registar log de atividade: " . $e->getMessage());
            return false;
        }
    }
}