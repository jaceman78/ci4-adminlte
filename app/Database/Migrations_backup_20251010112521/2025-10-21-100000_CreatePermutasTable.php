<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermutasTable extends Migration
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
            'aula_original_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'ID da aula que está a ser permutada'
            ],
            'data_aula_original' => [
                'type'    => 'DATE',
                'null'    => false,
                'comment' => 'Data da aula original a ser permutada'
            ],
            'data_aula_permutada' => [
                'type'    => 'DATE',
                'null'    => false,
                'comment' => 'Data em que a aula será reposta'
            ],
            'professor_autor_nif' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'comment'    => 'NIF do professor que pede a permuta'
            ],
            'professor_substituto_nif' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'comment'    => 'NIF do professor que vai substituir (pode ser o mesmo)'
            ],
            'sala_permutada_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Código da sala onde será dada a aula permutada'
            ],
            'grupo_permuta' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Identificador do grupo de permutas (para várias aulas no mesmo dia)'
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['pendente', 'aprovada', 'rejeitada', 'cancelada'],
                'default'    => 'pendente',
                'null'       => false,
            ],
            'observacoes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Justificação ou observações sobre a permuta'
            ],
            'motivo_rejeicao' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Motivo da rejeição, se aplicável'
            ],
            'aprovada_por_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'ID do user que aprovou/rejeitou'
            ],
            'data_aprovacao' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Data da aprovação/rejeição'
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
        
        // Índices para melhorar performance de queries
        $this->forge->addKey('aula_original_id');
        $this->forge->addKey('professor_autor_nif');
        $this->forge->addKey('professor_substituto_nif');
        $this->forge->addKey('estado');
        $this->forge->addKey('grupo_permuta');
        
        // Foreign Keys
        $this->forge->addForeignKey('aula_original_id', 'horario_aulas', 'id_aula', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('professor_autor_nif', 'user', 'NIF', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('professor_substituto_nif', 'user', 'NIF', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('aprovada_por_user_id', 'user', 'id', 'SET NULL', 'CASCADE');
        
        $this->forge->createTable('permutas', true, ['ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4']);
    }

    public function down()
    {
        $this->forge->dropTable('permutas', true);
    }
}
