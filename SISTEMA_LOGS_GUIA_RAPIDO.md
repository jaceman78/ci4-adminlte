# Sistema de Logs - Guia Completo de Uso

## 📋 Resumo Rápido

Sistema completo de auditoria implementado para rastrear todas as atividades em:
- ✅ **Permutas**
- ✅ **Créditos**  
- ✅ **Horários**
- ✅ **Tickets**

---

## 🚀 Quick Start

### 1. Instalar o Sistema

```bash
# Criar a tabela
mysql -u root -p gestaoescolar < CREATE_SYSTEM_LOGS_TABLE.sql
```

### 2. Usar no Controller

```php
// No construtor
public function __construct()
{
    helper('logs');
}

// Logar uma ação
log_permuta('create', $permutaId, "Permuta criada");
log_credito('use', $creditoId, "Crédito usado");
log_horario('import', null, "Horário importado");
log_ticket('create', $ticketId, "Ticket criado");
```

---

## 📚 Exemplos Práticos

### Permutas

```php
// Criar
log_permuta('create', $id, "Nova permuta solicitada", null, $dados);

// Aprovar
log_permuta('approve', $id, "Aprovada por {$admin}", 
    ['estado' => 'pendente'], 
    ['estado' => 'aprovada']
);

// Rejeitar
log_permuta('reject', $id, "Rejeitada - Motivo: conflito de horário",
    ['estado' => 'pendente'],
    ['estado' => 'rejeitada']
);
```

### Créditos

```php
// Criar automaticamente
log_credito('auto_create', $id, "Crédito gerado da visita '{$nome}'");

// Usar em permuta
log_credito('use', $id, "Usado na permuta #{$permutaId}",
    ['estado' => 'disponivel'],
    ['estado' => 'usado']
);

// Expirar
log_credito('expire', $id, "Crédito expirado automaticamente");
```

### Horários

```php
// Importação
log_horario('import', null, "Importadas {$total} aulas do CSV");

// Alteração de sala
log_horario('update', $aulaId, "Sala alterada",
    ['sala_id' => 'A101'],
    ['sala_id' => 'B205']
);
```

### Tickets

```php
// Novo ticket
log_ticket('create', $id, "Ticket: {$assunto}");

// Mudança de estado
log_ticket('status_change', $id, "Aberto → Em Progresso",
    ['estado' => 'aberto'],
    ['estado' => 'em_progresso']
);

// Atribuição
log_ticket('assign', $id, "Atribuído a {$tecnico}");
```

---

## 🔍 Consultar Logs

```php
// Por módulo
$logs = get_module_logs('permutas', 100);

// Por usuário
$logs = get_user_logs($userId, 50);

// Histórico de um registro
$logs = get_record_logs('permutas', $permutaId);
```

---

## 📊 Campos do Log

| Campo | Descrição | Exemplo |
|-------|-----------|---------|
| `module` | Módulo do sistema | permutas, creditos, horarios, tickets |
| `action` | Ação realizada | create, update, delete, approve, reject |
| `record_id` | ID do registro | 123, 456 |
| `description` | Descrição clara | "Permuta aprovada por Admin João" |
| `old_values` | Valores antes (JSON) | `{"estado": "pendente"}` |
| `new_values` | Valores depois (JSON) | `{"estado": "aprovada"}` |
| `severity` | Nível | info, warning, error, critical |

---

## 🎯 Ações Comuns

### Permutas
- `create` - Criar permuta
- `approve` - Aprovar
- `reject` - Rejeitar
- `cancel` - Cancelar
- `update` - Atualizar dados

### Créditos
- `create` - Criar manualmente
- `auto_create` - Gerado automaticamente
- `use` - Usar em permuta
- `expire` - Expirar
- `restore` - Restaurar crédito

### Horários
- `import` - Importar CSV
- `create` - Adicionar aula
- `update` - Alterar aula
- `delete` - Remover aula
- `bulk_update` - Alteração em massa

### Tickets
- `create` - Criar ticket
- `update` - Atualizar
- `status_change` - Mudar estado
- `assign` - Atribuir técnico
- `comment` - Adicionar comentário
- `close` - Fechar ticket

---

## 🧹 Manutenção

### Limpar Logs Antigos

```bash
# Via CLI
php spark logs:clean 90

# Via código
$logsModel->cleanOldLogs(90);
```

### Agendar Limpeza (Cron)

```cron
# Todo dia às 3h - manter últimos 90 dias
0 3 * * * cd /caminho/projeto && php spark logs:clean 90
```

---

## 🔐 Segurança

### Dados Sensíveis

```php
// ❌ NÃO LOGAR
- Senhas
- Tokens de autenticação
- Informações de cartões
- Dados médicos sensíveis

// ✅ LOGAR
- Ações do usuário
- Mudanças de estado
- Operações administrativas
- Erros e exceções
```

### Acesso Restrito

Apenas usuários `level >= 6` (administradores) podem visualizar logs completos.

---

## 📈 Performance

### Índices Criados

```sql
-- Para consultas rápidas
idx_user_id
idx_module
idx_action
idx_created_at
idx_module_action
idx_module_record
```

### Otimização

- Use `limit` nas consultas
- Limpe logs antigos regularmente
- Monitore o tamanho da tabela
- Use índices compostos para queries complexas

---

## ✅ Checklist de Implementação

### Permutas Controller
- [x] Log na criação
- [x] Log na aprovação
- [x] Log na rejeição
- [ ] Log no cancelamento
- [ ] Log na atualização

### Créditos
- [x] Log no uso
- [ ] Log na criação automática
- [ ] Log na expiração
- [ ] Log na restauração

### Horários
- [ ] Log na importação
- [ ] Log nas alterações
- [ ] Log nas exclusões

### Tickets
- [ ] Log na criação
- [ ] Log nas mudanças de estado
- [ ] Log nas atribuições
- [ ] Log nos comentários

---

## 🆘 Troubleshooting

### Logs não aparecem

1. Verificar se tabela existe
2. Verificar se helper está carregado
3. Verificar permissões de escrita
4. Verificar logs do PHP (`writable/logs/`)

### Tabela muito grande

```sql
-- Ver tamanho
SELECT 
    table_name AS `Table`,
    ROUND((data_length + index_length) / 1024 / 1024, 2) AS `Size (MB)`
FROM information_schema.tables
WHERE table_schema = 'gestaoescolar'
AND table_name = 'system_logs';

-- Limpar manualmente
DELETE FROM system_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

---

## 📞 Suporte

- **Documentação Completa:** `LOGS_IMPLEMENTATION_GUIDE.md`
- **Email:** suporte@escola.pt

---

**Versão:** 1.0  
**Data:** Novembro 2025
