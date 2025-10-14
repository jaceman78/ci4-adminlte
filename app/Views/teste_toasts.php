<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Toasts - Sistema de Gestão</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .test-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        .btn-test {
            margin: 5px;
            min-width: 200px;
        }
        .code-block {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center text-white mb-4">
                <h1><i class="fas fa-bell"></i> Sistema de Toasts</h1>
                <p class="lead">Teste todas as funcionalidades de notificações</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="test-card">
                    <h4><i class="fas fa-comment text-primary"></i> Notificações Básicas</h4>
                    <hr>
                    <button class="btn btn-success btn-test" onclick="toast.success('Operação realizada com sucesso!')">
                        <i class="fas fa-check"></i> Sucesso
                    </button>
                    <button class="btn btn-danger btn-test" onclick="toast.error('Erro ao processar a solicitação')">
                        <i class="fas fa-times"></i> Erro
                    </button>
                    <button class="btn btn-warning btn-test" onclick="toast.warning('Atenção! Verifique os dados')">
                        <i class="fas fa-exclamation-triangle"></i> Aviso
                    </button>
                    <button class="btn btn-info btn-test" onclick="toast.info('Sistema atualizado')">
                        <i class="fas fa-info-circle"></i> Informação
                    </button>
                    <div class="code-block mt-3">
                        toast.success('Mensagem');<br>
                        toast.error('Mensagem');<br>
                        toast.warning('Mensagem');<br>
                        toast.info('Mensagem');
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="test-card">
                    <h4><i class="fas fa-star text-warning"></i> Notificações Especiais</h4>
                    <hr>
                    <button class="btn btn-primary btn-test" onclick="showPersistentToast()">
                        <i class="fas fa-thumbtack"></i> Toast Persistente
                    </button>
                    <button class="btn btn-secondary btn-test" onclick="showLoadingToast()">
                        <i class="fas fa-spinner"></i> Toast Loading
                    </button>
                    <button class="btn btn-dark btn-test" onclick="showProgressToast()">
                        <i class="fas fa-tasks"></i> Toast Progresso
                    </button>
                    <button class="btn btn-outline-primary btn-test" onclick="showConfirmToast()">
                        <i class="fas fa-question-circle"></i> Toast Confirmação
                    </button>
                    <div class="code-block mt-3">
                        toast.persistent('info', 'Mensagem');<br>
                        const t = toast.loading('Carregando...');<br>
                        const p = toast.progress('Upload...', 50);
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="test-card">
                    <h4><i class="fas fa-code text-success"></i> Exemplos Práticos</h4>
                    <hr>
                    <button class="btn btn-success btn-test" onclick="simulateCreateEquipment()">
                        <i class="fas fa-plus"></i> Criar Equipamento
                    </button>
                    <button class="btn btn-primary btn-test" onclick="simulateUpdate()">
                        <i class="fas fa-edit"></i> Atualizar Dados
                    </button>
                    <button class="btn btn-danger btn-test" onclick="simulateDelete()">
                        <i class="fas fa-trash"></i> Eliminar Item
                    </button>
                    <button class="btn btn-warning btn-test" onclick="simulateValidationError()">
                        <i class="fas fa-exclamation"></i> Erro Validação
                    </button>
                </div>
            </div>

            <div class="col-md-6">
                <div class="test-card">
                    <h4><i class="fas fa-cog text-danger"></i> Controles</h4>
                    <hr>
                    <button class="btn btn-outline-danger btn-test" onclick="toast.clear()">
                        <i class="fas fa-broom"></i> Limpar Todos
                    </button>
                    <button class="btn btn-outline-secondary btn-test" onclick="showMultiple()">
                        <i class="fas fa-layer-group"></i> Múltiplos Toasts
                    </button>
                    <button class="btn btn-outline-info btn-test" onclick="showAllTypes()">
                        <i class="fas fa-palette"></i> Todos os Tipos
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="test-card">
                    <h4><i class="fas fa-book text-info"></i> Documentação Rápida</h4>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Funções Básicas</h6>
                            <div class="code-block">
                                toast.success(msg)<br>
                                toast.error(msg)<br>
                                toast.warning(msg)<br>
                                toast.info(msg)
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6>Funções Avançadas</h6>
                            <div class="code-block">
                                toast.persistent(type, msg)<br>
                                toast.loading(msg)<br>
                                toast.progress(msg, %)<br>
                                toast.confirm(msg, fn)
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6>Controles</h6>
                            <div class="code-block">
                                toast.clear()<br>
                                toast.hideLoading(t)<br>
                                toast.updateProgress(t, %)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 text-center text-white mt-4">
                <p><i class="fas fa-heart text-danger"></i> Sistema implementado por HardWork550</p>
                <a href="<?= base_url('equipamentos') ?>" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Voltar para Equipamentos
                </a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="<?= base_url('assets/js/toast-notifications.js') ?>"></script>

    <script>
        // Exemplos de funções
        function showPersistentToast() {
            toast.persistent('warning', 'Esta notificação permanece até clicar no X', 'Atenção Permanente');
        }

        function showLoadingToast() {
            const loading = toast.loading('Processando operação...');
            
            setTimeout(() => {
                toast.hideLoading(loading);
                toast.success('Operação concluída!');
            }, 3000);
        }

        function showProgressToast() {
            const progress = toast.progress('Upload em andamento...', 0);
            
            let percent = 0;
            const interval = setInterval(() => {
                percent += 10;
                toast.updateProgress(progress, percent);
                
                if (percent >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        toast.hideLoading(progress);
                        toast.success('Upload concluído!', '100%');
                    }, 500);
                }
            }, 300);
        }

        function showConfirmToast() {
            toast.confirm(
                'Tem certeza que deseja executar esta ação?',
                function() {
                    toast.success('Ação confirmada!');
                },
                function() {
                    toast.info('Ação cancelada');
                }
            );
        }

        function simulateCreateEquipment() {
            const loading = toast.loading('Criando equipamento...');
            
            setTimeout(() => {
                toast.hideLoading(loading);
                toast.success('Equipamento "Computador Dell" criado com sucesso!', 'Criado!');
            }, 1500);
        }

        function simulateUpdate() {
            const loading = toast.loading('Salvando alterações...');
            
            setTimeout(() => {
                toast.hideLoading(loading);
                toast.success('Dados atualizados com sucesso!', 'Atualizado!');
            }, 1500);
        }

        function simulateDelete() {
            toast.confirm(
                'Tem certeza que deseja eliminar este item?',
                function() {
                    const loading = toast.loading('Eliminando...');
                    setTimeout(() => {
                        toast.hideLoading(loading);
                        toast.success('Item eliminado com sucesso!', 'Eliminado!');
                    }, 1000);
                },
                function() {
                    toast.info('Eliminação cancelada');
                }
            );
        }

        function simulateValidationError() {
            toast.error('O campo "Marca" é obrigatório<br>O campo "Modelo" é obrigatório<br>Selecione um tipo de equipamento', 'Erro de Validação');
        }

        function showMultiple() {
            toast.info('Primeira notificação');
            setTimeout(() => toast.warning('Segunda notificação'), 500);
            setTimeout(() => toast.success('Terceira notificação'), 1000);
            setTimeout(() => toast.error('Quarta notificação'), 1500);
        }

        function showAllTypes() {
            toast.success('Tipo: Sucesso');
            setTimeout(() => toast.error('Tipo: Erro'), 200);
            setTimeout(() => toast.warning('Tipo: Aviso'), 400);
            setTimeout(() => toast.info('Tipo: Informação'), 600);
        }
    </script>
</body>
</html>
