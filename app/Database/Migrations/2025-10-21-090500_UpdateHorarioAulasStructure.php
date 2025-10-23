<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateHorarioAulasStructure extends Migration
{
    public function up()
    {
        $fieldsToAdd = [
            'codigo_turma' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'id_aula'
            ],
            'disciplina_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'codigo_turma'
            ],
            'user_nif' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'disciplina_id'
            ],
            'sala_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'user_nif'
            ],
            'turno' => [
                'type'       => 'ENUM',
                'constraint' => ['T1', 'T2'],
                'null'       => true,
                'after'      => 'sala_id'
            ],
            'dia_semana' => [
                'type'       => 'INT',
                'constraint' => 1,
                'null'       => true,
                'after'      => 'turno'
            ],
            'tempo' => [
                'type'       => 'INT',
                'constraint' => 5,
                'null'       => true,
                'after'      => 'dia_semana'
            ],
            'intervalo' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'tempo'
            ],
            'hora_inicio' => [
                'type'  => 'TIME',
                'null'  => true,
                'after' => 'intervalo'
            ],
            'hora_fim' => [
                'type'  => 'TIME',
                'null'  => true,
                'after' => 'hora_inicio'
            ]
        ];

        $existingColumns = array_map(static function ($col) {
            return $col->name;
        }, $this->db->getFieldData('horario_aulas'));

        foreach ($fieldsToAdd as $column => $definition) {
            if (!in_array($column, $existingColumns, true)) {
                $this->forge->addColumn('horario_aulas', [$column => $definition]);
            }
        }

        $columnsAfterAdd = array_map(static function ($col) {
            return $col->name;
        }, $this->db->getFieldData('horario_aulas'));

        if (in_array('disciplina_id', $columnsAfterAdd, true) && in_array('id_disciplina', $columnsAfterAdd, true)) {
            $this->db->query('UPDATE horario_aulas SET disciplina_id = id_disciplina WHERE disciplina_id IS NULL');
        }

        if (in_array('codigo_turma', $columnsAfterAdd, true) && in_array('id_turma', $columnsAfterAdd, true)) {
            $this->db->query('
                UPDATE horario_aulas ha
                JOIN turma t ON t.id_turma = ha.id_turma
                SET ha.codigo_turma = t.codigo
                WHERE ha.codigo_turma IS NULL AND t.codigo IS NOT NULL
            ');
        }

        if (in_array('user_nif', $columnsAfterAdd, true) && in_array('id_professor', $columnsAfterAdd, true)) {
            $this->db->query('
                UPDATE horario_aulas ha
                JOIN user u ON u.id = ha.id_professor
                SET ha.user_nif = u.NIF
                WHERE ha.user_nif IS NULL AND u.NIF IS NOT NULL
            ');
        }

        if (in_array('sala_id', $columnsAfterAdd, true) && in_array('id_sala', $columnsAfterAdd, true)) {
            $this->db->query('
                UPDATE horario_aulas ha
                JOIN salas s ON s.id = ha.id_sala
                SET ha.sala_id = s.codigo_sala
                WHERE ha.sala_id IS NULL AND s.codigo_sala IS NOT NULL
            ');
        }

        if (in_array('dia_semana', $columnsAfterAdd, true) && in_array('id_bloco', $columnsAfterAdd, true)) {
            $this->db->query('
                UPDATE horario_aulas ha
                JOIN blocos_horarios bh ON bh.id_bloco = ha.id_bloco
                SET ha.dia_semana = CASE bh.dia_semana
                    WHEN "Segunda_Feira" THEN 2
                    WHEN "Terca_Feira" THEN 3
                    WHEN "Quarta_Feira" THEN 4
                    WHEN "Quinta_Feira" THEN 5
                    WHEN "Sexta_Feira" THEN 6
                    WHEN "Sabado" THEN 7
                    ELSE NULL
                END,
                ha.hora_inicio = bh.hora_inicio,
                ha.hora_fim = bh.hora_fim,
                ha.intervalo = CONCAT(DATE_FORMAT(bh.hora_inicio, "%H:%i"), " - ", DATE_FORMAT(bh.hora_fim, "%H:%i"))
                WHERE ha.dia_semana IS NULL
            ');
        }

        // Remover FKs antigas antes de dropar as colunas
        $oldForeignKeys = ['horario_aulas_id_disciplina_foreign'];
        foreach ($oldForeignKeys as $fk) {
            $this->dropForeignKeyIfExists('horario_aulas', $fk);
        }

        $columnsToDrop = ['id_professor', 'id_disciplina', 'id_turma', 'id_sala', 'id_bloco', 'frequencia'];
        foreach ($columnsToDrop as $column) {
            if (in_array($column, $columnsAfterAdd, true)) {
                $this->forge->dropColumn('horario_aulas', $column);
            }
        }

        $indexes = [
            'idx_horario_codigo_turma' => 'codigo_turma',
            'idx_horario_disciplina_id' => 'disciplina_id',
            'idx_horario_user_nif' => 'user_nif',
            'idx_horario_sala_id' => 'sala_id',
            'idx_horario_dia_semana' => 'dia_semana'
        ];

        foreach ($indexes as $indexName => $column) {
            $exists = $this->db->query('
                SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "horario_aulas" AND INDEX_NAME = ?
            ', [$indexName])->getNumRows() > 0;

            if (!$exists && in_array($column, $columnsAfterAdd, true)) {
                $this->db->query("CREATE INDEX `{$indexName}` ON `horario_aulas`(`{$column}`)");
            }
        }

        $this->addForeignKeyIfPossible(
            'horario_aulas',
            'codigo_turma',
            'turma',
            'codigo',
            'fk_horario_codigo_turma'
        );

        $this->addForeignKeyIfPossible(
            'horario_aulas',
            'disciplina_id',
            'disciplina',
            'id_disciplina',
            'fk_horario_disciplina_id'
        );

        $this->addForeignKeyIfPossible(
            'horario_aulas',
            'user_nif',
            'user',
            'NIF',
            'fk_horario_user_nif'
        );

        // Não criamos FK para sala_id porque codigo_sala não é único globalmente
        // (pode ter o mesmo código em escolas diferentes)
        // $this->addForeignKeyIfPossible(
        //     'horario_aulas',
        //     'sala_id',
        //     'salas',
        //     'codigo_sala',
        //     'fk_horario_sala_id',
        //     'SET NULL'
        // );
    }

    public function down()
    {
        $foreignKeys = [
            'fk_horario_codigo_turma',
            'fk_horario_disciplina_id',
            'fk_horario_user_nif'
            // 'fk_horario_sala_id' não foi criada
        ];

        foreach ($foreignKeys as $fk) {
            $this->dropForeignKeyIfExists('horario_aulas', $fk);
        }

        $columnsToAdd = [
            'id_professor' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'id_aula'
            ],
            'id_disciplina' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'id_professor'
            ],
            'id_turma' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'id_disciplina'
            ],
            'id_sala' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'id_turma'
            ],
            'id_bloco' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'id_sala'
            ],
            'frequencia' => [
                'type'       => 'ENUM',
                'constraint' => ['Semanal', 'Quinzenal_Par', 'Quinzenal_Impar'],
                'null'       => true,
                'after'      => 'id_bloco'
            ]
        ];

        $existingColumns = array_map(static function ($col) {
            return $col->name;
        }, $this->db->getFieldData('horario_aulas'));

        foreach ($columnsToAdd as $column => $definition) {
            if (!in_array($column, $existingColumns, true)) {
                $this->forge->addColumn('horario_aulas', [$column => $definition]);
            }
        }

        $colsToDrop = ['codigo_turma', 'disciplina_id', 'user_nif', 'sala_id', 'turno', 'dia_semana', 'tempo', 'intervalo', 'hora_inicio', 'hora_fim'];
        foreach ($colsToDrop as $column) {
            if (in_array($column, $existingColumns, true)) {
                $this->forge->dropColumn('horario_aulas', $column);
            }
        }
    }

    private function addForeignKeyIfPossible(string $table, string $column, string $referencedTable, string $referencedColumn, string $fkName, string $onDelete = 'RESTRICT'): void
    {
        $columnExists = $this->db->query('
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
        ', [$table, $column])->getNumRows() > 0;

        $refColumnExists = $this->db->query('
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
        ', [$referencedTable, $referencedColumn])->getNumRows() > 0;

        if (!$columnExists || !$refColumnExists) {
            return;
        }

        $fkExists = $this->db->query('
            SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?
        ', [$table, $fkName])->getNumRows() > 0;

        if ($fkExists) {
            return;
        }

        $isUnique = $this->db->query('
            SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND NON_UNIQUE = 0
        ', [$referencedTable, $referencedColumn])->getNumRows() > 0;

        if (!$isUnique) {
            $duplicates = $this->db->query("SELECT `{$referencedColumn}`, COUNT(*) total FROM `{$referencedTable}` GROUP BY `{$referencedColumn}` HAVING total > 1")->getResultArray();
            if (!empty($duplicates)) {
                return;
            }
            $this->db->query("CREATE UNIQUE INDEX `idx_{$referencedTable}_{$referencedColumn}_uniq` ON `{$referencedTable}`(`{$referencedColumn}`)");
        }

    $this->db->query("ALTER TABLE `{$table}` ADD CONSTRAINT `{$fkName}` FOREIGN KEY (`{$column}`) REFERENCES `{$referencedTable}`(`{$referencedColumn}`) ON UPDATE CASCADE ON DELETE {$onDelete}");
    }

    private function dropForeignKeyIfExists(string $table, string $constraint): void
    {
        $fkExists = $this->db->query('
            SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?
        ', [$table, $constraint])->getNumRows() > 0;

        if ($fkExists) {
            $this->db->query("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
        }
    }
}
