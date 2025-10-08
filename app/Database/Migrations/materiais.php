<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class materiais  extends Migration
{
    public function up()
    {
        /**
         * MATERIAIS
         */
        /**
         * MATERIAIS
         */
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'nome'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'referencia'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'stock_atual' => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('materiais');
    }   
    public function down()
    {
        $this->forge->dropTable('materiais');
    }
}