<?= $this->extend('layout/master') ?>
<?= $this->section('title') ?>Estatísticas Kit Digital<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Estatísticas - Kit Digital</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('kit-digital-admin') ?>">Kit Digital</a></li>
                    <li class="breadcrumb-item active">Estatísticas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Por Ano de Escolaridade -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-graduation-cap"></i> Pedidos por Ano de Escolaridade</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartAno" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Por ASE (Escalões) -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-coins"></i> Pedidos por Escalão ASE</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartASE" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <!-- Por Estado -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-tasks"></i> Pedidos por Estado</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartEstado" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <!-- Por Turma -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users"></i> Pedidos por Turma</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartTurma" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Atualizado: 2025-12-04 - Todos os estados devem aparecer
// Dados do PHP
const dadosAno = <?= json_encode($porAno ?? []) ?>;
const dadosEstado = <?= json_encode($porEstado ?? []) ?>;
const dadosASE = <?= json_encode($porASE ?? []) ?>;
const dadosTurma = <?= json_encode($porTurma ?? []) ?>;

// Gráfico por Ano de Escolaridade
const ctxAno = document.getElementById('chartAno').getContext('2d');
const chartAno = new Chart(ctxAno, {
    type: 'bar',
    data: {
        labels: dadosAno.map(d => d.ano + 'º Ano'),
        datasets: [{
            label: 'Número de Pedidos',
            data: dadosAno.map(d => d.total),
            backgroundColor: [
                'rgba(54, 162, 235, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)',
                'rgba(255, 99, 132, 0.7)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(255, 99, 132, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            title: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Gráfico por ASE (Escalões)
const ctxASE = document.getElementById('chartASE').getContext('2d');

// Garantir ordem fixa: A, B, C, Sem Escalão
// Os dados vêm com labels traduzidos da BD
const ordemASE = ['Escalão A', 'Escalão B', 'Escalão C', 'Sem Escalão'];
const coresASE = {
    'Escalão A': 'rgba(220, 53, 69, 0.7)',   // Vermelho
    'Escalão B': 'rgba(255, 193, 7, 0.7)',   // Amarelo
    'Escalão C': 'rgba(40, 167, 69, 0.7)',   // Verde
    'Sem Escalão': 'rgba(108, 117, 125, 0.7)' // Cinza
};
const coresASEBorder = {
    'Escalão A': 'rgba(220, 53, 69, 1)',
    'Escalão B': 'rgba(255, 193, 7, 1)',
    'Escalão C': 'rgba(40, 167, 69, 1)',
    'Sem Escalão': 'rgba(108, 117, 125, 1)'
};

const labelsASE = [];
const dataASE = [];
const bgColorsASE = [];
const borderColorsASE = [];

ordemASE.forEach(ase => {
    const item = dadosASE.find(d => d.ase === ase);
    labelsASE.push(ase);
    const valor = item ? parseInt(item.total) : 0;
    dataASE.push(valor);
    bgColorsASE.push(coresASE[ase]);
    borderColorsASE.push(coresASEBorder[ase]);
});

const chartASE = new Chart(ctxASE, {
    type: 'bar',
    data: {
        labels: labelsASE,
        datasets: [{
            label: 'Número de Pedidos',
            data: dataASE,
            backgroundColor: bgColorsASE,
            borderColor: borderColorsASE,
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            title: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Gráfico por Estado
const ctxEstado = document.getElementById('chartEstado').getContext('2d');

// Garantir que todos os estados apareçam sempre
const todosEstados = ['pendente', 'por levantar', 'terminado', 'rejeitado', 'anulado'];
const labelsEstadosMap = {
    'pendente': 'Pendente',
    'por levantar': 'Por Levantar',
    'terminado': 'Terminado',
    'rejeitado': 'Rejeitado',
    'anulado': 'Anulado'
};
const coresEstadoMap = {
    'pendente': 'rgba(255, 193, 7, 0.7)',      // Amarelo
    'por levantar': 'rgba(23, 162, 184, 0.7)', // Azul claro
    'terminado': 'rgba(52, 58, 64, 0.7)',      // Preto
    'rejeitado': 'rgba(220, 53, 69, 0.7)',     // Vermelho
    'anulado': 'rgba(108, 117, 125, 0.7)'      // Cinza
};
const coresEstadoBorderMap = {
    'pendente': 'rgba(255, 193, 7, 1)',
    'por levantar': 'rgba(23, 162, 184, 1)',
    'terminado': 'rgba(52, 58, 64, 1)',
    'rejeitado': 'rgba(220, 53, 69, 1)',
    'anulado': 'rgba(108, 117, 125, 1)'
};

const labelsEstado = [];
const dataEstado = [];
const bgColorsEstado = [];
const borderColorsEstado = [];

todosEstados.forEach(estado => {
    const item = dadosEstado.find(d => d.estado === estado);
    labelsEstado.push(labelsEstadosMap[estado]);
    const valor = item ? parseInt(item.total) : 0;
    dataEstado.push(valor);
    bgColorsEstado.push(coresEstadoMap[estado]);
    borderColorsEstado.push(coresEstadoBorderMap[estado]);
});

console.log('Labels Estado:', labelsEstado);
console.log('Data Estado:', dataEstado);

const chartEstado = new Chart(ctxEstado, {
    type: 'bar',
    data: {
        labels: labelsEstado,
        datasets: [{
            label: 'Número de Pedidos',
            data: dataEstado,
            backgroundColor: bgColorsEstado,
            borderColor: borderColorsEstado,
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',  // Barras horizontais
        plugins: {
            legend: {
                display: false
            },
            title: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Gráfico por Turma
const ctxTurma = document.getElementById('chartTurma').getContext('2d');
const chartTurma = new Chart(ctxTurma, {
    type: 'bar',
    data: {
        labels: dadosTurma.map(d => d.turma),
        datasets: [{
            label: 'Número de Pedidos',
            data: dadosTurma.map(d => d.total),
            backgroundColor: 'rgba(75, 192, 192, 0.7)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            title: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
<?= $this->endSection() ?>
