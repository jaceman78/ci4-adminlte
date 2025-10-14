<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveSalaIdFromEquipamentos extends Migration
{
    public function up()
    {
        // Remover foreign key primeiro
        $this->forge->dropForeignKey('equipamentos', 'equipamentos_sala_id_foreign');
        
        // Remover coluna sala_id da tabela equipamentos
        // Agora usamos apenas a tabela equipamentos_sala para relacionamento
        $this->forge->dropColumn('equipamentos', 'sala_id');
    }

    public function down()
    {
        // Restaurar coluna sala_id se necessário fazer rollback
        $fields = [
            'sala_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id'
            ]
        ];
        
        $this->forge->addColumn('equipamentos', $fields);
        
        // Restaurar foreign key
        $this->forge->addForeignKey('sala_id', 'salas', 'id', 'SET NULL', 'CASCADE');
    }
}
