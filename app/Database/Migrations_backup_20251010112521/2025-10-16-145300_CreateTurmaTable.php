<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTurmaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_turma' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
                'auto_increment' => true,
            ],
            'ano' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'Ano de escolaridade (0=Pré-escolar, 1-12)'
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 63,
                'null'       => true,
                'comment'    => 'Nome/Identificação da turma'
            ],
            'dt_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'ID do Diretor de Turma'
            ],
            'anoletivo_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'tipologia_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id_turma', true);
        
        // Foreign Keys
        $this->forge->addForeignKey('anoletivo_id', 'ano_letivo', 'id_anoletivo', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('tipologia_id', 'tipologia', 'id_tipologia', 'SET NULL', 'CASCADE');
        
        $this->forge->createTable('turma', true, [
            'ENGINE'         => 'InnoDB',
            'DEFAULT CHARSET'=> 'utf8mb4',
            'COLLATE'        => 'utf8mb4_general_ci'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('turma', true);
    }
}
