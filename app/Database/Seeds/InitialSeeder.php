<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        /**
         * USERS
         */


        /**
         * ESCOLAS
         */
        $escolas = [
            ['nome' => 'Escola Secundária Central', 'morada' => 'Rua Principal, 123'],
            ['nome' => 'Escola Básica 2,3 Norte', 'morada' => 'Av. do Norte, 45']
        ];
        $this->db->table('escolas')->insertBatch($escolas);

        /**
         * SALAS
         */
        $salas = [
            ['escola_id' => 1, 'codigo_sala' => 'Lab1'],
            ['escola_id' => 1, 'codigo_sala' => 'Lab2'],
            ['escola_id' => 2, 'codigo_sala' => 'Sala TIC'],
        ];
        $this->db->table('salas')->insertBatch($salas);

        /**
         * EQUIPAMENTOS
         */
        $equipamentos = [
            ['sala_id' => 1, 'tipo' => 'PC', 'marca' => 'Dell', 'modelo' => 'Optiplex 7070', 'numero_serie' => 'SN123456'],
            ['sala_id' => 1, 'tipo' => 'Projetor', 'marca' => 'Epson', 'modelo' => 'EB-X41', 'numero_serie' => 'PRJ001'],
            ['sala_id' => 3, 'tipo' => 'Impressora', 'marca' => 'HP', 'modelo' => 'LaserJet 1020', 'numero_serie' => 'IMP-22'],
        ];
        $this->db->table('equipamentos')->insertBatch($equipamentos);

        /**
         * TIPOS AVARIA
         */
        $tiposAvaria = [
            ['descricao' => 'Não liga'],
            ['descricao' => 'Ecrã sem imagem'],
            ['descricao' => 'Problema de rede'],
            ['descricao' => 'Ruptura de cabo'],
            ['descricao' => 'Driver desatualizado'],
        ];
        $this->db->table('tipos_avaria')->insertBatch($tiposAvaria);

        /**
         * MATERIAIS
         */
        $materiais = [
            ['nome' => 'Disco SSD 240GB', 'referencia' => 'SSD240', 'stock_atual' => 5],
            ['nome' => 'Fonte de alimentação 500W', 'referencia' => 'PSU500', 'stock_atual' => 3],
            ['nome' => 'Cabo HDMI 2m', 'referencia' => 'HDMI2', 'stock_atual' => 10],
            ['nome' => 'Toner Preto HP 12A', 'referencia' => 'TN-HP12A', 'stock_atual' => 8],
        ];
        $this->db->table('materiais')->insertBatch($materiais);

        /**
         * TICKET DE EXEMPLO
         */
        $ticket = [
            'equipamento_id' => 1,
            'tipo_avaria_id' => 1,
            'reportado_por'  => 1,
            'descricao'      => 'PC não arranca mesmo após reinício.',
            'estado'         => 'novo',
            'prioridade'     => 'alta',
            'criado_em'      => date('Y-m-d H:i:s')
        ];
        $this->db->table('tickets')->insert($ticket);
    }
}
