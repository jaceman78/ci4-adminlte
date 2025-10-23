<?php

/**
 * Helper para gestão de estados de tickets
 */

if (!function_exists('get_estado_badge')) {
    /**
     * Retorna o HTML do badge do estado
     * 
     * @param string $codigo Código do estado
     * @param bool $comIcone Se deve incluir ícone
     * @return string HTML do badge
     */
    function get_estado_badge(string $codigo, bool $comIcone = true): string
    {
        $estadosModel = new \App\Models\EstadosTicketModel();
        return $estadosModel->renderBadge($codigo, $comIcone);
    }
}

if (!function_exists('get_estados_dropdown')) {
    /**
     * Retorna array com estados para dropdown
     * 
     * @return array
     */
    function get_estados_dropdown(): array
    {
        $estadosModel = new \App\Models\EstadosTicketModel();
        return $estadosModel->getEstadosDropdown();
    }
}

if (!function_exists('get_estados_ativos')) {
    /**
     * Retorna todos os estados ativos
     * 
     * @return array
     */
    function get_estados_ativos(): array
    {
        $estadosModel = new \App\Models\EstadosTicketModel();
        return $estadosModel->getEstadosAtivos();
    }
}

if (!function_exists('pode_transicionar_estado')) {
    /**
     * Verifica se pode fazer transição de um estado para outro
     * 
     * @param string $estadoOrigem Código do estado atual
     * @param string $estadoDestino Código do estado destino
     * @param int $nivelUsuario Nível do usuário
     * @return bool
     */
    function pode_transicionar_estado(string $estadoOrigem, string $estadoDestino, int $nivelUsuario): bool
    {
        $estadosModel = new \App\Models\EstadosTicketModel();
        return $estadosModel->transicaoPermitida($estadoOrigem, $estadoDestino, $nivelUsuario);
    }
}

if (!function_exists('get_proximos_estados')) {
    /**
     * Retorna os próximos estados possíveis baseado no estado atual e nível do usuário
     * 
     * @param string $estadoAtual Código do estado atual
     * @param int $nivelUsuario Nível do usuário
     * @return array
     */
    function get_proximos_estados(string $estadoAtual, int $nivelUsuario): array
    {
        $estadosModel = new \App\Models\EstadosTicketModel();
        return $estadosModel->getProximosEstados($estadoAtual, $nivelUsuario);
    }
}

if (!function_exists('get_estado_info')) {
    /**
     * Retorna informações completas do estado
     * 
     * @param string $codigo Código do estado
     * @return array|null
     */
    function get_estado_info(string $codigo): ?array
    {
        $estadosModel = new \App\Models\EstadosTicketModel();
        return $estadosModel->getEstadoPorCodigo($codigo);
    }
}

if (!function_exists('validar_estados_lista')) {
    /**
     * Cria string de validação para in_list com estados ativos
     * 
     * @return string
     */
    function validar_estados_lista(): string
    {
        $estados = get_estados_ativos();
        $codigos = array_column($estados, 'codigo');
        return 'in_list[' . implode(',', $codigos) . ']';
    }
}
