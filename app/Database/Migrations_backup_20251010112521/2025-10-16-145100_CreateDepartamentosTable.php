<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDepartamentosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'cod_departamento' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
            ],
            'nomedepartamento' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 1,
                'comment'    => '0=Inativo, 1=Ativo'
            ],
        ]);

        $this->forge->addKey('cod_departamento', true);
        $this->forge->createTable('departamentos', true, [
            'ENGINE'         => 'InnoDB',
            'DEFAULT CHARSET'=> 'utf8mb4',
            'COLLATE'        => 'utf8mb4_general_ci'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('departamentos', true);
    }
}
