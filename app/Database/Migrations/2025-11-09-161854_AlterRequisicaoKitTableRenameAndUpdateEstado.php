<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterRequisicaoKitTableRenameAndUpdateEstado extends Migration
{
    public function up()
    {
        // Rename table from requesicao_kit to requisicao_kit
        $this->forge->renameTable('requesicao_kit', 'requisicao_kit');
        
        // Modify estado column to add 'por levantar' enum
        $this->db->query("ALTER TABLE requisicao_kit MODIFY estado ENUM('pendente', 'aprovado', 'rejeitado', 'por levantar') NOT NULL DEFAULT 'pendente'");
    }

    public function down()
    {
        // Revert estado column to original enum values
        $this->db->query("ALTER TABLE requisicao_kit MODIFY estado ENUM('pendente', 'aprovado', 'rejeitado') NOT NULL DEFAULT 'pendente'");
        
        // Rename table back to requesicao_kit
        $this->forge->renameTable('requisicao_kit', 'requesicao_kit');
    }
}
