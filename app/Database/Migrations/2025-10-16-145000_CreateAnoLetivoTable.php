<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnoLetivoTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_anoletivo' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
                'auto_increment' => true,
            ],
            'anoletivo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => true,
                'default'    => 0,
                'comment'    => '0=Inativo, 1=Ativo'
            ],
        ]);

        $this->forge->addKey('id_anoletivo', true);
        $this->forge->createTable('ano_letivo', true, [
            'ENGINE'         => 'InnoDB',
            'DEFAULT CHARSET'=> 'latin1',
            'COLLATE'        => 'latin1_swedish_ci'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('ano_letivo', true);
    }
}
