<?php

use App\Models\ActivityLogModel;

if (!function_exists('get_current_user_id')) {
    /**
     * Obter o ID do utilizador atual da sessão
     */
    function get_current_user_id(): ?int
    {
        $session = session();
        $userId = $session->get('user_id') ?? $session->get('id');
        return $userId ? (int)$userId : null;
    }
}

if (!function_exists('log_activity')) {
    /**
     * Registar atividade no log (versão melhorada)
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
        try {
            $activityLogModel = new ActivityLogModel();
            
            // Obter informações da requisição
            $request = \Config\Services::request();
            $ipAddress = $request->getIPAddress();
            $userAgent = $request->getUserAgent()->getAgentString();

            $data = [
                'user_id' => $userId,
                'modulo' => $modulo,
                'acao' => $acao,
                'registro_id' => $registroId,
                'descricao' => $descricao,
                'dados_anteriores' => $dadosAnteriores ? json_encode($dadosAnteriores) : null,
                'dados_novos' => $dadosNovos ? json_encode($dadosNovos) : null,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'detalhes' => $detalhes ? json_encode($detalhes) : null,
                'criado_em' => date('Y-m-d H:i:s')
            ];

            return $activityLogModel->insert($data) !== false;
        } catch (Exception $e) {
            // Log do erro (opcional)
            log_message('error', 'Erro ao registar atividade: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('log_user_activity')) {
    /**
     * Registar atividade de utilizador (shortcut)
     */
    function log_user_activity(
        string $acao,
        string $descricao,
        ?int $registroId = null,
        ?array $dadosAnteriores = null,
        ?array $dadosNovos = null
    ): bool {
        $userId = get_current_user_id();
        return log_activity($userId, 'users', $acao, $descricao, $registroId, $dadosAnteriores, $dadosNovos);
    }
}

if (!function_exists('log_escola_activity')) {
    /**
     * Registar atividade de escola (shortcut)
     */
    function log_escola_activity(
        string $acao,
        string $descricao,
        ?int $escolaId = null,
        ?array $dadosAnteriores = null,
        ?array $dadosNovos = null
    ): bool {
        $userId = get_current_user_id();
        return log_activity($userId, 'escolas', $acao, $descricao, $escolaId, $dadosAnteriores, $dadosNovos);
    }
}

if (!function_exists('log_sala_activity')) {
    /**
     * Registar atividade de sala (shortcut)
     */
    function log_sala_activity(
        string $acao,
        string $descricao,
        ?int $salaId = null,
        ?array $dadosAnteriores = null,
        ?array $dadosNovos = null
    ): bool {
        $userId = get_current_user_id();
        return log_activity($userId, 'salas', $acao, $descricao, $salaId, $dadosAnteriores, $dadosNovos);
    }
}

if (!function_exists('sanitize_log_data')) {
    /**
     * Sanitizar dados sensíveis antes de registar no log
     */
    function sanitize_log_data(array $data): array
    {
        $sensitiveFields = ['password', 'password_confirm', 'token', 'secret', 'key', 'api_key'];
        
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $sensitiveFields)) {
                $data[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $data[$key] = sanitize_log_data($value);
            }
        }
        
        return $data;
    }
}
