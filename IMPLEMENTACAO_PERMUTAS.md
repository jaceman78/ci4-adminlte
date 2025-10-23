# Sistema de Permutas - Implementação Completa

## 📋 Resumo da Implementação

Sistema completo de gestão de permutas de aulas para professores, incluindo:
- Pedido de permutas (individuais ou em grupo)
- Sistema de aprovação/rejeição
- Notificações por email
- Visualização de horários
- Dashboard integrado

---

## 🗄️ Base de Dados

### Tabela `permutas`
**Migration**: `app/Database/Migrations/2025-10-21-100000_CreatePermutasTable.php`

**Campos**:
- `id` - Chave primária
- `aula_original_id` - FK para `horario_aulas`
- `data_aula_original` - Data da aula a permutar (DATE)
- `data_aula_permutada` - Data de reposição (DATE)
- `professor_autor_nif` - FK para `user.NIF` (quem pede)
- `professor_substituto_nif` - FK para `user.NIF` (quem substitui, pode ser o mesmo)
- `sala_permutada_id` - Código da sala (VARCHAR, opcional)
- `grupo_permuta` - Identificador para agrupar várias permutas do mesmo pedido (VARCHAR)
- `estado` - ENUM: `pendente`, `aprovada`, `rejeitada`, `cancelada`
- `observacoes` - Justificação/motivo (TEXT)
- `motivo_rejeicao` - Motivo de rejeição, se aplicável (TEXT)
- `aprovada_por_user_id` - FK para `user.id`
- `data_aprovacao` - DATETIME
- `created_at`, `updated_at` - Timestamps

**Índices**:
- `aula_original_id`
- `professor_autor_nif`
- `professor_substituto_nif`
- `estado`
- `grupo_permuta`

**Foreign Keys**:
- `aula_original_id` → `horario_aulas.id_aula`
- `professor_autor_nif` → `user.NIF`
- `professor_substituto_nif` → `user.NIF`
- `aprovada_por_user_id` → `user.id`

---

## 🔧 Backend

### 1. Model: `PermutaModel`
**Localização**: `app/Models/PermutaModel.php`

**Métodos Principais**:
- `getPermutasProfessor($professorNif, $estado = null)` - Lista permutas de um professor
- `getPermutasPorGrupo($grupoId)` - Busca permutas de um grupo
- `gerarGrupoPermuta()` - Gera ID único para grupo
- `aprovarPermuta($permutaId, $userId)` - Aprova permuta
- `rejeitarPermuta($permutaId, $userId, $motivo)` - Rejeita permuta
- `cancelarPermuta($permutaId)` - Cancela permuta (apenas autor)
- `getEstatisticasProfessor($professorNif)` - Retorna contagens (pendentes, aprovadas, rejeitadas, como substituto)
- `verificarConflitoPermuta($professorNif, $dataPermuta, $horaInicio, $horaFim)` - Verifica conflitos de horário
- `getDetalhesPermuta($permutaId)` - Detalhes completos com JOINs

**Validações**:
- Campos obrigatórios: aula_original_id, datas, NIFs, estado
- Datas devem ser válidas
- Estado deve estar em lista permitida

---

### 2. Controller: `PermutasController`
**Localização**: `app/Controllers/PermutasController.php`

**Métodos Implementados**:

#### Visualização
- `index()` - Meu Horário (já existente, mantido)
- `minhasPermutas()` - Listagem de permutas do professor
- `verPermuta($permutaId)` - Detalhes de uma permuta

#### Gestão de Permutas
- `pedirPermuta($idAula)` - Formulário para pedir permuta
  - Busca professores da mesma turma
  - Lista outras aulas do mesmo dia (para agrupamento)
  - Carrega salas disponíveis

- `salvarPermuta()` - Processa e salva permuta
  - Valida dados
  - Verifica propriedade da aula
  - Cria grupo se houver aulas adicionais
  - Usa transação para múltiplas inserções
  - Envia emails

- `cancelarPermuta($permutaId)` - Cancela permuta (apenas autor, apenas se pendente)

#### Aprovação/Rejeição (Administradores)
- `aprovarPermuta($permutaId)` - Aprova permuta (level >= 3)
- `rejeitarPermuta()` - Rejeita com motivo (level >= 3)

#### Sistema de Emails
- `enviarEmailsPedidoPermuta()` - Envia para:
  - `escoladigital@aejoaodebarros.pt` (aprovação)
  - Autor (confirmação de pedido)
  - Substituto (se diferente do autor)

- `enviarEmailsAprovacao()` - Notifica autor e substituto

- `enviarEmailsRejeicao()` - Notifica autor e substituto com motivo

---

## 🎨 Frontend

### Views Criadas

#### 1. `form_permuta.php`
**Localização**: `app/Views/permutas/form_permuta.php`

**Funcionalidades**:
- Exibe informações da aula original
- Formulário com:
  - Data da aula a permutar (date picker)
  - Professor substituto (select2)
  - Data de reposição (date picker)
  - Sala para reposição (select2)
  - Checkboxes para incluir outras aulas do mesmo dia
  - Textarea para observações
- Validação client-side
- Submissão via AJAX
- SweetAlert para feedback

**Campos do Formulário**:
```html
- aula_original_id (hidden)
- data_aula_original (date, required)
- professor_substituto_nif (select, required)
- data_aula_permutada (date, required)
- sala_permutada_id (select, optional)
- aulas_adicionais[] (checkbox array)
- observacoes (textarea)
```

---

#### 2. `minhas_permutas.php`
**Localização**: `app/Views/permutas/minhas_permutas.php`

**Funcionalidades**:
- Cards com estatísticas (pendentes, aprovadas, rejeitadas, como substituto)
- DataTable com listagem completa
- Filtros e busca
- Ações: Ver detalhes, Cancelar (se pendente)
- Badge de estado colorido
- Indicador de grupo de permutas

**Colunas da Tabela**:
- ID
- Aula (disciplina, turma, horário)
- Data Original
- Data Reposição
- Professor Substituto
- Estado (badge colorido)
- Criado
- Ações (ver, cancelar)

---

#### 3. `detalhes_permuta.php`
**Localização**: `app/Views/permutas/detalhes_permuta.php`

**Funcionalidades**:
- Badge grande de estado
- Informações da aula original
- Detalhes da permuta (datas, professores, sala, grupo)
- Observações/Justificação
- Informações de aprovação/rejeição (se aplicável)
- Motivo de rejeição (se rejeitada)
- Botões de ação contextuais:
  - **Professor Autor (pendente)**: Cancelar
  - **Administrador (pendente)**: Aprovar / Rejeitar

**Ações AJAX**:
- Cancelar (com confirmação)
- Aprovar (com confirmação)
- Rejeitar (com input de motivo obrigatório)

---

### Dashboard Atualizado

**Ficheiro**: `app/Views/dashboard/tecnico_dashboard.php`

**Widget Adicionado** (condicional - apenas se tiver NIF):
- 4 small-boxes com estatísticas de permutas
- Tabela com 5 permutas mais recentes
- Links para "Ver Todas"

**Controller**: `app/Controllers/DashboardController.php`
- Método `tecnicoDashboard()` atualizado
- Busca estatísticas: `$permutaModel->getEstatisticasProfessor($userNif)`
- Busca permutas recentes limitadas a 5

---

## 🛣️ Rotas

**Ficheiro**: `app/Config/Routes.php`

```php
$routes->group('permutas', function($routes) {
    // Visualização
    $routes->get('/', 'PermutasController::index');                          // Meu Horário
    $routes->get('minhas', 'PermutasController::minhasPermutas');             // As Minhas Permutas
    $routes->get('ver/(:num)', 'PermutasController::verPermuta/$1');         // Detalhes
    
    // Criar e gerir
    $routes->get('pedir/(:num)', 'PermutasController::pedirPermuta/$1');     // Formulário
    $routes->post('salvar', 'PermutasController::salvarPermuta');            // Salvar
    $routes->post('cancelar/(:num)', 'PermutasController::cancelarPermuta/$1'); // Cancelar
    
    // Administração
    $routes->post('aprovar/(:num)', 'PermutasController::aprovarPermuta/$1');   // Aprovar (admin)
    $routes->post('rejeitar', 'PermutasController::rejeitarPermuta');           // Rejeitar (admin)
});
```

---

## 🧭 Navegação

### Sidebar Atualizada
**Ficheiro**: `app/Views/layout/partials/sidebar.php`

**Menu "Horário & Permutas"**:
```
📅 Horário & Permutas
  ├─ 👁️ Meu Horário (/permutas)
  └─ ✅ As Minhas Permutas (/permutas/minhas)
```

---

## 🔔 Sistema de Notificações

### Emails Implementados

#### 1. Pedido de Permuta
**Destinatários**:
- `escoladigital@aejoaodebarros.pt` (para aprovação)
- Professor autor (confirmação de pedido)
- Professor substituto (se diferente)

**Conteúdo**:
- Identificação dos professores
- Link para detalhes
- Pedido de aprovação (para escola)

#### 2. Aprovação
**Destinatários**:
- Professor autor
- Professor substituto (se diferente)

**Conteúdo**:
- Confirmação de aprovação
- Link para detalhes

#### 3. Rejeição
**Destinatários**:
- Professor autor
- Professor substituto (se diferente)

**Conteúdo**:
- Notificação de rejeição
- Motivo da rejeição
- Link para detalhes

---

## 🔒 Permissões e Segurança

### Níveis de Acesso

**Professores (level 5)**:
- ✅ Ver próprio horário
- ✅ Pedir permutas
- ✅ Ver suas permutas
- ✅ Cancelar permutas pendentes (apenas as suas)
- ❌ Aprovar/Rejeitar

**Administradores (level >= 3)**:
- ✅ Todas as permissões de professores
- ✅ Ver detalhes de qualquer permuta
- ✅ Aprovar permutas
- ✅ Rejeitar permutas

### Validações de Segurança

1. **Criar Permuta**:
   - Aula deve pertencer ao professor
   - Todas as aulas adicionais devem pertencer ao professor
   - Datas devem ser válidas

2. **Cancelar**:
   - Apenas o autor pode cancelar
   - Apenas se estado = 'pendente'

3. **Ver Detalhes**:
   - Autor pode ver
   - Substituto pode ver
   - Administradores podem ver

4. **Aprovar/Rejeitar**:
   - Apenas administradores (level >= 3)
   - Apenas se estado = 'pendente'

---

## 🎯 Funcionalidades Especiais

### 1. Grupo de Permutas
- Professor pode selecionar várias aulas do mesmo dia
- Todas ficam com o mesmo `grupo_permuta`
- Gestão em bloco (aprovar/rejeitar todas juntas)
- Identificação visual no badge

### 2. Permuta com Si Próprio
- Professor pode selecionar-se como substituto
- Útil para reposição em outra data
- Sistema permite e trata corretamente

### 3. Professores da Mesma Turma
- Sistema lista apenas professores que dão aulas na mesma turma
- Facilita seleção de substituto adequado
- Baseado em `codigo_turma` da aula

### 4. Sala Flexível
- Campo sala é opcional
- Se não preenchido, assume sala original
- Se preenchido, nova sala é registada

---

## 📊 Estatísticas e Relatórios

### Métricas Disponíveis (por professor)
- Total de permutas pendentes
- Total de permutas aprovadas
- Total de permutas rejeitadas
- Vezes que foi substituto (aprovadas)

### Visualizações
- Dashboard: Cards coloridos + tabela recente
- Página "As Minhas Permutas": Cards + DataTable completa
- Filtros por estado disponíveis no model

---

## 🧪 Testes Recomendados

### Fluxo Completo de Teste
1. ✅ Login como professor (level 5)
2. ✅ Aceder a "Meu Horário"
3. ✅ Clicar numa aula e "Pedir Permuta"
4. ✅ Preencher formulário com dados válidos
5. ✅ Incluir aulas adicionais do mesmo dia
6. ✅ Submeter permuta
7. ✅ Verificar email de confirmação
8. ✅ Aceder a "As Minhas Permutas"
9. ✅ Ver detalhes da permuta
10. ✅ Login como admin (level >= 3)
11. ✅ Aceder à permuta
12. ✅ Aprovar permuta
13. ✅ Verificar emails de aprovação
14. ✅ Verificar estado atualizado

### Testes de Segurança
- ❌ Tentar cancelar permuta de outro professor
- ❌ Tentar aprovar sem ser admin
- ❌ Tentar pedir permuta de aula que não é sua
- ❌ Tentar ver detalhes de permuta alheia (não autor/substituto)

---

## 📝 Notas de Implementação

### Campos NIF
- Sistema depende de `user.NIF` estar preenchido
- Professores sem NIF verão mensagem informativa
- Dashboard só mostra widget se NIF existir

### Encoding
- Todas as views usam UTF-8
- Emails configurados para UTF-8
- Caracteres especiais portugueses suportados

### Responsividade
- Todas as views são responsivas (Bootstrap 5)
- DataTables configuradas para dispositivos móveis
- Small-boxes adaptam-se a diferentes resoluções

### Performance
- Índices criados em campos de busca frequente
- JOINs otimizados
- Paginação via DataTables
- Limite de 5 permutas no dashboard

---

## 🚀 Próximos Passos (Futuro)

### Melhorias Sugeridas
1. **Calendário Visual** - Ver permutas num calendário mensal
2. **Notificações In-App** - Além de email, notificações no sistema
3. **Histórico de Alterações** - Log de mudanças de estado
4. **Relatórios** - Estatísticas por turma, disciplina, período
5. **Aprovação Automática** - Regras para auto-aprovar certos tipos
6. **Conflitos** - Validação mais rigorosa de conflitos de horário
7. **Substituição Múltipla** - Um professor pode pedir a vários
8. **Templates de Email** - Views HTML para emails mais bonitos

---

## ✅ Checklist de Implementação

- [x] Migration criada e executada
- [x] Model com todas as validações
- [x] Controller com todos os métodos
- [x] View formulário de pedido
- [x] View listagem de permutas
- [x] View detalhes de permuta
- [x] Sistema de emails implementado
- [x] Rotas configuradas
- [x] Menu na sidebar
- [x] Widget no dashboard
- [x] Permissões e segurança
- [x] Grupo de permutas
- [x] Estatísticas
- [x] AJAX e SweetAlert
- [x] DataTables configuradas
- [x] Responsividade

---

## 🎓 Documentação Técnica

### Fluxo de Dados

```
1. Professor acede ao horário
2. Clica em "Pedir Permuta" numa aula
3. Sistema carrega:
   - Dados da aula
   - Professores da turma
   - Outras aulas do dia
   - Salas disponíveis
4. Professor preenche:
   - Data aula original
   - Data reposição
   - Professor substituto
   - Sala (opcional)
   - Aulas adicionais (opcional)
   - Observações
5. Sistema valida e salva
6. Gera grupo_permuta se múltiplas aulas
7. Envia emails:
   - Para escola (aprovação)
   - Para autor (confirmação)
   - Para substituto (notificação)
8. Estado = 'pendente'
9. Admin acede e aprova/rejeita
10. Sistema atualiza estado
11. Envia emails de aprovação/rejeição
12. Professor vê estado atualizado
```

### Estrutura de Base de Dados

```sql
permutas
├── id (PK)
├── aula_original_id (FK → horario_aulas)
├── data_aula_original
├── data_aula_permutada
├── professor_autor_nif (FK → user.NIF)
├── professor_substituto_nif (FK → user.NIF)
├── sala_permutada_id
├── grupo_permuta (para agrupar múltiplas)
├── estado (ENUM)
├── observacoes
├── motivo_rejeicao
├── aprovada_por_user_id (FK → user.id)
├── data_aprovacao
└── timestamps
```

---

**Implementação Concluída em: 21 de Outubro de 2025**  
**Migration Batch: 21**  
**Versão: 1.0**
