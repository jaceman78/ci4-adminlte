<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRequesicaoKit extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'numero_aluno' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'turma' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
            ],
            'nif' => [
                'type' => 'VARCHAR',
                'constraint' => 9,
            ],
            'ase' => [
                'type' => 'ENUM',
                'constraint' => [
                    'Escalão A',
                    'Escalão B',
                    'Escalão C',
                    'Sem Escalão',
                ],
                'default' => 'Sem Escalão',
            ],
            'email_aluno' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'email_ee' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'estado' => [
                'type' => 'ENUM',
                'constraint' => ['atribuido','pendente','anulado','rejeitado'],
                'default' => 'pendente',
            ],
            'obs' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nif');
        $this->forge->createTable('requesicao_kit', true);
    }

    public function down()
    {
        $this->forge->dropTable('requesicao_kit', true);
    }
}
