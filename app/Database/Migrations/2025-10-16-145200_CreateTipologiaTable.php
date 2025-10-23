<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTipologiaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_tipologia' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
                'auto_increment' => true,
            ],
            'nome_tipologia' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => true,
                'default'    => 1,
                'comment'    => '0=Inativo, 1=Ativo'
            ],
        ]);

        $this->forge->addKey('id_tipologia', true);
        $this->forge->createTable('tipologia', true, [
            'ENGINE'         => 'InnoDB',
            'DEFAULT CHARSET'=> 'latin1',
            'COLLATE'        => 'latin1_swedish_ci'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tipologia', true);
    }
}
