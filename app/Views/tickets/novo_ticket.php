<?= $this->extend('layout/master') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= $title ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?= $title ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Criar Novo Ticket de Avaria</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="novoTicketForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="equipamento_id">Equipamento</label>
                                    <select class="form-control" id="equipamento_id" name="equipamento_id" required>
                                        <option value="">Selecione um equipamento</option>
                                        <?php foreach ($equipamentos as $equipamento): ?>
                                            <option value="<?= $equipamento['id'] ?>"><?= esc($equipamento['marca'] . ' ' . $equipamento['modelo']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="sala_id">Sala</label>
                                    <select class="form-control" id="sala_id" name="sala_id" required>
                                        <option value="">Selecione uma sala</option>
                                        <?php foreach ($salas as $sala): ?>
                                            <option value="<?= $sala['id'] ?>"><?= esc($sala['codigo_sala']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="tipo_avaria_id">Tipo de Avaria</label>
                                    <select class="form-control" id="tipo_avaria_id" name="tipo_avaria_id" required>
                                        <option value="">Selecione um tipo de avaria</option>
                                        <?php foreach ($tiposAvaria as $tipoAvaria): ?>
                                            <option value="<?= $tipoAvaria['id'] ?>"><?= esc($tipoAvaria['descricao']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="descricao">Descrição da Avaria</label>
                                    <textarea class="form-control" id="descricao" name="descricao" rows="5" placeholder="Descreva detalhadamente a avaria..." required></textarea>
                                    <small class="form-text text-muted">Mínimo de 10 caracteres.</small>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Criar Ticket</button>
                                <a href="<?= site_url('/dashboard') ?>" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#novoTicketForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: '<?= site_url("tickets/create") ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('button[type="submit"]').prop('disabled', true).text('Criando...');
            },
            success: function(response) {
                if (response.status === 201) {
                    toastr.success(response.messages.success || 'Ticket criado com sucesso!');
                    $('#novoTicketForm')[0].reset();
                    setTimeout(function() {
                        window.location.href = '<?= site_url("tickets/meus") ?>';
                    }, 2000);
                } else {
                    toastr.error('Erro ao criar ticket.');
                }
            },
            error: function(xhr) {
                var response = JSON.parse(xhr.responseText);
                if (response.messages && response.messages.error) {
                    if (typeof response.messages.error === 'object') {
                        // Erros de validação
                        $.each(response.messages.error, function(field, message) {
                            toastr.error(message);
                        });
                    } else {
                        toastr.error(response.messages.error);
                    }
                } else {
                    toastr.error('Erro interno do servidor.');
                }
            },
            complete: function() {
                $('button[type="submit"]').prop('disabled', false).text('Criar Ticket');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
