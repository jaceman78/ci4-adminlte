# Sistema de Remarcação de Férias com Aprovação

## Data: 18/03/2026

## Visão Geral

O sistema de remarcação foi atualizado para incluir um processo de aprovação pela secretaria antes de cancelar o pedido do professor.

## Fluxo de Remarcação

### Anterior (Imediato)
1. Professor solicita remarcação
2. Pedido é **cancelado imediatamente**
3. Dias devolvidos ao saldo
4. Professor pode marcar novas férias

### Novo (Com Aprovação)
1. Professor solicita remarcação → Estado: `remarcacao_solicitada`
2. **Secretaria avalia o pedido**
3. Se **aprovado**:
   - Pedido é cancelado
   - Dias devolvidos ao saldo
   - Professor recebe email de confirmação
   - Professor pode marcar novas férias
4. Se **rejeitado**:
   - Estado volta ao anterior (`aprovado`, `aguarda_assinatura` ou `concluido`)
   - Férias permanecem como estão
   - Professor recebe email com motivo da rejeição

## Alterações no Sistema

### 1. Base de Dados

**Tabela:** `ferias_pedido`
**Campo:** `estado` (ENUM)

Novo valor adicionado: `'remarcacao_solicitada'`

**SQL de Migração:** `ALTER_FERIAS_PEDIDO_ADD_REMARCACAO_SOLICITADA.sql`

### 2. Controller (FeriasController.php)

#### Métodos Atualizados:

**`solicitarRemarcacao($pedidoId)`**
- Agora muda o estado para `'remarcacao_solicitada'` em vez de cancelar
- Envia email à secretaria notificando
- Registra log da ação

#### Novos Métodos:

**`aprovarRemarcacao($pedidoId)`** - Secretaria
- Valida se está no estado `'remarcacao_solicitada'`
- Cancela o pedido (estado: `'cancelado'`)
- Devolve dias ao saldo do professor
- Envia email ao professor confirmando
- Registra log da aprovação

**`rejeitarRemarcacao($pedidoId)`** - Secretaria
- Valida se está no estado `'remarcacao_solicitada'`
- Restaura estado anterior do pedido
- Adiciona motivo da rejeição às observações
- Envia email ao professor com motivo
- Registra log da rejeição

### 3. Rotas (Routes.php)

Novas rotas adicionadas:
```php
$routes->post('aprovar-remarcacao/(:num)', 'FeriasController::aprovarRemarcacao/$1');
$routes->post('rejeitar-remarcacao/(:num)', 'FeriasController::rejeitarRemarcacao/$1');
```

### 4. Helper (ferias_helper.php)

**`formatar_estado_ferias($estado)`**
- Adicionado badge para novo estado:
  ```php
  'remarcacao_solicitada' => '<span class="badge bg-warning"><i class="fas fa-clock"></i> Remarcação Solicitada</span>'
  ```

### 5. Views

#### professor_pedidos.php
- **Botão de remarcação:** Desabilitado quando está em `'remarcacao_solicitada'`
- **Badge "Aguardando":** Exibido no lugar do botão quando em estado pendente
- **Modal atualizado:** Informa sobre aprovação da secretaria
- **Formatação de estado:** JavaScript atualizado com novo estado

## Instalação

### Passo 1: Executar Migração SQL

```sql
-- Execute no phpMyAdmin ou MySQL CLI
ALTER TABLE `ferias_pedido`
MODIFY COLUMN `estado` ENUM(
    'por_preencher',
    'submetido',
    'em_aprovacao',
    'aprovado',
    'rejeitado',
    'cancelado',
    'aguarda_assinatura',
    'concluido',
    'remarcacao_solicitada'
) DEFAULT 'por_preencher';
```

Ou execute o ficheiro completo: `ALTER_FERIAS_PEDIDO_ADD_REMARCACAO_SOLICITADA.sql`

### Passo 2: Verificar Alterações

Todas as alterações de código já foram aplicadas:
- ✅ Controller atualizado
- ✅ Rotas adicionadas
- ✅ Helper atualizado
- ✅ Views atualizadas

## Interface para Secretaria

A secretaria verá pedidos com estado `'remarcacao_solicitada'` e poderá:

1. **Aprovar:** Cancela o pedido e devolve os dias
2. **Rejeitar:** Mantém o pedido original e informa o motivo ao professor

### Implementação Sugerida na View da Secretaria

```php
<?php if ($pedido['estado'] === 'remarcacao_solicitada'): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>Remarcação Solicitada</strong>
    <p>O professor solicitou remarcação deste pedido.</p>
    
    <button class="btn btn-success" onclick="aprovarRemarcacao(<?= $pedido['id'] ?>)">
        <i class="fas fa-check"></i> Aprovar Remarcação
    </button>
    
    <button class="btn btn-danger" onclick="rejeitarRemarcacao(<?= $pedido['id'] ?>)">
        <i class="fas fa-times"></i> Rejeitar Remarcação
    </button>
</div>
<?php endif; ?>
```

### JavaScript para Secretaria

```javascript
function aprovarRemarcacao(pedidoId) {
    Swal.fire({
        title: 'Aprovar Remarcação?',
        text: 'O pedido será cancelado e os dias devolvidos ao professor.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, aprovar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'ferias/aprovar-remarcacao/' + pedidoId,
                type: 'POST',
                success: function(response) {
                    Swal.fire('Aprovado!', response.message, 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Erro', xhr.responseJSON?.message || 'Erro ao aprovar', 'error');
                }
            });
        }
    });
}

function rejeitarRemarcacao(pedidoId) {
    Swal.fire({
        title: 'Rejeitar Remarcação?',
        input: 'textarea',
        inputLabel: 'Motivo da rejeição',
        inputPlaceholder: 'Digite o motivo...',
        inputAttributes: {
            'aria-label': 'Digite o motivo da rejeição'
        },
        showCancelButton: true,
        confirmButtonText: 'Rejeitar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value) {
                return 'Por favor, indique o motivo da rejeição'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'ferias/rejeitar-remarcacao/' + pedidoId,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ motivo: result.value }),
                success: function(response) {
                    Swal.fire('Rejeitado!', response.message, 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Erro', xhr.responseJSON?.message || 'Erro ao rejeitar', 'error');
                }
            });
        }
    });
}
```

## Emails Automáticos

### 1. Professor solicita remarcação
- **Destinatários:** Secretaria (quem aprovou) + Professor
- **Mensagem:** Remarcação solicitada, aguarda aprovação

### 2. Secretaria aprova
- **Destinatário:** Professor
- **Mensagem:** Remarcação aprovada, pedido cancelado, dias devolvidos

### 3. Secretaria rejeita
- **Destinatário:** Professor
- **Mensagem:** Remarcação rejeitada com motivo, férias permanecem como estão

## Validações

- ✅ Apenas pedidos `'aprovado'`, `'aguarda_assinatura'` ou `'concluido'` podem solicitar remarcação
- ✅ Apenas estado `'remarcacao_solicitada'` pode ser aprovado/rejeitado
- ✅ Secretaria (níveis 3+) pode aprovar/rejeitar
- ✅ Professor recebe feedback em todas as etapas
- ✅ Logs completos de todas as ações

## Benefícios

1. **Controle:** Secretaria tem controle total sobre remarcações
2. **Auditoria:** Todos os pedidos ficam registados com motivos
3. **Comunicação:** Emails automáticos mantêm todos informados
4. **Flexibilidade:** Secretaria pode rejeitar pedidos inadequados
5. **Transparência:** Professor é sempre informado da decisão

## Próximos Passos

1. ✅ Executar migração SQL
2. ⏳ Testar fluxo completo:
   - Professor solicita remarcação
   - Verificar estado muda para `'remarcacao_solicitada'`
   - Secretaria aprova/rejeita
   - Verificar emails são enviados
   - Verificar logs são criados
3. ⏳ Implementar interface na página da secretaria (se ainda não existir)

## Troubleshooting

### Erro: "Tipo de estado inválido"
**Solução:** Execute a migração SQL para adicionar o novo estado ao ENUM

### Pedido não fica pendente
**Solução:** Verifique se o código do controller foi atualizado corretamente (linha ~408)

### Email não é enviado
**Solução:** Verifique configuração SMTP e função `enviar_email_ferias()`

---

**Versão:** 2.0
**Compatibilidade:** Sistema de Férias v1.0+
