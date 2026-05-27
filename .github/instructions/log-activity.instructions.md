---
applyTo: "app/Controllers/**/*.php"
---

## Regras de log_activity nesta aplicação

Sempre que editares um controller em `app/Controllers/`, aplica estas regras:

### Assinatura obrigatória

```php
log_activity(
    string $module,    // módulo em minúsculas: 'tickets', 'ferias', 'permutas', ...
    string $action,    // ação: 'create', 'update', 'delete', 'approve', 'reject', ...
    mixed  $recordId,  // ID do registo (int|null) — NÃO é o user_id
    string $description,
    ?array $oldValues, // dados antes — DEVE ser array ou null, nunca int
    ?array $newValues, // dados depois — DEVE ser array ou null
    string $severity   // 'info' | 'warning' | 'error' | 'critical'  (padrão: 'info')
): bool
```

### Erros comuns a evitar

| Errado | Correto |
|--------|---------|
| `log_activity(session()->get('user_id'), 'Tickets', ...)` | `log_activity('tickets', ...)` — user_id é lido da sessão automaticamente |
| `log_activity('tickets', 'update', $desc, $ticketId, ...)` | `log_activity('tickets', 'update', $ticketId, $desc, ...)` — $recordId antes de $description |
| `log_activity(..., $ticketId, ['old'=>...], ['new'=>...])` | `log_activity(..., $recordId, $desc, ['old'=>...], ['new'=>...])` — nunca passar int como $oldValues |

### Wrappers disponíveis (usar em vez de log_activity quando existem)

```php
log_ticket($action, $ticketId, $description, $oldValues, $newValues)
log_permuta($action, $permutaId, $description, $oldValues, $newValues)
log_credito($action, $creditoId, $description, $oldValues, $newValues)
log_horario($action, $horarioId, $description, $oldValues, $newValues)
log_error_activity($module, $action, $description, $recordId)
log_critical_activity($module, $action, $description, $recordId)
```

### Quando adicionar logs

- ✅ Criar, atualizar, eliminar um registo (sempre)
- ✅ Aprovar, rejeitar, mudar estado/prioridade/status
- ✅ Falhas de validação e registo não encontrado (`severity: 'warning'`)
- ✅ Erros em bloco `catch` (`severity: 'error'`)
- ✅ Acessos não autorizados (`severity: 'critical'`)
- ❌ Listagens simples não-sensíveis (`index()`, `show()` básicos)
