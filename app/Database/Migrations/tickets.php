<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class tickets  extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'auto_increment' => true],
            'equipamento_id'    => ['type' => 'INT', 'null' => false],
            'sala_id'           => ['type' => 'INT', 'null' => false],
            'tipo_avaria_id'    => ['type' => 'INT', 'null' => false],
            'user_id'           => ['type' => 'INT', 'null' => false],
            'atribuido_user_id' => ['type' => 'INT', 'null' => true],
            'ticket_aceite'     => ['type' => 'BOOLEAN', 'default' => false],
            'descricao'         => ['type' => 'TEXT', 'null' => false],
            'estado'            => [
                'type'       => 'ENUM',
                'constraint' => ['novo','em_resolucao','aguarda_peca','reparado','anulado'],
                'default'    => 'novo',
                'null'       => false
            ],
            'prioridade'        => [
                'type'       => 'ENUM',
                'constraint' => ['baixa','media','alta','critica'],
                'default'    => 'media',
                'null'       => false
            ],
            'created_at'        => ['type' => 'DATETIME', 'null' => false, 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at'        => ['type' => 'DATETIME', 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'on_update' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('equipamento_id', 'equipamentos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('sala_id', 'salas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tipo_avaria_id', 'tipos_avaria', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('atribuido_user_id', 'user', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('tickets');
    }
    public function down()
    {
        $this->forge->dropTable('tickets');
    }
}