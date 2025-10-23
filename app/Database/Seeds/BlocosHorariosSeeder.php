<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BlocosHorariosSeeder extends Seeder
{
    public function run()
    {
        // Definir os blocos horários base
        $blocos = [
            ['hora_inicio' => '08:00:00', 'hora_fim' => '08:50:00', 'designacao' => '1º Bloco'],
            ['hora_inicio' => '08:50:00', 'hora_fim' => '09:40:00', 'designacao' => '2º Bloco'],
            ['hora_inicio' => '09:50:00', 'hora_fim' => '10:40:00', 'designacao' => '3º Bloco'],
            ['hora_inicio' => '10:40:00', 'hora_fim' => '11:30:00', 'designacao' => '4º Bloco'],
            ['hora_inicio' => '11:40:00', 'hora_fim' => '12:30:00', 'designacao' => '5º Bloco'],
            ['hora_inicio' => '12:30:00', 'hora_fim' => '13:20:00', 'designacao' => '6º Bloco'],
            ['hora_inicio' => '13:30:00', 'hora_fim' => '14:20:00', 'designacao' => '7º Bloco'],
            ['hora_inicio' => '14:20:00', 'hora_fim' => '15:10:00', 'designacao' => '8º Bloco'],
            ['hora_inicio' => '15:20:00', 'hora_fim' => '16:10:00', 'designacao' => '9º Bloco'],
            ['hora_inicio' => '16:10:00', 'hora_fim' => '17:00:00', 'designacao' => '10º Bloco'],
            ['hora_inicio' => '17:10:00', 'hora_fim' => '18:00:00', 'designacao' => '11º Bloco'],
            ['hora_inicio' => '18:00:00', 'hora_fim' => '18:50:00', 'designacao' => '12º Bloco'],
        ];

        // Dias da semana
        $dias = ['Segunda_Feira', 'Terca_Feira', 'Quarta_Feira', 'Quinta_Feira', 'Sexta_Feira', 'Sabado'];

        $data = [];
        
        // Criar blocos para cada dia da semana
        foreach ($dias as $dia) {
            foreach ($blocos as $bloco) {
                $data[] = [
                    'hora_inicio' => $bloco['hora_inicio'],
                    'hora_fim'    => $bloco['hora_fim'],
                    'designacao'  => $bloco['designacao'],
                    'dia_semana'  => $dia,
                ];
            }
        }

        // Inserir os dados em lotes
        $this->db->table('blocos_horarios')->insertBatch($data);
        
        echo "✓ BlocosHorariosSeeder: " . count($data) . " blocos horários inseridos com sucesso\n";
        echo "  - " . count($blocos) . " blocos por dia\n";
        echo "  - " . count($dias) . " dias da semana\n";
    }
}
