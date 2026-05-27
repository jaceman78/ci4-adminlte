# Filtro de Convocatórias por Email Enviado

## 📋 Objetivo

Apenas convocatórias para as quais já foi enviado email aparecem nos dashboards de todos os níveis de utilizadores. Isso evita que convocatórias ainda em edição sejam exibidas prematuramente.

## 🔄 Alterações Implementadas

### 1. **Alteração de Base de Dados**
- **Arquivo**: `ALTER_CONVOCATORIA_ADD_EMAIL_ENVIADO.sql`
- **Coluna Adicionada**: `email_enviado_em DATETIME NULL`
- **Índice**: `idx_email_enviado`
- **Descrição**: Armazena a data e hora do envio do email. NULL = email não enviado

```sql
ALTER TABLE `convocatoria` 
ADD COLUMN `email_enviado_em` DATETIME NULL COMMENT 'Data e hora do envio do email. NULL = não enviado' AFTER `data_confirmacao`,
ADD INDEX `idx_email_enviado` (`email_enviado_em`);
```

### 2. **SessaoExameController.php**
- **Método Alterado**: `enviarEmailConvocatoria()`
- **Linha**: ~665
- **Alteração**: Ao enviar email com sucesso, agora registra a data/hora em `email_enviado_em`

```php
if ($email->send()) {
    // Registrar o envio do email na base de dados
    try {
        $db = \Config\Database::connect();
        $updated = $db->table('convocatoria')
           ->where('id', $convocatoria['id'])
           ->update([
               'email_enviado_em' => date('Y-m-d H:i:s')
           ]);
        
        log_message('info', 'Email enviado e registrado para convocatoria ID ' . $convocatoria['id']);
    } catch (\Exception $e) {
        log_message('error', 'Erro ao atualizar convocatoria: ' . $e->getMessage());
    }
    
    return [
        'success' => true,
        'message' => 'Email enviado com sucesso'
    ];
}
```

### 3. **ConvocatoriaModel.php**

#### a) **allowedFields**
- **Adicionado**: `'email_enviado_em'` ao array de campos permitidos

```php
protected $allowedFields = [
    'sessao_exame_id',
    'user_id',
    'sessao_exame_sala_id',
    'funcao',
    'estado_confirmacao',
    'presenca',
    'data_confirmacao',
    'email_enviado_em', // ← NOVO
    'observacoes',
    'created_at',
    'updated_at'
];
```

#### b) **getByProfessor()**
- **Linha**: ~136
- **Filtro Adicionado**: `->where('convocatoria.email_enviado_em IS NOT NULL')`
- **Impacto**: Afeta todos os dashboards de professores

```php
public function getByProfessor($userId, $apenasAtivas = true)
{
    $this->select('...')
        ->join('...')
        ->where('convocatoria.user_id', $userId)
        ->where('convocatoria.email_enviado_em IS NOT NULL'); // ← NOVO FILTRO
    
    if ($apenasAtivas) {
        $this->where('sessao_exame.data_exame >=', date('Y-m-d'))
             ->where('sessao_exame.ativo', 1);
    }
    
    return $this->orderBy('sessao_exame.data_exame', 'ASC')
                ->orderBy('sessao_exame.hora_exame', 'ASC')
                ->findAll();
}
```

#### c) **getPendentes()**
- **Linha**: ~176
- **Filtro Adicionado**: `->where('convocatoria.email_enviado_em IS NOT NULL')`
- **Impacto**: Afeta listagens de convocatórias pendentes

```php
public function getPendentes($userId = null)
{
    $this->select('...')
        ->join('...')
        ->where('convocatoria.estado_confirmacao', 'Pendente')
        ->where('convocatoria.email_enviado_em IS NOT NULL') // ← NOVO FILTRO
        ->where('sessao_exame.ativo', 1);
    
    if ($userId) {
        $this->where('convocatoria.user_id', $userId);
    }
    
    return $this->orderBy('sessao_exame.data_exame', 'ASC')
                ->findAll();
}
```

## 🎯 Impacto nos Dashboards

### Níveis de Utilizador Afetados:
- **Todos os níveis** (0-9)

### Controladores Impactados:
1. **DashboardController.php**
   - Usa `getByProfessor()` → já filtrado automaticamente
   - Linhas: 102, 257, 341

2. **MinhasConvocatoriasController.php**
   - Usa `getByProfessor()` → já filtrado automaticamente
   - Linhas: 40, 223, 273

## 📊 Fluxo de Funcionamento

```
1. Admin/Sec. Exames cria convocatórias → email_enviado_em = NULL
   └→ Convocatórias NÃO aparecem nos dashboards

2. Admin/Sec. Exames clica "Enviar Emails" na sessão
   └→ Sistema envia emails aos professores
   └→ Para cada email enviado com sucesso: email_enviado_em = data/hora atual
   └→ Convocatórias AGORA aparecem nos dashboards

3. Professor acessa dashboard
   └→ Vê apenas convocatórias onde email_enviado_em IS NOT NULL
   └→ Pode confirmar/rejeitar/solicitar permuta
```

## ✅ Vantagens

1. **Evita Confusão**: Professores não veem convocatórias ainda em edição
2. **Controle de Publicação**: Envio de email funciona como "publicar" a convocatória
3. **Rastreabilidade**: Sabe-se exatamente quando cada convocatória foi comunicada
4. **Flexibilidade**: Admin pode fazer alterações antes de comunicar

## 🔧 Como Testar

### 1. Criar Convocatória sem Enviar Email
```
1. Acesse /sessoes-exame
2. Selecione uma sessão
3. Clique em "Alocar Salas" e adicione professores
4. NÃO clique em "Enviar Emails"
5. Faça login como um professor convocado
6. Verifique que a convocatória NÃO aparece no dashboard
```

### 2. Enviar Email e Verificar Aparecimento
```
1. Faça login como Admin/Sec. Exames
2. Acesse /sessoes-exame
3. Selecione a mesma sessão
4. Clique em "Enviar Emails"
5. Aguarde confirmação de envio
6. Faça login como o professor convocado
7. Verifique que a convocatória AGORA aparece no dashboard
```

### 3. Verificar Base de Dados
```sql
-- Ver convocatórias com email enviado
SELECT 
    id, 
    user_id, 
    funcao, 
    email_enviado_em,
    estado_confirmacao
FROM convocatoria 
WHERE email_enviado_em IS NOT NULL;

-- Ver convocatórias sem email enviado
SELECT 
    id, 
    user_id, 
    funcao, 
    email_enviado_em
FROM convocatoria 
WHERE email_enviado_em IS NULL;
```

## 📝 Notas de Manutenção

### Outros Métodos que Podem Precisar do Filtro no Futuro:
- `getWithDetails()` - Se usado em dashboards
- `getBySessao()` - Apenas para visualização admin (não precisa filtro)
- `getConvocatoriasBySessaoComProfessores()` - Usado para marcação de presenças (não precisa filtro)

### Se Adicionar Novos Métodos de Busca:
1. Verifique se é usado em dashboards/views de professores
2. Se sim, adicione: `->where('convocatoria.email_enviado_em IS NOT NULL')`
3. Se não (apenas admin), não adicione o filtro

## 🗓️ Data de Implementação
**15 de Março de 2026**

## ✨ Conclusão

O sistema agora funciona como um workflow de publicação:
- **Rascunho** (email_enviado_em = NULL) → Apenas admin vê
- **Publicado** (email_enviado_em != NULL) → Todos veem

Isso dá controle total ao secretariado de exames sobre quando comunicar as convocatórias aos professores.
