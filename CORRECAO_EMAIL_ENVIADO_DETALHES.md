# Correção: Email Enviado Não Aparecia em Detalhes

**Data**: 16/01/2026  
**Problema**: Quando clicava em "Enviar email para todos" na página de detalhes da sessão, os emails eram enviados com sucesso mas não ficava registado "Email Enviado" na interface.

## Causa Raiz

A view `detalhes.php` estava a verificar campos com nomes incorretos:
- Verificava `$conv['email_enviado']` mas na base de dados o campo é `email_enviado_em`
- Verificava `$conv['data_envio_email']` mas na base de dados o campo é `email_enviado_em`

## Estrutura da Base de Dados

```sql
ALTER TABLE `convocatoria` 
ADD COLUMN `email_enviado_em` DATETIME NULL COMMENT 'Data e hora do envio do email. NULL = não enviado';
```

- **Campo**: `email_enviado_em` (tipo DATETIME)
- **NULL**: Email não enviado
- **Data**: Email enviado nessa data/hora

## Fluxo Correto

1. **Frontend** (detalhes.php): Botão "Enviar email para todos"
2. **AJAX**: POST para `sessoes-exame/enviar-convocatorias-todas/:id`
3. **Controller** (`SessaoExameController::enviarConvocatoriasTodas`):
   - Loop por todas as convocatórias da sessão
   - Chama `enviarEmailConvocatoria()` para cada uma
4. **Controller** (`SessaoExameController::enviarEmailConvocatoria`):
   - Envia o email
   - **Atualiza** `email_enviado_em` com data/hora atual
5. **Frontend**: Recarrega a página
6. **View**: Mostra badge "Sim" se `email_enviado_em` não for NULL

## Correção Aplicada

### Arquivo: `app/Views/sessoes_exame/detalhes.php`

**Antes (ERRADO)**:
```php
<?php if (!empty($conv['email_enviado'])): ?>
    <span class="badge bg-success" title="Enviado em <?= date('d/m/Y H:i', strtotime($conv['data_envio_email'])) ?>">
        <i class="bi bi-check-circle"></i> Sim
    </span>
    <br>
    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($conv['data_envio_email'])) ?></small>
```

**Depois (CORRETO)**:
```php
<?php if (!empty($conv['email_enviado_em'])): ?>
    <span class="badge bg-success" title="Enviado em <?= date('d/m/Y H:i', strtotime($conv['email_enviado_em'])) ?>">
        <i class="bi bi-check-circle"></i> Sim
    </span>
    <br>
    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($conv['email_enviado_em'])) ?></small>
```

## Verificação

### Antes do Envio
✅ Campo na BD: `email_enviado_em = NULL`  
✅ Interface: Badge "Não" (cinza)

### Depois do Envio
✅ Campo na BD: `email_enviado_em = '2026-01-16 14:35:22'`  
✅ Interface: Badge "Sim" (verde) + data/hora

## Arquivos Modificados

1. ✅ `app/Views/sessoes_exame/detalhes.php` - Corrigidos campos `email_enviado_em`

## Nota Técnica

O controller (`SessaoExameController.php` linha 688) já estava a funcionar corretamente:
```php
$db->table('convocatoria')
   ->where('id', $convocatoria['id'])
   ->update([
       'email_enviado_em' => date('Y-m-d H:i:s')
   ]);
```

O problema era **apenas na view** que usava nomes de campos que não existiam na base de dados.
