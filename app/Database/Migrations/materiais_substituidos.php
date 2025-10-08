<?php

namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class materiais_substituidos  extends Migration
{
    public function up()
    {
               /**
         * MATERIAIS SUBSTITUIDOS
         */
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'auto_increment' => true],
            'registo_id' => ['type' => 'INT'],
            'material_id'=> ['type' => 'INT'],
            'quantidade' => ['type' => 'INT', 'default' => 1],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('registo_id', 'registos_reparacao', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('material_id', 'materiais', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('materiais_substituidos');

    }
    public function down()
    {
        $this->forge->dropTable('materiais_substituidos');
    }
}