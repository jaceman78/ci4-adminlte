<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TipologiaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_tipologia'   => 1,
                'nome_tipologia' => 'Regular',
                'status'         => 1,
            ],
            [
                'id_tipologia'   => 2,
                'nome_tipologia' => 'Profissional',
                'status'         => 1,
            ],
            [
                'id_tipologia'   => 3,
                'nome_tipologia' => 'CEF',
                'status'         => 0,
            ],
        ];

        // Inserir os dados
        $this->db->table('tipologia')->insertBatch($data);
        
        // Ajustar o AUTO_INCREMENT para 5 conforme especificado
        $this->db->query('ALTER TABLE tipologia AUTO_INCREMENT = 5');
        
        echo "✓ TipologiaSeeder: " . count($data) . " tipologias inseridas com sucesso\n";
    }
}
