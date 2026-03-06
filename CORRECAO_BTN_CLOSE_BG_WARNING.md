# Correção: Botão Close em Modais com bg-warning

## 🚨 PROBLEMA

O botão `btn-close` do Bootstrap 5 apresenta **rendering incorreto** em modais com fundo amarelo (`bg-warning`), exibindo um "X" com aparência estranha ou pouco visível.

**Causa**: O Bootstrap 5 usa um SVG inline com filtro CSS para criar o ícone "X". Em fundos claros (especialmente amarelo #ffc107), o filtro padrão não renderiza corretamente.

---

## ✅ SOLUÇÃO

Adicionar um **filtro CSS inline** ao botão close nas modais com `bg-warning`:

```html
<!-- ❌ ANTES (incorreto) -->
<div class="modal-header bg-warning">
    <h5 class="modal-title">Título</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
</div>

<!-- ✅ DEPOIS (correto) -->
<div class="modal-header bg-warning">
    <h5 class="modal-title">Título</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar" 
            style="filter: invert(1) grayscale(100%) brightness(0);"></button>
</div>
```

### Explicação do filtro CSS:

```css
filter: invert(1) grayscale(100%) brightness(0);
```

- **`invert(1)`** → Inverte as cores do ícone
- **`grayscale(100%)`** → Remove qualquer saturação de cor
- **`brightness(0)`** → Força a cor final para preto puro

**Resultado**: Um "X" preto, nítido e com contraste perfeito no fundo amarelo.

---

## 📋 CHECKLIST PARA NOVAS MODAIS

Ao criar uma nova modal com `bg-warning`, aplicar esta checklist:

1. ✅ Verificar se o header tem classe `bg-warning`
2. ✅ Adicionar o filtro CSS inline ao botão close
3. ✅ Testar visualmente o rendering do "X"
4. ✅ Verificar acessibilidade (contraste adequado)

---

## 🔍 COMO ENCONTRAR MODAIS COM O PROBLEMA

### Pesquisa por padrão (grep/search):

```bash
# Buscar modais com bg-warning
grep -r "modal-header bg-warning" app/Views/

# Buscar btn-close SEM o filtro
grep -r 'btn-close.*data-bs-dismiss' app/Views/ | grep -v 'filter: invert'
```

### Comando para listar todas as ocorrências:

Execute no terminal PowerShell:

```powershell
Get-ChildItem -Path "app\Views" -Recurse -Include *.php | 
    Select-String -Pattern "modal-header bg-warning" | 
    Select-Object -ExpandProperty Path -Unique
```

---

## 📁 MODAIS JÁ CORRIGIDAS (Data: 12/02/2026)

### Dashboards:
1. ✅ `app/Views/dashboard/user_dashboard.php` - 2 modais
   - modalDetalhesPermuta
   - modalPedirPermuta

2. ✅ `app/Views/dashboard/tecnico_dashboard.php` - 2 modais
   - modalDetalhesPermuta
   - modalPedirPermuta

3. ✅ `app/Views/dashboard/admin_dashboard.php` - 1 modal
   - modalDetalhesPermuta

4. ✅ `app/Views/dashboard/super_admin_dashboard.php` - 1 modal
   - modalDetalhesPermuta

### Outras Views:
5. ✅ `app/Views/equipamentos/equipamentos_index.php` - 1 modal
   - confirmMudancaTicketsModal

6. ✅ `app/Views/tickets/view_ticket.php` - 1 modal
   - modalReabrirTicket

7. ✅ `app/Views/logs/activity_log_index.php` - 1 modal
   - cleanLogsModal

8. ✅ `app/Views/kit_digital_admin/index.php` - 1 modal
   - Modal de confirmação de anulação

9. ✅ `app/Views/gestao_letiva/anos_letivos_index.php` - 1 modal
   - confirmAtivarModal

**Total**: 11 modais corrigidas em 9 arquivos

---

## 🎨 ALTERNATIVA: Classe CSS Reutilizável

Se preferires não usar inline styles, podes criar uma classe CSS global:

### 1. Adicionar ao CSS principal (public/assets/css/custom.css):

```css
/* Correção btn-close em fundos amarelos */
.btn-close-dark {
    filter: invert(1) grayscale(100%) brightness(0);
}
```

### 2. Usar a classe nas modais:

```html
<button type="button" class="btn-close btn-close-dark" 
        data-bs-dismiss="modal" aria-label="Fechar"></button>
```

### Vantagens da classe:
- ✅ Código HTML mais limpo
- ✅ Centralização da correção
- ✅ Fácil manutenção futura

### Desvantagens:
- ❌ Requer edição de CSS global
- ❌ Precisa garantir que CSS está carregado

---

## 🔄 QUANDO APLICAR ESTA CORREÇÃO?

### ✅ APLICAR quando:
- Modal tem classe `bg-warning` no header
- Botão close parece estranho ou pouco visível
- Fundos claros/amarelos dificultam visualização do "X"

### ❌ NÃO APLICAR quando:
- Modal usa `bg-primary`, `bg-danger`, `bg-success`, `bg-dark` → Use `btn-close-white`
- Modal tem fundo branco/claro padrão → Não precisa de correção
- Já está visível e com bom contraste

---

## 📖 REFERÊNCIAS

### Cores de fundo no Bootstrap 5:

| Classe | Cor | Botão Close Recomendado |
|--------|-----|------------------------|
| `bg-warning` | Amarelo (#ffc107) | `btn-close` + filtro CSS |
| `bg-light` | Cinza claro | `btn-close` (padrão) |
| `bg-white` | Branco | `btn-close` (padrão) |
| `bg-primary` | Azul escuro | `btn-close-white` |
| `bg-danger` | Vermelho | `btn-close-white` |
| `bg-success` | Verde | `btn-close-white` |
| `bg-dark` | Preto | `btn-close-white` |

### Documentação Oficial:
- [Bootstrap 5 Close Button](https://getbootstrap.com/docs/5.3/components/close-button/)
- [CSS Filter Property](https://developer.mozilla.org/en-US/docs/Web/CSS/filter)

---

## 🛠️ SCRIPT DE CORREÇÃO AUTOMÁTICA

Se precisares corrigir múltiplos arquivos de uma vez, usa este script PowerShell:

```powershell
# Script: fix_btn_close_warning.ps1
# Corrige btn-close em todas as modais bg-warning

$files = Get-ChildItem -Path "app\Views" -Recurse -Include *.php

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    
    # Padrão: bg-warning + btn-close SEM filtro
    $pattern = '(modal-header bg-warning.*?<button[^>]+btn-close[^>]+data-bs-dismiss[^>]+)aria-label="[^"]*">'
    $replacement = '$1aria-label="Fechar" style="filter: invert(1) grayscale(100%) brightness(0);">'
    
    if ($content -match $pattern) {
        $newContent = $content -replace $pattern, $replacement
        Set-Content -Path $file.FullName -Value $newContent -NoNewline
        Write-Host "Corrigido: $($file.FullName)" -ForegroundColor Green
    }
}

Write-Host "Correção concluída!" -ForegroundColor Cyan
```

**Uso**:
```powershell
cd C:\xampp\htdocs\ci4-adminlte
.\fix_btn_close_warning.ps1
```

---

## ✨ RESUMO RÁPIDO

**1 linha para copiar e colar**:

```html
style="filter: invert(1) grayscale(100%) brightness(0);"
```

**Aplicar em**: `<button class="btn-close">` dentro de `<div class="modal-header bg-warning">`

**Resultado**: Botão "X" preto, nítido e visível ✅

---

**Última atualização**: 12/02/2026  
**Versão**: 1.0  
**Aplicado em**: 11 modais, 9 arquivos  
**Status**: ✅ Todos os casos conhecidos corrigidos
