<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveDiaSemanaMFromHorarioAulas extends Migration
{
    public function up()
    {
        // Remover o campo dia_semana da tabela horario_aulas
        // A informação do dia da semana já existe na tabela blocos_horarios
        $this->forge->dropColumn('horario_aulas', 'dia_semana');
    }

    public function down()
    {
        // Recriar o campo dia_semana caso seja necessário rollback
        $this->forge->addColumn('horario_aulas', [
            'dia_semana' => [
                'type'       => 'INT',
                'constraint' => 1,
                'null'       => false,
                'after'      => 'id_sala',
                'comment'    => '1=Segunda, 2=Terça, 3=Quarta, 4=Quinta, 5=Sexta, 6=Sábado'
            ]
        ]);
    }
}
