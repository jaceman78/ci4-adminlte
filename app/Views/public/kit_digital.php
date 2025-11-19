<?= $this->extend('layout/public') ?>
<?= $this->section('title') ?>Kit Digital<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.conditions-box { background:#fff; border:1px solid #e9ecef; border-radius:10px; padding:24px; max-height:380px; overflow:auto; }
.required::after { content:' *'; color:#dc3545; }
.captcha-box { background:#f8f9fa; border:1px dashed #ced4da; border-radius:8px; padding:12px 16px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="py-5">
  <div class="container">
    <div class="row mb-4">
      <div class="col-lg-10 mx-auto text-center">
        <h1 class="fw-bold mb-2">Requisição de Kit Digital</h1>
        <p class="text-muted">Preencha o formulário abaixo para requerer o Kit Digital em regime de comodato.</p>
      </div>
    </div>

    <?php if(!empty($success)): ?>
      <div class="alert alert-success"><?= esc($success) ?></div>
    <?php endif; ?>
    <?php if(!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
        <?php foreach((array)$errors as $e): ?>
          <li><?= esc($e) ?></li>
        <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="row g-4">
      <div class="col-lg-6">
        <h5 class="fw-bold mb-3">Condições Gerais</h5>
        <div class="conditions-box small">
<pre class="mb-0" style="white-space: pre-wrap;">CONDIÇÕES GERAIS
1. Os equipamentos cedidos destinam-se a ser utilizados, exclusivamente, para fins do processo de ensino e aprendizagem do Aluno, com início em 16/5/2023 e término na data de conclusão do ciclo de estudos que o Aluno frequenta no momento da cedência, nomeadamente, nas seguintes situações:
a. Quando os alunos tenham completado o ciclo ou nível de ensino a que se destinam os equipamentos a fornecer ou a escolaridade obrigatória (no final do 4º, 9º ou 12ºano);
b. Nas situações de transferências de alunos para outro AE/EnA distinto do 2.º outorgante;
c. Em caso de aplicação de medidas disciplinares sancionatórias ao aluno que determinem a «transferência de escola» ou a «expulsão da escola», previstas, respetivamente, nas alíneas d) e e) do n.º 2 do artigo 28.º do Estatuto do Aluno e ÉticaEscolar, aprovado pela Lei n.º 51/2012, de 5 de setembro, na sua redação atual;
d. Com a saída do aluno do Ensino Público.

2. Nos casos previstos no número 1., a devolução dos equipamentos informáticos, conetividade e serviços conexos pelo EE ou pelo aluno deve ocorrer através da entrega dos mesmos nas instalações da sede do AE/EnA no prazo máximo de uma semana, após a verificação dos factos aí descritos;

3. Caso a entrega dos equipamentos não tenha lugar no prazo previsto no n.º anterior, o/a Encarregado/a de Educação/Aluno/a (comodatário/a) será notificado/a pelo na sede do Agrupamento de Escolas João de Barros, Seixal, para a entrega dos equipamentos no término do período previsto no n.º 1, para os contactos indicados pelo/a EE, para esta finalidade, ou na falta, para a sua morada;

4. O equipamento informático deve ser entregue limpo de ficheiros pessoais dos seus utilizadores e subcessionários;

5. O Aluno maior de idade comodatário obriga-se a zelar pela conservação dos bens e equipamentos que lhe são cedidos por comodato (empréstimo), devendo restituí-los no fim do período indicado nos pontos anteriores nas condições que resultam de um uso responsável e prudente, sob pena do acionamento de obrigações contratualmente previstas por perda ou deterioração dos bens e equipamentos;

6. A instalação de programas ou aplicações informáticas (software) no equipamento cedido, deve ser feita exclusivamente para fins do processo de ensino e aprendizagem;

7. A instalação ou remoção de partes ou componentes (hardware) do equipamento é expressamente proibida;

8. O Aluno maior de idade (comodatário) está autorizado a deslocar os equipamentos para fora da morada da sua residência ou domicílio indicado neste auto de entrega, exclusivamente para fins relacionados com o processo de ensino e aprendizagem e bem assim nas situações em que sejam previamente autorizados pelo Ministério da Educação ou pelo/a Diretor/a do AE/EnA;

9. O Aluno maior de idade comodatário obriga-se a comunicar imediatamente ao Agrupamento de Escolas João de Barros, Seixal a perda ou o roubo dos bens ou
equipamentos;

10. O Aluno maior de idade comodatário obriga-se, ainda, a suportar todas as despesas devidas pela recuperação dos bens ou equipamentos sempre que os danos advenham de mau uso ou negligência na sua conservação;

11. É vedada ao Aluno maior de idade (comodatário) a possibilidade de sub-comodatar ou locar os bens ou equipamentos objeto cedido a terceiros;

12. Em tudo o que não consta nos pontos anteriores, são aplicáveis à presente cedência de equipamentos para o acesso e a utilização de recursos didáticos e educativos digitais, as  disposições constantes dos artigos 1129.º a 1137.º do Código Civil, relativas ao contrato de comodato.

TRATAMENTO DE DADOS PESSOAIS, DECLARAÇÃO DE CONSENTIMENTO E EXERCÍCIO DE DIREITOS

13. O tratamento de dados pessoais é realizado no âmbito da Medida «Universalização da Escola Digital», com base na gestão da relação contratual, para efeitos de gestão da entrega dos equipamentos informáticos, de acordo com os termos e condições da Política de Proteção de Dados acessível em https://registoequipamento.escoladigital.min-educ.pt.

14. O Aluno maior de id, sendo titular dos dados pessoais constantes do presente auto de entrega de bens ou equipamentos informáticos autoriza expressamente a que os mesmos sejam objeto de recolha, utilização, registo e tratamento, ao abrigo da alínea a) do n.º1 do art.6.º do Regulamento Geral sobre Proteção de Dados (RGPD), para efeitos de monitorização, verificação, controloe avaliação no quadro da implementação dos Fundos Europeus Estruturais e de Investimento (FEEI) e respetivo reporte à Comissão Europeia e restantes entidades envolvidas, no âmbito dos respetivos projetos comunitários financiadores e sempre que solicitado pelas autoridades nacionais e comunitárias legalmente competentes, no âmbito das quais também podem ser solicitados comprovativos de matrícula e da condição de beneficiário do escalão de Ação Social Escolar identificado no proémio pelas mesmas autoridades
SIM, ACEITO que os meus dados pessoais sejam objeto de recolha, utilização, registo e tratamento, para os efeitos indicados no presente documento

15. O Aluno maior de idade, enquanto titular dos dados pessoais, está consciente de quepode solicitar informações, apresentar reclamações, comunicar incidentes ou exercer direitos de proteção de dados, designadamente e entre outros, os direitos de acesso, retificação, oposição ou limitação do tratamento, portabilidade, apagamento ou retirada do consentimento, através de contacto com o Encarregado da Proteção de Dados do Agrupamento de Escola ou Escola não Agrupada, cujos contactos estão disponíveis na respetiva Política de Proteção de Dados.
</pre>
        </div>
      </div>

      <div class="col-lg-6">
  <h5 class="fw-bold mb-3">Formulário</h5>
  <form method="post" action="<?= site_url('kit-digital/requerer') ?>">
          <?= csrf_field() ?>
          <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off">

    <noscript><div class="alert alert-warning small">É necessário JavaScript para submeter o formulário.</div></noscript>

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
                    <option value="<?= esc($t['codigo']) ?>" <?= old('turma')===$t['codigo'] ? 'selected' : '' ?>><?= esc($t['codigo']) ?></option>
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
              <label class="form-label required">Ação Social Escolar</label>
              <select class="form-select" name="ase" required>
                <option value="Sem Escalão" <?= old('ase')==='Sem Escalão' ? 'selected' : '' ?>>Sem Escalão</option>
                <option value="Escalão A" <?= old('ase')==='Escalão A' ? 'selected' : '' ?>>Escalão A</option>
                <option value="Escalão B" <?= old('ase')==='Escalão B' ? 'selected' : '' ?>>Escalão B</option>
                <option value="Escalão C" <?= old('ase')==='Escalão C' ? 'selected' : '' ?>>Escalão C</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label required">Email do Aluno</label>
              <input type="email" class="form-control" name="email_aluno" value="<?= old('email_aluno') ?>" required>
            </div>
          </div>

          <div class="mb-3 mt-3">
            <label class="form-label required">Email do Encarregado de Educação</label>
            <input type="email" class="form-control" name="email_ee" value="<?= old('email_ee') ?>" required>
          </div>

          <div class="captcha-box mb-3">
            <span class="me-2">Desafio anti‑bot:</span>
            <strong><?= (int)$captcha_a ?> + <?= (int)$captcha_b ?> = </strong>
            <input type="number" name="captcha_answer" class="form-control d-inline-block ms-2" style="width:120px" inputmode="numeric" step="1" min="0" required>
          </div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="aceito" name="aceito" value="1" required <?= old('aceito') ? 'checked' : '' ?>>
            <label class="form-check-label" for="aceito">
              Declaro que li e aceito as Condições Gerais e a Política de Proteção de Dados.
            </label>
          </div>

          <button type="submit" class="btn btn-primary">
            <i class="bi bi-send"></i> Submeter Requisição
          </button>
        </form>

        <!-- Botão voltar ao início -->
        <div class="text-center mt-4">
          <?php 
            $publicHost = getenv('PUBLIC_HOST') ?: env('PUBLIC_HOST', 'public.escoladigital.cloud');
            $publicUrl = 'https://' . $publicHost . '/';
          ?>
          <a href="<?= $publicUrl ?>" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Voltar ao Início
          </a>
        </div>

        <div class="text-center mt-4">
          <img src="https://registoequipamento.escoladigital.min-educ.pt/images/ED_LogosEuropaFinanciamento.png" alt="Financiamento" class="img-fluid" style="max-height:80px;">
        </div>
      </div>
    </div>
  </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Feedback de submissão e captura de eventuais bloqueios de validação
(function(){
  const form = document.querySelector('form[action*="kit-digital/requerer"]');
  if(!form) return;
  form.addEventListener('submit', function(e){
    // Mostrar spinner leve
    let btn = form.querySelector('button[type=submit]');
    if(btn && !btn.dataset.loading){
      btn.dataset.loading = '1';
      btn.disabled = true;
      const original = btn.innerHTML;
      btn.dataset.originalHtml = original;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>A enviar...';
      setTimeout(()=>{ // se passados 6s ainda não houve redirect, reativar botão
        if(btn && btn.dataset.loading){
          btn.disabled = false;
          btn.innerHTML = original + ' <span class="text-danger small">(sem resposta - verifique campos)</span>';
          delete btn.dataset.loading;
        }
      },6000);
    }
    // Reportar ao console os campos (para debugging rápido)
    const fd = new FormData(form);
    console.debug('[KitDigital] submit payload', Object.fromEntries(fd.entries()));
  });

  // Remover foco automático involuntário se houver erro de pattern
  form.querySelectorAll('input[pattern]').forEach(inp => {
    inp.addEventListener('invalid', () => {
      inp.classList.add('is-invalid');
    });
    inp.addEventListener('input', () => inp.classList.remove('is-invalid'));
  });
})();
</script>
<?= $this->endSection() ?>
