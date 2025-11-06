<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDescritivoToDisciplina extends Migration
{
    public function up()
    {
        // Adicionar campo descritivo após o campo nome
        $this->forge->addColumn('disciplina', [
            'descritivo' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'nome',
                'comment'    => 'Descrição detalhada da disciplina'
            ]
        ]);
    }

    public function down()
    {
        // Remover o campo descritivo
        $this->forge->dropColumn('disciplina', 'descritivo');
    }
}
