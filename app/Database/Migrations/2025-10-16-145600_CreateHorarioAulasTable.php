<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHorarioAulasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_aula' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
                'auto_increment' => true,
            ],
            'id_professor' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'FK: users.id (professor)'
            ],
            'id_disciplina' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'id_turma' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'id_sala' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => false,
            ],
            'dia_semana' => [
                'type'       => 'INT',
                'constraint' => 1,
                'null'       => false,
                'comment'    => '1=Segunda, 2=Terça, 3=Quarta, 4=Quinta, 5=Sexta, 6=Sábado'
            ],
            'id_bloco' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'frequencia' => [
                'type'       => 'ENUM',
                'constraint' => ['Semanal', 'Quinzenal_Par', 'Quinzenal_Impar'],
                'default'    => 'Semanal',
                'null'       => true,
                'comment'    => 'Frequência da aula'
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

        $this->forge->addKey('id_aula', true);
        
        // Índices para otimizar consultas
        $this->forge->addKey('id_professor');
        $this->forge->addKey('id_turma');
        $this->forge->addKey('id_disciplina');
        $this->forge->addKey('id_sala');
        $this->forge->addKey('dia_semana');
        $this->forge->addKey(['dia_semana', 'id_bloco']);
        
        $this->forge->createTable('horario_aulas', true, [
            'ENGINE'         => 'InnoDB',
            'DEFAULT CHARSET'=> 'latin1',
            'COLLATE'        => 'latin1_swedish_ci'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('horario_aulas', true);
    }
}
