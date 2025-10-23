<?php

namespace App\Controllers;

class CriarDisciplinasFaltantesController extends BaseController
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
            $tempFile = tempnam(sys_get_temp_dir(), 'disciplinas_create_');
            file_put_contents($tempFile, $content);
            $filepath = $tempFile;
        }
        
        $handle = fopen($filepath, 'r');
        
        if (!$handle) {
            return "Erro ao abrir ficheiro CSV";
        }
        
        $disciplinasCSV = [];
        
        // Pular primeira linha (cabeçalho)
        fgets($handle);
        
        while (($line = fgets($handle)) !== false) {
            $campos = explode(";", trim($line));
            
            if (count($campos) >= 2) {
                $disciplinaId = trim($campos[1]);
                if (!empty($disciplinaId) && !isset($disciplinasCSV[$disciplinaId])) {
                    $disciplinasCSV[$disciplinaId] = true;
                }
            }
        }
        fclose($handle);
        
        // Verificar quais existem na BD
        $disciplinasExistem = $db->table('disciplina')->select('id_disciplina')->get()->getResultArray();
        $disciplinasNaBD = array_column($disciplinasExistem, 'id_disciplina');
        
        $output = "<h2>Criar Disciplinas em Falta</h2><hr>";
        
        // Encontrar disciplinas que não existem
        $disciplinasParaCriar = [];
        foreach (array_keys($disciplinasCSV) as $disciplinaId) {
            if (!in_array($disciplinaId, $disciplinasNaBD)) {
                $disciplinasParaCriar[] = $disciplinaId;
            }
        }
        
        if (empty($disciplinasParaCriar)) {
            $output .= "<p style='color:green;'><strong>✅ Todas as disciplinas do CSV já existem na BD!</strong></p>";
            return $output;
        }
        
        $output .= "<p><strong>Disciplinas a criar:</strong> " . count($disciplinasParaCriar) . "</p>";
        $output .= "<ul>";
        
        $criadas = 0;
        $erros = [];
        
        foreach ($disciplinasParaCriar as $disciplinaId) {
            try {
                // Criar abreviatura a partir do ID (máximo 50 caracteres)
                $abreviatura = substr($disciplinaId, 0, 50);
                
                // Inserir disciplina
                $data = [
                    'id_disciplina' => $disciplinaId,
                    'abreviatura' => $abreviatura,
                    'descritivo' => $disciplinaId, // Usar o mesmo valor
                    'tipologia_id' => 1 // Usar tipologia padrão (ajustar se necessário)
                ];
                
                $db->table('disciplina')->insert($data);
                $criadas++;
                $output .= "<li style='color:green;'>✅ <strong>{$disciplinaId}</strong> - criada com sucesso</li>";
                
            } catch (\Exception $e) {
                $erros[] = $disciplinaId;
                $output .= "<li style='color:red;'>❌ <strong>{$disciplinaId}</strong> - Erro: " . $e->getMessage() . "</li>";
            }
        }
        
        $output .= "</ul><hr>";
        $output .= "<p><strong>Resumo:</strong></p>";
        $output .= "<ul>";
        $output .= "<li>✅ Criadas com sucesso: {$criadas}</li>";
        $output .= "<li>❌ Erros: " . count($erros) . "</li>";
        $output .= "</ul>";
        
        if ($criadas > 0) {
            $output .= "<p style='color:green;'><strong>Agora pode tentar importar o CSV novamente!</strong></p>";
        }
        
        return $output;
    }
}
