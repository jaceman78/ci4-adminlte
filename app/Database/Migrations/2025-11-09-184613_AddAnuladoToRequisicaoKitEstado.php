<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAnuladoToRequisicaoKitEstado extends Migration
{
    public function up()
    {
        // Adicionar 'anulado' ao enum do campo estado
        $this->db->query("ALTER TABLE requisicao_kit MODIFY estado ENUM('pendente', 'aprovado', 'rejeitado', 'por levantar', 'anulado') NOT NULL DEFAULT 'pendente'");
    }

    public function down()
    {
        // Remover 'anulado' do enum
        $this->db->query("ALTER TABLE requisicao_kit MODIFY estado ENUM('pendente', 'aprovado', 'rejeitado', 'por levantar') NOT NULL DEFAULT 'pendente'");
    }
}
