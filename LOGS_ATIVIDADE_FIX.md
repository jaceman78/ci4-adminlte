# Correção do Sistema de Logs - logs_atividade

## Resumo
Este documento descreve as alterações feitas para corrigir o sistema de logs do módulo de permutas, garantindo que ele utilize a tabela existente `logs_atividade` em vez de criar uma nova tabela `system_logs`.

## Problema Identificado
O sistema tinha dois mecanismos de logging:
1. **Existente**: Tabela `logs_atividade` com modelo `ActivityLogModel` (campos em português)
2. **Novo**: Tabela `system_logs` com modelo `LogsModel` (campos em inglês)

O `logs_helper.php` (usado pelo `PermutasController`) estava configurado para usar o `LogsModel`, que apontava para `system_logs`. Isso causava:
- Tentativa de criar uma nova tabela
- Erro ao salvar permutas (logs não eram registrados corretamente)
- Duplicação desnecessária de infraestrutura de logging

## Solução Implementada

### 1. Atualização do LogsModel (`app/Models/LogsModel.php`)

#### Mudanças na Configuração da Tabela
```php
// ANTES:
protected $table = 'system_logs';

// DEPOIS:
protected $table = 'logs_atividade';
```

#### Mudanças nos Campos Permitidos
```php
// ANTES:
protected $allowedFields = [
    'user_id', 'user_nif', 'user_name',
    'module', 'action', 'record_id', 'description',
    'old_values', 'new_values',
    'ip_address', 'user_agent', 'severity',
    'created_at'
];

// DEPOIS:
protected $allowedFields = [
    'user_id', 'modulo', 'acao', 'registro_id', 'descricao',
    'dados_anteriores', 'dados_novos',
    'ip_address', 'user_agent', 'detalhes',
    'criado_em'
];
```

#### Mudanças no Método logActivity()
O método foi atualizado para mapear campos do formato novo (inglês) para o formato existente (português):

```php
public function logActivity(array $data)
{
    // Mapeia campos automaticamente:
    // 'module' → 'modulo'
    // 'action' → 'acao'
    // 'record_id' → 'registro_id'
    // 'description' → 'descricao'
    // 'old_values' → 'dados_anteriores'
    // 'new_values' → 'dados_novos'
    // 'created_at' → 'criado_em'
    
    // Campos extras (user_nif, user_name, severity) são armazenados em 'detalhes' como JSON
}
```

### 2. Atualização do logs_helper.php (`app/Helpers/logs_helper.php`)

#### Validação de user_id
Adicionada validação para prevenir erros de constraint de chave estrangeira:

```php
function log_activity(...) {
    // Extrai user_id da sessão (suporta 'ID' ou 'id')
    $userId = $userData['ID'] ?? $userData['id'] ?? null;
    
    // Valida se user_id existe
    if (!$userId) {
        log_message('warning', "Tentativa de log sem user_id");
        return false;
    }
    
    // Verifica se o usuário existe no banco
    $userExists = $userModel->find($userId);
    if (!$userExists) {
        log_message('warning', "user_id inexistente: {$userId}");
        return false;
    }
    
    // Prossegue com o log...
}
```

## Mapeamento de Campos

| Campo Novo (Inglês) | Campo Existente (Português) | Localização |
|---------------------|----------------------------|-------------|
| `module` | `modulo` | Campo direto |
| `action` | `acao` | Campo direto |
| `record_id` | `registro_id` | Campo direto |
| `description` | `descricao` | Campo direto |
| `old_values` | `dados_anteriores` | Campo direto (JSON) |
| `new_values` | `dados_novos` | Campo direto (JSON) |
| `created_at` | `criado_em` | Campo direto |
| `user_id` | `user_id` | Campo direto |
| `ip_address` | `ip_address` | Campo direto |
| `user_agent` | `user_agent` | Campo direto |
| `user_nif` | N/A | Armazenado em `detalhes` (JSON) |
| `user_name` | N/A | Armazenado em `detalhes` (JSON) |
| `severity` | N/A | Armazenado em `detalhes` (JSON) |

## Constraint de Chave Estrangeira

A tabela `logs_atividade` possui a seguinte constraint:
```sql
FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE
```

**Implicações:**
- `user_id` deve sempre referenciar um usuário válido
- Se `user_id` for NULL ou inválido, o INSERT falhará
- A validação implementada previne esses erros

## Funções Helper Disponíveis

### 1. log_activity()
Função genérica para registrar qualquer atividade:
```php
log_activity(
    string $module,      // ex: 'permutas', 'creditos'
    string $action,      // ex: 'create', 'update', 'delete'
    $recordId,          // ID do registro afetado
    string $description, // Descrição da ação
    ?array $oldValues,   // Valores anteriores (opcional)
    ?array $newValues,   // Valores novos (opcional)
    string $severity     // 'info', 'warning', 'error', 'critical'
): bool
```

### 2. log_permuta()
Função específica para logs de permutas:
```php
log_permuta(
    string $action,      // 'create', 'approve', 'reject', etc.
    $permutaId,         // ID da permuta
    string $description, // Descrição
    ?array $oldValues,   // Valores anteriores (opcional)
    ?array $newValues    // Valores novos (opcional)
): bool
```

### 3. log_credito()
Função específica para logs de créditos:
```php
log_credito(
    string $action,      // 'create', 'use', 'cancel'
    $creditoId,         // ID do crédito
    string $description, // Descrição
    ?array $oldValues,   // Valores anteriores (opcional)
    ?array $newValues    // Valores novos (opcional)
): bool
```

## Exemplo de Uso no PermutasController

```php
// Ao criar uma permuta
log_permuta(
    'create',
    $permutaId,
    "Permuta criada - Aula ID: {$aulaId}",
    null,
    $permutaData
);

// Ao aprovar uma permuta
log_permuta(
    'approve',
    $permutaId,
    "Permuta aprovada por {$userName}",
    ['estado' => 'pendente'],
    ['estado' => 'aprovada']
);

// Ao usar um crédito
log_credito(
    'use',
    $creditoId,
    "Crédito usado na permuta #{$permutaId}",
    ['estado' => 'disponivel'],
    ['estado' => 'usado']
);
```

## Estrutura da Tabela logs_atividade

```sql
CREATE TABLE logs_atividade (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    acao VARCHAR(100) NOT NULL,
    registro_id VARCHAR(50),
    descricao VARCHAR(500) NOT NULL,
    dados_anteriores TEXT,
    dados_novos TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    detalhes TEXT,
    criado_em DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE
);
```

## Testes de Validação

Foram realizados os seguintes testes:
1. ✓ LogsModel aponta para a tabela `logs_atividade`
2. ✓ Todos os campos estão mapeados corretamente
3. ✓ Funções helper (`log_permuta`, `log_credito`) existem e funcionam
4. ✓ Validação de `user_id` está implementada
5. ✓ Sintaxe PHP está correta em todos os arquivos

## Compatibilidade

### Compatibilidade com Código Existente
- ✓ O `ActivityLogModel` continua funcionando normalmente
- ✓ O `log_helper.php` (antigo) continua funcionando
- ✓ O novo sistema (`logs_helper.php`) agora usa a mesma tabela

### Migração de Dados
Não é necessária migração de dados, pois:
- Estamos usando a tabela existente `logs_atividade`
- Não há dados em `system_logs` para migrar

## Arquivos Modificados

1. `app/Models/LogsModel.php`
   - Alterada referência de tabela
   - Atualizados campos permitidos
   - Atualizado método `logActivity()` com mapeamento de campos
   - Atualizados métodos de consulta

2. `app/Helpers/logs_helper.php`
   - Adicionada validação de `user_id`
   - Adicionada verificação de existência do usuário
   - Melhorada extração de `user_id` da sessão

## Próximos Passos

1. Verificar funcionamento em ambiente de desenvolvimento
2. Testar criação de permutas e verificar se os logs são salvos corretamente
3. Monitorar logs do sistema para qualquer erro relacionado
4. Considerar remoção do arquivo `CREATE_SYSTEM_LOGS_TABLE.sql` (já não é necessário)

## Observações Importantes

- A tabela `logs_atividade` já existe no banco de dados `sistema_gestao`
- NÃO será criada uma nova tabela
- Todos os logs de permutas serão salvos na tabela existente
- A constraint de FK garante integridade referencial dos logs

## Suporte

Para problemas ou dúvidas sobre o sistema de logs:
1. Verificar logs do sistema em `writable/logs/`
2. Verificar se o `user_id` está presente na sessão
3. Verificar se o usuário existe na tabela `user`
4. Verificar as permissões da tabela `logs_atividade`
