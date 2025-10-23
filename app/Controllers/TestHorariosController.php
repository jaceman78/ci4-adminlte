<?php

namespace App\Controllers;

class TestHorariosController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $output = "<h2>Verificação de Dados para Horários</h2><hr>";
        
        // Verificar índices da tabela horario_aulas
        $output .= "<h3>Índices da tabela horario_aulas</h3>";
        $indexes = $db->query("SHOW INDEXES FROM horario_aulas")->getResultArray();
        $output .= "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
        $output .= "<tr><th>Key Name</th><th>Column</th><th>Non Unique</th><th>Index Type</th></tr>";
        foreach ($indexes as $idx) {
            $nonUnique = $idx['Non_unique'] == 0 ? '<strong style="color:red;">UNIQUE</strong>' : 'Não único';
            $output .= "<tr>";
            $output .= "<td>{$idx['Key_name']}</td>";
            $output .= "<td>{$idx['Column_name']}</td>";
            $output .= "<td>{$nonUnique}</td>";
            $output .= "<td>{$idx['Index_type']}</td>";
            $output .= "</tr>";
        }
        $output .= "</table>";
        
        $output .= "<hr>";
        
        // 1. Professores com NIF
        $output .= "<h3>1. Professores (level=5) com NIF</h3>";
        $professores = $db->query("SELECT id, name, NIF, level FROM user WHERE level = 5 AND NIF IS NOT NULL AND NIF != '' LIMIT 10")->getResultArray();
        $output .= "<p><strong>Total:</strong> " . count($professores) . "</p>";
        if (count($professores) > 0) {
            $output .= "<ul>";
            foreach ($professores as $prof) {
                $output .= "<li>{$prof['name']} (NIF: {$prof['NIF']}, Level: {$prof['level']})</li>";
            }
            $output .= "</ul>";
        } else {
            $output .= "<p style='color:red;'>ATENÇÃO: Não existem professores com level=5 e NIF preenchido!</p>";
        }
        
        // 2. Turmas com código
        $output .= "<hr><h3>2. Turmas com código</h3>";
        $turmas = $db->query("SELECT id_turma, codigo, nome, ano FROM turma WHERE codigo IS NOT NULL AND codigo != '' LIMIT 10")->getResultArray();
        $output .= "<p><strong>Total:</strong> " . count($turmas) . "</p>";
        if (count($turmas) > 0) {
            $output .= "<ul>";
            foreach ($turmas as $turma) {
                $output .= "<li>{$turma['codigo']} - {$turma['nome']} ({$turma['ano']}º ano)</li>";
            }
            $output .= "</ul>";
        } else {
            $output .= "<p style='color:red;'>ATENÇÃO: Não existem turmas com código preenchido!</p>";
        }
        
        // 3. Disciplinas
        $output .= "<hr><h3>3. Disciplinas</h3>";
        $disciplinas = $db->query("SELECT id_disciplina, abreviatura, descritivo FROM disciplina LIMIT 10")->getResultArray();
        $output .= "<p><strong>Total:</strong> " . count($disciplinas) . "</p>";
        if (count($disciplinas) > 0) {
            $output .= "<ul>";
            foreach ($disciplinas as $disc) {
                $output .= "<li>{$disc['id_disciplina']} - {$disc['descritivo']} ({$disc['abreviatura']})</li>";
            }
            $output .= "</ul>";
        }
        
        // 4. Salas
        $output .= "<hr><h3>4. Salas</h3>";
        $salas = $db->query("SELECT id, codigo_sala, descricao FROM salas LIMIT 10")->getResultArray();
        $output .= "<p><strong>Total:</strong> " . count($salas) . "</p>";
        if (count($salas) > 0) {
            $output .= "<ul>";
            foreach ($salas as $sala) {
                $output .= "<li>{$sala['codigo_sala']}" . ($sala['descricao'] ? " - {$sala['descricao']}" : "") . "</li>";
            }
            $output .= "</ul>";
        }
        
        // 5. Estrutura da tabela horario_aulas
        $output .= "<hr><h3>5. Estrutura da tabela horario_aulas</h3>";
        $columns = $db->getFieldData('horario_aulas');
        $output .= "<ul>";
        foreach ($columns as $col) {
            $primaryKey = $col->primary_key ? " <strong>(PRIMARY KEY)</strong>" : "";
            $nullable = $col->nullable ? "NULL" : "NOT NULL";
            $output .= "<li><strong>{$col->name}</strong>: {$col->type} ({$nullable}){$primaryKey}</li>";
        }
        $output .= "</ul>";
        
        // 6. Registros existentes em horario_aulas
        $output .= "<hr><h3>6. Registros em horario_aulas</h3>";
        $horarios = $db->query("SELECT * FROM horario_aulas LIMIT 5")->getResultArray();
        $output .= "<p><strong>Total de registros:</strong> " . $db->query("SELECT COUNT(*) as total FROM horario_aulas")->getRow()->total . "</p>";
        if (count($horarios) > 0) {
            $output .= "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
            $output .= "<tr><th>ID</th><th>Turma</th><th>Disciplina</th><th>Professor (NIF)</th><th>Sala</th><th>Dia Semana</th><th>Hora Início</th><th>Hora Fim</th><th>Turno</th></tr>";
            foreach ($horarios as $h) {
                $output .= "<tr>";
                $output .= "<td>{$h['id_aula']}</td>";
                $output .= "<td>{$h['codigo_turma']}</td>";
                $output .= "<td>{$h['disciplina_id']}</td>";
                $output .= "<td>{$h['user_nif']}</td>";
                $output .= "<td>{$h['sala_id']}</td>";
                $output .= "<td>{$h['dia_semana']}</td>";
                $output .= "<td>{$h['hora_inicio']}</td>";
                $output .= "<td>{$h['hora_fim']}</td>";
                $output .= "<td>{$h['turno']}</td>";
                $output .= "</tr>";
            }
            $output .= "</table>";
        } else {
            $output .= "<p>Nenhum registro encontrado (tabela vazia).</p>";
        }
        
        // 7. Verificar FKs existentes
        $output .= "<hr><h3>7. Foreign Keys na tabela horario_aulas</h3>";
        $fks = $db->query("
            SELECT 
                CONSTRAINT_NAME, 
                COLUMN_NAME, 
                REFERENCED_TABLE_NAME, 
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'horario_aulas'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ")->getResultArray();
        
        if (count($fks) > 0) {
            $output .= "<ul>";
            foreach ($fks as $fk) {
                $output .= "<li><strong>{$fk['CONSTRAINT_NAME']}</strong>: ";
                $output .= "{$fk['COLUMN_NAME']} → {$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']}";
                $output .= "</li>";
            }
            $output .= "</ul>";
        } else {
            $output .= "<p>Nenhuma FK encontrada.</p>";
        }
        
        return $output;
    }
}
