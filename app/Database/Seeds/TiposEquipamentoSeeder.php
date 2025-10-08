<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TiposEquipamentoSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nome' => 'Computador',
                'descricao' => 'Desktop ou portátil'
            ],
            [
                'nome' => 'Impressora',
                'descricao' => 'Impressora laser ou jato de tinta'
            ],
            [
                'nome' => 'Projetor',
                'descricao' => 'Projetor multimédia'
            ],
            [
                'nome' => 'Switch',
                'descricao' => 'Equipamento de rede'
            ],
            [
                'nome' => 'Tablet',
                'descricao' => 'Dispositivo móvel'
            ],
        ];

        // Insere os dados
        $this->db->table('tipos_equipamento')->insertBatch($data);
    }
}