# Guia de Implementação de Logs de Atividade

## 📋 Visão Geral

Este guia estabelece o padrão para implementar logs de atividade em todos os módulos da aplicação. Os logs permitem rastrear todas as ações dos utilizadores, facilitando auditoria, debugging e análise de uso.

## 🎯 Quando Adicionar Logs

Adicionar logs em **TODAS** as operações CRUD:
- ✅ **CREATE**: Criação de novos registos
- ✅ **READ/VIEW**: Visualização de detalhes (opcional, mas recomendado para dados sensíveis)
- ✅ **UPDATE**: Edição de registos existentes
- ✅ **DELETE**: Eliminação de registos
- ✅ **EXPORT**: Exportação de dados
- ✅ **IMPORT**: Importação de dados
- ✅ **LOGIN/LOGOUT**: Autenticação
- ✅ **CUSTOM**: Ações específicas do módulo

## 📦 Helper Necessário

Certifique-se que o helper está carregado em `app/Config/Autoload.php`:

```php
public $helpers = ['log'];
```

## 🔧 Função log_activity()

```php
log_activity(
    ?int $userId,              // ID do utilizador (obrigatório)
    string $modulo,            // Nome do módulo (ex: 'turmas', 'disciplinas')
    string $acao,              // Ação realizada (ex: 'create', 'update', 'delete')
    string $descricao,         // Descrição detalhada da ação
    ?int $registroId = null,   // ID do registo afetado (opcional)
    ?array $dadosAnteriores = null,  // Estado antes da alteração (opcional)
    ?array $dadosNovos = null,       // Estado após a alteração (opcional)
    ?array $detalhes = null    // Detalhes adicionais (opcional)
): bool
```

## 📝 Padrões de Nomenclatura

### Módulos
Use nomes descritivos em minúsculas:
- `turmas`
- `disciplinas`
- `horarios`
- `blocos`
- `tipologias`
- `anos_letivos`
- `permutas`
- `tickets`
- `equipamentos`
- `users`

### Ações
Use verbos em inglês (padrão REST):
- `create` - Criação
- `update` - Atualização
- `delete` - Eliminação
- `view` - Visualização
- `list` - Listagem
- `export` - Exportação
- `import` - Importação
- `approve` - Aprovação
- `reject` - Rejeição
- `cancel` - Cancelamento

### Descrições
Seja específico e inclua informações relevantes:
- ✅ "Criou turma '7A' do 7º ano para o ano letivo 2024/2025"
- ✅ "Atualizou disciplina 'Matemática' (MAT): alterou carga horária de 150 para 180 minutos"
- ✅ "Eliminou bloco horário '08:00-09:00' (ID: 5)"
- ❌ "Criou turma" (pouco descritivo)
- ❌ "Atualizou registo" (muito vago)

## 🚀 Exemplos de Implementação

### 1. CREATE (Criar)

```php
public function store()
{
    $userId = session()->get('LoggedUserData')['id'] ?? null;
    
    // Validação
    if (!$this->validate($rules)) {
        return $this->response->setJSON([
            'success' => false,
            'errors' => $this->validator->getErrors()
        ]);
    }
    
    $data = [
        'nome' => $this->request->getPost('nome'),
        'ano' => $this->request->getPost('ano'),
        'codigo' => $this->request->getPost('codigo'),
        // ... outros campos
    ];
    
    // Inserir
    $turmaId = $this->turmaModel->insert($data);
    
    if ($turmaId) {
        // LOG: Registar criação
        log_activity(
            $userId,
            'turmas',
            'create',
            "Criou turma '{$data['nome']}' do {$data['ano']}º ano (Código: {$data['codigo']})",
            $turmaId,
            null,  // Sem dados anteriores
            $data  // Dados da nova turma
        );
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Turma criada com sucesso',
            'id' => $turmaId
        ]);
    }
    
    return $this->response->setJSON([
        'success' => false,
        'message' => 'Erro ao criar turma'
    ]);
}
```

### 2. UPDATE (Atualizar)

```php
public function update($id)
{
    $userId = session()->get('LoggedUserData')['id'] ?? null;
    
    // Buscar dados anteriores
    $dadosAnteriores = $this->turmaModel->find($id);
    
    if (!$dadosAnteriores) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Turma não encontrada'
        ]);
    }
    
    // Novos dados
    $dadosNovos = [
        'nome' => $this->request->getPost('nome'),
        'ano' => $this->request->getPost('ano'),
        'codigo' => $this->request->getPost('codigo'),
        // ... outros campos
    ];
    
    // Atualizar
    if ($this->turmaModel->update($id, $dadosNovos)) {
        // Construir descrição das alterações
        $alteracoes = [];
        foreach ($dadosNovos as $campo => $novoValor) {
            if ($dadosAnteriores[$campo] != $novoValor) {
                $alteracoes[] = "{$campo}: '{$dadosAnteriores[$campo]}' → '{$novoValor}'";
            }
        }
        
        // LOG: Registar atualização
        log_activity(
            $userId,
            'turmas',
            'update',
            "Atualizou turma '{$dadosNovos['nome']}' (ID: {$id}): " . implode(', ', $alteracoes),
            $id,
            $dadosAnteriores,
            $dadosNovos
        );
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Turma atualizada com sucesso'
        ]);
    }
    
    return $this->response->setJSON([
        'success' => false,
        'message' => 'Erro ao atualizar turma'
    ]);
}
```

### 3. DELETE (Eliminar)

```php
public function delete($id)
{
    $userId = session()->get('LoggedUserData')['id'] ?? null;
    
    // Buscar dados antes de eliminar
    $turma = $this->turmaModel->find($id);
    
    if (!$turma) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Turma não encontrada'
        ]);
    }
    
    // Verificar dependências (opcional)
    $alunosCount = $this->alunoModel->where('turma_id', $id)->countAllResults();
    
    if ($this->turmaModel->delete($id)) {
        // LOG: Registar eliminação
        log_activity(
            $userId,
            'turmas',
            'delete',
            "Eliminou turma '{$turma['nome']}' do {$turma['ano']}º ano (Código: {$turma['codigo']})",
            $id,
            $turma,  // Guardar dados eliminados
            null,
            ['alunos_afetados' => $alunosCount]  // Detalhes adicionais
        );
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Turma eliminada com sucesso'
        ]);
    }
    
    return $this->response->setJSON([
        'success' => false,
        'message' => 'Erro ao eliminar turma'
    ]);
}
```

### 4. VIEW (Visualizar)

```php
public function show($id)
{
    $userId = session()->get('LoggedUserData')['id'] ?? null;
    
    $turma = $this->turmaModel->find($id);
    
    if (!$turma) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Turma não encontrada'
        ]);
    }
    
    // LOG: Registar visualização (opcional, usar apenas para dados sensíveis)
    log_activity(
        $userId,
        'turmas',
        'view',
        "Visualizou detalhes da turma '{$turma['nome']}'",
        $id
    );
    
    return $this->response->setJSON([
        'success' => true,
        'data' => $turma
    ]);
}
```

### 5. EXPORT (Exportar)

```php
public function exportCSV()
{
    $userId = session()->get('LoggedUserData')['id'] ?? null;
    
    $turmas = $this->turmaModel->findAll();
    $count = count($turmas);
    
    // Gerar CSV
    $filename = 'turmas_' . date('Y-m-d_His') . '.csv';
    // ... código de exportação ...
    
    // LOG: Registar exportação
    log_activity(
        $userId,
        'turmas',
        'export',
        "Exportou {$count} turmas para CSV (arquivo: {$filename})",
        null,
        null,
        null,
        ['total_registos' => $count, 'formato' => 'CSV', 'arquivo' => $filename]
    );
    
    return $this->response->download($filepath, null);
}
```

### 6. IMPORT (Importar)

```php
public function importCSV()
{
    $userId = session()->get('LoggedUserData')['id'] ?? null;
    
    $file = $this->request->getFile('csv_file');
    
    if (!$file->isValid()) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Arquivo inválido'
        ]);
    }
    
    // Processar CSV
    $imported = 0;
    $errors = 0;
    // ... código de importação ...
    
    // LOG: Registar importação
    log_activity(
        $userId,
        'turmas',
        'import',
        "Importou {$imported} turmas de CSV (Sucesso: {$imported}, Erros: {$errors})",
        null,
        null,
        null,
        [
            'arquivo' => $file->getName(),
            'total_processados' => $imported + $errors,
            'sucesso' => $imported,
            'erros' => $errors
        ]
    );
    
    return $this->response->setJSON([
        'success' => true,
        'message' => "Importação concluída: {$imported} turmas importadas",
        'stats' => ['imported' => $imported, 'errors' => $errors]
    ]);
}
```

## 🎨 Configuração dos Badges no ActivityLogController

Adicionar novos módulos ao array de badges:

```php
// Em app/Controllers/ActivityLogController.php - método getDataTable()

$moduloBadges = [
    'users' => 'bg-primary text-dark',
    'escolas' => 'bg-success text-dark',
    'salas' => 'bg-info text-dark',
    'auth' => 'bg-warning text-dark',
    'system' => 'bg-secondary text-white',
    'logs' => 'bg-dark text-white',
    'datatable_query' => 'bg-info text-dark',
    
    // GESTÃO LETIVA
    'turmas' => 'bg-primary text-dark',
    'disciplinas' => 'bg-success text-dark',
    'horarios' => 'bg-info text-dark',
    'blocos' => 'bg-warning text-dark',
    'tipologias' => 'bg-secondary text-white',
    'anos_letivos' => 'bg-primary text-dark',
    
    // OUTROS MÓDULOS
    'permutas' => 'bg-warning text-dark',
    'tickets' => 'bg-danger text-white',
    'equipamentos' => 'bg-info text-dark',
    'tipos_equipamentos' => 'bg-secondary text-white',
    'tipos_avaria' => 'bg-danger text-white',
    'materiais' => 'bg-success text-dark'
];
```

## ✅ Checklist de Implementação

Ao adicionar logs a um novo módulo, verificar:

- [ ] Helper `log` está carregado no Autoload.php
- [ ] Todos os métodos CRUD têm logs
- [ ] User ID é obtido da sessão
- [ ] Descrições são específicas e informativas
- [ ] Dados anteriores são guardados em UPDATE e DELETE
- [ ] Dados novos são guardados em CREATE e UPDATE
- [ ] Detalhes adicionais são incluídos quando relevante
- [ ] Badge do módulo foi adicionado ao ActivityLogController
- [ ] Logs de erro são registados em caso de falha
- [ ] Logs não bloqueiam a operação principal (usar try-catch se necessário)

## 🔍 Visualização de Logs

Os logs podem ser visualizados em:
- **URL**: `/logs`
- **Permissão**: Level 9 (Super Admin)
- **Recursos**: Filtros por utilizador, módulo, ação, data; Exportação CSV; Visualização detalhada; Eliminação seletiva

## 💡 Boas Práticas

1. **Sempre registar logs APÓS o sucesso da operação**
   - ✅ `$this->model->insert($data); log_activity(...);`
   - ❌ `log_activity(...); $this->model->insert($data);`

2. **Não deixar logs bloquearem operações críticas**
   ```php
   try {
       log_activity(...);
   } catch (\Exception $e) {
       log_message('error', 'Erro ao registar log: ' . $e->getMessage());
       // Continuar com a operação normal
   }
   ```

3. **Ser consistente nos nomes de módulos e ações**
   - Use sempre minúsculas
   - Use underscores para separar palavras
   - Mantenha os mesmos nomes em todo o sistema

4. **Incluir contexto suficiente na descrição**
   - Quem? Que? Como? Quando? são respondidos pela estrutura
   - Descrição deve responder: O quê exatamente?

5. **Não registar dados sensíveis em plain text**
   - Senhas: NUNCA
   - Tokens: NUNCA
   - Dados pessoais sensíveis: Ofuscar ou referenciar apenas o ID

## 🚨 Tratamento de Erros

```php
// Registar tentativas falhadas
if (!$this->turmaModel->insert($data)) {
    log_activity(
        $userId,
        'turmas',
        'create_failed',
        "Tentou criar turma '{$data['nome']}' mas falhou",
        null,
        null,
        $data,
        ['erro' => $this->turmaModel->errors()]
    );
    
    return $this->response->setJSON([
        'success' => false,
        'message' => 'Erro ao criar turma'
    ]);
}
```

---

## 📚 Referências

- Helper: `app/Helpers/log_helper.php`
- Model: `app/Models/ActivityLogModel.php`
- Controller: `app/Controllers/ActivityLogController.php`
- View: `app/Views/logs/activity_log_index.php`

---

**Última atualização**: 22 de Outubro de 2025
