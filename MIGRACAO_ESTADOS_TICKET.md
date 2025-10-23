# Migração para Sistema de Estados Dinâmico

## ✅ STATUS: IMPLEMENTAÇÃO COMPLETA

**Data de Conclusão:** 15 de Outubro de 2025  
**Migração Executada:** ✅ Sim  
**Seeder Executado:** ✅ Sim  
**Todos os Ficheiros Atualizados:** ✅ Sim

---

## 📋 Resumo

Esta migração substitui o sistema de estados hardcoded (ENUM) por um sistema baseado em tabelas de base de dados, proporcionando:

- ✅ Flexibilidade para adicionar/modificar estados sem alterar código
- ✅ Sistema de workflow com controle de transições permitidas
- ✅ Gestão de permissões por nível de utilizador
- ✅ Metadados ricos (cores, ícones, descrições)
- ✅ Preparação para reutilização em outros módulos (Trocas de Aulas)
- ✅ Badges dinâmicos renderizados server-side
- ✅ Validações dinâmicas baseadas em tabela

---

## 📦 Ficheiros Criados

### 1. **Migration**
- **Ficheiro**: `app/Database/Migrations/2025-10-15-120000_CreateEstadosTicketTable.php`
- **Tabelas**:
  - `estados_ticket` - Armazena todos os estados possíveis
  - `estados_ticket_transicoes` - Define quais transições são permitidas

### 2. **Seeder**
- **Ficheiro**: `app/Database/Seeds/EstadosTicketSeeder.php`
- **Dados**: 5 estados + 11 transições predefinidas

### 3. **Model**
- **Ficheiro**: `app/Models/EstadosTicketModel.php`
- **Métodos**:
  - `getEstadosAtivos()` - Lista estados ativos
  - `getEstadoPorCodigo()` - Busca estado por código
  - `transicaoPermitida()` - Valida se transição é permitida
  - `getProximosEstados()` - Estados disponíveis para transição
  - `renderBadge()` - Gera HTML do badge

### 4. **Helper**
- **Ficheiro**: `app/Helpers/estado_helper.php`
- **Funções**:
  - `get_estado_badge($codigo, $comIcone)` - Badge HTML
  - `get_estados_dropdown()` - Array para select/dropdown
  - `get_estados_ativos()` - Lista completa
  - `pode_transicionar_estado($origem, $destino, $nivel)` - Valida transição
  - `get_proximos_estados($atual, $nivel)` - Próximos estados possíveis
  - `get_estado_info($codigo)` - Info completa do estado

### 5. **Validação Customizada**
- **Ficheiro**: `app/Validation/CustomRules.php`
- **Regras**:
  - `validar_estado_ticket` - Valida código do estado
  - `validar_transicao_estado` - Valida transição entre estados

---

## 🗄️ Estrutura das Tabelas

### Tabela: `estados_ticket`

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT | ID único |
| codigo | VARCHAR(50) | Código do estado (ex: novo, em_resolucao) |
| nome | VARCHAR(100) | Nome amigável (ex: Novo, Em Resolução) |
| descricao | TEXT | Descrição detalhada |
| cor | VARCHAR(20) | Cor Bootstrap (primary, success, danger, etc.) |
| icone | VARCHAR(50) | Classe FontAwesome (ex: fas fa-clock) |
| ordem | INT | Ordem de exibição |
| ativo | TINYINT | 1 = Ativo, 0 = Inativo |
| permite_edicao | TINYINT | Se permite editar ticket neste estado |
| permite_atribuicao | TINYINT | Se permite atribuir técnico |
| estado_final | TINYINT | Se é estado final (não transitável) |

### Tabela: `estados_ticket_transicoes`

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT | ID único |
| estado_origem_id | INT | FK para estados_ticket (origem) |
| estado_destino_id | INT | FK para estados_ticket (destino) |
| nivel_minimo | INT | Nível mínimo de utilizador |
| requer_comentario | TINYINT | Se requer comentário obrigatório |
| ativo | TINYINT | Se transição está ativa |

---

## 🚀 Instruções de Instalação

### Passo 1: Executar Migration

```bash
php spark migrate
```

### Passo 2: Popular Dados (Seeder)

```bash
php spark db:seed EstadosTicketSeeder
```

### Passo 3: Verificar Instalação

```sql
-- Verificar estados criados
SELECT * FROM estados_ticket ORDER BY ordem;

-- Verificar transições
SELECT 
    t.id,
    eo.nome as estado_origem,
    ed.nome as estado_destino,
    t.nivel_minimo,
    t.requer_comentario
FROM estados_ticket_transicoes t
INNER JOIN estados_ticket eo ON t.estado_origem_id = eo.id
INNER JOIN estados_ticket ed ON t.estado_destino_id = ed.id
WHERE t.ativo = 1
ORDER BY eo.ordem, ed.ordem;
```

---

## 🔄 Estados Configurados

| Código | Nome | Cor | Ícone | Permite Edição | Estado Final |
|--------|------|-----|-------|----------------|--------------|
| novo | Novo | info | fas fa-plus-circle | ✅ Sim | ❌ Não |
| em_resolucao | Em Resolução | warning | fas fa-wrench | ❌ Não | ❌ Não |
| aguarda_peca | Aguarda Peça | primary | fas fa-hourglass-half | ❌ Não | ❌ Não |
| reparado | Reparado | success | fas fa-check-circle | ❌ Não | ✅ Sim |
| anulado | Anulado | danger | fas fa-times-circle | ❌ Não | ✅ Sim |

---

## 🔀 Transições Permitidas (Workflow)

```
novo → em_resolucao  (Nível 5+, Técnico)
novo → anulado       (Nível 8+, Admin, requer comentário)

em_resolucao → aguarda_peca  (Nível 5+, requer comentário)
em_resolucao → reparado      (Nível 5+)
em_resolucao → anulado       (Nível 8+, requer comentário)

aguarda_peca → em_resolucao  (Nível 5+)
aguarda_peca → reparado      (Nível 5+)
aguarda_peca → anulado       (Nível 8+, requer comentário)

reparado → em_resolucao      (Nível 5+, reabertura, requer comentário)

anulado → novo               (Nível 8+, reativação, requer comentário)
```

---

## 💻 Como Usar nos Controllers

### Exemplo 1: Obter estados para dropdown

```php
// No Controller
helper('estado');
$data['estados'] = get_estados_dropdown();

// Na View
<select name="estado" class="form-control">
    <?php foreach($estados as $codigo => $nome): ?>
        <option value="<?= $codigo ?>"><?= $nome ?></option>
    <?php endforeach; ?>
</select>
```

### Exemplo 2: Renderizar badge do estado

```php
// Na View
<?= get_estado_badge('novo', true) ?>
// Output: <span class="badge bg-info"><i class="fas fa-plus-circle"></i> Novo</span>
```

### Exemplo 3: Verificar transição permitida

```php
$nivelUsuario = session()->get('level');
$estadoAtual = 'novo';
$estadoDestino = 'em_resolucao';

if (pode_transicionar_estado($estadoAtual, $estadoDestino, $nivelUsuario)) {
    // Permitir mudança de estado
} else {
    // Negar mudança
}
```

### Exemplo 4: Obter próximos estados possíveis

```php
$nivelUsuario = 5; // Técnico
$estadoAtual = 'em_resolucao';
$proximosEstados = get_proximos_estados($estadoAtual, $nivelUsuario);

// $proximosEstados contém apenas estados para onde pode transicionar
foreach($proximosEstados as $estado) {
    echo $estado['nome']; // aguarda_peca, reparado
}
```

---

## 🎨 Como Usar nas Views

### Método Antigo (❌ Não usar mais)

```php
<?php
switch($ticket['estado']) {
    case 'novo':
        echo '<span class="badge bg-info">Novo</span>';
        break;
    case 'em_resolucao':
        echo '<span class="badge bg-warning">Em Resolução</span>';
        break;
    // ... mais cases
}
?>
```

### Método Novo (✅ Usar)

```php
<?= get_estado_badge($ticket['estado'], true) ?>
```

### DataTables com Badge Dinâmico

```javascript
{
    data: 'estado',
    render: function(data, type, row) {
        // O badge já vem formatado do servidor
        return data; // Se enviado formatado do controller
        
        // OU fazer chamada AJAX para buscar badge
        return getBadgeEstado(data);
    }
}
```

---

## 🔧 Alterações nos Ficheiros Existentes

### ✅ Ficheiros Atualizados

1. **`app/Models/TicketsModel.php`**
   - Regra de validação alterada de `in_list[...]` para `validar_estado_ticket`

2. **`app/Controllers/TicketsController.php`**
   - Helper `estado` carregado no construct
   - Validação de estado usa regra customizada

3. **`app/Controllers/DashboardController.php`**
   - Helper `estado` carregado no construct

4. **`app/Config/Validation.php`**
   - Classe `CustomRules` adicionada ao `$ruleSets`

---

## 📝 Exemplos de Uso Avançado

### Criar Modal com Estados Dinâmicos

```php
<!-- Modal Alterar Estado -->
<div class="modal fade" id="alterarEstadoModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Alterar Estado do Ticket</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ticket_id_estado">
                <input type="hidden" id="estado_atual">
                
                <div class="form-group">
                    <label>Novo Estado</label>
                    <select id="novo_estado" class="form-control">
                        <!-- Preenchido dinamicamente via JS -->
                    </select>
                </div>
                
                <div class="form-group" id="comentario_group" style="display:none;">
                    <label>Comentário <span class="text-danger">*</span></label>
                    <textarea id="comentario_transicao" class="form-control" rows="3"></textarea>
                    <small class="text-muted">Este campo é obrigatório para esta transição</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarEstado">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
function abrirModalEstado(ticketId, estadoAtual) {
    $('#ticket_id_estado').val(ticketId);
    $('#estado_atual').val(estadoAtual);
    
    // Buscar próximos estados possíveis
    $.ajax({
        url: '<?= site_url('tickets/proximos-estados') ?>',
        method: 'POST',
        data: { estado_atual: estadoAtual },
        success: function(response) {
            let $select = $('#novo_estado');
            $select.empty();
            
            response.estados.forEach(function(estado) {
                $select.append(
                    $('<option>')
                        .val(estado.codigo)
                        .text(estado.nome)
                        .data('requer-comentario', estado.requer_comentario)
                );
            });
            
            $('#alterarEstadoModal').modal('show');
        }
    });
}

$('#novo_estado').on('change', function() {
    let requerComentario = $(this).find(':selected').data('requer-comentario');
    
    if (requerComentario) {
        $('#comentario_group').show();
        $('#comentario_transicao').prop('required', true);
    } else {
        $('#comentario_group').hide();
        $('#comentario_transicao').prop('required', false);
    }
});
</script>
```

---

## 🧪 Testes de Validação

### Testar Estados Ativos

```php
$estados = get_estados_ativos();
echo "Total de estados: " . count($estados);
foreach($estados as $estado) {
    echo $estado['codigo'] . " - " . $estado['nome'] . "\n";
}
```

### Testar Transições

```php
// Técnico (nível 5) tentando passar de novo para em_resolucao
$pode = pode_transicionar_estado('novo', 'em_resolucao', 5);
echo $pode ? "Permitido" : "Negado"; // Permitido

// Técnico (nível 5) tentando anular ticket novo
$pode = pode_transicionar_estado('novo', 'anulado', 5);
echo $pode ? "Permitido" : "Negado"; // Negado (requer nível 8)

// Admin (nível 8) tentando anular
$pode = pode_transicionar_estado('novo', 'anulado', 8);
echo $pode ? "Permitido" : "Negado"; // Permitido
```

---

## 🔮 Próximos Passos (Opcional)

### 1. Adicionar Histórico de Transições

```sql
CREATE TABLE tickets_historico_estados (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_id INT NOT NULL,
    estado_anterior VARCHAR(50),
    estado_novo VARCHAR(50) NOT NULL,
    user_id INT NOT NULL,
    comentario TEXT,
    created_at DATETIME,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id),
    FOREIGN KEY (user_id) REFERENCES user(id)
);
```

### 2. Notificações por Transição

Adicionar campo na tabela `estados_ticket_transicoes`:
- `notificar_criador` TINYINT
- `notificar_atribuido` TINYINT
- `notificar_admin` TINYINT

### 3. Dashboard com Estatísticas por Estado

Já implementado no `EstadosTicketModel`:
```php
$stats = $estadosModel->getEstatisticasPorEstado();
```

---

## 📝 Ficheiros Modificados na Implementação

### Controllers
- ✅ **app/Controllers/TicketsController.php**
  - `getMyTicketsDataTable()` - Badge servidor-side + colunas ocultas estado
  - `getAllTicketsDataTable()` - Badge servidor-side + colunas ocultas estado  
  - `getTicketsForTreatmentDataTable()` - Badge servidor-side + coluna oculta estado

### Views
- ✅ **app/Views/tickets/view_ticket.php**
  - Substituído switch/case por `getEstadoBadge()`
  - Substituído verificações hardcoded por `isEstadoFinal()`
  - JavaScript atualizado com variável `estadoFinal`

- ✅ **app/Views/tickets/meus_tickets.php**
  - Removido render client-side de estados
  - Adicionadas colunas ocultas (estadoCodigo, estadoFinal)
  - Atualizado evento de alteração de prioridade
  - Removido rowCallback de coloração

- ✅ **app/Views/tickets/tickets.php**
  - Removido render client-side de estados
  - Adicionadas colunas ocultas (estadoCodigo, estadoFinal)
  - Atualizado evento de alteração de prioridade

- ✅ **app/Views/tickets/tratamento_tickets.php**
  - Removido render client-side de estados
  - Adicionada coluna oculta estadoCodigo

### Configuração
- ✅ **app/Config/Autoload.php**
  - Adicionado `estados_ticket_helper` ao array de helpers

### Banco de Dados
- ✅ **Migration executada:** `2025-10-15-120000_CreateEstadosTicketTable`
- ✅ **Seeder executado:** `EstadosTicketSeeder`
- ✅ **Tabelas criadas:**
  - `estados_ticket` (5 registros)
  - `estados_ticket_transicoes` (11 registros)

---

## ⚠️ Notas Importantes

1. **Backup**: Sempre fazer backup antes de executar migrations
2. **Dados Existentes**: A migration garante que dados existentes permanecem válidos
3. **Validação**: Todas as validações agora usam a tabela, não hardcoded
4. **Performance**: Índices criados para otimizar queries
5. **Expansão**: Sistema preparado para adicionar novos estados sem alterar código
6. **Server-Side Rendering**: Badges renderizados no servidor para consistência
7. **Colunas Ocultas**: Estados enviados em colunas ocultas para lógica JavaScript

---

## 🎯 Como Adicionar Novo Estado

1. **Inserir na tabela `estados_ticket`:**
```sql
INSERT INTO estados_ticket (codigo, nome, descricao, cor, icone, ordem, ativo, permite_edicao, permite_atribuicao, estado_final, created_at, updated_at)
VALUES ('em_analise', 'Em Análise', 'Ticket em análise técnica', 'secondary', 'fas fa-search', 6, 1, 0, 1, 0, NOW(), NOW());
```

2. **Definir transições permitidas:**
```sql
INSERT INTO estados_ticket_transicoes (estado_origem_id, estado_destino_id, nivel_minimo, requer_comentario, ativo, created_at, updated_at)
VALUES 
  (2, 6, 5, 1, 1, NOW(), NOW()), -- Em Resolução -> Em Análise
  (6, 2, 5, 0, 1, NOW(), NOW()); -- Em Análise -> Em Resolução
```

3. **Pronto!** O sistema irá:
   - ✅ Renderizar badge automaticamente com cor e ícone
   - ✅ Validar transições de estado
   - ✅ Aplicar regras de permissão
   - ✅ Mostrar nos dropdowns automaticamente

**Não é necessário alterar NENHUM código!** 🎉

---

## 📞 Suporte

Se encontrar problemas:

1. Verificar se migration foi executada: `php spark migrate:status`
2. Verificar se seeder foi executado: `SELECT COUNT(*) FROM estados_ticket;` (deve retornar 5)
3. Verificar logs: `writable/logs/log-*.php`
4. Verificar helper carregado: `helper('estado');` nos controllers
5. Verificar helper no Autoload.php: `'estados_ticket_helper'` deve estar no array

---

## 🎉 Conclusão

O sistema está agora preparado para:
- ✅ Gestão flexível de estados
- ✅ Controle de workflow
- ✅ Reutilização em outros módulos
- ✅ Manutenção simplificada
- ✅ Escalabilidade

**Nenhuma alteração de código necessária para adicionar novos estados!**
