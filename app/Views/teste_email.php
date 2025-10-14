<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Email</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">🧪 Teste de Configuração de Email</h3>
                    </div>
                    <div class="card-body">
                        <form id="emailTestForm">
                            <div class="mb-3">
                                <label for="to_email" class="form-label">Email de Destino:</label>
                                <input type="email" class="form-control" id="to_email" name="to_email" 
                                       value="antonioneto@aejoaodebarros.pt" required>
                                <small class="form-text text-muted">Email para onde será enviado o teste</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Assunto:</label>
                                <input type="text" class="form-control" id="subject" name="subject" 
                                       value="Teste de Email - Sistema de Gestão" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">Mensagem:</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required>Este é um email de teste do Sistema de Gestão Escolar.

Se recebeu este email, significa que a configuração SMTP está funcionando corretamente!

Data/Hora: <?= date('d/m/Y H:i:s') ?>
</textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" id="sendBtn">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Email de Teste
                            </button>
                        </form>
                        
                        <div id="result" class="mt-4"></div>
                        
                        <hr class="my-4">
                        
                        <div class="alert alert-info">
                            <h5>📋 Configurações Atuais (.env):</h5>
                            <ul class="mb-0">
                                <li><strong>Protocolo:</strong> <?= getenv('email.protocol') ?></li>
                                <li><strong>SMTP Host:</strong> <?= getenv('email.SMTPHost') ?></li>
                                <li><strong>SMTP Port:</strong> <?= getenv('email.SMTPPort') ?></li>
                                <li><strong>SMTP User:</strong> <?= getenv('email.SMTPUser') ?></li>
                                <li><strong>SMTP Crypto:</strong> <?= getenv('email.SMTPCrypto') ?></li>
                                <li><strong>From Email:</strong> <?= getenv('email.fromEmail') ?></li>
                                <li><strong>From Name:</strong> <?= getenv('email.fromName') ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('#emailTestForm').on('submit', function(e) {
                e.preventDefault();
                
                const $btn = $('#sendBtn');
                const $result = $('#result');
                
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Enviando...');
                $result.html('');
                
                $.ajax({
                    url: '<?= base_url('debug/test-email') ?>',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $result.html(`
                                <div class="alert alert-success">
                                    <h5>✅ Email enviado com sucesso!</h5>
                                    <p class="mb-0">${response.message}</p>
                                </div>
                            `);
                        } else {
                            $result.html(`
                                <div class="alert alert-danger">
                                    <h5>❌ Erro ao enviar email</h5>
                                    <p class="mb-0">${response.message}</p>
                                    ${response.debug ? '<pre class="mt-2">' + response.debug + '</pre>' : ''}
                                </div>
                            `);
                        }
                    },
                    error: function(xhr) {
                        $result.html(`
                            <div class="alert alert-danger">
                                <h5>❌ Erro na requisição</h5>
                                <p class="mb-0">${xhr.responseJSON?.message || 'Erro desconhecido'}</p>
                            </div>
                        `);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Enviar Email de Teste');
                    }
                });
            });
        });
    </script>
</body>
</html>
