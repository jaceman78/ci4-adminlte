<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnoLetivoSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_anoletivo' => 1,
                'anoletivo'    => 2022,
                'status'       => 0,
            ],
            [
                'id_anoletivo' => 2,
                'anoletivo'    => 2023,
                'status'       => 1,
            ],
            [
                'id_anoletivo' => 3,
                'anoletivo'    => 2024,
                'status'       => 0,
            ],
        ];

        // Inserir os dados
        $this->db->table('ano_letivo')->insertBatch($data);
        
        // Ajustar o AUTO_INCREMENT para 9 conforme especificado
        $this->db->query('ALTER TABLE ano_letivo AUTO_INCREMENT = 9');
        
        echo "✓ AnoLetivoSeeder: 3 anos letivos inseridos com sucesso\n";
    }
}
