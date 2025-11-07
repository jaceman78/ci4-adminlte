<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameNomeToAbreviaturaInDisciplina extends Migration
{
    public function up()
    {
        // Renomear campo nome para abreviatura
        $fields = [
            'nome' => [
                'name' => 'abreviatura',
                'type' => 'VARCHAR',
                'constraint' => 255,
            ]
        ];
        
        $this->forge->modifyColumn('disciplina', $fields);
    }

    public function down()
    {
        // Reverter: renomear abreviatura para nome
        $fields = [
            'abreviatura' => [
                'name' => 'nome',
                'type' => 'VARCHAR',
                'constraint' => 255,
            ]
        ];
        
        $this->forge->modifyColumn('disciplina', $fields);
    }
}
