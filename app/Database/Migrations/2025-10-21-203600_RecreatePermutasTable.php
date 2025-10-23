<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RecreatePermutasTable extends Migration
{
    public function up()
    {
        // Dropar a tabela antiga se existir
        $this->forge->dropTable('permutas', true);
        
        // Criar a nova tabela com a estrutura correta
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
                'comment'    => 'FK para horario_aulas'
            ],
            'data_aula_original' => [
                'type' => 'DATE',
                'null' => false,
                'comment' => 'Data da aula a permutar'
            ],
            'data_aula_permutada' => [
                'type' => 'DATE',
                'null' => false,
                'comment' => 'Data da reposição'
            ],
            'professor_autor_nif' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'comment'    => 'NIF do professor que pediu a permuta'
            ],
            'professor_substituto_nif' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'comment'    => 'NIF do professor que fará a substituição'
            ],
            'sala_permutada_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Sala para a reposição (null = mesma sala)'
            ],
            'grupo_permuta' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'ID do grupo se várias permutas juntas'
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
                'comment' => 'Observações do professor'
            ],
            'motivo_rejeicao' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Motivo da rejeição (se aplicável)'
            ],
            'aprovada_por_user_id' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'ID do user que aprovou/rejeitou'
            ],
            'data_aprovacao' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Data e hora da aprovação/rejeição'
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
        $this->forge->addKey('aula_original_id');
        $this->forge->addKey('professor_autor_nif');
        $this->forge->addKey('professor_substituto_nif');
        $this->forge->addKey('estado');
        $this->forge->addKey('grupo_permuta');

        $this->forge->createTable('permutas', true);

        // Adicionar foreign keys
        $this->db->query("
            ALTER TABLE permutas
            ADD CONSTRAINT permutas_aula_original_fk
            FOREIGN KEY (aula_original_id) REFERENCES horario_aulas(id_aula)
            ON DELETE CASCADE ON UPDATE CASCADE
        ");

        $this->db->query("
            ALTER TABLE permutas
            ADD CONSTRAINT permutas_professor_autor_fk
            FOREIGN KEY (professor_autor_nif) REFERENCES user(NIF)
            ON DELETE CASCADE ON UPDATE CASCADE
        ");

        $this->db->query("
            ALTER TABLE permutas
            ADD CONSTRAINT permutas_professor_substituto_fk
            FOREIGN KEY (professor_substituto_nif) REFERENCES user(NIF)
            ON DELETE CASCADE ON UPDATE CASCADE
        ");

        $this->db->query("
            ALTER TABLE permutas
            ADD CONSTRAINT permutas_aprovador_fk
            FOREIGN KEY (aprovada_por_user_id) REFERENCES user(id)
            ON DELETE SET NULL ON UPDATE CASCADE
        ");
    }

    public function down()
    {
        $this->forge->dropTable('permutas', true);
    }
}
