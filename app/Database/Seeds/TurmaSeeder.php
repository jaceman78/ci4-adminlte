<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TurmaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Ano letivo 2022 (id_anoletivo = 1) - Regular
            ['ano' => 10, 'nome' => 'G', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 4, 'nome' => '3.º/4.º A N', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 10, 'nome' => '10GPSI', 'anoletivo_id' => 1, 'tipologia_id' => 2],
            ['ano' => 9, 'nome' => '9EJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 4, 'nome' => '4.ºAJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 10, 'nome' => '10B', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 12, 'nome' => '12E', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 11, 'nome' => '11E', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 6, 'nome' => '6C', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 9, 'nome' => '9A', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 9, 'nome' => '9C', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 9, 'nome' => '9E', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 6, 'nome' => '6A', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 11, 'nome' => '11GPSI', 'anoletivo_id' => 1, 'tipologia_id' => 2],
            ['ano' => 12, 'nome' => '12C', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 5, 'nome' => '5D', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 10, 'nome' => '10C', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 3, 'nome' => '3.º/4.º AJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 3, 'nome' => '3.º AN', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 9, 'nome' => '9DJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 4, 'nome' => '4.º BM', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 10, 'nome' => '10J', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 7, 'nome' => '7D', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 5, 'nome' => '5A', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 10, 'nome' => '10E', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 4, 'nome' => '4.º AM', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 10, 'nome' => '10H', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 8, 'nome' => '8D', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 10, 'nome' => '10I', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 7, 'nome' => '7B', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 11, 'nome' => '11A', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 6, 'nome' => '6B', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 9, 'nome' => '9B', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 3, 'nome' => '3.º AM', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 5, 'nome' => '5C', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 8, 'nome' => '8B', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 10, 'nome' => '10F', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 9, 'nome' => '9D', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 4, 'nome' => '4.º A N', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 10, 'nome' => '10A', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 10, 'nome' => '10GEI', 'anoletivo_id' => 1, 'tipologia_id' => 2],
            ['ano' => 10, 'nome' => '10D', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 8, 'nome' => '8A', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 12, 'nome' => '12F', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 7, 'nome' => '7A', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 8, 'nome' => '8E', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 12, 'nome' => '12G', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 6, 'nome' => '6E', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 12, 'nome' => 'GPSI_20_23', 'anoletivo_id' => 1, 'tipologia_id' => 2],
            ['ano' => 9, 'nome' => '9BJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 9, 'nome' => '9F', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 11, 'nome' => '11GEI', 'anoletivo_id' => 1, 'tipologia_id' => 2],
            ['ano' => 2, 'nome' => '2.º BM', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 12, 'nome' => '12A', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 11, 'nome' => '11C', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 11, 'nome' => '11D', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 9, 'nome' => '9AJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 12, 'nome' => 'GEI_20_23', 'anoletivo_id' => 1, 'tipologia_id' => 2],
            ['ano' => 1, 'nome' => '1.º CM', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 9, 'nome' => '9CJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 12, 'nome' => '12D', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 11, 'nome' => '11B', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 3, 'nome' => '3.º BM', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 12, 'nome' => '12B', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 11, 'nome' => '11F', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 1, 'nome' => '1.º BM', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 7, 'nome' => '7C', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 5, 'nome' => '5B', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 2, 'nome' => '2.º AN', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 0, 'nome' => 'Pré AN', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 2, 'nome' => '2.º AM', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 1, 'nome' => '1.º AM', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 1, 'nome' => '1.ºAN', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 0, 'nome' => 'Pré AM', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 8, 'nome' => '8EJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 8, 'nome' => '8CJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 8, 'nome' => '8AJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 8, 'nome' => '8BJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 7, 'nome' => '7BJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 8, 'nome' => '8DJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 2, 'nome' => '2.ºAJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 7, 'nome' => '7E', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 8, 'nome' => '8C', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 1, 'nome' => '1.ºAJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 0, 'nome' => 'Pré DM', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 2, 'nome' => '2.º BN', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 0, 'nome' => 'Pré CM', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 7, 'nome' => '7CJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 7, 'nome' => '7DJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 7, 'nome' => '7AJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 7, 'nome' => '7FJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 6, 'nome' => '6D', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 4, 'nome' => '4.º CM', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 11, 'nome' => '11G', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 0, 'nome' => 'Pré BM', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            ['ano' => 7, 'nome' => '7EJ', 'anoletivo_id' => 1, 'tipologia_id' => 1],
            
            // Ano letivo 2023 (id_anoletivo = 2)
            ['ano' => 11, 'nome' => 'G', 'anoletivo_id' => 2, 'tipologia_id' => 1],
            ['ano' => 10, 'nome' => '9EJ', 'anoletivo_id' => 2, 'tipologia_id' => 1],
            ['ano' => 11, 'nome' => '11GEI', 'anoletivo_id' => 2, 'tipologia_id' => 2],
            ['ano' => 12, 'nome' => '12GPSI', 'anoletivo_id' => 2, 'tipologia_id' => 2],
        ];

        // Inserir os dados
        foreach ($data as $turma) {
            $this->db->table('turma')->insert($turma);
        }
        
        echo "✓ TurmaSeeder: " . count($data) . " turmas inseridas com sucesso\n";
    }
}
