# Sistema de Gestão de Equipamentos - Implementação Completa

## 📋 Resumo das Alterações

### 1. **Estrutura de Base de Dados**

#### Tabela `equipamentos`
- **Removida** coluna `sala_id` (migration executada)
- Equipamentos agora só têm dados intrínsecos (tipo, marca, modelo, etc.)
- Relacionamento com salas gerido exclusivamente por `equipamentos_sala`

#### Tabela `equipamentos_sala` (já existia)
- `equipamento_id` - FK para equipamentos
- `sala_id` - FK para salas
- `data_entrada` - timestamp de quando entrou na sala
- `data_saida` - NULL se ainda está na sala, preenchido quando sai
- `motivo_movimentacao` - razão da movimentação
- `user_id` - quem fez a movimentação
- `observacoes` - notas adicionais

### 2. **Controller - EquipamentosController.php**

#### Novos Métodos:

**`createWithSala()`**
- Cria equipamento E atribui sala numa transação
- Valida dados do equipamento e sala
- Regista na tabela `equipamentos_sala` se fornecida sala
- Permite criar equipamento sem sala (por_atribuir)

**`atribuirSala()`**
- Atribui sala a equipamento que não tem sala
- Valida se equipamento já tem sala
- Regista movimentação com motivo

**`editarSala()` / `moverEquipamento()`**
- Move equipamento de uma sala para outra
- Fecha o registo anterior (preenche `data_saida`)
- Cria novo registo com nova sala
- Mantém histórico completo

**`removerSala()`**
- Remove equipamento da sala atual
- Fecha o registo (preenche `data_saida`)
- Útil para reparação, abate, etc.

**`getHistorico($equipamentoId)`**
- Retorna histórico completo de movimentações
- Inclui sala, datas, motivo, usuário

**`getEquipamentoCompleto($id)`**
- Retorna equipamento + sala atual + escola
- Usado para preencher modals de edição

**`getDataTable()`** - Atualizado
- JOIN com `equipamentos_sala` para mostrar sala atual
- JOIN com `escolas` para mostrar escola
- Mostra "Sem atribuição" se não tem sala

### 3. **View - equipamentos_index.php**

#### Modal de Criar/Editar Equipamento
Reorganizado em 2 seções:

**Seção Localização:**
- Dropdown **Escola** (primeiro)
- Dropdown **Sala** (cascata, depende de escola)
- Campo **Motivo** (aparece quando seleciona sala)
- Permite deixar vazio = equipamento sem sala

**Seção Dados do Equipamento:**
- Tipo, Marca, Modelo, Número de Série
- Estado: ativo, fora_servico, por_atribuir, abate
- Data de Aquisição, Observações

#### Nova Modal "Gerir Localização"
Única modal para 3 operações:

**1. Atribuir Sala** (equipamento sem sala)
- Seleciona escola → sala
- Motivo obrigatório

**2. Mudar Sala** (equipamento já tem sala)
- Mostra sala atual
- Seleciona nova escola → nova sala
- Motivo obrigatório

**3. Remover Sala** (tirar de sala)
- Mostra sala atual
- Confirma remoção
- Motivo obrigatório

#### DataTable Atualizada
Colunas:
- ID
- **Escola** (da sala atual ou "Sem atribuição")
- **Sala** (código da sala ou "Sem atribuição")
- Tipo
- Marca/Modelo (combinado)
- Número de Série
- Estado (badge colorido)
- **Ações** (expandido)

#### Botões de Ação (dropdown)
- **Ver Detalhes** (ícone olho)
- **Editar Equipamento** (ícone editar)
- **Gerir Localização** (dropdown):
  - Se TEM sala: "Mudar Sala", "Remover Sala"
  - Se NÃO TEM sala: "Atribuir Sala"
  - "Histórico" (sempre disponível)
- **Eliminar** (ícone lixo)

### 4. **JavaScript - assets/js/equipamentos.js**

#### Funções Principais:

**`initializeDataTable()`**
- Configura DataTable com novas colunas
- Renderiza badges de estado
- Cria botões de ação dinâmicos

**`initializeCascadingDropdowns()`**
- Escola → Salas (modal criar/editar)
- Escola → Salas (modal gerir localização)

**`openCreateModal()`**
- Limpa formulário
- Prepara para novo equipamento

**`editEquipamento(id)`**
- Carrega dados completos
- Preenche escola e sala se existir

**`atribuirSalaEquipamento(id)`**
- Abre modal em modo "atribuir"
- Campos vazios, prontos para seleção

**`editarSalaEquipamento(id)`**
- Abre modal em modo "editar/mover"
- Mostra sala atual
- Permite escolher nova sala

**`removerSalaEquipamento(id)`**
- Abre modal em modo "remover"
- Mostra sala atual
- Pede confirmação e motivo

**`verHistorico(id)`**
- Carrega histórico via AJAX
- Mostra timeline de movimentações
- Usa modal de estatísticas

**`submitEquipamentoForm()`**
- POST para `createWithSala` (novo) ou `update` (editar)
- Inclui sala se selecionada
- Recarrega table após sucesso

**`submitGerirSalaForm()`**
- Identifica ação (atribuir/editar/remover)
- POST para endpoint correto
- Recarrega table após sucesso

### 5. **Model - EquipamentosSalaModel.php** (já existia)

Métodos disponíveis:
- `getSalaAtual($equipamentoId)` - Sala onde está agora
- `getHistoricoEquipamento($equipamentoId)` - Todas movimentações
- `getEquipamentosPorSala($salaId)` - Equipamentos numa sala
- `moverEquipamento($eqId, $novaSala, $motivo, $userId)` - Transferência
- `atribuirSala($eqId, $salaId, $userId)` - Primeira atribuição
- `removerDeSala($eqId, $motivo, $userId)` - Remoção
- `getEquipamentosSemSala()` - Equipamentos por atribuir

### 6. **Rotas Adicionadas** (Routes.php)

```php
$routes->post("createWithSala", "EquipamentosController::createWithSala");
$routes->get("getEquipamentoCompleto/(:num)", "EquipamentosController::getEquipamentoCompleto/$1");
$routes->post("atribuirSala", "EquipamentosController::atribuirSala");
$routes->post("editarSala", "EquipamentosController::editarSala");
$routes->post("removerSala", "EquipamentosController::removerSala");
$routes->get("getHistorico/(:num)", "EquipamentosController::getHistorico/$1");
```

## 🎯 Funcionalidades Implementadas

### ✅ Requisitos Cumpridos:

**1. Modal Inserir Equipamento:**
- ✅ 1.1. Escolher escola
- ✅ 1.2. Escolher sala (depende de escola)
- ✅ 1.3. Registar equipamento
- ✅ 1.4. Associar na tabela equipamentos_sala

**2. Equipamento sem sala:**
- ✅ Pode ser criado sem escola/sala
- ✅ Estado "por_atribuir"
- ✅ Aparece como "Sem atribuição" na listagem

**3. Gestão de Salas (DataTable):**
- ✅ 3.1. Editar sala atribuída (Mudar Sala)
- ✅ 3.2. Apagar sala atribuída (Remover Sala)
- ✅ 3.3. Atribuir sala (para equipamentos sem sala)
- ✅ **EXTRA**: Ver histórico de movimentações

### ➕ Funcionalidades Adicionais:

**Histórico Completo:**
- Todas as movimentações são registadas
- Quem moveu, quando, de onde para onde
- Motivo obrigatório em cada movimentação

**Validações:**
- Não permite atribuir sala se já tem
- Não permite remover se não tem
- Valida existência de escola e sala
- Motivo obrigatório em movimentações

**Logs de Atividade:**
- Todas as ações registadas em `logs_atividade`
- Rastreabilidade completa

**UI/UX:**
- Badges coloridos por estado
- Dropdown organizado por contexto
- Modals reutilizáveis
- Mensagens de feedback

**Relatórios:**
- Equipamentos por sala
- Equipamentos sem sala
- Histórico individual
- Estatísticas globais

## 📊 Fluxos de Trabalho

### Fluxo 1: Novo Equipamento COM Sala
1. Clicar "Novo Equipamento"
2. Preencher dados do equipamento
3. Selecionar escola
4. Selecionar sala (aparece após escola)
5. Preencher motivo (ex: "Novo equipamento")
6. Guardar
   - Cria em `equipamentos`
   - Cria em `equipamentos_sala` com data_entrada = now

### Fluxo 2: Novo Equipamento SEM Sala
1. Clicar "Novo Equipamento"
2. Preencher dados do equipamento
3. Deixar escola/sala vazios
4. Estado = "por_atribuir"
5. Guardar
   - Cria em `equipamentos`
   - NÃO cria em `equipamentos_sala`

### Fluxo 3: Atribuir Sala Posteriormente
1. Na listagem, identificar equipamento sem sala
2. Clicar dropdown "Gerir Localização" → "Atribuir Sala"
3. Selecionar escola → sala
4. Preencher motivo
5. Confirmar
   - Cria em `equipamentos_sala` com data_entrada = now

### Fluxo 4: Mover Entre Salas
1. Na listagem, equipamento já tem sala
2. Clicar dropdown "Gerir Localização" → "Mudar Sala"
3. Ver sala atual
4. Selecionar nova escola → nova sala
5. Preencher motivo (ex: "Transferência por necessidade")
6. Confirmar
   - Atualiza registo antigo: data_saida = now
   - Cria novo registo: data_entrada = now

### Fluxo 5: Remover de Sala
1. Na listagem, equipamento tem sala
2. Clicar dropdown "Gerir Localização" → "Remover Sala"
3. Ver sala atual
4. Preencher motivo (ex: "Equipamento para reparação")
5. Confirmar remoção
   - Atualiza registo: data_saida = now
   - Equipamento fica sem sala

### Fluxo 6: Ver Histórico
1. Clicar dropdown "Gerir Localização" → "Histórico"
2. Ver timeline de todas movimentações
   - Sala, datas entrada/saída, motivo, quem moveu

## 🔧 Manutenção e Extensões Futuras

### Possíveis Melhorias:

**Relatórios:**
- Equipamentos que nunca foram atribuídos
- Equipamentos mais movimentados
- Tempo médio em cada sala
- Taxa de rotatividade por tipo

**Notificações:**
- Email quando equipamento é movido
- Alerta se equipamento está há muito tempo numa sala
- Notificar responsável da escola

**Validações Avançadas:**
- Limite de equipamentos por sala
- Incompatibilidades de tipo vs sala
- Restrições de movimentação por permissão

**Export:**
- Histórico em PDF
- Relatório de movimentações por período
- Inventário completo por escola/sala

**Dashboard:**
- Mapa de calor de salas com mais equipamentos
- Gráficos de movimentações ao longo do tempo
- Equipamentos próximos de fim de vida útil

## 📝 Notas Importantes

1. **Transações:** Criação de equipamento com sala usa transação DB
2. **Foreign Keys:** Mantidas entre equipamentos_sala → equipamentos e salas
3. **Soft Deletes:** Equipamentos podem ter `deleted_at` (não perde histórico)
4. **Timestamps:** Todas as tabelas têm created_at e updated_at
5. **Logging:** Todas as ações críticas são registadas em logs_atividade

## 🐛 Troubleshooting

**Problema:** Dropdown de salas não carrega
- Verificar se rota `salas/getByEscola/:id` está acessível
- Verificar console do navegador para erros AJAX
- Verificar se `escolaId` está sendo passado corretamente

**Problema:** Erro ao criar equipamento com sala
- Verificar se tabela `equipamentos_sala` existe
- Verificar foreign keys
- Verificar logs em `writable/logs/`

**Problema:** Histórico não aparece
- Verificar se registos em `equipamentos_sala` têm `created_at`
- Verificar se JOIN com `salas` e `user` está correto

## ✨ Conclusão

Sistema completamente funcional com:
- ✅ Gestão completa de equipamentos
- ✅ Relacionamento N:N com histórico (equipamentos ↔ salas)
- ✅ Rastreabilidade total de movimentações
- ✅ Interface intuitiva com modals contextuais
- ✅ Validações robustas
- ✅ Logs de atividade
- ✅ Código limpo e bem estruturado
- ✅ JavaScript modular em arquivo separado
