<?php
/**
 * Script para otimizar imagens para uso no PDF
 * Redimensiona e comprime as imagens para evitar problemas de memória
 */

// Caminhos das imagens
$publicPath = __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;
$imagemEsjb = $publicPath . 'esjb_logo_com _nome.png';
$imagemRp = $publicPath . 'RP_Edu_2024.png';

// Caminhos das imagens otimizadas
$imagemEsjbOtimizada = $publicPath . 'esjb_logo_pdf.png';
$imagemRpOtimizada = $publicPath . 'RP_Edu_pdf.png';

function otimizarImagem($origem, $destino, $larguraMax = 200) {
    if (!file_exists($origem)) {
        echo "Erro: Arquivo não encontrado: $origem\n";
        return false;
    }

    // Obter informações da imagem
    $info = getimagesize($origem);
    if ($info === false) {
        echo "Erro: Não é uma imagem válida: $origem\n";
        return false;
    }

    $larguraOriginal = $info[0];
    $alturaOriginal = $info[1];
    $tipo = $info[2];

    // Calcular novas dimensões mantendo proporção
    $ratio = $larguraOriginal / $alturaOriginal;
    if ($larguraOriginal > $larguraMax) {
        $novaLargura = $larguraMax;
        $novaAltura = (int)($larguraMax / $ratio);
    } else {
        $novaLargura = $larguraOriginal;
        $novaAltura = $alturaOriginal;
    }

    // Criar imagem de acordo com o tipo
    switch ($tipo) {
        case IMAGETYPE_PNG:
            $imagemOriginal = imagecreatefrompng($origem);
            break;
        case IMAGETYPE_JPEG:
            $imagemOriginal = imagecreatefromjpeg($origem);
            break;
        case IMAGETYPE_GIF:
            $imagemOriginal = imagecreatefromgif($origem);
            break;
        default:
            echo "Erro: Tipo de imagem não suportado\n";
            return false;
    }

    if (!$imagemOriginal) {
        echo "Erro: Não foi possível criar imagem a partir de: $origem\n";
        return false;
    }

    // Criar nova imagem redimensionada
    $imagemNova = imagecreatetruecolor($novaLargura, $novaAltura);

    // Preservar transparência para PNG
    imagealphablending($imagemNova, false);
    imagesavealpha($imagemNova, true);
    $transparent = imagecolorallocatealpha($imagemNova, 255, 255, 255, 127);
    imagefilledrectangle($imagemNova, 0, 0, $novaLargura, $novaAltura, $transparent);

    // Redimensionar
    imagecopyresampled(
        $imagemNova, 
        $imagemOriginal, 
        0, 0, 0, 0, 
        $novaLargura, 
        $novaAltura, 
        $larguraOriginal, 
        $alturaOriginal
    );

    // Salvar como PNG com compressão
    $resultado = imagepng($imagemNova, $destino, 9);

    // Liberar memória
    imagedestroy($imagemOriginal);
    imagedestroy($imagemNova);

    if ($resultado) {
        $tamanhoOriginal = filesize($origem);
        $tamanhoNovo = filesize($destino);
        $percentual = round((1 - $tamanhoNovo / $tamanhoOriginal) * 100, 2);
        
        echo "✓ Otimizada: " . basename($origem) . "\n";
        echo "  Dimensões: {$larguraOriginal}x{$alturaOriginal} → {$novaLargura}x{$novaAltura}\n";
        echo "  Tamanho: " . round($tamanhoOriginal/1024, 2) . "KB → " . round($tamanhoNovo/1024, 2) . "KB (redução de {$percentual}%)\n\n";
        return true;
    }

    return false;
}

echo "=== Otimização de Imagens para PDF ===\n\n";

// Otimizar logo ESJB
if (file_exists($imagemEsjb)) {
    otimizarImagem($imagemEsjb, $imagemEsjbOtimizada, 200);
} else {
    echo "⚠ Imagem ESJB não encontrada: $imagemEsjb\n\n";
}

// Otimizar logo República Portuguesa
if (file_exists($imagemRp)) {
    otimizarImagem($imagemRp, $imagemRpOtimizada, 200);
} else {
    echo "⚠ Imagem RP não encontrada: $imagemRp\n\n";
}

echo "=== Otimização Concluída ===\n";
