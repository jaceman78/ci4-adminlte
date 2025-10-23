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

if (!function_exists('is_user_logged_in')) {
    function is_user_logged_in(): bool
    {
        return session()->has('user_id') || session()->has('id');
    }
}

if (!function_exists('get_user_level_name')) {
    /**
     * Retorna o nome do nível de acesso do utilizador
     * 
     * @param int|null $level Nível do utilizador (se não fornecido, usa o da sessão)
     * @return string Nome do nível de acesso
     */
    function get_user_level_name(?int $level = null): string
    {
        if ($level === null) {
            $level = session()->get('LoggedUserData')['level'] ?? 0;
        }

        $levels = [
            9 => 'Super Administrador',
            8 => 'Administrador',
            7 => 'Técnico Sénior',
            6 => 'Técnico',
            5 => 'Técnico Júnior',
            4 => 'Utilizador Avançado',
            3 => 'Utilizador',
            2 => 'Utilizador',
            1 => 'Utilizador',
            0 => 'Convidado',
        ];

        return $levels[$level] ?? 'Utilizador';
    }
}