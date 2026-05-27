# 📚 Implementação do Sistema de Categorias de Professores

## 📋 Visão Geral

Este documento descreve a implementação completa de um sistema modular de categorias de professores para o Sistema de Gestão Escolar baseado em CodeIgniter 4 + AdminLTE.

**Data de Implementação:** 13 de Março de 2026

---

## 🎯 Objetivos

1. **Modularizar** as categorias de professores através de uma tabela dedicada
2. **Normalizar** a base de dados criando uma Foreign Key na tabela `user`
3. **Facilitar** a gestão e manutenção das categorias
4. **Permitir** expansão futura com novos campos e funcionalidades

---

## 🗂️ Estrutura de Ficheiros Criados/Modificados

### 📁 Ficheiros SQL (Raiz do Projeto)

1. **`CREATE_TABLE_CATEGORIA_PROFESSORES.sql`**
   - Cria a tabela `categoria_professores`
   - Popula com as 23 categorias definidas
   - Inclui verificações e estatísticas

2. **`ALTER_USER_ADD_FK_CATEGORIA.sql`**
   - Altera a tabela `user` para adicionar FK
   - Cria backup dos dados existentes
   - Mapeia categorias antigas para novas IDs
   - Adiciona constraint e índices

### 📁 Migrations (CodeIgniter 4)

**Localização:** `app/Database/Migrations/`

1. **`2026-03-13-105200_CreateCategoriaProfessoresTable.php`**
   - Migration para criar tabela categoria_professores
   - Insere automaticamente as 23 categorias

2. **`2026-03-13-105300_AddCategoriaProfessoresFkToUser.php`**
   - Migration para adicionar FK na tabela user
   - Tratamento de campo categoria (criar ou modificar)

### 📁 Models

**Localização:** `app/Models/`

1. **`CategoriaProfessoresModel.php`** (NOVO)
   - Model completo para gestão de categorias
   - Métodos especializados para consultas
   - Validação de dados

2. **`UserModel.php`** (MODIFICADO)
   - Adicionados métodos para trabalhar com categorias
   - Validação atualizada para FK
   - Joins com categoria_professores

### 📁 Controllers

**Localização:** `app/Controllers/`

1. **`UserController.php`** (MODIFICADO)
   - Carrega modelo CategoriaProfessoresModel
   - Passa categorias para a view
   - Método getUser() atualizado para incluir relações

### 📁 Views

**Localização:** `app/Views/users/`

1. **`user_index.php`** (MODIFICADO)
   - Select de categoria carrega dinamicamente da BD
   - Agrupamento por tipo de ensino (optgroup)
   - JavaScript atualizado para mostrar nome da categoria

---

## 📊 Estrutura da Tabela `categoria_professores`

```sql
CREATE TABLE `categoria_professores` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(150) NOT NULL COMMENT 'Nome da categoria',
    `codigo` VARCHAR(20) NOT NULL COMMENT 'Código identificador',
    `tipo_ensino` ENUM(...) NOT NULL COMMENT 'Tipo de ensino',
    `tipo_quadro` ENUM(...) NOT NULL COMMENT 'Tipo de quadro',
    `tipo_nomeacao` ENUM(...) NOT NULL DEFAULT 'N/A',
    `ordem` INT(11) NOT NULL DEFAULT 0 COMMENT 'Ordem de exibição',
    `ativo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NULL,
    UNIQUE KEY `uk_codigo` (`codigo`),
    KEY `idx_tipo_ensino` (`tipo_ensino`),
    KEY `idx_tipo_quadro` (`tipo_quadro`),
    KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB;
```

### 📝 Categorias Implementadas (23)

#### 🎓 Professores do 2º e 3º Ciclos e Secundário (7)
1. Quadro Escola - Nomeação Definitiva (`P23S_QE_ND`)
2. Quadro Escola - Nomeação Provisória (`P23S_QE_NP`)
3. QZP - Nomeação Definitiva (`P23S_QZP_ND`)
4. QZP - Nomeação Provisória (`P23S_QZP_NP`)
5. Quadro Agrupamento - Nomeação Definitiva (`P23S_QA_ND`)
6. Contratado (`P23S_CONT`)
7. Estagiário (`P23S_EST`)

#### 👨‍🏫 Professores do 1º Ciclo (6)
8. Quadro Escola - Nomeação Definitiva (`P1C_QE_ND`)
9. QZP - Nomeação Definitiva (`P1C_QZP_ND`)
10. QZP - Nomeação Provisória (`P1C_QZP_NP`)
11. Quadro Agrupamento - Nomeação Definitiva (`P1C_QA_ND`)
12. Quadro Escola - Nomeação Provisória (`P1C_QE_NP`)
13. Contratado (`P1C_CONT`)

#### 👶 Educadores de Infância (4)
14. QZP - Nomeação Definitiva (`EDU_INF_QZP_ND`)
15. QZP - Nomeação Provisória (`EDU_INF_QZP_NP`)
16. Quadro Agrupamento - Nomeação Definitiva (`EDU_INF_QA_ND`)
17. Quadro Escola - Nomeação Definitiva (`EDU_INF_QE_ND`)

#### ♿ Professores de Educação Especial (5)
18. Quadro Escola - Nomeação Definitiva (`PEE_QE_ND`)
19. Quadro Escola - Nomeação Provisória (`PEE_QE_NP`)
20. QZP - Nomeação Definitiva (`PEE_QZP_ND`)
21. Quadro Agrupamento - Nomeação Definitiva (`PEE_QA_ND`)
22. Contratado (`PEE_CONT`)

#### ➕ Outras (1)
23. Outra (`OUTRA`)

---

## 🔄 Alterações na Tabela `user`

### Antes
```sql
`categoria` VARCHAR(100) NULL
```

### Depois
```sql
`categoria` INT(11) UNSIGNED NULL,
CONSTRAINT `fk_user_categoria_professores` FOREIGN KEY (`categoria`) 
    REFERENCES `categoria_professores`(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
KEY `idx_categoria` (`categoria`)
```

---

## 🚀 Instruções de Implementação

### Opção 1: Usando Scripts SQL Diretos

**Passo 1:** Executar criação da tabela
```bash
mysql -u root -p sistema_gestao < CREATE_TABLE_CATEGORIA_PROFESSORES.sql
```

**Passo 2:** Executar alteração na tabela user
```bash
mysql -u root -p sistema_gestao < ALTER_USER_ADD_FK_CATEGORIA.sql
```

⚠️ **IMPORTANTE:** Antes de executar o Passo 2:
- Faça backup da base de dados
- Revise o mapeamento de categorias antigas nas linhas 44-62 do script
- Ajuste conforme os valores existentes na sua BD

### Opção 2: Usando Migrations do CodeIgniter 4

**Passo 1:** Executar migrations
```bash
php spark migrate
```

**Passo 2:** Verificar status
```bash
php spark migrate:status
```

**Passo 3:** Em caso de erro, fazer rollback
```bash
php spark migrate:rollback
```

---

## 📖 Uso do Sistema

### No Controller

```php
use App\Models\CategoriaProfessoresModel;

$categoriaModel = new CategoriaProfessoresModel();

// Obter categorias ativas
$categorias = $categoriaModel->getCategoriasAtivas();

// Obter por tipo de ensino
$categorias2Ciclo = $categoriaModel->getByTipoEnsino('2º e 3º Ciclos e Sec.');

// Obter para dropdown
$dropdown = $categoriaModel->getForDropdown();

// Passar para a view
return view('users/user_index', ['categorias' => $categorias]);
```

### Na View (PHP)

```php
<select class="form-select" name="categoria">
    <option value="">Selecione...</option>
    <?php foreach ($categorias as $categoria): ?>
        <option value="<?= $categoria['id'] ?>">
            <?= esc($categoria['nome']) ?>
        </option>
    <?php endforeach; ?>
</select>
```

### No UserModel

```php
// Obter utilizador com categoria
$user = $userModel->getUserWithRelations($userId);
echo $user['categoria_nome']; // Nome da categoria

// Obter utilizadores de uma categoria
$users = $userModel->getUsersByCategoria($categoriaId);

// Estatísticas por categoria
$stats = $userModel->getEstatisticasPorCategoria();
```

---

## 🔍 Métodos Disponíveis

### CategoriaProfessoresModel

| Método | Descrição | Retorno |
|--------|-----------|---------|
| `getCategoriasAtivas()` | Todas as categorias ativas | array |
| `getByTipoEnsino($tipo)` | Categorias por tipo de ensino | array |
| `getByTipoQuadro($tipo)` | Categorias por tipo de quadro | array |
| `getByCodigo($codigo)` | Categoria pelo código | array\|null |
| `getCategoriasAgrupadasPorTipoEnsino()` | Categorias agrupadas | array |
| `toggleStatus($id, $status)` | Ativar/desativar categoria | bool |
| `reordenar($ordens)` | Reordenar categorias | bool |
| `getEstatisticas()` | Estatísticas gerais | array |
| `getForDropdown()` | Array para dropdown/select | array |
| `pesquisar($termo)` | Pesquisa por nome/código | array |

### UserModel (Novos métodos)

| Método | Descrição | Retorno |
|--------|-----------|---------|
| `getUsersWithCategoria()` | Users com info de categoria | array |
| `getUserWithRelations($id)` | User com grupo e categoria | array\|null |
| `getUsersByCategoria($catId)` | Users de uma categoria | array |
| `getUsersByTipoEnsino($tipo)` | Users por tipo de ensino | array |
| `getEstatisticasPorCategoria()` | Estatísticas por categoria | array |

---

## 🧪 Testes Recomendados

### 1. Teste de Criação de Utilizador
```php
// Criar utilizador com categoria
$userData = [
    'name' => 'João Silva',
    'email' => 'joao@aejoaodebarros.pt',
    'categoria' => 1, // ID da categoria
    'level' => 1
];
$userModel->insert($userData);
```

### 2. Teste de Atualização
```php
// Atualizar categoria de utilizador
$userModel->update($userId, ['categoria' => 5]);
```

### 3. Teste de Delete Cascade
```php
// Verificar comportamento ON DELETE SET NULL
$categoriaModel->delete($categoriaId);
// A categoria nos utilizadores deve ficar NULL
```

### 4. Teste de Consultas com Join
```php
$user = $userModel->getUserWithRelations($userId);
print_r($user['categoria_nome']); // Deve mostrar nome da categoria
```

---

## 🔒 Segurança e Validação

### Validações Implementadas

**CategoriaProfessoresModel:**
- Nome: obrigatório, max 150 caracteres
- Código: obrigatório, único, max 20 caracteres
- Enums validados para valores permitidos

**UserModel:**
- Categoria: deve ser INT e existir na tabela categoria_professores
- Validação: `integer|is_not_unique[categoria_professores.id]`

### Constraints de FK

- **ON DELETE:** `SET NULL` - Evita perda de dados de utilizadores
- **ON UPDATE:** `CASCADE` - Mantém integridade referencial

---

## 📈 Melhorias Futuras Sugeridas

1. **Interface de Gestão de Categorias**
   - CRUD completo via web para categorias
   - Ordenação drag-and-drop
   - Ativar/desativar categorias

2. **Relatórios**
   - Dashboard com gráficos por categoria
   - Exportação de relatórios por tipo de ensino
   - Estatísticas de distribuição

3. **API RESTful**
   - Endpoints para gestão de categorias
   - Documentação com Swagger/OpenAPI

4. **Auditoria**
   - Log de alterações de categoria de utilizadores
   - Histórico de mudanças

5. **Importação/Exportação**
   - Importar categorias de CSV/Excel
   - Exportar relatórios formatados

---

## ⚠️ Notas Importantes

### Migração de Dados Existentes

Se já possui dados na tabela `user` com categorias antigas (VARCHAR):

1. **FAÇA BACKUP** da base de dados antes
2. Revise o mapeamento no script `ALTER_USER_ADD_FK_CATEGORIA.sql`
3. Ajuste as linhas 44-62 com os valores corretos
4. Teste primeiro em ambiente de desenvolvimento

### Exemplo de Mapeamento

```sql
-- Se tinha "PQND" como VARCHAR, mapear para ID da categoria
UPDATE `user_categoria_backup` ucb
INNER JOIN `categoria_professores` cp ON cp.codigo = 'P23S_QE_ND'
SET ucb.categoria_nova_id = cp.id
WHERE ucb.categoria_antiga = 'PQND';
```

### Rollback

Se precisar reverter as alterações:

**Via Migration:**
```bash
php spark migrate:rollback
php spark migrate:rollback
```

**Via SQL:**
1. Restaurar backup da base de dados
2. Ou executar manualmente:
```sql
ALTER TABLE `user` DROP FOREIGN KEY `fk_user_categoria_professores`;
DROP TABLE `categoria_professores`;
```

---

## 📞 Suporte

Para questões ou problemas:
1. Verificar logs: `writable/logs/`
2. Consultar documentação do CodeIgniter 4
3. Rever este documento

---

## ✅ Checklist de Implementação

- [ ] Backup da base de dados realizado
- [ ] Scripts SQL revisados e ajustados
- [ ] Migrations executadas com sucesso
- [ ] Models criados/atualizados
- [ ] Controller atualizado
- [ ] View atualizada
- [ ] Testes realizados
- [ ] Documentação atualizada
- [ ] Equipa informada das alterações

---

## 📝 Changelog

### v1.0 - 13/03/2026
- ✅ Criação da tabela `categoria_professores`
- ✅ Adição de FK na tabela `user`
- ✅ Criação do CategoriaProfessoresModel
- ✅ Atualização do UserModel com métodos de relação
- ✅ Atualização do UserController
- ✅ Atualização da view user_index.php
- ✅ 23 categorias de professores implementadas
- ✅ Migrations CodeIgniter 4 criadas
- ✅ Documentação completa

---

**Sistema de Gestão Escolar**  
**CodeIgniter 4 + AdminLTE**  
**© 2026 Agrupamento de Escolas João de Barros**
