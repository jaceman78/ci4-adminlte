<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Pedido de Acumulação de Férias</title>
    <style>
        @page {
            margin: 15mm 18mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .header-logo-rp {
            width: 50%;
            vertical-align: middle;
            text-align: left;
        }
        .header-logo-escola {
            width: 50%;
            vertical-align: middle;
            text-align: right;
        }
        .header-logo-rp img {
            height: 34px;
            width: auto;
        }
        .header-logo-escola img {
            height: 40px;
            width: auto;
        }
        .header-sep {
            border-bottom: 1.5px solid #000;
            margin: 6px 0 10px 0;
        }
        .titulo {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 20px 0 25px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .destinatario {
            text-align: right;
            margin-right: 40px;
            margin-bottom: 25px;
            font-size: 9.5pt;
            font-weight: bold;
            line-height: 1.6;
        }
        .campo-linha {
            margin: 10px 0;
            font-size: 9.5pt;
        }
        .campo-linha strong {
            font-weight: bold;
        }
        .corpo-texto {
            margin: 25px 0 30px 0;
            font-size: 9.5pt;
            line-height: 1.7;
            text-align: justify;
        }
        .data-linha {
            text-align: center;
            margin: 30px 0 25px 0;
            font-size: 9.5pt;
        }
        .pede-deferimento {
            text-align: center;
            margin-bottom: 45px;
            font-size: 9.5pt;
        }
        .assinatura-linha {
            text-align: center;
            border-top: 1px solid #000;
            width: 300px;
            margin: 0 auto;
            padding-top: 4px;
            font-size: 8.5pt;
        }
        .tabela-inferior {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 8.5pt;
        }
        .tabela-inferior th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 6px 8px;
            font-weight: bold;
            text-align: center;
            font-size: 8.5pt;
        }
        .tabela-inferior td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }
        .tabela-inferior td.info-pessoal {
            width: 55%;
        }
        .tabela-inferior td.despacho {
            width: 45%;
        }
        .info-texto {
            font-size: 8.5pt;
            line-height: 1.5;
            color: #333;
            min-height: 80px;
        }
        .despacho-opcoes {
            margin-top: 10px;
        }
        .despacho-opcao {
            margin: 8px 0;
            font-size: 9pt;
        }
        .checkbox-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 5px;
            vertical-align: middle;
        }
        .checkbox-checked {
            background-color: #000;
        }
        .diretor-assinatura {
            margin-top: 20px;
            font-size: 9pt;
            font-weight: bold;
        }
        .rodape-sistema {
            text-align: center;
            font-size: 6.5pt;
            margin-top: 15px;
            padding-top: 5px;
            border-top: 1px solid #ccc;
            color: #666;
        }
    </style>
</head>
<body>

    <!-- CABEÇALHO -->
    <table class="header-table">
        <tr>
            <td class="header-logo-rp">
                <img src="<?= $logoRP ?>" alt="República Portuguesa / Ministério da Educação">
            </td>
            <td class="header-logo-escola">
                <img src="<?= $logoEscola ?>" alt="Agrupamento de Escolas João de Barros">
            </td>
        </tr>
    </table>
    <div class="header-sep"></div>

    <!-- TÍTULO -->
    <div class="titulo">Pedido de Acumulação de Férias</div>

    <!-- DESTINATÁRIO -->
    <div class="destinatario">
        Exmo. Senhor<br>
        Diretor
    </div>

    <!-- DADOS DO PROFESSOR -->
    <div class="campo-linha">
        <strong>Nome:</strong> <?= esc($pedido['nome_professor']) ?>
    </div>
    <div class="campo-linha">
        <strong>Categoria:</strong> <?= esc($pedido['categoria'] ?? '') ?>
    </div>

    <!-- CORPO DO PEDIDO -->
    <div class="corpo-texto">
        Solicito a V. Exa. a concessão de <strong><?= (int) $pedido['dias_sobrantes'] ?></strong> 
        <?= (int) $pedido['dias_sobrantes'] === 1 ? 'dia' : 'dias' ?> de férias 
        por acumulação de acordo com o art.º 89 do ECD<?php if (!empty($pedido['motivo_acumulacao'])): ?>, 
        pelo motivo de <?= esc($pedido['motivo_acumulacao']) ?><?php endif; ?>.
    </div>

    <!-- DATA E LOCAL -->
    <?php
    $mesNomesPt = ['','janeiro','fevereiro','março','abril','maio','junho',
                   'julho','agosto','setembro','outubro','novembro','dezembro'];
    ?>
    <div class="data-linha">
        Corroios, <?= date('d') ?> de <?= $mesNomesPt[(int)date('n')] ?> de <?= date('Y') ?>
    </div>

    <!-- PEDE DEFERIMENTO -->
    <div class="pede-deferimento">
        Pede deferimento,
    </div>

    <div class="assinatura-linha">
        &nbsp;
    </div>

    <!-- TABELA INFERIOR: INFORMAÇÃO DO SERVIÇO DE PESSOAL | DESPACHO -->
    <table class="tabela-inferior">
        <tr>
            <th>INFORMAÇÃO DO SERVIÇO DE PESSOAL</th>
            <th>DESPACHO</th>
        </tr>
        <tr>
            <td class="info-pessoal">
                <div class="info-texto">
                    De acordo com o art.º 89 do decreto lei n.º 41/2012<br>
                    de 21 de fevereiro…………………………………….<br>
                    ………………………………………………………………<br>
                    ………………………………………………………………<br>
                    ………………………………………………………………<br>
                    ………………………………………………………………<br>
                    <br>
                    Corroios, ____ / ____ / ______
                </div>
            </td>
            <td class="despacho">
                <div style="font-weight:bold; font-size:9pt;">LICENÇA:</div>
                <div class="despacho-opcoes">
                    <?php if (!empty($pedido['acumulacao_estado']) && $pedido['acumulacao_estado'] === 'autorizada'): ?>
                    <div class="despacho-opcao">
                        <span class="checkbox-box checkbox-checked"></span> Autorizada
                    </div>
                    <div class="despacho-opcao">
                        <span class="checkbox-box"></span> Não Autorizada
                    </div>
                    <?php elseif (!empty($pedido['acumulacao_estado']) && $pedido['acumulacao_estado'] === 'nao_autorizada'): ?>
                    <div class="despacho-opcao">
                        <span class="checkbox-box"></span> Autorizada
                    </div>
                    <div class="despacho-opcao">
                        <span class="checkbox-box checkbox-checked"></span> Não Autorizada
                    </div>
                    <?php else: ?>
                    <div class="despacho-opcao">
                        <span class="checkbox-box"></span> Autorizada
                    </div>
                    <div class="despacho-opcao">
                        <span class="checkbox-box"></span> Não Autorizada
                    </div>
                    <?php endif; ?>
                </div>
                <div class="diretor-assinatura">
                    O Diretor,
                    <br><br><br>
                    ____________________
                </div>
            </td>
        </tr>
    </table>

    <!-- RODAPÉ SISTEMA -->
    <div class="rodape-sistema">
        Processado por computador pelo software Sistema de Gestão Escolar • Copyright© - Agrupamento de Escolas João de Barros<br>
        <span style="font-size:6pt;">Documento Acumulação #<?= $pedido['id'] ?> | NIF <?= esc($pedido['user_nif']) ?> | Gerado em <?= date('d/m/Y H:i') ?></span>
    </div>

</body>
</html>
