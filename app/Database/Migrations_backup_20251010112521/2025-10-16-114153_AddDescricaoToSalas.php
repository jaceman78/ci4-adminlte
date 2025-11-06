<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDescricaoToSalas extends Migration
{
    public function up()
    {
        $fields = [
            'descricao' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'codigo_sala'
            ],
        ];
        
        $this->forge->addColumn('salas', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('salas', 'descricao');
    }
}
