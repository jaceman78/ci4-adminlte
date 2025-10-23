<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDisciplinaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_disciplina' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
                'auto_increment' => true,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'horas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => true,
                'comment'    => 'Carga horária semanal'
            ],
            'tipologia_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id_disciplina', true);
        
        // Foreign Key
        $this->forge->addForeignKey('tipologia_id', 'tipologia', 'id_tipologia', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('disciplina', true, [
            'ENGINE'         => 'InnoDB',
            'DEFAULT CHARSET'=> 'latin1',
            'COLLATE'        => 'latin1_swedish_ci'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('disciplina', true);
    }
}
