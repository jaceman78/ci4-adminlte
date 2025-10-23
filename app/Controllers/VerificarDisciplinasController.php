<?php

namespace App\Controllers;

class VerificarDisciplinasController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Ler o ficheiro CSV
        $filepath = ROOTPATH . 'Ficheiros_tipo_importacoes/horario_aulas_v2.csv';
        
        // Detectar encoding e converter para UTF-8
        $content = file_get_contents($filepath);
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            // Salvar conteúdo convertido temporariamente
            $tempFile = tempnam(sys_get_temp_dir(), 'disciplinas_');
            file_put_contents($tempFile, $content);
            $filepath = $tempFile;
        }
        
        $handle = fopen($filepath, 'r');
        
        if (!$handle) {
            return "Erro ao abrir ficheiro CSV";
        }
        
        $disciplinasCSV = [];
        $linha = 0;
        
        // Pular primeira linha (cabeçalho)
        fgets($handle);
        
        while (($line = fgets($handle)) !== false) {
            $linha++;
            $campos = explode(";", trim($line));
            
            if (count($campos) >= 2) {
                $disciplinaId = trim($campos[1]);
                if (!empty($disciplinaId) && !isset($disciplinasCSV[$disciplinaId])) {
                    $disciplinasCSV[$disciplinaId] = [];
                }
                if (!empty($disciplinaId)) {
                    $disciplinasCSV[$disciplinaId][] = $linha + 1; // +1 porque pulamos cabeçalho
                }
            }
        }
        fclose($handle);
        
        // Verificar quais existem na BD
        $output = "<h2>Verificação de Disciplinas no CSV vs Base de Dados</h2><hr>";
        $output .= "<p><strong>Total de disciplinas únicas no CSV:</strong> " . count($disciplinasCSV) . "</p>";
        
        $disciplinasExistem = $db->table('disciplina')->select('id_disciplina')->get()->getResultArray();
        $disciplinasNaBD = array_column($disciplinasExistem, 'id_disciplina');
        
        $output .= "<p><strong>Total de disciplinas na BD:</strong> " . count($disciplinasNaBD) . "</p><hr>";
        
        // Disciplinas que NÃO existem
        $output .= "<h3 style='color:red;'>Disciplinas do CSV que NÃO existem na BD:</h3>";
        $output .= "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
        $output .= "<tr><th>Disciplina ID</th><th>Primeiras 5 Linhas do CSV</th></tr>";
        
        $naoExistem = 0;
        foreach ($disciplinasCSV as $disciplinaId => $linhas) {
            if (!in_array($disciplinaId, $disciplinasNaBD)) {
                $naoExistem++;
                $primeirasLinhas = array_slice($linhas, 0, 5);
                $output .= "<tr>";
                $output .= "<td><strong>" . htmlspecialchars($disciplinaId) . "</strong></td>";
                $output .= "<td>" . implode(', ', $primeirasLinhas) . "</td>";
                $output .= "</tr>";
            }
        }
        $output .= "</table>";
        $output .= "<p><strong>Total:</strong> {$naoExistem} disciplinas não encontradas</p><hr>";
        
        // Disciplinas que EXISTEM
        $output .= "<h3 style='color:green;'>Disciplinas do CSV que existem na BD:</h3>";
        $output .= "<ul>";
        $existem = 0;
        foreach ($disciplinasCSV as $disciplinaId => $linhas) {
            if (in_array($disciplinaId, $disciplinasNaBD)) {
                $existem++;
                if ($existem <= 20) { // Mostrar apenas as primeiras 20
                    $output .= "<li>" . htmlspecialchars($disciplinaId) . " (usado em " . count($linhas) . " linhas)</li>";
                }
            }
        }
        if ($existem > 20) {
            $output .= "<li><em>... e mais " . ($existem - 20) . " disciplinas</em></li>";
        }
        $output .= "</ul>";
        $output .= "<p><strong>Total:</strong> {$existem} disciplinas encontradas</p>";
        
        return $output;
    }
}
