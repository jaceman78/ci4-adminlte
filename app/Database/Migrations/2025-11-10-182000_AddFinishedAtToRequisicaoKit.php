<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFinishedAtToRequisicaoKit extends Migration
{
    public function up()
    {
        $fields = [
            'finished_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at'
            ],
        ];
        $this->forge->addColumn('requisicao_kit', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('requisicao_kit', 'finished_at');
    }
}
