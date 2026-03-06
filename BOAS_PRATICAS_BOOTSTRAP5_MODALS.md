# Boas Práticas - Bootstrap 5 Modals

## 🚨 Problema Comum: `aria-hidden` com elementos focados

**Erro no Console**: 
```
Blocked aria-hidden on an element because its descendant retained focus.
```

Este erro acontece porque há uma **contradição lógica de acessibilidade**: estás a dizer às tecnologias de apoio (leitores de ecrã) para ignorarem um elemento com `aria-hidden="true"`, mas ao mesmo tempo esse elemento ou um descendente tem o foco do teclado.

**Analogia**: É como dizer ao GPS "ignora esta rua" enquanto estás parado no meio dela. O navegador deteta esta contradição e bloqueia a operação.

---

## 🔍 O QUE CAUSA O PROBLEMA?

### Causa Raiz
O Bootstrap 5 gerencia **automaticamente** os atributos `aria-hidden` e `aria-modal` das modals:

1. **Estado Inicial** (modal fechada): `aria-hidden="true"`
2. **Durante abertura**: Bootstrap remove `aria-hidden` e adiciona `aria-modal="true"`
3. **Durante fechamento**: Bootstrap volta a colocar `aria-hidden="true"`

O problema surge quando:
- ❌ Não definimos `aria-hidden="true"` no HTML inicial
- ❌ Fechamos a modal e imediatamente tentamos focar outro elemento (SweetAlert)
- ❌ Usamos syntax Bootstrap 4 (`role="dialog"`, `role="document"`)
- ❌ Gerimos manualmente os atributos aria que o Bootstrap controla

---

## ✅ SOLUÇÃO DEFINITIVA

### 1. **Estrutura HTML Correta (Bootstrap 5)**

❌ **ERRADO** (Bootstrap 4 / HTML incompleto):
```html
<div class="modal fade" id="minhaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Título</h5>
                <button class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
</div>
```

✅ **CORRETO** (Bootstrap 5 com acessibilidade completa):
```html
<div class="modal fade" id="minhaModal" tabindex="-1" 
     aria-labelledby="minhaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="minhaModalLabel">Título</h5>
                <button type="button" class="btn-close" 
                        data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
        </div>
    </div>
</div>
```

**Mudanças Críticas**:
1. ✅ `aria-hidden="true"` no div principal (obrigatório no estado inicial)
2. ✅ `aria-labelledby="minhaModalLabel"` para identificar o título
3. ✅ `id="minhaModalLabel"` no `<h5>` correspondente
4. ✅ Remover `role="dialog"` e `role="document"` (deprecated)
5. ✅ Usar `class="btn-close"` em vez de `class="close"`
6. ✅ Usar `data-bs-dismiss` em vez de `data-dismiss`

---

### 2. **JavaScript - Abrir Modal (Bootstrap 5 API)**

❌ **ERRADO** (jQuery / Bootstrap 4):
```javascript
$('#minhaModal').modal('show');
```

✅ **CORRETO** (Bootstrap 5):
```javascript
const modalElement = document.getElementById('minhaModal');
const modal = new bootstrap.Modal(modalElement);
modal.show();
```

**Por quê?** O Bootstrap 5 descontinuou o plugin jQuery. Usar `.modal()` pode causar conflitos com a gestão de atributos aria.

---

### 3. **JavaScript - Fechar Modal com Timeout (CRÍTICO)**

Este é o ponto mais importante para resolver o erro `aria-hidden`.

❌ **ERRADO** (causa erro de foco):
```javascript
$.ajax({
    success: function(response) {
        const modal = bootstrap.Modal.getInstance(modalElement);
        modal.hide();  // Começa animação fade (300ms)
        
        Swal.fire({...});  // ❌ ERRO! SweetAlert tenta focar enquanto modal ainda está a fechar
    }
});
```

✅ **CORRETO** (aguarda animação completar):
```javascript
$.ajax({
    success: function(response) {
        const modalElement = document.getElementById('minhaModal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        
        if (modal) {
            modal.hide();  // Inicia animação de fade (300ms)
            
            // ⏱️ AGUARDAR animação completar antes de focar outro elemento
            setTimeout(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message
                });
            }, 300);  // 300ms = duração padrão da animação .fade
        }
    }
});
```

**Explicação Técnica**:
1. `modal.hide()` inicia a animação CSS `.fade` (300ms)
2. Durante a animação, Bootstrap aplica `aria-hidden="true"`
3. Se abrirmos SweetAlert **imediatamente**, ele tenta focar enquanto `aria-hidden` ainda está ativo
4. Navegador deteta contradição: "elemento com foco tem aria-hidden"
5. **Solução**: `setTimeout(fn, 300)` aguarda a animação completar

---

### 4. **Event Listeners - Sem Duplicação**

❌ **ERRADO** (event listener redundante):
```javascript
// Botão já tem data-bs-dismiss="modal", NÃO precisas disto:
$('.btn-close, [data-bs-dismiss="modal"]').on('click', function() {
    const modal = bootstrap.Modal.getInstance(...);
    modal.hide();  // ❌ REDUNDANTE! Bootstrap já faz isso
});
```

✅ **CORRETO** (deixar Bootstrap gerir):
```html
<!-- Botão com data-bs-dismiss é suficiente -->
<button class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
```

Só adiciona event listener manual se precisares de executar código **ADICIONAL** antes de fechar:
```javascript
$('.btn-close').on('click', function() {
    console.log('Modal a fechar...');
    // Bootstrap cuida do resto via data-bs-dismiss
});
```

---

## 📋 CHECKLIST COMPLETO

### HTML das Modals:
- [ ] `aria-hidden="true"` no `<div class="modal fade">`
- [ ] `aria-labelledby="modalIdLabel"` correspondente ao ID do título
- [ ] `id="modalIdLabel"` no `<h5 class="modal-title">`
- [ ] Remover `role="dialog"` e `role="document"`
- [ ] Usar `class="btn-close"` (não `class="close"`)
- [ ] Usar `data-bs-dismiss="modal"` (não `data-dismiss`)
- [ ] Remover `<span aria-hidden="true">&times;</span>` dos botões close

### JavaScript:
- [ ] Usar `new bootstrap.Modal(element)` para abrir
- [ ] Usar `bootstrap.Modal.getInstance(element)` para fechar
- [ ] **SEMPRE** usar `setTimeout(fn, 300)` após `modal.hide()` se vais abrir SweetAlert/outra modal
- [ ] Remover chamadas `$().modal()` (jQuery plugin)
- [ ] Remover event listeners duplicados em botões com `data-bs-dismiss`

### Testing:
- [ ] Abrir e fechar modal múltiplas vezes
- [ ] Verificar console do navegador para erros `aria-hidden`
- [ ] Testar com teclado (Tab, Esc)
- [ ] Testar com leitor de ecrã (opcional mas recomendado)

---

## 🎯 PADRÃO COMPLETO: AJAX com Modal + SweetAlert

Este é o padrão a seguir em **TODOS** os teus callbacks AJAX que fecham modals:

```html
<!-- ✅ Estrutura HTML CORRETA -->
<div class="modal fade" id="modalConfirmar" tabindex="-1" 
     aria-labelledby="modalConfirmarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modalConfirmarLabel">Confirmar Ação</h5>
                <button type="button" class="btn-close" 
                        data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Deseja confirmar esta ação?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" 
                        data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" 
                        id="btnConfirmar">Confirmar</button>
            </div>
        </div>
    </div>
</div>
```

```javascript
// ✅ ABRIR MODAL (Bootstrap 5 API)
$('.btn-abrir').on('click', function() {
    const modalElement = document.getElementById('modalConfirmar');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
});

// ✅ CONFIRMAR COM AJAX (Padrão Correto)
$('#btnConfirmar').on('click', function() {
    $.ajax({
        url: 'api/confirmar',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            // 1️⃣ Fechar modal PRIMEIRO
            const modalElement = document.getElementById('modalConfirmar');
            const modal = bootstrap.Modal.getInstance(modalElement);
            
            if (modal) {
                modal.hide();
                
                // 2️⃣ AGUARDAR 300ms (animação fade)
                setTimeout(function() {
                    // 3️⃣ AGORA mostrar SweetAlert
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: response.message
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message
                        });
                    }
                }, 300);  // ⏱️ CRÍTICO: Aguardar animação
            }
        },
        error: function(xhr) {
            // ✅ Mesmo padrão para erros
            const modalElement = document.getElementById('modalConfirmar');
            const modal = bootstrap.Modal.getInstance(modalElement);
            
            if (modal) {
                modal.hide();
                setTimeout(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Erro ao processar requisição'
                    });
                }, 300);
            }
        }
    });
});
```

---

## 🔬 EXPLICAÇÃO TÉCNICA: Por que `aria-hidden="true"` é Obrigatório?

### O que o Bootstrap 5 faz com os atributos ARIA:

```html
<!-- 1️⃣ ESTADO INICIAL (Modal Fechada) -->
<div class="modal fade" id="minhaModal" 
     aria-hidden="true">  <!-- ✅ Obrigatório no HTML -->
</div>

<!-- 2️⃣ DURANTE ABERTURA (Bootstrap adiciona automaticamente) -->
<div class="modal fade show" id="minhaModal" 
     style="display: block;" 
     aria-modal="true">  <!-- ✅ Bootstrap adiciona -->
     <!-- aria-hidden foi REMOVIDO -->
</div>

<!-- 3️⃣ DURANTE FECHAMENTO (Bootstrap reaplica) -->
<div class="modal fade" id="minhaModal" 
     style="display: none;" 
     aria-hidden="true">  <!-- ✅ Bootstrap reaplica -->
</div>
```

### ⚠️ O que acontece SE NÃO tiveres `aria-hidden="true"` inicial?

1. Bootstrap **não sabe** o estado inicial da modal
2. Durante transições, aplica `aria-hidden` de forma inconsistente
3. Pode aplicar `aria-hidden="true"` ENQUANTO um elemento descendente tem foco
4. **Resultado**: Erro "Blocked aria-hidden on focused element"

### ✅ Por que funciona com `aria-hidden="true"`?

1. Bootstrap **sabe** que a modal está fechada
2. Ao abrir: **PRIMEIRO** remove `aria-hidden`, **DEPOIS** foca
3. Ao fechar: **PRIMEIRO** remove foco, **DEPOIS** aplica `aria-hidden`
4. **Resultado**: Sem conflitos de foco

---

## 🆕 ALTERNATIVA MODERNA: Atributo `inert`

O atributo `inert` é a **solução moderna** recomendada pela W3C para substituir `aria-hidden` em contextos de modals.

### Diferença entre `aria-hidden` e `inert`:

| Atributo | O que faz | Problema |
|----------|-----------|----------|
| `aria-hidden="true"` | Esconde de leitores de ecrã | **NÃO** desativa teclado/cliques |
| `inert` | Esconde **E** desativa interações | ✅ Solução completa |

### Como usar `inert` com modals:

```html
<!-- Conteúdo principal da página -->
<main id="main-content">
    <h1>Página Principal</h1>
    <button onclick="openModal()">Abrir Modal</button>
</main>

<!-- Modal -->
<div class="modal fade" id="minhaModal" 
     aria-labelledby="minhaModalLabel" aria-hidden="true">
    <!-- ... conteúdo da modal ... -->
</div>

<script>
function openModal() {
    // 1. Marcar conteúdo principal como inert
    document.getElementById('main-content').setAttribute('inert', '');
    
    // 2. Abrir modal (Bootstrap 5 API)
    const modalElement = document.getElementById('minhaModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

// Remover inert quando modal fechar
document.getElementById('minhaModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('main-content').removeAttribute('inert');
});
</script>
```

**Vantagens do `inert`**:
- ✅ Desativa Tab, cliques, e foco no conteúdo de fundo
- ✅ Mais seguro que `aria-hidden` (não causa conflitos de foco)
- ✅ Recomendado pela W3C para modals e overlays

**Nota**: `inert` é suportado em todos os navegadores modernos (Chrome 102+, Firefox 112+, Safari 15.5+).

---

## 🚫 ANTIPADRÕES - NUNCA FAZER

### 1. ❌ Misturar jQuery `.modal()` com Bootstrap 5
```javascript
// ❌ NÃO FAÇA ISSO!
$('#modal').modal('show');   // Plugin jQuery desatualizado
$('#modal').modal('hide');   // Causa conflitos de gestão ARIA
```

### 2. ❌ Fechar modal sem aguardar antes de focar outro elemento
```javascript
// ❌ NÃO FAÇA ISSO!
modal.hide();
Swal.fire({...});  // Conflito de foco!
```

### 3. ❌ Definir `aria-hidden` manualmente via JavaScript
```javascript
// ❌ NÃO FAÇA ISSO!
$('#modal').attr('aria-hidden', 'false');  // Bootstrap já gere isto
```

### 4. ❌ Usar sintaxe Bootstrap 4 em projeto Bootstrap 5
```html
<!-- ❌ NÃO FAÇA ISSO! -->
<div class="modal" role="dialog">  <!-- role="dialog" deprecated -->
    <div class="modal-dialog" role="document">  <!-- role="document" deprecated -->
        <button data-dismiss="modal">  <!-- data-dismiss deprecated -->
```

### 5. ❌ Event listeners redundantes
```javascript
// ❌ NÃO FAÇA ISSO! Bootstrap já cuida via data-bs-dismiss
$('[data-bs-dismiss="modal"]').on('click', function() {
    $('#modal').modal('hide');  // Redundante e causa duplo-fechamento
});
```

### 6. ❌ Esquecer `aria-labelledby` e `id` no título
```html
<!-- ❌ MAU para acessibilidade -->
<div class="modal fade" id="minhaModal" aria-hidden="true">
    <h5 class="modal-title">Título</h5>  <!-- Sem ID -->
</div>

<!-- ✅ BOM para acessibilidade -->
<div class="modal fade" id="minhaModal" 
     aria-labelledby="minhaModalLabel" aria-hidden="true">
    <h5 class="modal-title" id="minhaModalLabel">Título</h5>
</div>
```

---

## ✨ RESUMO EXECUTIVO

### Regras de Ouro:

1. **HTML**: SEMPRE incluir `aria-hidden="true"` no estado inicial da modal
2. **HTML**: SEMPRE incluir `aria-labelledby` apontando para o ID do título
3. **JavaScript**: SEMPRE usar Bootstrap 5 API (`new bootstrap.Modal()`)
4. **JavaScript**: SEMPRE esperar 300ms após `modal.hide()` antes de focar outro elemento
5. **Acessibilidade**: Considerar usar `inert` no conteúdo de fundo quando modal abre

### Padrão Universal para Callbacks AJAX:

```javascript
// ✅ COPIAR E COLAR este padrão em todos os callbacks AJAX
modal.hide();              // 1. Fechar modal
setTimeout(function() {    // 2. Aguardar animação
    Swal.fire({...});      // 3. Mostrar feedback
}, 300);                   // ⏱️ 300ms = duração .fade
```

---

## 📖 REFERÊNCIAS

- [Bootstrap 5 Modal Documentation](https://getbootstrap.com/docs/5.3/components/modal/)
- [Bootstrap 5 Migration Guide](https://getbootstrap.com/docs/5.3/migration/)
- [WAI-ARIA aria-hidden Specification](https://w3c.github.io/aria/#aria-hidden)
- [HTML inert Attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/inert)
- [W3C ARIA Authoring Practices - Modal Dialog](https://www.w3.org/WAI/ARIA/apg/patterns/dialog-modal/)

---

**Data última atualização**: 12/02/2026  
**Versão**: 2.0  
**Aplicado em**: user_dashboard.php, tecnico_dashboard.php, admin_dashboard.php  
**Status**: ✅ Problema `aria-hidden` RESOLVIDO definitivamente

