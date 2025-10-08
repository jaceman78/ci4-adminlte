<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class registos_reparacao  extends Migration
{
    public function up()
    {
        /**
         * REGISTOS DE REPARAÇÃO
         */
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'auto_increment' => true],
            'ticket_id'      => ['type' => 'INT'],
            'user_id'     => ['type' => 'INT'],
            'descricao'      => ['type' => 'TEXT', 'null' => true],
            'tempo_gasto_min'=> ['type' => 'INT', 'null' => true],
            'criado_em'      => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('ticket_id', 'tickets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'user', 'id', 'CASCADE', 'CASCADE');    // Corrigido!
        $this->forge->createTable('registos_reparacao');
    }
    public function down()
    {
        $this->forge->dropTable('registos_reparacao');
    }
}