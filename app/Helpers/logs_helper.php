<?php

if (!function_exists('log_activity')) {
    /**
     * Helper para registrar atividades no sistema
     * 
     * @param string $module Módulo (permutas, creditos, horarios, tickets, etc)
     * @param string $action Ação (create, update, delete, approve, reject, etc)
     * @param mixed $recordId ID do registro
     * @param string $description Descrição da ação
     * @param array|null $oldValues Valores antigos
     * @param array|null $newValues Valores novos
     * @param string $severity Severidade (info, warning, error, critical)
     * @return bool
     */
    function log_activity(
        string $module,
        string $action,
        $recordId = null,
        string $description = '',
        ?array $oldValues = null,
        ?array $newValues = null,
        string $severity = 'info'
    ): bool {
        try {
            // Evitar recursão: não gravar logs do próprio sistema de logs
            if ($module === 'logs') {
                return true; // Retornar sucesso mas não gravar
            }
            
            $activityLogModel = new \App\Models\ActivityLogModel();
            $session = session();
            
            // Obter dados do usuário da sessão
            $userData = $session->get('LoggedUserData');
            
            // Obter user_id - tentar ambas as chaves (ID maiúsculo e id minúsculo)
            $userId = $userData['ID'] ?? $userData['id'] ?? null;
            
            // Se não houver user_id, não registar o log (evita erro de FK)
            if (!$userId) {
                log_message('warning', "Tentativa de log sem user_id. Dados da sessão: " . json_encode($userData));
                return false;
            }
            
            // Obter IP e User Agent
            $request = \Config\Services::request();
            
            $logData = [
                'user_id' => $userId,
                'modulo' => $module,
                'acao' => $action,
                'registro_id' => $recordId,
                'descricao' => $description,
                'dados_anteriores' => $oldValues,
                'dados_novos' => $newValues,
                'ip_address' => $request->getIPAddress(),
                'user_agent' => $request->getUserAgent()->getAgentString(),
                'criado_em' => date('Y-m-d H:i:s')
            ];
            
            return $activityLogModel->logActivity($logData);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao registrar log: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('log_permuta')) {
    /**
     * Log específico para permutas
     */
    function log_permuta(string $action, $permutaId, string $description, ?array $oldValues = null, ?array $newValues = null): bool
    {
        return log_activity('permutas', $action, $permutaId, $description, $oldValues, $newValues);
    }
}

if (!function_exists('log_credito')) {
    /**
     * Log específico para créditos
     */
    function log_credito(string $action, $creditoId, string $description, ?array $oldValues = null, ?array $newValues = null): bool
    {
        return log_activity('creditos', $action, $creditoId, $description, $oldValues, $newValues);
    }
}

if (!function_exists('log_horario')) {
    /**
     * Log específico para horários
     */
    function log_horario(string $action, $horarioId, string $description, ?array $oldValues = null, ?array $newValues = null): bool
    {
        return log_activity('horarios', $action, $horarioId, $description, $oldValues, $newValues);
    }
}

if (!function_exists('log_ticket')) {
    /**
     * Log específico para tickets
     */
    function log_ticket(string $action, $ticketId, string $description, ?array $oldValues = null, ?array $newValues = null): bool
    {
        return log_activity('tickets', $action, $ticketId, $description, $oldValues, $newValues);
    }
}

if (!function_exists('log_error_activity')) {
    /**
     * Log de erro com severidade error
     */
    function log_error_activity(string $module, string $action, string $description, $recordId = null): bool
    {
        return log_activity($module, $action, $recordId, $description, null, null, 'error');
    }
}

if (!function_exists('log_critical_activity')) {
    /**
     * Log crítico
     */
    function log_critical_activity(string $module, string $action, string $description, $recordId = null): bool
    {
        return log_activity($module, $action, $recordId, $description, null, null, 'critical');
    }
}

if (!function_exists('log_warning_activity')) {
    /**
     * Log de aviso
     */
    function log_warning_activity(string $module, string $action, string $description, $recordId = null): bool
    {
        return log_activity($module, $action, $recordId, $description, null, null, 'warning');
    }
}

if (!function_exists('get_user_logs')) {
    /**
     * Obter logs de um usuário
     */
    function get_user_logs($userIdentifier, int $limit = 50): array
    {
        $logsModel = new \App\Models\LogsModel();
        return $logsModel->getLogsByUser($userIdentifier, $limit);
    }
}

if (!function_exists('get_module_logs')) {
    /**
     * Obter logs de um módulo
     */
    function get_module_logs(string $module, int $limit = 100): array
    {
        $logsModel = new \App\Models\LogsModel();
        return $logsModel->getLogsByModule($module, $limit);
    }
}

if (!function_exists('get_record_logs')) {
    /**
     * Obter histórico completo de um registro
     */
    function get_record_logs(string $module, $recordId, int $limit = 50): array
    {
        $logsModel = new \App\Models\LogsModel();
        return $logsModel->getLogsByRecord($module, $recordId, $limit);
    }
}
