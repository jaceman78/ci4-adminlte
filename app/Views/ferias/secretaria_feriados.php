<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1><i class="bi bi-calendar-x"></i> Gestão de Feriados</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Feriados de <?= $ano ?></h3>
                    <div class="card-tools">
                        <button class="btn btn-success btn-sm" onclick="gerarFeriados()">
                            <i class="bi bi-star"></i> Gerar Automáticos
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="adicionarFeriado()">
                            <i class="bi bi-plus"></i> Adicionar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>Tipo</th>
                                <th width="80">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($feriados as $f): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($f['data'])) ?></td>
                                <td><?= esc($f['descricao']) ?></td>
                                <td>
                                    <?php if ($f['tipo'] == 'fixo'): ?>
                                        <span class="badge bg-primary">Fixo</span>
                                    <?php elseif ($f['tipo'] == 'movel'): ?>
                                        <span class="badge bg-warning">Móvel</span>
                                    <?php else: ?>
                                        <span class="badge bg-info">Municipal</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="remover(<?= $f['id'] ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
function gerarFeriados() {
    Swal.fire({
        title: 'Gerar Feriados Automáticos',
        input: 'number',
        inputLabel: 'Ano',
        inputValue: <?= $ano + 1 ?>,
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= base_url('ferias/gerar-feriados-ano') ?>', { ano: result.value }, function() {
                Swal.fire('Sucesso!', 'Feriados gerados', 'success').then(() => location.reload());
            });
        }
    });
}

function adicionarFeriado() {
    Swal.fire({
        title: 'Adicionar Feriado',
        html: `
            <input id="data" type="date" class="form-control mb-2" placeholder="Data">
            <input id="desc" type="text" class="form-control mb-2" placeholder="Descrição">
            <select id="tipo" class="form-control mb-2">
                <option value="fixo">Fixo</option>
                <option value="movel">Móvel</option>
                <option value="municipal">Municipal</option>
            </select>
        `,
        showCancelButton: true,
        preConfirm: () => {
            return {
                data: document.getElementById('data').value,
                descricao: document.getElementById('desc').value,
                tipo: document.getElementById('tipo').value
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const csrfToken = document.cookie.split('; ').find(r => r.startsWith('csrf_cookie_name='))?.split('=')[1] ?? '';
            $.ajax({
                url: '<?= base_url('ferias/adicionar-feriado') ?>',
                type: 'POST',
                data: Object.assign(result.value, { '<?= csrf_token() ?>': csrfToken }),
                success: function() {
                    Swal.fire('Sucesso!', 'Feriado adicionado', 'success').then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Erro', xhr.responseJSON?.messages?.error || 'Erro ao adicionar feriado', 'error');
                }
            });
        }
    });
}

function remover(id) {
    Swal.fire({
        title: 'Remover?',
        icon: 'warning',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('ferias/remover-feriado/') ?>' + id,
                type: 'DELETE',
                success: function() {
                    Swal.fire('Removido!', '', 'success').then(() => location.reload());
                }
            });
        }
    });
}
</script>

<?= $this->endSection() ?>
