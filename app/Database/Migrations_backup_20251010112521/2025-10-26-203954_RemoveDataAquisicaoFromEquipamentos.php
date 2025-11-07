<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveDataAquisicaoFromEquipamentos extends Migration
{
    public function up()
    {
        // Remover coluna data_aquisicao da tabela equipamentos
        $this->forge->dropColumn('equipamentos', 'data_aquisicao');
    }

    public function down()
    {
        // Adicionar a coluna de volta em caso de rollback
        $this->forge->addColumn('equipamentos', [
            'data_aquisicao' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'estado'
            ]
        ]);
    }
}
