<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class Salas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'escola_id' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
            ],
            'codigo_sala' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'capacidade' => [
                'type'       => 'INT',
                'constraint' => 3,
                'null'       => true,
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
        $this->forge->addKey('id', true);
        $this->forge->createTable('salas');
    }

    public function down()
    {
        $this->forge->dropTable('salas');
    }
}