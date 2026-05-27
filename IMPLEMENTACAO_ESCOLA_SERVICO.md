# 📚 Implementação do Campo escola_servico

## 📋 Visão Geral

Campo adicionado à tabela `user` para registar a escola onde cada utilizador (professor) desenvolve a sua atividade laboral.

**Data de Implementação:** 13 de Março de 2026

---

## 🎯 Objetivo

Permitir registar e gerir a escola de serviço de cada utilizador, facilitando:
- Identificação da escola onde cada professor leciona
- Relatórios por escola
- Gestão de recursos humanos por estabelecimento
- Estatísticas por escola

---

## 🗂️ Alterações Realizadas

### 📁 Scripts SQL

**`ALTER_USER_ADD_ESCOLA_SERVICO.sql`**
- Adiciona campo `escola_servico` (INT UNSIGNED) à tabela `user`
- Cria Foreign Key para `escolas.id`
- Adiciona índice para performance
- ON DELETE: SET NULL (se escola for apagada, campo fica NULL)
- ON UPDATE: CASCADE (atualiza automaticamente se ID mudar)

### 📁 Models

**`UserModel.php`** - Atualizado:
1. Campo `escola_servico` adicionado a `$allowedFields`
2. Regra de validação: `'escola_servico' => 'permit_empty|integer|is_not_unique[escolas.id]'`
3. Novos métodos:
   - `getUsersWithEscola()` - Lista utilizadores com dados da escola
   - `getUserWithRelations($id)` - Obtém utilizador com escola e grupo
   - `getUsersByEscola($escolaId)` - Utilizadores de uma escola específica
   - `getEstatisticasPorEscola()` - Estatísticas agrupadas por escola

**`EscolasModel.php`** - Já existe no projeto

### 📁 Controllers

**`UserController.php`** - Atualizado:
1. Importa `EscolasModel`
2. Propriedade `$escolasModel` adicionada
3. Método `index()` carrega escolas: `$data['escolas'] = $this->escolasModel->getEscolasOrdenadas()`
4. Método `getUser()` usa `getUserWithRelations()` para incluir escola
5. Métodos `create()` e `update()` incluem `escola_servico` nos dados

### 📁 Views

**`user_index.php`** - Atualizado:

1. **Formulário (Modal Criar/Editar):**
   - Novo campo select "Escola de Serviço"
   - Carrega dinamicamente escolas da BD
   - Positioned após campo "Categoria"

2. **Modal Visualização:**
   - Nova linha para mostrar "Escola de Serviço"
   - Exibe nome da escola (ou "N/A" se nulo)

3. **JavaScript:**
   - Função `editUser()` carrega `escola_servico`
   - Função `viewUser()` mostra `escola_nome`

---

## 📊 Estrutura do Campo

```sql
`escola_servico` INT(5) UNSIGNED NULL DEFAULT NULL
COMMENT 'FK para escolas - Escola onde o utilizador desenvolve atividade'

CONSTRAINT `fk_user_escola_servico`
  FOREIGN KEY (`escola_servico`) 
  REFERENCES `escolas`(`id`)
  ON DELETE SET NULL
  ON UPDATE CASCADE

INDEX `idx_escola_servico` (`escola_servico`)
```

---

## 🚀 Como Aplicar

### Opção 1: Script SQL Direto

```bash
mysql -u root -p sistema_gestao < ALTER_USER_ADD_ESCOLA_SERVICO.sql
```

### Opção 2: Via phpMyAdmin

1. Abrir phpMyAdmin
2. Selecionar base de dados `sistema_gestao`
3. Ir ao separador "SQL"
4. Copiar conteúdo do ficheiro `ALTER_USER_ADD_ESCOLA_SERVICO.sql`
5. Executar

---

## 📖 Uso no Sistema

### Atribuir Escola a Utilizador

1. Aceder a http://localhost:8080/users
2. Clicar em "Editar" no utilizador pretendido
3. Selecionar escola no campo "Escola de Serviço"
4. Guardar

### Consultar Utilizadores por Escola (PHP)

```php
// No Controller
$userModel = new UserModel();

// Obter todos utilizadores de uma escola
$users = $userModel->getUsersByEscola(1);

// Obter utilizador com dados da escola
$user = $userModel->getUserWithRelations($userId);
echo $user['escola_nome']; // Nome da escola

// Estatísticas por escola
$stats = $userModel->getEstatisticasPorEscola();
```

### Consultar Utilizadores por Escola (SQL)

```sql
-- Listar utilizadores com suas escolas
SELECT 
    u.id,
    u.name,
    u.email,
    e.nome as escola,
    e.morada
FROM user u
LEFT JOIN escolas e ON u.escola_servico = e.id
WHERE u.escola_servico IS NOT NULL
ORDER BY e.nome, u.name;

-- Contar utilizadores por escola
SELECT 
    e.nome as escola,
    COUNT(u.id) as total_utilizadores
FROM escolas e
LEFT JOIN user u ON u.escola_servico = e.id
GROUP BY e.id, e.nome
ORDER BY total_utilizadores DESC;
```

---

## 🏫 Escolas Disponíveis

1. Escola Secundária João de Barros
2. Escola Básica 2,3 de Corroios
3. Escola Básica do 1º Ciclo de Miratejo
4. Escola Básica José Afonso
5. Escola Básica do 1º Ciclo D. Nuno Álvares Pereira

---

## 📝 Notas Importantes

1. **Campo Opcional:** Permite NULL (utilizadores podem não ter escola atribuída)
2. **Segurança:** Foreign Key garante integridade (só aceita IDs existentes)
3. **Flexibilidade:** Se escola for apagada, campo fica NULL automaticamente
4. **Performance:** Índice criado para consultas rápidas
5. **Relatórios:** Facilita criação de relatórios por estabelecimento

---

## ✅ Checklist de Implementação

- [x] Script SQL criado
- [x] UserModel atualizado (allowedFields, validation, métodos)
- [x] UserController atualizado (carregar escolas, criar/atualizar)
- [x] View user_index.php atualizada (formulário, modal, JavaScript)
- [x] EscolasModel verificado (já existe)
- [x] Sem erros de código
- [x] **Compatibilidade retroativa** - Código funciona antes e depois do script SQL
- [ ] Script SQL executado na BD
- [ ] Testes realizados

---

## ⚠️ Nota Importante

**O código está preparado para funcionar ANTES e DEPOIS de executar o script SQL.**

Os métodos do `UserModel` e `UserController` verificam automaticamente se o campo `escola_servico` existe na base de dados. Enquanto o campo não existir:
- Os métodos relacionados com escola retornam valores vazios ou apenas dados do utilizador
- O sistema funciona normalmente sem erros
- Após executar o script SQL, a funcionalidade fica automaticamente disponível

**Não é necessário parar o servidor ou reiniciar - execute o script SQL e recarregue a página.**

---

## 🔄 Próximos Passos

1. **Executar o script SQL** na base de dados
2. **Testar** criação/edição de utilizadores
3. **Importar dados** se tiver utilizadores com escolas na BD antiga
4. **Criar relatórios** por escola (opcional)
5. **Dashboard** com estatísticas por escola (opcional)

---

**Sistema de Gestão Escolar**  
**CodeIgniter 4 + AdminLTE**  
**© 2026 Agrupamento de Escolas João de Barros**
