# Sistema de Gestão Letiva - Interfaces

## ✅ Implementação Concluída

### 📋 Resumo
Foi implementado o sistema completo de interfaces para a **Gestão Letiva**, incluindo 6 módulos principais com operações CRUD completas.

---

## 🎯 Componentes Criados

### 1. Controllers (6 arquivos)
Localização: `app/Controllers/`

- ✅ **TurmasController.php** - Gestão de turmas
- ✅ **DisciplinasController.php** - Gestão de disciplinas
- ✅ **HorariosController.php** - Gestão de horários (com verificação de conflitos)
- ✅ **BlocosController.php** - Gestão de blocos horários
- ✅ **TipologiasController.php** - Gestão de tipologias de curso
- ✅ **AnosLetivosController.php** - Gestão de anos letivos (com função de ativar)

**Funcionalidades comuns em todos os controllers:**
- Controle de acesso (nível 6+)
- Métodos CRUD completos (index, create, update, delete, get)
- Retorno de dados em formato JSON para DataTables
- Tratamento de erros com validação

**Funcionalidade especial:**
- **HorariosController**: Verificação de conflitos (professor, turma, sala)
- **AnosLetivosController**: Método `ativar()` para ativar ano letivo

---

### 2. Views (6 arquivos)
Localização: `app/Views/gestao_letiva/`

- ✅ **turmas_index.php**
- ✅ **disciplinas_index.php**
- ✅ **horarios_index.php**
- ✅ **blocos_index.php**
- ✅ **tipologias_index.php**
- ✅ **anos_letivos_index.php**

**Recursos implementados em todas as views:**
- DataTables com pesquisa, ordenação e paginação
- Modais Bootstrap para criar/editar
- Botões de ação (Editar, Eliminar)
- Confirmação antes de eliminar
- Mensagens toastr (sucesso/erro)
- Tradução PT-PT nas DataTables
- Design responsivo (AdminLTE 3.2.0)
- Breadcrumbs de navegação

**Recursos especiais:**
- **horarios_index.php**: Alerta de conflitos, formulário complexo com múltiplos selects
- **anos_letivos_index.php**: Botão "Ativar" para anos inativos

---

### 3. Rotas
Localização: `app/Config/Routes.php`

Adicionadas 6 grupos de rotas com prefixo e métodos CRUD:

```php
// 📚 Gestão Letiva
- /turmas (GET, POST para getDataTable, create, update, delete, get)
- /disciplinas (GET, POST para getDataTable, create, update, delete, get)
- /horarios (GET, POST para getDataTable, create, update, delete, get)
- /blocos (GET, POST para getDataTable, create, update, delete, get)
- /tipologias (GET, POST para getDataTable, create, update, delete, get)
- /anos-letivos (GET, POST para getDataTable, create, update, delete, get, ativar)
```

**Total de rotas:** 41 novas rotas

---

### 4. Menu Sidebar
Localização: `app/Views/layout/partials/sidebar.php`

Adicionado menu **"Gestão Letiva"** com:
- Posicionamento: Acima do menu Dashboard
- Restrição: Nível 6 ou superior
- 6 itens de submenu com ícones Bootstrap Icons:
  - 👥 Turmas (bi-people-fill)
  - 📚 Disciplinas (bi-book)
  - 📅 Horários (bi-calendar3)
  - 🕐 Blocos (bi-clock)
  - 🏷️ Tipologias (bi-tags)
  - 📆 Anos Letivos (bi-calendar-range)
- Detecção de página ativa
- Classe `menu-open` quando em submenu ativo

---

## 📊 Estrutura de Dados

### Turmas
- Campos: ano (0-12), nome, dt_id, anoletivo_id, tipologia_id
- Relações: Ano Letivo, Tipologia

### Disciplinas
- Campos: nome, horas (opcional), tipologia_id
- Relações: Tipologia

### Horários (Central)
- Campos: id_professor, id_disciplina, id_turma, id_sala, dia_semana (1-6), id_bloco, frequencia
- Relações: Professor (User), Disciplina, Turma, Sala, Bloco Horário
- **Validação**: Conflitos de professor, turma e sala

### Blocos Horários
- Campos: hora_inicio, hora_fim, designacao, dia_semana (ENUM)
- Dias: Segunda a Sábado
- 72 blocos existentes (12 por dia)

### Tipologias
- Campos: nome_tipologia, status
- 3 registros: Regular, Profissional, CEF

### Anos Letivos
- Campos: anoletivo (INT 4 dígitos), status (0/1)
- **Regra**: Apenas 1 ano pode estar ativo
- Método especial: `ativarAno()` desativa todos e ativa o selecionado

---

## 🔐 Controle de Acesso

**Nível mínimo requerido:** 6

Implementado em:
1. ✅ Sidebar (PHP: `<?php if ($userLevel >= 6): ?>`)
2. ✅ Todos os controllers (método `index()`)
3. ✅ Redirecionamento para home com mensagem de erro se acesso negado

---

## 🎨 Interface

### Design Pattern
- AdminLTE 3.2.0 (Bootstrap 5)
- DataTables 1.13.7 (com tradução PT-PT)
- Bootstrap Icons
- Toastr para notificações
- Modais Bootstrap para formulários

### Responsividade
- Tabelas responsivas (DataTables responsive)
- Grid Bootstrap para formulários
- Mobile-friendly

---

## ✨ Funcionalidades Implementadas

### CRUD Completo
- ✅ Criar (Modal com formulário validado)
- ✅ Ler (DataTable com AJAX)
- ✅ Atualizar (Modal pré-preenchido)
- ✅ Eliminar (Com confirmação)

### Extras
- ✅ Pesquisa em tempo real (DataTables)
- ✅ Ordenação por colunas
- ✅ Paginação
- ✅ Mensagens de feedback (toastr)
- ✅ Validação client-side (HTML5 required)
- ✅ Validação server-side (Models)
- ✅ Verificação de conflitos (Horários)
- ✅ Ativação de ano letivo (Anos Letivos)

---

## 📝 Próximos Passos Sugeridos

### Funcionalidades Adicionais
1. **Permutas de Aulas** (tabela já criada)
   - Controller para gestão de permutas
   - View para listar/aprovar/rejeitar permutas
   - Workflow de aprovação

2. **Visualizações Alternativas**
   - Grade de horários (visualização semanal)
   - Horário por professor
   - Horário por turma
   - Horário por sala

3. **Relatórios**
   - Export para Excel/PDF
   - Estatísticas de ocupação
   - Conflitos detectados

4. **Importação/Exportação**
   - Import CSV de turmas
   - Import CSV de disciplinas
   - Export de horários completos

---

## 🧪 Testes Recomendados

### Teste de Acesso
- [ ] Tentar aceder com nível < 6 (deve redirecionar)
- [ ] Aceder com nível >= 6 (deve mostrar menu e páginas)

### Teste CRUD
Para cada módulo:
- [ ] Criar novo registro
- [ ] Editar registro existente
- [ ] Eliminar registro
- [ ] Verificar DataTable carrega corretamente
- [ ] Verificar validações funcionam

### Teste de Conflitos (Horários)
- [ ] Criar horário para professor X em Segunda às 8h
- [ ] Tentar criar outro horário para mesmo professor no mesmo horário
- [ ] Deve mostrar alerta de conflito
- [ ] Repetir para turma e sala

### Teste de Ativação (Anos Letivos)
- [ ] Criar ano letivo 2024 (ativo)
- [ ] Criar ano letivo 2025 (inativo)
- [ ] Ativar ano 2025
- [ ] Verificar se ano 2024 ficou inativo

---

## 📁 Arquivos Criados

```
app/
├── Controllers/
│   ├── AnosLetivosController.php ✅
│   ├── BlocosController.php ✅
│   ├── DisciplinasController.php ✅
│   ├── HorariosController.php ✅
│   ├── TipologiasController.php ✅
│   └── TurmasController.php ✅
├── Views/
│   └── gestao_letiva/
│       ├── anos_letivos_index.php ✅
│       ├── blocos_index.php ✅
│       ├── disciplinas_index.php ✅
│       ├── horarios_index.php ✅
│       ├── tipologias_index.php ✅
│       └── turmas_index.php ✅
├── Config/
│   └── Routes.php ✅ (atualizado)
└── Views/layout/partials/
    └── sidebar.php ✅ (atualizado)
```

**Total:** 8 arquivos criados + 2 atualizados

---

## 🚀 Como Usar

### Acessar o Sistema
1. Fazer login com utilizador nível 6+
2. No menu lateral, clicar em **"Gestão Letiva"**
3. Selecionar o módulo desejado

### Criar Registro
1. Clicar no botão **"Novo [Módulo]"**
2. Preencher o formulário no modal
3. Clicar em **"Guardar"**
4. Verificar mensagem de sucesso

### Editar Registro
1. Na tabela, clicar no botão **editar** (ícone lápis)
2. Modal abrirá com dados pré-preenchidos
3. Alterar campos desejados
4. Clicar em **"Guardar"**

### Eliminar Registro
1. Na tabela, clicar no botão **eliminar** (ícone lixeira)
2. Confirmar ação
3. Verificar mensagem de sucesso

---

## ⚠️ Notas Importantes

1. **Conflitos de Horários**: O sistema valida automaticamente conflitos de professor, turma e sala ao criar/editar horários.

2. **Ano Letivo Ativo**: Apenas um ano letivo pode estar ativo por vez. Ao ativar um ano, todos os outros são automaticamente desativados.

3. **Relações de Dados**: Alguns registros podem estar vinculados (ex: Turma depende de Ano Letivo e Tipologia). Eliminar registros vinculados pode causar erros de integridade referencial.

4. **Validações**: Todos os formulários possuem validação HTML5 (client-side) e validação do Model (server-side).

5. **Acesso**: Apenas utilizadores com nível 6 ou superior podem aceder às páginas de Gestão Letiva.

---

## 📞 Suporte

Para dúvidas ou problemas:
- Verificar logs em `writable/logs/`
- Verificar console do navegador (F12)
- Verificar resposta AJAX na aba Network
- Verificar validações retornadas pelo servidor

---

**Data de Implementação:** <?= date('d/m/Y') ?>  
**Versão do Sistema:** CodeIgniter 4.6.1  
**Template:** AdminLTE 3.2.0
