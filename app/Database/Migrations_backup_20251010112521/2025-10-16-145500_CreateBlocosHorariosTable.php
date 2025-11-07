<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBlocosHorariosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_bloco' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
                'auto_increment' => true,
            ],
            'hora_inicio' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'hora_fim' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'designacao' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'dia_semana' => [
                'type'       => 'ENUM',
                'constraint' => ['Segunda_Feira', 'Terca_Feira', 'Quarta_Feira', 'Quinta_Feira', 'Sexta_Feira', 'Sabado'],
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_bloco', true);
        
        $this->forge->createTable('blocos_horarios', true, [
            'ENGINE'         => 'InnoDB',
            'DEFAULT CHARSET'=> 'utf8mb4',
            'COLLATE'        => 'utf8mb4_general_ci'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('blocos_horarios', true);
    }
}
