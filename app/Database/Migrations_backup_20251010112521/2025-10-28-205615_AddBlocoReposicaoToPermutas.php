<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBlocoReposicaoToPermutas extends Migration
{
    public function up()
    {
        // Adicionar coluna bloco_reposicao_id à tabela permutas
        $this->forge->addColumn('permutas', [
            'bloco_reposicao_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'sala_permutada_id',
                'comment'    => 'ID do bloco horário de reposição (quando aplicável)'
            ]
        ]);

        // Adicionar foreign key para blocos_horarios
        $this->forge->addForeignKey(
            'permutas',
            'bloco_reposicao_id',
            'blocos_horarios',
            'id_bloco',
            'SET NULL',
            'CASCADE',
            'fk_permutas_bloco_reposicao'
        );
    }

    public function down()
    {
        // Remover foreign key primeiro
        $this->forge->dropForeignKey('permutas', 'fk_permutas_bloco_reposicao');
        
        // Remover coluna
        $this->forge->dropColumn('permutas', 'bloco_reposicao_id');
    }
}
