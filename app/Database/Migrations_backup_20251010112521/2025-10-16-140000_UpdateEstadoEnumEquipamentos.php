<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateEstadoEnumEquipamentos extends Migration
{
    public function up()
    {
        // Alterar o ENUM do campo estado para os novos valores
        $sql = "ALTER TABLE equipamentos 
                MODIFY COLUMN estado ENUM('ativo', 'fora_servico', 'por_atribuir', 'abate') 
                NOT NULL DEFAULT 'ativo'";
        
        $this->db->query($sql);
    }

    public function down()
    {
        // Reverter para os valores originais
        $sql = "ALTER TABLE equipamentos 
                MODIFY COLUMN estado ENUM('ativo', 'inativo', 'pendente') 
                NOT NULL DEFAULT 'ativo'";
        
        $this->db->query($sql);
    }
}
