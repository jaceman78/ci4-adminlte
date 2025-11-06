<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAulasCreditoTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'professor_nif' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'codigo_turma' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'disciplina_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'turno' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'comment'    => 'T1, T2, etc. NULL se não tiver turnos',
            ],
            'data_visita' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'origem' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'comment'    => 'Descrição da visita de estudo',
            ],
            'ano_letivo_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['disponivel', 'usado', 'expirado', 'cancelado'],
                'default'    => 'disponivel',
                'null'       => false,
            ],
            'usado_em_permuta_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'data_uso' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'observacoes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'criado_por_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'cancelado_por_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'motivo_cancelamento' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        
        // Índices para performance
        $this->forge->addKey(['professor_nif', 'estado']);
        $this->forge->addKey(['codigo_turma', 'disciplina_id']);
        $this->forge->addKey('ano_letivo_id');
        $this->forge->addKey('usado_em_permuta_id');
        
        $this->forge->createTable('aulas_credito');

        // Foreign Keys - formato correto: (column, referenceTable, referenceColumn, onUpdate, onDelete)
        // Não vamos adicionar FK para anos_letivos por enquanto (pode não existir a tabela)
        // Não vamos adicionar FK para permutas (relação opcional)
        // Não vamos adicionar FK para user (NIF não é PK)
    }

    public function down()
    {
        $this->forge->dropTable('aulas_credito', true);
    }
}
