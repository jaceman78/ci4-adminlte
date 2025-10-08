<?php 

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class user extends Migration
{
    public function up()
    {
        /**
         * USERS
         */
    $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'oauth_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'NIF' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'profile_img' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => false,
                'default'    => 'default.png',
            ],
            'grupo_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'level' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                //'default' => 'CURRENT_TIMESTAMP',
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
               // 'default' => null,
                'on_update' => 'CURRENT_TIMESTAMP', // Atualiza automaticamente
            ],
        ]);

        $this->forge->addKey('id', true); // Chave primária
        $this->forge->createTable('user');
    }
    public function down()
    {
        $this->forge->dropTable('user');
    }
}