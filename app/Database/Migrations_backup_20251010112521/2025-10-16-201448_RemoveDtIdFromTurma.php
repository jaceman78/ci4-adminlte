<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveDtIdFromTurma extends Migration
{
    public function up()
    {
        // Remover coluna dt_id da tabela turma
        $this->forge->dropColumn('turma', 'dt_id');
    }

    public function down()
    {
        // Adicionar coluna dt_id de volta
        $fields = [
            'dt_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'nome'
            ]
        ];
        $this->forge->addColumn('turma', $fields);
    }
}
