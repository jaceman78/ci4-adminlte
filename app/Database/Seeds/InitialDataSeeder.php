<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // ESCOLAS
        $db->table('escolas')->insert([
            'nome'   => 'Escola Secundária A',
            'morada' => 'Rua Principal, 1',
        ]);
        $escolaId = $db->insertID();

        // SALAS
        $db->table('salas')->insertBatch([
            ['escola_id' => $escolaId, 'codigo_sala' => 'A101'],
            ['escola_id' => $escolaId, 'codigo_sala' => 'A102'],
            ['escola_id' => $escolaId, 'codigo_sala' => 'B201'],
        ]);
        $salaA101 = $db->table('salas')->where('codigo_sala', 'A101')->get()->getRowArray();
        $salaId = $salaA101['id'] ?? null;

        // TIPOS DE EQUIPAMENTO
        $db->table('tipos_equipamento')->insertBatch([
            ['nome' => 'Computador', 'descricao' => 'PC de secretária'],
            ['nome' => 'Portátil',  'descricao' => 'Laptop para docentes'],
            ['nome' => 'Projetor',  'descricao' => 'Projetor multimédia'],
        ]);
        $tipoComputador = $db->table('tipos_equipamento')->where('nome', 'Computador')->get()->getRowArray();
        $tipoId = $tipoComputador['id'] ?? null;

        // TIPOS DE AVARIA
        $db->table('tipos_avaria')->insertBatch([
            ['descricao' => 'Não liga'],
            ['descricao' => 'Ecrã partido'],
            ['descricao' => 'Problema de rede'],
        ]);

        // MATERIAIS
        $db->table('materiais')->insertBatch([
            ['nome' => 'Cabo HDMI', 'referencia' => 'HDMI-001', 'stock_atual' => 20],
            ['nome' => 'Toner Preto', 'referencia' => 'TN-2500', 'stock_atual' => 5],
            ['nome' => 'Teclado USB', 'referencia' => 'KB-100', 'stock_atual' => 10],
        ]);

        // EQUIPAMENTOS (sala_id é opcional nas migrations, usamos sala A101)
        if ($salaId && $tipoId) {
            $db->table('equipamentos')->insertBatch([
                [
                    'sala_id'      => $salaId,
                    'tipo_id'      => $tipoId,
                    'marca'        => 'Dell',
                    'modelo'       => 'OptiPlex 3080',
                    'numero_serie' => 'SN-Dell-0001',
                    'estado'       => 'ativo',
                    'data_aquisicao'=> date('Y-m-d'),
                ],
                [
                    'sala_id'      => $salaId,
                    'tipo_id'      => $tipoId,
                    'marca'        => 'HP',
                    'modelo'       => 'ProDesk 400',
                    'numero_serie' => 'SN-HP-0002',
                    'estado'       => 'ativo',
                    'data_aquisicao'=> date('Y-m-d'),
                ],
            ]);
        }

        // Mensagem de sucesso na consola
        echo "Seed concluído: escolas, salas, tipos_equipamento, tipos_avaria, materiais e equipamentos (quando possível).\n";
    }
}