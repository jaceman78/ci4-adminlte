<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTelefoneToUser extends Migration
{
    public function up()
    {
        // Adicionar campo telefone após o email na tabela user
        $fields = [
            'telefone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'email'
            ]
        ];
        
        $this->forge->addColumn('user', $fields);
    }

    public function down()
    {
        // Remover campo telefone caso seja necessário rollback
        $this->forge->dropColumn('user', 'telefone');
    }
}
