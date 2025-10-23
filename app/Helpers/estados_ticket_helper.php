<?php

/**
 * Helper para Estados de Ticket
 * Funções auxiliares para trabalhar com estados dinâmicos de tickets
 */

if (!function_exists('getEstadoBadge')) {
    /**
     * Retorna HTML do badge para um estado
     *
     * @param string $estadoCodigo Código do estado (ex: 'novo', 'em_resolucao')
     * @param bool $comIcone Se deve incluir ícone
     * @return string HTML do badge
     */
    function getEstadoBadge(string $estadoCodigo, bool $comIcone = true): string
    {
        static $estados = null;

        // Carregar estados apenas uma vez (cache estático)
        if ($estados === null) {
            $model = new \App\Models\EstadosTicketModel();
            $estadosList = $model->getEstadosAtivos();
            $estados = [];
            foreach ($estadosList as $estado) {
                $estados[$estado['codigo']] = $estado;
            }
        }

        if (!isset($estados[$estadoCodigo])) {
            return '<span class="badge bg-secondary">Desconhecido</span>';
        }

        $estado = $estados[$estadoCodigo];
        $icone = $comIcone && !empty($estado['icone']) ? '<i class="' . esc($estado['icone']) . '"></i> ' : '';

        return sprintf(
            '<span class="badge bg-%s">%s%s</span>',
            esc($estado['cor']),
            $icone,
            esc($estado['nome'])
        );
    }
}

if (!function_exists('getEstadosSelectOptions')) {
    /**
     * Retorna opções HTML para select de estados
     *
     * @param string|null $estadoSelecionado Código do estado selecionado
     * @param bool $apenasAtivos Se deve retornar apenas estados ativos
     * @return string HTML das options
     */
    function getEstadosSelectOptions(?string $estadoSelecionado = null, bool $apenasAtivos = true): string
    {
        $model = new \App\Models\EstadosTicketModel();
        $estados = $apenasAtivos ? $model->getEstadosAtivos() : $model->findAll();

        $html = '';
        foreach ($estados as $estado) {
            $selected = ($estadoSelecionado === $estado['codigo']) ? ' selected' : '';
            $html .= sprintf(
                '<option value="%s"%s>%s</option>',
                esc($estado['codigo']),
                $selected,
                esc($estado['nome'])
            );
        }

        return $html;
    }
}

if (!function_exists('getEstadosProximosOptions')) {
    /**
     * Retorna opções HTML para select de próximos estados permitidos
     *
     * @param string $estadoAtualCodigo Código do estado atual
     * @param int $nivelUsuario Nível do usuário logado
     * @param string|null $estadoSelecionado Código do estado selecionado
     * @return string HTML das options
     */
    function getEstadosProximosOptions(string $estadoAtualCodigo, int $nivelUsuario, ?string $estadoSelecionado = null): string
    {
        $model = new \App\Models\EstadosTicketModel();
        $proximosEstados = $model->getProximosEstados($estadoAtualCodigo, $nivelUsuario);

        // Incluir estado atual como primeira opção
        $estadoAtual = $model->getEstadoPorCodigo($estadoAtualCodigo);
        if ($estadoAtual) {
            $selected = ($estadoSelecionado === $estadoAtual['codigo'] || $estadoSelecionado === null) ? ' selected' : '';
            $html = sprintf(
                '<option value="%s"%s>%s (atual)</option>',
                esc($estadoAtual['codigo']),
                $selected,
                esc($estadoAtual['nome'])
            );
        } else {
            $html = '';
        }

        // Adicionar próximos estados
        foreach ($proximosEstados as $estado) {
            $selected = ($estadoSelecionado === $estado['codigo']) ? ' selected' : '';
            $requerComentario = $estado['requer_comentario'] ? ' *' : '';
            $html .= sprintf(
                '<option value="%s"%s>%s%s</option>',
                esc($estado['codigo']),
                $selected,
                esc($estado['nome']),
                $requerComentario
            );
        }

        return $html;
    }
}

if (!function_exists('isEstadoFinal')) {
    /**
     * Verifica se um estado é final (não pode mais ser alterado)
     *
     * @param string $estadoCodigo Código do estado
     * @return bool
     */
    function isEstadoFinal(string $estadoCodigo): bool
    {
        $model = new \App\Models\EstadosTicketModel();
        $estado = $model->getEstadoPorCodigo($estadoCodigo);

        return $estado ? (bool)$estado['estado_final'] : false;
    }
}

if (!function_exists('podeEditarTicket')) {
    /**
     * Verifica se um ticket pode ser editado baseado no estado
     *
     * @param string $estadoCodigo Código do estado
     * @return bool
     */
    function podeEditarTicket(string $estadoCodigo): bool
    {
        $model = new \App\Models\EstadosTicketModel();
        $estado = $model->getEstadoPorCodigo($estadoCodigo);

        return $estado ? (bool)$estado['permite_edicao'] : false;
    }
}

if (!function_exists('podeAtribuirTecnico')) {
    /**
     * Verifica se pode atribuir técnico baseado no estado
     *
     * @param string $estadoCodigo Código do estado
     * @return bool
     */
    function podeAtribuirTecnico(string $estadoCodigo): bool
    {
        $model = new \App\Models\EstadosTicketModel();
        $estado = $model->getEstadoPorCodigo($estadoCodigo);

        return $estado ? (bool)$estado['permite_atribuicao'] : false;
    }
}

if (!function_exists('transicaoPermitida')) {
    /**
     * Verifica se uma transição de estado é permitida
     *
     * @param string $estadoOrigemCodigo Código do estado de origem
     * @param string $estadoDestinoCodigo Código do estado de destino
     * @param int $nivelUsuario Nível do usuário
     * @return bool
     */
    function transicaoPermitida(string $estadoOrigemCodigo, string $estadoDestinoCodigo, int $nivelUsuario): bool
    {
        $model = new \App\Models\EstadosTicketModel();
        return $model->transicaoPermitida($estadoOrigemCodigo, $estadoDestinoCodigo, $nivelUsuario);
    }
}

if (!function_exists('getEstadoInfo')) {
    /**
     * Retorna informações completas de um estado
     *
     * @param string $estadoCodigo Código do estado
     * @return array|null
     */
    function getEstadoInfo(string $estadoCodigo): ?array
    {
        $model = new \App\Models\EstadosTicketModel();
        return $model->getEstadoPorCodigo($estadoCodigo);
    }
}

if (!function_exists('getCorEstado')) {
    /**
     * Retorna a cor do badge de um estado
     *
     * @param string $estadoCodigo Código do estado
     * @return string
     */
    function getCorEstado(string $estadoCodigo): string
    {
        $model = new \App\Models\EstadosTicketModel();
        $estado = $model->getEstadoPorCodigo($estadoCodigo);

        return $estado ? $estado['cor'] : 'secondary';
    }
}
