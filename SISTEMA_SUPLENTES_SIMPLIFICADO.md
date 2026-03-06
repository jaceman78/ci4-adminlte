# Sistema de Suplentes para Vigilâncias de Exames

## 📋 Visão Geral

Sistema que permite convocar professores suplentes para períodos (manhã/tarde) independentemente do exame específico. Os suplentes ficam numa sala de espera e são chamados quando necessário para substituir vigilantes ausentes.

## ✨ Características

- ✅ **Exames virtuais** de suplentes (não requerem alunos inscritos)
- ✅ **Convocação ad-hoc** - Adicionar/remover suplentes livremente até ao dia do exame
- ✅ **Dois períodos** - Manhã (SUP-MANHA) e Tarde (SUP-TARDE)
- ✅ **Alocação de salas** sem restrição de número de alunos
- ✅ **Sistema integrado** - Usa toda a infraestrutura existente de convocatórias

## 🎯 Como Usar

### 1️⃣ Criar Sessão de Suplentes

1. Aceder a **Sec. Exames → Sessões de Exame**
2. Clicar em **Nova Sessão**
3. No formulário:
   - **Exame**: Selecionar `SUP-MANHA` ou `SUP-TARDE`
   - **Fase**: Escolher fase apropriada (ex: Época Especial)
   - **Data**: Data do exame (ex: 20/07/2026)
   - **Hora**: 
     - Manhã: 08:30 (30min antes do início)
     - Tarde: 13:30 (30min antes do início)
   - **Duração**: Tempo total do período (ex: 300min = 5h)
   - **Tolerância**: 30 minutos
   - **Nº Alunos**: ⚠️ **Deixar em branco** (campo desabilitado automaticamente)
   - **Observações**: Ex: "Suplentes para período da manhã - Várias provas (Alemão, Francês, Espanhol)"

4. Guardar sessão

### 2️⃣ Alocar Sala de Espera

1. Na lista de sessões, clicar em **Alocar Salas**
2. Selecionar sala onde os suplentes vão aguardar
3. No campo **Nº de Alunos na Sala**, deixar 0 ou qualquer valor
4. Guardar alocação

⚠️ **Nota**: Para exames de suplentes, não há validação de número de alunos

### 3️⃣ Convocar Suplentes

1. Aceder a **Sec. Exames → Convocatórias/Vigilâncias**
2. Filtrar pela sessão de suplentes criada
3. Clicar em **Convocar Vigilantes**
4. Adicionar/remover professores à vontade (ad-hoc)
5. Enviar notificações

**Pode continuar a adicionar/remover suplentes até ao dia do exame!**

### 4️⃣ No Dia do Exame

1. Suplentes marcam presença na sala de espera
2. Quando necessário, secretariado designa suplente para substituir vigilante ausente
3. Sistema regista a substituição

## 📊 Exemplo Prático

### Cenário: 20 de Julho de 2026

**Manhã:**
- 09:00 - Matemática (150min)
- 09:00 - Português (150min)
- 09:00 - Física (120min)

**Sessão de Suplentes Manhã:**
- Código: SUP-MANHA
- Horário: 08:30 - 14:00 (5h30)
- Sala: Sala dos Professores
- Suplentes: 8 professores convocados
- Tipo: Ad-hoc (ajustável até ao dia)

**Tarde:**
- 14:00 - Alemão (120min)
- 14:00 - História (120min)

**Sessão de Suplentes Tarde:**
- Código: SUP-TARDE
- Horário: 13:30 - 17:00 (3h30)
- Sala: Sala dos Professores
- Suplentes: 5 professores convocados

## 🗄️ Estrutura de Base de Dados

### Tabela `exame`
- `tipo_prova` = 'Suplentes' (novo valor no ENUM)
- `ano_escolaridade` = NULL para suplentes
- Códigos: `SUP-MANHA` e `SUP-TARDE`

### Tabela `sessao_exame`
- `num_alunos` = NULL ou 0 para suplentes
- Duração longa (300+ minutos)

### Tabela `convocatoria`
- Funciona normalmente
- Professores convocados para sessão de suplentes

## 🔧 Alterações Técnicas

### 1. Base de Dados (`MIGRATION_EXAMES_SUPLENTES.sql`)
```sql
ALTER TABLE exame 
MODIFY COLUMN tipo_prova ENUM('Exame Nacional', 'Prova Final', 'MODa', 'Suplentes');

ALTER TABLE exame 
MODIFY COLUMN ano_escolaridade INT(2) NULL;

INSERT INTO exame (codigo_prova, nome_prova, tipo_prova, ano_escolaridade, ativo) 
VALUES 
('SUP-MANHA', 'Suplentes - Período da Manhã', 'Suplentes', NULL, 1),
('SUP-TARDE', 'Suplentes - Período da Tarde', 'Suplentes', NULL, 1);
```

### 2. Controller (`SessaoExameSalaController.php`)
- Adicionado `ExameModel`
- Validação condicional: ignora verificação de alunos se `tipo_prova = 'Suplentes'`
- Aplica-se aos métodos `store()` e `update()`

### 3. View (`sessoes_exame/index.php`)
- Campo `num_alunos` desabilitado automaticamente para exames de suplentes
- Badge informativo: "Não aplicável para Suplentes"
- Atributo `data-tipo` nas opções do select de exames

## 📝 Queries Úteis

### Listar sessões de suplentes
```sql
SELECT 
    se.id,
    e.nome_prova,
    se.data_exame,
    se.hora_exame,
    se.duracao_minutos,
    COUNT(c.id) as num_suplentes_convocados,
    SUM(CASE WHEN c.presenca = 'Presente' THEN 1 ELSE 0 END) as suplentes_presentes
FROM sessao_exame se
JOIN exame e ON e.id = se.exame_id
LEFT JOIN convocatoria c ON c.sessao_exame_id = se.id
WHERE e.tipo_prova = 'Suplentes'
GROUP BY se.id
ORDER BY se.data_exame, se.hora_exame;
```

### Ver suplentes de um período específico
```sql
SELECT 
    c.id,
    u.name,
    u.email,
    c.presenca,
    c.estado_confirmacao
FROM convocatoria c
JOIN user u ON u.id = c.user_id
JOIN sessao_exame se ON se.id = c.sessao_exame_id
JOIN exame e ON e.id = se.exame_id
WHERE e.tipo_prova = 'Suplentes'
    AND se.data_exame = '2026-07-20'
    AND e.codigo_prova = 'SUP-MANHA';
```

## 🔄 Rollback

Se necessário reverter as alterações:

```sql
-- Remover exames de suplentes
DELETE FROM exame WHERE tipo_prova = 'Suplentes';

-- Restaurar ENUM
ALTER TABLE exame 
MODIFY COLUMN tipo_prova ENUM('Exame Nacional', 'Prova Final', 'MODa') NOT NULL;

-- Restaurar ano_escolaridade NOT NULL
ALTER TABLE exame 
MODIFY COLUMN ano_escolaridade INT(2) NOT NULL;
```

## ✅ Vantagens da Solução

1. **Zero alterações à estrutura principal** - Usa tabelas existentes
2. **Sem código novo complexo** - Apenas ajustes de validação
3. **Interface familiar** - Usa mesmos formulários e listas
4. **Manutenção simples** - Não cria sistemas paralelos
5. **Flexibilidade total** - Secretariado controla número de suplentes
6. **Integração completa** - Marcação presença, relatórios, etc.

## 📅 Data de Implementação

**12 de Fevereiro de 2026**

---

**Desenvolvido por:** GitHub Copilot  
**Versão:** 1.0
