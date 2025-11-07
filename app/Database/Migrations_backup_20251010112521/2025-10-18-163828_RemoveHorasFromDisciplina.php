<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveHorasFromDisciplina extends Migration
{
    public function up()
    {
        // Remover campo horas da tabela disciplina
        $this->forge->dropColumn('disciplina', 'horas');
    }

    public function down()
    {
        // Recriar campo horas caso seja necessário rollback
        $fields = [
            'horas' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'descritivo'
            ]
        ];
        
        $this->forge->addColumn('disciplina', $fields);
    }
}
