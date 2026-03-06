<?php
// Test script para diagnosticar o problema com PDF de faltas
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste de Geração de PDF de Faltas</h1>";

// Verificar se Dompdf está instalado
echo "<h2>1. Verificando Dompdf</h2>";
try {
    require __DIR__ . '/vendor/autoload.php';
    $options = new \Dompdf\Options();
    echo "<p style='color:green'>✓ Dompdf está instalado</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Erro ao carregar Dompdf: " . $e->getMessage() . "</p>";
    exit;
}

// Simular dados para teste
echo "<h2>2. Simulando dados de teste</h2>";
$sessao = [
    'codigo_prova' => '639',
    'nome_prova' => 'Português',
    'tipo_prova' => 'Exame Nacional',
    'fase' => '1ª Fase',
    'data_exame' => '2026-06-16',
    'hora_exame' => '09:30:00',
    'duracao' => '120'
];

$faltas = [
    [
        'professor_nome' => 'João Silva',
        'funcao' => 'Vigilante',
        'codigo_sala' => 'A01',
        'presenca' => 'Falta'
    ],
    [
        'professor_nome' => 'Maria Santos',
        'funcao' => 'Suplente',
        'codigo_sala' => '',
        'presenca' => 'Falta Justificada'
    ]
];

$estatisticas = [
    'total' => 10,
    'presentes' => 7,
    'faltas' => 2,
    'faltas_justificadas' => 1,
    'pendentes' => 0
];

echo "<p style='color:green'>✓ Dados de teste criados</p>";

// Testar a view
echo "<h2>3. Testando a view</h2>";
try {
    // Definir função helper se não existir
    if (!function_exists('base_url')) {
        function base_url($uri = '') {
            return 'http://localhost:8080/' . ltrim($uri, '/');
        }
    }
    if (!function_exists('esc')) {
        function esc($data, $context = 'html') {
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
    }

    ob_start();
    include __DIR__ . '/app/Views/convocatorias/pdf_faltas.php';
    $html = ob_get_clean();
    
    echo "<p style='color:green'>✓ View carregada com sucesso (" . strlen($html) . " bytes)</p>";
    echo "<details><summary>Ver HTML gerado</summary><pre>" . htmlspecialchars(substr($html, 0, 1000)) . "...</pre></details>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Erro ao carregar view: " . $e->getMessage() . "</p>";
    exit;
}

// Testar geração do PDF
echo "<h2>4. Testando geração do PDF</h2>";
try {
    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('isRemoteEnabled', true);
    $options->set('chroot', __DIR__);

    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    echo "<p style='color:green'>✓ PDF gerado com sucesso</p>";
    echo "<p>Clique no botão abaixo para baixar o PDF de teste:</p>";
    
    // Salvar PDF temporário para teste
    $pdfOutput = $dompdf->output();
    $tempFile = sys_get_temp_dir() . '/test_faltas_' . time() . '.pdf';
    file_put_contents($tempFile, $pdfOutput);
    
    echo "<a href='data:application/pdf;base64," . base64_encode($pdfOutput) . "' download='teste_faltas.pdf' class='btn' style='display:inline-block;padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px'>Baixar PDF de Teste</a>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Erro ao gerar PDF: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr><p><strong>Diagnóstico completo.</strong></p>";
?>
