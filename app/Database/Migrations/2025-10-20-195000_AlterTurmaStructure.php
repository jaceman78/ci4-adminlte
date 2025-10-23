<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterTurmaStructure extends Migration
{
    public function up()
    {
        // Adicionar novos campos à tabela turma (tornar novos campos NULL para evitar falhas em tabelas já com dados)
        $fields = [
            'codigo' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'id_turma'
            ],
            'abreviatura' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'codigo'
            ],
            'descritivo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'abreviatura'
            ],
            'num_alunos' => [
                'type' => 'INT',
                'null' => true,
                'default' => 0,
                'after' => 'ano'
            ],
            'secretario_nif' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'num_alunos'
            ],
            'escola_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'secretario_nif'
            ],
            'dir_turma_nif' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'escola_id'
            ]
        ];

        // Adicionar colunas (ignorando erros se já existirem)
        foreach ($fields as $fieldName => $def) {
            $exists = false;
            foreach ($this->db->getFieldData('turma') as $col) {
                if (isset($col->name) && $col->name === $fieldName) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $this->forge->addColumn('turma', [$fieldName => $def]);
            }
        }

        // Remover campo dt_id antigo (se existir)
        $dtIdExists = false;
        foreach ($this->db->getFieldData('turma') as $col) {
            if (isset($col->name) && $col->name === 'dt_id') {
                $dtIdExists = true;
                break;
            }
        }
        if ($dtIdExists) {
            $this->forge->dropColumn('turma', 'dt_id');
        }

        // Adicionar foreign key nomeada para escola (se não existir)
        $fkExists = $this->db->query("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
            WHERE TABLE_NAME='turma' AND CONSTRAINT_TYPE='FOREIGN KEY' AND CONSTRAINT_NAME='fk_turma_escola' AND TABLE_SCHEMA=DATABASE()
        ")->getNumRows() > 0;
        if (!$fkExists) {
            // Garantir que a coluna existe, é UNSIGNED e NULLable (compatível com escolas.id)
            $this->db->query('ALTER TABLE `turma` MODIFY COLUMN `escola_id` INT UNSIGNED NULL');
            $this->db->query('ALTER TABLE `turma` ADD CONSTRAINT `fk_turma_escola` FOREIGN KEY (`escola_id`) REFERENCES `escolas`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        }

        // Adicionar índices para melhorar performance (se não existirem)
        $indexes = [
            'idx_secretario_nif' => 'secretario_nif',
            'idx_dir_turma_nif' => 'dir_turma_nif',
            'idx_codigo' => 'codigo',
        ];
        foreach ($indexes as $idxName => $colName) {
            $idxExists = $this->db->query("
                SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS 
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'turma' AND INDEX_NAME = ?
            ", [$idxName])->getNumRows() > 0;
            if (!$idxExists) {
                $this->db->query("CREATE INDEX `$idxName` ON `turma`(`$colName`)");
            }
        }
    }

    public function down()
    {
        // Remover índices se existirem
        $idxToDrop = ['idx_secretario_nif', 'idx_dir_turma_nif', 'idx_codigo'];
        foreach ($idxToDrop as $idxName) {
            $idxExists = $this->db->query("
                SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS 
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'turma' AND INDEX_NAME = ?
            ", [$idxName])->getNumRows() > 0;
            if ($idxExists) {
                $this->db->query("DROP INDEX `$idxName` ON `turma`");
            }
        }

        // Remover foreign key nomeada se existir
        $fkExists = $this->db->query("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
            WHERE TABLE_NAME='turma' AND CONSTRAINT_TYPE='FOREIGN KEY' AND CONSTRAINT_NAME='fk_turma_escola' AND TABLE_SCHEMA=DATABASE()
        ")->getNumRows() > 0;
        if ($fkExists) {
            $this->forge->dropForeignKey('turma', 'fk_turma_escola');
        }

        // Remover novos campos se existirem
        $colsToDrop = ['codigo','abreviatura','descritivo','num_alunos','secretario_nif','escola_id','dir_turma_nif'];
        $existing = array_map(function($c){ return $c->name; }, $this->db->getFieldData('turma'));
        foreach ($colsToDrop as $col) {
            if (in_array($col, $existing, true)) {
                $this->forge->dropColumn('turma', $col);
            }
        }

        // Recriar campo dt_id antigo se não existir
        $hasDtId = false;
        foreach ($this->db->getFieldData('turma') as $col) {
            if (isset($col->name) && $col->name === 'dt_id') {
                $hasDtId = true;
                break;
            }
        }
        if (!$hasDtId) {
            $fields = [
                'dt_id' => [
                    'type' => 'INT',
                    'null' => true,
                    'after' => 'nome'
                ]
            ];
            $this->forge->addColumn('turma', $fields);
        }
    }
}
