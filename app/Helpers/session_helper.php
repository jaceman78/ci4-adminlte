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