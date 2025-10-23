<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermutasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_permuta' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
                'auto_increment' => true,
            ],
            'id_professor_original' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'FK: users.id (professor original)'
            ],
            'id_turma' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'id_disciplina' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'id_sala_original' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
            ],
            'data_original' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'id_bloco_original' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'motivo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'id_professor_substituto' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'FK: users.id (professor substituto)'
            ],
            'data_nova' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Data da aula compensatória'
            ],
            'id_bloco_novo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'Novo bloco horário'
            ],
            'id_sala_nova' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Nova sala'
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['Pendente', 'Aprovada', 'Rejeitada', 'Cancelada', 'Concluida'],
                'default'    => 'Pendente',
                'null'       => false,
            ],
            'data_criacao' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'data_aprovacao' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'observacoes_aprovador' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_permuta', true);
        
        // Índices para otimizar consultas
        $this->forge->addKey('id_professor_original');
        $this->forge->addKey('id_professor_substituto');
        $this->forge->addKey('id_turma');
        $this->forge->addKey('id_disciplina');
        $this->forge->addKey('data_original');
        $this->forge->addKey('data_nova');
        $this->forge->addKey('estado');
        $this->forge->addKey(['estado', 'data_original']);
        
        $this->forge->createTable('permutas', true, [
            'ENGINE'         => 'InnoDB',
            'DEFAULT CHARSET'=> 'latin1',
            'COLLATE'        => 'latin1_swedish_ci'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('permutas', true);
    }
}
