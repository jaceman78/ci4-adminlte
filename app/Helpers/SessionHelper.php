// app/Helpers/SessionHelper.php (criar se não existir)
<?php

if (!function_exists('get_current_user_id')) {
    function get_current_user_id(): ?int
    {
        return session()->get('user_id') ?? session()->get('id') ?? null;
    }
}

if (!function_exists('get_current_user_level')) {
    function get_current_user_level(): int
    {
        return session()->get('level') ?? 0;
    }
}
        // Log de acesso à página de logs
        $userId = session()->get('user_id') ?? session()->get('id');
        if ($userId) {
            log_activity(
                (int)$userId,
                'logs',
                'view_page',
                'Acedeu à página de logs de atividade'
            );
        }
