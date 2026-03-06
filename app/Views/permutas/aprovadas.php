<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($page_title) ?></h1>
                <p class="text-muted"><?= esc($page_subtitle) ?></p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('/permutas') ?>">Horário & Permutas</a></li>
                    <li class="breadcrumb-item active">Permutas Aprovadas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <!-- Filtros -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title"><i class="bi bi-funnel"></i> Filtros</h3>
            </div>
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <label for="filtroDataInicio" class="form-label">Data Início:</label>
                        <input type="date" class="form-control" id="filtroDataInicio" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="filtroDataFim" class="form-label">Data Fim:</label>
                        <input type="date" class="form-control" id="filtroDataFim" value="<?= date('Y-m-d', strtotime('+1 month')) ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="filtroEscola" class="form-label">Filtrar por Escola:</label>
                        <select class="form-select" id="filtroEscola">
                            <option value="">Todas as Escolas</option>
                            <?php if (!empty($escolas)): ?>
                                <?php foreach ($escolas as $escola): ?>
                                    <option value="<?= esc($escola['id']) ?>"><?= esc($escola['nome_escola']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="filtroPavilhao" class="form-label">Filtrar por Pavilhão/Sala:</label>
                        <select class="form-select" id="filtroPavilhao">
                            <option value="">Todos os Pavilhões</option>
                            <option value="C0">C0xx</option>
                            <option value="C1">C1xx</option>
                            <option value="D0">D0xx</option>
                            <option value="D1">D1xx</option>
                            <option value="E0">E0xx</option>
                            <option value="E1">E1xx</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-secondary w-100" id="btnLimparFiltros">
                            <i class="bi bi-x-circle"></i> Limpar Filtros
                        </button>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger w-100" id="btnGerarPdf" title="Gerar PDF com os resultados filtrados">
                            <i class="bi bi-file-pdf"></i> PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de Permutas Aprovadas -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h3 class="card-title"><i class="bi bi-check2-square"></i> Permutas Aprovadas</h3>
            </div>
            <div class="card-body">
                <table id="tabelaPermutasAprovadas" class="table table-bordered table-striped table-hover nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Data Permuta</th>
                            <th>Professor Autor</th>
                            <th>Professor Substituto</th>
                            <th>Sala Permutada</th>
                            <th>Bloco Reposição</th>
                            <th>Turma</th>
                            <th>Escola</th>
                            <th>Data Aprovação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dados carregados via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    let table;

    // Função para carregar dados
    function carregarDados() {
        const dataInicio = $('#filtroDataInicio').val();
        const dataFim = $('#filtroDataFim').val();
        const escola = $('#filtroEscola').val();
        const pavilhao = $('#filtroPavilhao').val();

        if (table) {
            table.destroy();
        }

        table = $('#tabelaPermutasAprovadas').DataTable({
            ajax: {
                url: '<?= base_url('permutas/getPermutasAprovadas') ?>',
                data: {
                    data_inicio: dataInicio,
                    data_fim: dataFim,
                    escola: escola,
                    pavilhao: pavilhao
                },
                dataSrc: 'data'
            },
            columns: [
                {
                    data: 'data_aula_permutada',
                    render: function(data) {
                        if (!data) return '-';
                        const date = new Date(data);
                        return date.toLocaleDateString('pt-PT');
                    }
                },
                { data: 'professor_autor_nome' },
                { data: 'professor_substituto_nome' },
                {
                    data: null,
                    render: function(data) {
                        return data.sala_permutada_id ? 
                            `${data.codigo_sala || data.sala_permutada_id} ${data.sala_descricao ? '- ' + data.sala_descricao : ''}` : 
                            '-';
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        if (!data.hora_inicio || !data.hora_fim) return '-';
                        return `${data.hora_inicio.substring(0,5)} - ${data.hora_fim.substring(0,5)}` + 
                               (data.bloco_designacao ? ` (${data.bloco_designacao})` : '');
                    }
                },
                { data: 'codigo_turma' },
                { data: 'nome_escola' },
                {
                    data: 'data_aprovacao',
                    render: function(data) {
                        if (!data) return '-';
                        const date = new Date(data);
                        return date.toLocaleDateString('pt-PT');
                    }
                }
            ],
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
            },
            order: [[0, 'desc']],
            pageLength: 25
        });
    }

    // Carregar dados inicialmente
    carregarDados();

    // Aplicar filtros
    $('#filtroDataInicio, #filtroDataFim, #filtroEscola, #filtroPavilhao').on('change', function() {
        carregarDados();
    });

    // Limpar filtros
    $('#btnLimparFiltros').on('click', function() {
        $('#filtroDataInicio').val('<?= date('Y-m-d') ?>');
        $('#filtroDataFim').val('<?= date('Y-m-d', strtotime('+1 month')) ?>');
        $('#filtroEscola').val('');
        $('#filtroPavilhao').val('');
        carregarDados();
    });

    // Gerar PDF
    $('#btnGerarPdf').on('click', function() {
        const dataInicio = $('#filtroDataInicio').val();
        const dataFim = $('#filtroDataFim').val();
        const escola = $('#filtroEscola').val();
        const pavilhao = $('#filtroPavilhao').val();

        // Construir URL com parâmetros
        let url = '<?= base_url('permutas/gerarPdfAprovadas') ?>';
        const params = new URLSearchParams();
        
        if (dataInicio) params.append('data_inicio', dataInicio);
        if (dataFim) params.append('data_fim', dataFim);
        if (escola) params.append('escola', escola);
        if (pavilhao) params.append('pavilhao', pavilhao);

        if (params.toString()) {
            url += '?' + params.toString();
        }

        // Abrir PDF em nova janela
        window.open(url, '_blank');
    });
});
</script>
<?= $this->endSection() ?>
