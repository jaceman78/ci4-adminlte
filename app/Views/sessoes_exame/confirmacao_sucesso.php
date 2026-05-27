<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .confirmation-card {
            max-width: 600px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .header-success i {
            font-size: 80px;
            margin-bottom: 20px;
        }
        .card-body {
            padding: 40px;
        }
        .info-item {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            width: 150px;
            color: #6c757d;
        }
        .info-value {
            flex: 1;
            color: #212529;
        }
        .alert-info-custom {
            background-color: #e7f3ff;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="confirmation-card">
        <div class="header-success">
            <i class="bi bi-check-circle-fill"></i>
            <h1 class="mb-0"><?= esc($title) ?></h1>
        </div>
        <div class="card-body">
            <div class="alert alert-success">
                <i class="bi bi-check-lg me-2"></i>
                <?= esc($message) ?>
            </div>

            <?php if (isset($convocatoria)): ?>
            <h5 class="mt-4 mb-3">Detalhes da Convocatória</h5>
            <div class="info-item">
                <span class="info-label">Professor:</span>
                <span class="info-value"><strong><?= esc($convocatoria['professor_nome']) ?></strong></span>
            </div>
            <div class="info-item">
                <span class="info-label">Prova:</span>
                <span class="info-value"><?= esc($convocatoria['codigo_prova']) ?> - <?= esc($convocatoria['nome_prova']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Fase:</span>
                <span class="info-value"><?= esc($convocatoria['fase']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Data:</span>
                <span class="info-value"><?= date('d/m/Y', strtotime($convocatoria['data_exame'])) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Hora:</span>
                <span class="info-value"><?= date('H:i', strtotime($convocatoria['hora_exame'])) ?>h</span>
            </div>
            <div class="info-item">
                <span class="info-label">Função:</span>
                <span class="info-value"><span class="badge bg-primary"><?= esc($convocatoria['funcao']) ?></span></span>
            </div>
            <?php if (!empty($convocatoria['codigo_sala'])): ?>
            <div class="info-item">
                <span class="info-label">Sala:</span>
                <span class="info-value"><?= esc($convocatoria['codigo_sala']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!isset($ja_confirmada) || !$ja_confirmada): ?>
            <div class="info-item">
                <span class="info-label">Confirmado em:</span>
                <span class="info-value"><?= date('d/m/Y H:i') ?></span>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <div class="alert-info-custom mt-4">
                <?php
                // Local de comparecimento:
                // - Coordenação da Escola: Para 6º ano (vigilantes, suplentes, equipas de apoio, verificação calculadoras)
                //   OU para equipas de apoio/suplentes em dias com provas do 6º ano
                // - Secretariado de Exames: Para outros anos
                
                // Verificar se é equipa de apoio ou suplentes
                $ehEquipaEspecial = isset($convocatoria['tipo_prova']) && in_array($convocatoria['tipo_prova'], ['Verificacao Calculadoras', 'Apoio TIC', 'Estrutura de Apoio', 'Suplentes', 'Verificação de Materiais']);
                
                if (isset($convocatoria['tipo_prova']) && ($convocatoria['tipo_prova'] === 'MODa' || $convocatoria['tipo_prova'] === 'ModA')) {
                    $localComparecimento = 'Escola Básica de Corroios, na Sala de Apoio ao Secretariado de Exames';
                } elseif ($convocatoria['ano_escolaridade'] == 4) {
                    $localComparecimento = 'Estrutura de Apoio';
                } elseif ($ehEquipaEspecial && isset($temProva4AnoNoDia) && $temProva4AnoNoDia) {
                    $localComparecimento = 'Estrutura de Apoio';
                } elseif ($convocatoria['ano_escolaridade'] == 6) {
                    // Prova é do 6º ano
                    $localComparecimento = 'Coordenação da Escola';
                } elseif ($ehEquipaEspecial && isset($temProva6AnoNoDia) && $temProva6AnoNoDia) {
                    // Equipa de apoio/suplentes em dia com prova do 6º ano
                    $localComparecimento = 'Coordenação da Escola';
                } else {
                    // Outros casos
                    $localComparecimento = 'Secretariado de Exames';
                }
                ?>
                <strong><i class="bi bi-info-circle me-2"></i>Lembrete Importante:</strong>
                <?php
                $ehProva = ($convocatoria['fase'] === 'Prova Ensaio')
                        || in_array($convocatoria['tipo_prova'] ?? '', ['MODa', 'ModA'])
                        || in_array($convocatoria['ano_escolaridade'] ?? 0, [4, 6]);
                $textoEventoLocal = $ehProva ? 'prova' : 'exame';
                ?>
                <p class="mb-0 mt-2">Deverá comparecer na <strong><?= $localComparecimento ?></strong> com <strong>45 minutos de antecedência</strong> ao horário de início da <?= $textoEventoLocal ?>.</p>
            </div>

            <div class="text-center mt-4">
                <a href="<?= base_url('dashboard') ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-house-door"></i> Ir para o Dashboard
                </a>
                <p class="text-muted mb-0 mt-3">Pode fechar esta janela ou aceder ao sistema.</p>
            </div>
        </div>
    </div>
</body>
</html>
