<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class logs_atividade extends Migration
{
    public function up()
    {
         $this->forge->addField([
            'id'              => ['type' => 'INT', 'auto_increment' => true],
            'user_id'         => ['type' => 'INT'],
            'modulo'          => ['type' => 'VARCHAR', 'constraint' => 50],
            'acao'            => ['type' => 'VARCHAR', 'constraint' => 100],
            'registro_id'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'descricao'       => ['type' => 'VARCHAR', 'constraint' => 500],
            'dados_anteriores'=> ['type' => 'TEXT', 'null' => true],
            'dados_novos'     => ['type' => 'TEXT', 'null' => true],
            'ip_address'      => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'detalhes'        => ['type' => 'TEXT', 'null' => true],
            'criado_em'       => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('logs_atividade');
    }
    public function down()
    {
        $this->forge->dropTable('logs_atividade');
    }
}