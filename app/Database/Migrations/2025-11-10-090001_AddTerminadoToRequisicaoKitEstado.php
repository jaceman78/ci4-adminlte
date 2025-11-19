<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTerminadoToRequisicaoKitEstado extends Migration
{
    public function up()
    {
        // Adicionar 'terminado' ao enum do campo estado
        $this->db->query("ALTER TABLE requisicao_kit MODIFY estado ENUM('pendente', 'aprovado', 'rejeitado', 'por levantar', 'anulado', 'terminado') NOT NULL DEFAULT 'pendente'");
    }

    public function down()
    {
        // Remover 'terminado' do enum
        $this->db->query("ALTER TABLE requisicao_kit MODIFY estado ENUM('pendente', 'aprovado', 'rejeitado', 'por levantar', 'anulado') NOT NULL DEFAULT 'pendente'");
    }
}
