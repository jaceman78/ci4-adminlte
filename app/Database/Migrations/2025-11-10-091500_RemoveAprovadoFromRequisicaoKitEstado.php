<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveAprovadoFromRequisicaoKitEstado extends Migration
{
    public function up()
    {
        // Migrar estados existentes 'aprovado' para 'por levantar'
        $this->db->query("UPDATE requisicao_kit SET estado = 'por levantar' WHERE estado = 'aprovado'");

        // Remover 'aprovado' do ENUM
        $this->db->query("ALTER TABLE requisicao_kit MODIFY estado ENUM('pendente','por levantar','rejeitado','anulado','terminado') NOT NULL DEFAULT 'pendente'");
    }

    public function down()
    {
        // Reintroduzir 'aprovado' no ENUM (não altera os dados atuais)
        $this->db->query("ALTER TABLE requisicao_kit MODIFY estado ENUM('pendente','aprovado','por levantar','rejeitado','anulado','terminado') NOT NULL DEFAULT 'pendente'");
    }
}
