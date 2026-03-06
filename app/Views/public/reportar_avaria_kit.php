<?= $this->extend('layout/public') ?>
<?= $this->section('title') ?>Reportar Avaria - Kit Digital<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.required::after { content:' *'; color:#dc3545; }
.captcha-box { background:#f8f9fa; border:1px dashed #ced4da; border-radius:8px; padding:12px 16px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="py-5">
  <div class="container">
    <div class="row mb-4">
      <div class="col-lg-10 mx-auto text-center">
        <h1 class="fw-bold mb-2">Reportar Avaria do Kit Digital</h1>
        <p class="text-muted">Preencha o formulário abaixo para reportar uma avaria no seu equipamento Kit Digital.</p>
      </div>
    </div>

    <?php if(!empty($success)): ?>
      <div class="alert alert-success alert-dismissible fade show">
        <?= esc($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    
    <?php if(!empty($errors)): ?>
      <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">
        <?php foreach((array)$errors as $e): ?>
          <li><?= esc($e) ?></li>
        <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Formulário de Reporte</h5>
            
            <form method="post" action="/reportar-avaria-kit/enviar" id="avariaForm">
              <?= csrf_field() ?>
              <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off">

              <noscript>
                <div class="alert alert-warning small">É necessário JavaScript para submeter o formulário.</div>
              </noscript>

              <div class="mb-3">
                <label class="form-label required">Número de Aluno</label>
                <input type="text" maxlength="5" pattern="[0-9]{5}" inputmode="numeric" class="form-control" name="numero_aluno" value="<?= old('numero_aluno') ?>" required>
                <div class="form-text">5 dígitos (conforme cartão de estudante)</div>
              </div>

              <div class="mb-3">
                <label class="form-label required">Nome</label>
                <input type="text" class="form-control" name="nome" value="<?= old('nome') ?>" required>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label required">Turma</label>
                  <select class="form-select" name="turma" required>
                    <option value="">Selecione...</option>
                    <?php if (!empty($turmas)): ?>
                      <?php foreach ($turmas as $t): ?>
                        <option value="<?= esc($t['codigo']) ?>" <?= old('turma')===$t['codigo'] ? 'selected' : '' ?>>
                          <?= esc($t['codigo']) ?>
                        </option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label required">NIF do Aluno</label>
                  <input type="text" maxlength="9" pattern="[0-9]{9}" inputmode="numeric" class="form-control" name="nif" value="<?= old('nif') ?>" required>
                </div>
              </div>

              <div class="row g-3 mt-1">
                <div class="col-md-6">
                  <label class="form-label required">Email do Aluno</label>
                  <input type="email" class="form-control" name="email_aluno" value="<?= old('email_aluno') ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label required">Email do Encarregado de Educação</label>
                  <input type="email" class="form-control" name="email_ee" value="<?= old('email_ee') ?>" required>
                </div>
              </div>

              <div class="mb-3 mt-3">
                <label class="form-label required">Descrição da Avaria</label>
                <textarea class="form-control" name="avaria" rows="5" required placeholder="Descreva detalhadamente a avaria do equipamento..."><?= old('avaria') ?></textarea>
                <div class="form-text">Mínimo 10 caracteres. Seja específico sobre o problema.</div>
              </div>

              <div class="captcha-box mb-3">
                <span class="me-2">Desafio anti‑bot:</span>
                <strong><?= (int)$captcha_a ?> + <?= (int)$captcha_b ?> = </strong>
                <input type="number" name="captcha_answer" class="form-control d-inline-block ms-2" style="width:120px" inputmode="numeric" step="1" min="0" required>
              </div>

              <button type="submit" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-send"></i> Enviar Reporte de Avaria
              </button>
            </form>

            <!-- Botões de navegação -->
            <div class="text-center mt-4 d-flex gap-2 justify-content-center">
              <a href="/kit-digital" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar para Requisição
              </a>
              <?php 
                $currentHost = $_SERVER['HTTP_HOST'] ?? '';
                
                // Verificar se estamos em localhost
                if (strpos($currentHost, 'localhost') !== false || strpos($currentHost, '127.0.0.1') !== false) {
                  // Ambiente local
                  $publicUrl = 'http://localhost:8080/public';
                } else {
                  // Ambiente de produção
                  $publicUrl = 'https://public.escoladigital.cloud';
                }
              ?>
              <a href="<?= $publicUrl ?>" class="btn btn-outline-primary">
                <i class="bi bi-house"></i> Página Inicial
              </a>
            </div>

            <div class="text-center mt-4">
              <img src="https://registoequipamento.escoladigital.min-educ.pt/images/ED_LogosEuropaFinanciamento.png" alt="Financiamento" class="img-fluid" style="max-height:80px;">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Modal de Sucesso -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">
          <i class="bi bi-check-circle-fill me-2"></i>Reporte Enviado com Sucesso!
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center py-4">
        <div class="mb-3">
          <i class="bi bi-envelope-check" style="font-size: 4rem; color: #198754;"></i>
        </div>
        <h5 class="mb-3">Obrigado pelo seu reporte!</h5>
        <p class="text-muted mb-3" id="successMessage">
          O seu reporte de avaria foi recebido com sucesso e encontra-se em estado <strong>Pendente</strong>.
        </p>
        <div class="alert alert-info mb-0">
          <i class="bi bi-info-circle me-2"></i>
          Receberá um email de confirmação e será contactado(a) assim que a avaria for analisada.
        </div>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">
          <i class="bi bi-check-lg"></i> Entendido
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Erro -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>Erro ao Enviar Reporte
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="errorMessage" class="alert alert-danger mb-0"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Submissão via AJAX com feedback usando Modal Box
(function(){
  const form = document.getElementById('avariaForm');
  if(!form) return;
  
  form.addEventListener('submit', function(e){
    e.preventDefault();
    
    // Validação HTML5
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }
    
    const btn = form.querySelector('button[type=submit]');
    const originalHtml = btn.innerHTML;
    
    // Mostrar spinner
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>A enviar...';
    
    // Preparar dados
    const formData = new FormData(form);
    
    // Enviar via AJAX
    fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      btn.disabled = false;
      btn.innerHTML = originalHtml;
      
      if (data.success) {
        // Mostrar modal de sucesso
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        document.getElementById('successMessage').innerHTML = data.message || 
          'O seu reporte de avaria foi recebido com sucesso e encontra-se em estado <strong>Pendente</strong>.';
        successModal.show();
        
        // Limpar formulário
        form.reset();
        
        // Regenerar captcha
        regenerateCaptcha();
        
        // Scroll para o topo
        window.scrollTo({ top: 0, behavior: 'smooth' });
      } else {
        // Mostrar modal de erro
        const errors = Array.isArray(data.errors) ? data.errors : Object.values(data.errors || {});
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        document.getElementById('errorMessage').innerHTML = errors.join('<br>');
        errorModal.show();
      }
    })
    .catch(error => {
      btn.disabled = false;
      btn.innerHTML = originalHtml;
      console.error('Erro:', error);
      
      // Mostrar modal de erro
      const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
      document.getElementById('errorMessage').innerHTML = 'Erro ao enviar reporte. Por favor, tente novamente.';
      errorModal.show();
    });
  });
  
  // Função para regenerar captcha
  function regenerateCaptcha() {
    fetch('/reportar-avaria-kit')
      .then(response => response.text())
      .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newCaptcha = doc.querySelector('.captcha-box strong');
        if (newCaptcha) {
          document.querySelector('.captcha-box strong').innerHTML = newCaptcha.innerHTML;
          document.querySelector('input[name="captcha_answer"]').value = '';
        }
      })
      .catch(err => console.error('Erro ao regenerar captcha:', err));
  }
  
  // Validação visual de campos com pattern
  form.querySelectorAll('input[pattern]').forEach(inp => {
    inp.addEventListener('invalid', () => {
      inp.classList.add('is-invalid');
    });
    inp.addEventListener('input', () => inp.classList.remove('is-invalid'));
  });
  
  // Validação de textarea mínimo 10 caracteres
  const textareaAvaria = form.querySelector('textarea[name="avaria"]');
  if (textareaAvaria) {
    textareaAvaria.addEventListener('input', function() {
      if (this.value.length > 0 && this.value.length < 10) {
        this.setCustomValidity('A descrição deve ter pelo menos 10 caracteres.');
        this.classList.add('is-invalid');
      } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
      }
    });
  }
})();
</script>
<?= $this->endSection() ?>
