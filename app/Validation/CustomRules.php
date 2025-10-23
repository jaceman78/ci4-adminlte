<?php

namespace App\Validation;

use App\Models\EstadosTicketModel;

class CustomRules
{
    /**
     * Valida se o estado do ticket é válido e está ativo
     */
    public function validar_estado_ticket(string $value, ?string &$error = null): bool
    {
        $estadosModel = new EstadosTicketModel();
        $estado = $estadosModel->getEstadoPorCodigo($value);

        if (!$estado) {
            $error = 'O estado do ticket é inválido.';
            return false;
        }

        return true;
    }

    /**
     * Valida se a transição de estado é permitida
     * 
     * Uso: validar_transicao_estado[estado_atual,nivel_usuario]
     */
    public function validar_transicao_estado(string $estadoDestino, string $params, array $data, ?string &$error = null): bool
    {
        $params = explode(',', $params);
        
        if (count($params) < 2) {
            $error = 'Parâmetros insuficientes para validação de transição.';
            return false;
        }

        $estadoAtual = $params[0];
        $nivelUsuario = (int)$params[1];

        $estadosModel = new EstadosTicketModel();
        
        if (!$estadosModel->transicaoPermitida($estadoAtual, $estadoDestino, $nivelUsuario)) {
            $error = 'Transição de estado não permitida.';
            return false;
        }

        return true;
    }
}
