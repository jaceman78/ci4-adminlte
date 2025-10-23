<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EstadosTicketSeeder extends Seeder
{
    public function run()
    {
        // Estados base do sistema
        $estados = [
            [
                'id'                  => 1,
                'codigo'              => 'novo',
                'nome'                => 'Novo',
                'descricao'           => 'Ticket criado, aguarda atribuição a um técnico',
                'cor'                 => 'primary',
                'icone'               => 'fas fa-plus-circle',
                'ordem'               => 1,
                'ativo'               => 1,
                'permite_edicao'      => 1,
                'permite_atribuicao'  => 1,
                'estado_final'        => 0,
            ],
            [
                'id'                  => 2,
                'codigo'              => 'em_resolucao',
                'nome'                => 'Em Resolução',
                'descricao'           => 'Ticket atribuído a um técnico e em processo de resolução',
                'cor'                 => 'warning',
                'icone'               => 'fas fa-wrench',
                'ordem'               => 2,
                'ativo'               => 1,
                'permite_edicao'      => 0,
                'permite_atribuicao'  => 1,
                'estado_final'        => 0,
            ],
            [
                'id'                  => 3,
                'codigo'              => 'aguarda_peca',
                'nome'                => 'Aguarda Peça',
                'descricao'           => 'Ticket em pausa, aguarda chegada de peça/material necessário',
                'cor'                 => 'primary',
                'icone'               => 'fas fa-hourglass-half',
                'ordem'               => 3,
                'ativo'               => 1,
                'permite_edicao'      => 0,
                'permite_atribuicao'  => 1,
                'estado_final'        => 0,
            ],
            [
                'id'                  => 4,
                'codigo'              => 'reparado',
                'nome'                => 'Reparado',
                'descricao'           => 'Ticket resolvido com sucesso',
                'cor'                 => 'success',
                'icone'               => 'fas fa-check-circle',
                'ordem'               => 4,
                'ativo'               => 1,
                'permite_edicao'      => 0,
                'permite_atribuicao'  => 0,
                'estado_final'        => 1,
            ],
            [
                'id'                  => 5,
                'codigo'              => 'anulado',
                'nome'                => 'Anulado',
                'descricao'           => 'Ticket cancelado ou anulado',
                'cor'                 => 'danger',
                'icone'               => 'fas fa-times-circle',
                'ordem'               => 5,
                'ativo'               => 1,
                'permite_edicao'      => 0,
                'permite_atribuicao'  => 0,
                'estado_final'        => 1,
            ],
        ];

        // Inserir estados
        $builder = $this->db->table('estados_ticket');
        foreach ($estados as $estado) {
            $estado['created_at'] = date('Y-m-d H:i:s');
            $estado['updated_at'] = date('Y-m-d H:i:s');
            $builder->insert($estado);
        }

        // Definir transições permitidas no workflow
        $transicoes = [
            // De Novo para...
            ['estado_origem_id' => 1, 'estado_destino_id' => 2, 'nivel_minimo' => 5, 'requer_comentario' => 0], // Novo -> Em Resolução
            ['estado_origem_id' => 1, 'estado_destino_id' => 5, 'nivel_minimo' => 8, 'requer_comentario' => 1], // Novo -> Anulado (só Admin)
            
            // De Em Resolução para...
            ['estado_origem_id' => 2, 'estado_destino_id' => 3, 'nivel_minimo' => 5, 'requer_comentario' => 1], // Em Resolução -> Aguarda Peça
            ['estado_origem_id' => 2, 'estado_destino_id' => 4, 'nivel_minimo' => 5, 'requer_comentario' => 0], // Em Resolução -> Reparado
            ['estado_origem_id' => 2, 'estado_destino_id' => 5, 'nivel_minimo' => 8, 'requer_comentario' => 1], // Em Resolução -> Anulado (só Admin)
            
            // De Aguarda Peça para...
            ['estado_origem_id' => 3, 'estado_destino_id' => 2, 'nivel_minimo' => 5, 'requer_comentario' => 0], // Aguarda Peça -> Em Resolução
            ['estado_origem_id' => 3, 'estado_destino_id' => 4, 'nivel_minimo' => 5, 'requer_comentario' => 0], // Aguarda Peça -> Reparado
            ['estado_origem_id' => 3, 'estado_destino_id' => 5, 'nivel_minimo' => 8, 'requer_comentario' => 1], // Aguarda Peça -> Anulado (só Admin)
            
            // De Reparado para...
            ['estado_origem_id' => 4, 'estado_destino_id' => 2, 'nivel_minimo' => 5, 'requer_comentario' => 1], // Reparado -> Em Resolução (reabertura)
            
            // De Anulado para...
            ['estado_origem_id' => 5, 'estado_destino_id' => 1, 'nivel_minimo' => 8, 'requer_comentario' => 1], // Anulado -> Novo (reativar - só Admin)
        ];

        $builderTransicoes = $this->db->table('estados_ticket_transicoes');
        foreach ($transicoes as $transicao) {
            $transicao['ativo'] = 1;
            $transicao['created_at'] = date('Y-m-d H:i:s');
            $transicao['updated_at'] = date('Y-m-d H:i:s');
            $builderTransicoes->insert($transicao);
        }

        echo "Estados de ticket e transições criados com sucesso!\n";
    }
}
