<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeIdDisciplinaToVarchar extends Migration
{
    public function up()
    {
        // PASSO 1: Verificar e remover foreign keys existentes
        if ($this->db->DBDriver === 'MySQLi') {
            // Verificar se FK existe antes de remover
            $query = $this->db->query("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'horario_aulas' 
                AND COLUMN_NAME = 'id_disciplina'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            $result = $query->getResultArray();
            if (!empty($result)) {
                $fkName = $result[0]['CONSTRAINT_NAME'];
                $this->db->query("ALTER TABLE horario_aulas DROP FOREIGN KEY `{$fkName}`");
                echo "Removed FK: {$fkName}\n";
            }
        }
        
        // PASSO 2: Modificar id_disciplina na tabela disciplina PRIMEIRO (parent table)
        $this->forge->modifyColumn('disciplina', [
            'id_disciplina' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ]
        ]);
        
        // PASSO 3: Modificar id_disciplina em horario_aulas (child table)
        $this->forge->modifyColumn('horario_aulas', [
            'id_disciplina' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ]
        ]);
        
        // PASSO 4: Recriar foreign key
        $this->db->query("
            ALTER TABLE horario_aulas 
            ADD CONSTRAINT horario_aulas_id_disciplina_foreign 
            FOREIGN KEY (id_disciplina) 
            REFERENCES disciplina(id_disciplina) 
            ON DELETE CASCADE 
            ON UPDATE CASCADE
        ");
    }

    public function down()
    {
        // PASSO 1: Verificar e remover foreign key
        if ($this->db->DBDriver === 'MySQLi') {
            $query = $this->db->query("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'horario_aulas' 
                AND COLUMN_NAME = 'id_disciplina'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            $result = $query->getResultArray();
            if (!empty($result)) {
                $fkName = $result[0]['CONSTRAINT_NAME'];
                $this->db->query("ALTER TABLE horario_aulas DROP FOREIGN KEY `{$fkName}`");
            }
        }
        
        // PASSO 2: Reverter id_disciplina na tabela disciplina PRIMEIRO
        // Nota: Isto só funcionará se não houver IDs alfanuméricos nos dados
        $this->forge->modifyColumn('disciplina', [
            'id_disciplina' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
                'auto_increment' => true,
            ]
        ]);
        
        // PASSO 3: Reverter id_disciplina em horario_aulas
        $this->forge->modifyColumn('horario_aulas', [
            'id_disciplina' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ]
        ]);
        
        // PASSO 4: Recriar foreign key
        $this->db->query("
            ALTER TABLE horario_aulas 
            ADD CONSTRAINT horario_aulas_id_disciplina_foreign 
            FOREIGN KEY (id_disciplina) 
            REFERENCES disciplina(id_disciplina) 
            ON DELETE CASCADE 
            ON UPDATE CASCADE
        ");
    }
}
