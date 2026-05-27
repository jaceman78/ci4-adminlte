<?= $this->extend('layout/master') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Modo de Manutenção</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Manutenção</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Status Atual -->
        <div class="row">
            <div class="col-md-12">
                <div class="card <?= $config['ativo'] ? 'card-danger' : 'card-success' ?>">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-<?= $config['ativo'] ? 'exclamation-triangle' : 'check-circle' ?>"></i>
                            Estado Atual
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4>
                                    <?php if ($config['ativo']): ?>
                                        <span class="badge bg-danger">Site em Manutenção</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Site Operacional</span>
                                    <?php endif; ?>
                                </h4>
                                
                                <?php if ($config['ativo']): ?>
                                    <p class="mt-3">
                                        <strong>Desde:</strong> 
                                        <?= $config['data_inicio'] ? date('d/m/Y H:i', strtotime($config['data_inicio'])) : 'N/A' ?>
                                    </p>
                                    <?php if ($config['data_fim_prevista']): ?>
                                        <p>
                                            <strong>Fim Previsto:</strong> 
                                            <?= date('d/m/Y H:i', strtotime($config['data_fim_prevista'])) ?>
                                        </p>
                                    <?php endif; ?>
                                    <p>
                                        <strong>Mensagem Atual:</strong><br>
                                        <?= esc($config['mensagem']) ?>
                                    </p>
                                <?php else: ?>
                                    <p class="mt-3">
                                        O site está operacional e acessível a todos os utilizadores.
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 text-end">
                                <?php if ($config['ativo']): ?>
                                    <button type="button" class="btn btn-success btn-lg" id="btnDesativar">
                                        <i class="bi bi-check-circle"></i> Desativar Manutenção
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-danger btn-lg" id="btnAtivar">
                                        <i class="bi bi-exclamation-triangle"></i> Ativar Manutenção
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuração -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-gear"></i>
                            Configuração
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="formManutencao">
                            <div class="mb-3">
                                <label for="mensagem" class="form-label">Mensagem de Manutenção</label>
                                <textarea class="form-control" id="mensagem" name="mensagem" rows="4"><?= esc($config['mensagem']) ?></textarea>
                                <small class="form-text text-muted">
                                    Esta mensagem será exibida aos utilizadores quando o site estiver em manutenção.
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="data_fim_prevista" class="form-label">Data e Hora Prevista para Fim (opcional)</label>
                                <input type="datetime-local" class="form-control" id="data_fim_prevista" name="data_fim_prevista" 
                                       value="<?= $config['data_fim_prevista'] ? date('Y-m-d\TH:i', strtotime($config['data_fim_prevista'])) : '' ?>">
                                <small class="form-text text-muted">
                                    Se definida, será exibida na página de manutenção.
                                </small>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar Configuração
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-info-circle"></i>
                            Informações Importantes
                        </h3>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Quando o modo de manutenção está ativado, todos os utilizadores (exceto nível 9) serão redirecionados para uma página de aviso.</li>
                            <li>Utilizadores com nível 9 podem sempre aceder ao site, mesmo em modo de manutenção.</li>
                            <li>O modo de manutenção é útil para realizar atualizações ou correções no sistema sem interrupções.</li>
                            <li>Certifique-se de desativar o modo após concluir a manutenção.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Ativar manutenção
    $('#btnAtivar').on('click', function() {
        Swal.fire({
            title: 'Ativar Modo de Manutenção?',
            text: 'Todos os utilizadores (exceto nível 9) não poderão aceder ao site.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, ativar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const mensagem = $('#mensagem').val();
                const dataFim = $('#data_fim_prevista').val();

                $.ajax({
                    url: '<?= base_url('manutencao/ativar') ?>',
                    type: 'POST',
                    data: {
                        mensagem: mensagem,
                        data_fim_prevista: dataFim
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Sucesso!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Erro!', 'Ocorreu um erro ao ativar o modo de manutenção.', 'error');
                    }
                });
            }
        });
    });

    // Desativar manutenção
    $('#btnDesativar').on('click', function() {
        Swal.fire({
            title: 'Desativar Modo de Manutenção?',
            text: 'O site voltará a ficar acessível a todos os utilizadores.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, desativar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('manutencao/desativar') ?>',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Sucesso!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Erro!', 'Ocorreu um erro ao desativar o modo de manutenção.', 'error');
                    }
                });
            }
        });
    });

    // Salvar configuração
    $('#formManutencao').on('submit', function(e) {
        e.preventDefault();

        const mensagem = $('#mensagem').val();
        const dataFim = $('#data_fim_prevista').val();

        $.ajax({
            url: '<?= base_url('manutencao/atualizar') ?>',
            type: 'POST',
            data: {
                mensagem: mensagem,
                data_fim_prevista: dataFim
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Sucesso!',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Erro!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Erro!', 'Ocorreu um erro ao guardar a configuração.', 'error');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
