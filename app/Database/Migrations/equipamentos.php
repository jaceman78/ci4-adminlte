<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class Equipamentos extends Migration
{
    public function up()
    {
        $this->forge->addField([
        'id'             => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
        'sala_id'        => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'null' => true],
        'tipo_id'        => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'null' => false],
        'marca'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'modelo'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'numero_serie'   => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true, 'null' => true],
        'estado'         => ['type' => 'ENUM', 'constraint' => ['ativo', 'inativo', 'pendente'], 'default' => 'ativo', 'null' => false],
        'data_aquisicao' => ['type' => 'DATE', 'null' => true],
        'observacoes'    => ['type' => 'TEXT', 'null' => true],
        'created_at'     => ['type' => 'DATETIME', 'null' => true],
        'updated_at'     => ['type' => 'DATETIME', 'null' => true],

        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sala_id', 'salas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tipo_id', 'tipos_equipamento', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('equipamentos');
    }

    public function down()
    {
        $this->forge->dropTable('equipamentos');
    }
}