# Sistema de Verificação de Calculadoras

## 📋 Visão Geral

Sistema que permite convocar uma equipa de professores para validar calculadoras científicas antes dos exames de Matemática e Física-Química. Segue o mesmo modelo dos Suplentes, permitindo convocação ad-hoc com número flexível de professores.

## ✨ Características

- ✅ **Exames virtuais** para verificação de calculadoras (não requerem alunos inscritos)
- ✅ **Convocação ad-hoc** - Adicionar/remover professores livremente até ao dia do exame
- ✅ **Número indeterminado** - Quantidade flexível de professores conforme necessidade
- ✅ **Alocação de salas** sem restrição de número de alunos
- ✅ **Sistema integrado** - Usa toda a infraestrutura existente de convocatórias
- ✅ **Função dedicada** - 'Verificar Calculadoras' já disponível na tabela convocatoria

## 🎯 Como Usar

### 1️⃣ Criar Sessão de Verificação de Calculadoras

1. Aceder a **Sec. Exames → Sessões de Exame**
2. Clicar em **Nova Sessão**
3. No formulário:
   - **Exame**: Selecionar `CALC-VER` (Verificação de Calculadoras)
   - **Fase**: Escolher fase apropriada (ex: 1ªfase, 2ªfase)
   - **Data**: Data do exame de Matemática/Física-Química
   - **Hora**: 
     - Recomendado: 1h antes do início do exame (ex: 08:00 para exame às 09:00)
     - Permite tempo suficiente para verificar todas as calculadoras
   - **Duração**: Tempo estimado para verificação (ex: 60-90 minutos)
   - **Tolerância**: 15-30 minutos
   - **Nº Alunos**: ⚠️ **Deixar em branco** (campo desabilitado automaticamente)
   - **Observações**: Ex: "Verificação de calculadoras - Exame de Matemática A 12º ano"

4. Guardar sessão

### 2️⃣ Alocar Local de Verificação

1. Na lista de sessões, clicar em **Alocar Salas**
2. Selecionar local onde será feita a verificação:
   - Entrada principal da escola
   - Sala de portaria
   - Sala específica designada
3. No campo **Nº de Alunos na Sala**, deixar 0
4. Guardar alocação

⚠️ **Nota**: Para verificação de calculadoras, não há validação de número de alunos

### 3️⃣ Convocar Professores Verificadores

1. Aceder a **Sec. Exames → Convocatórias/Vigilâncias**
2. Filtrar pela sessão de verificação criada
3. Clicar em **Convocar Vigilantes**
4. Adicionar professores:
   - **Função**: Será automaticamente "Verificar Calculadoras"
   - **Quantidade**: Flexível conforme necessidade (3-5 professores típico)
5. Enviar notificações

**Pode continuar a adicionar/remover professores até ao dia do exame!**

### 4️⃣ No Dia do Exame

1. Professores verificadores marcam presença no sistema
2. Posicionam-se no local designado (entrada/portaria)
3. Verificam calculadoras dos alunos:
   - Conferem se são modelos permitidos
   - Verificam se não têm funcionalidades proibidas (CAS, ligação à internet, etc.)
   - Aplicam autocolante/marcação de aprovação
4. Preenchem observações no sistema se necessário

## 📊 Exemplo Prático

### Cenário: Exames de Matemática - 15 de Junho de 2026

**Situação:**
- 09:00 - Matemática A (12º ano) - 150 alunos
- 09:00 - Matemática B (12º ano) - 80 alunos
- Total: 230 calculadoras para verificar

**Sessão de Verificação:**
- Código: CALC-VER
- Data: 15/06/2026
- Horário: 08:00 - 09:30 (1h30)
- Local: Entrada Principal
- Professores: 5 convocados
- Estimativa: ~2-3 minutos por calculadora = 230 × 2min ÷ 5 professores = ~90min

**Fluxo:**
1. **07:45** - Professores chegam e marcam presença
2. **08:00** - Início da verificação
3. **08:00-09:00** - Pico de chegada dos alunos
4. **09:00-09:30** - Retardatários e casos especiais
5. **09:30** - Fim da verificação

### Cenário: Física-Química - 10 de Julho de 2026

**Situação:**
- 14:00 - Física-Química A (11º ano) - 120 alunos

**Sessão de Verificação:**
- Código: CALC-VER
- Data: 10/07/2026
- Horário: 13:00 - 14:15 (1h15)
- Local: Portaria
- Professores: 3 convocados
- Número ajustável: Se notar que é pouco, pode adicionar mais até ao dia

## 🗄️ Estrutura de Base de Dados

### Tabela `exame`
- `tipo_prova` = 'Verificação Calculadoras' (novo valor no ENUM)
- `ano_escolaridade` = NULL para verificação de calculadoras
- Código: `CALC-VER`

### Tabela `sessao_exame`
- `num_alunos` = NULL ou 0 para verificação de calculadoras
- Duração típica: 60-90 minutos

### Tabela `convocatoria`
- `funcao` = 'Verificar Calculadoras' (já existente no ENUM)
- Funciona normalmente com todas as features:
  - Confirmação do professor
  - Marcação de presença
  - Notificações por email
  - Relatórios e estatísticas

## 🔧 Alterações Técnicas Implementadas

### 1. Base de Dados (`MIGRATION_VERIFICACAO_CALCULADORAS.sql`)
```sql
-- Adicionar tipo ao ENUM
ALTER TABLE exame 
MODIFY COLUMN tipo_prova ENUM(
    'Exame Nacional', 
    'Prova Final', 
    'MODa', 
    'Suplentes',
    'Verificação Calculadoras'
);

-- Criar exame virtual
INSERT INTO exame (codigo_prova, nome_prova, tipo_prova, ano_escolaridade, ativo) 
VALUES ('CALC-VER', 'Verificação de Calculadoras', 'Verificação Calculadoras', NULL, 1);
```

### 2. Controllers Atualizados

#### `ConvocatoriasController.php`
```php
// Linha ~125: Verificar tipo sem limite
$semLimite = ($exame && in_array($exame['tipo_prova'], ['Suplentes', 'Verificação Calculadoras']));
```

#### `SessaoExameSalaController.php`
```php
// Linhas ~97, ~310, ~398: Validação condicional de alunos
$semValidacaoAlunos = ($exame && in_array($exame['tipo_prova'], ['Suplentes', 'Verificação Calculadoras']));

if (!$semValidacaoAlunos) {
    // Validar número de alunos apenas para exames normais
}
```

#### `SessaoExameController.php`
```php
// Linha ~161: Detectar sessões especiais
$isSessaoEspecial = in_array($sessao['tipo_prova'], ['Suplentes', 'Verificação Calculadoras']);
```

### 3. Views Atualizadas

#### `sessoes_exame/index.php`
```javascript
// JavaScript: Desabilitar campo num_alunos
if (tipoProva === 'Suplentes' || tipoProva === 'Verificação Calculadoras') {
    $('#numAlunos').val('').prop('disabled', true);
    $('#numAlunosContainer small').html(`<span class="badge bg-info">Não aplicável</span>`);
}
```

#### `sessoes_exame/alocar_salas.php`
```php
// PHP: Detectar tipo especial
<?php $isTipoEspecial = in_array($sessao['tipo_prova'], ['Suplentes', 'Verificação Calculadoras']); ?>

// Campo num_alunos não obrigatório
<input type="number" <?php if (!$isTipoEspecial): ?>required<?php endif; ?>>
```

#### `sessoes_exame/detalhes.php`
```php
// PHP: Labels dinâmicos
if ($sessao['tipo_prova'] === 'Verificação Calculadoras') {
    $labelEspecial = 'Professores para Verificação';
}
```

## 📝 Queries Úteis

### Listar sessões de verificação de calculadoras
```sql
SELECT 
    se.id,
    se.data_exame,
    se.hora_exame,
    se.duracao_minutos,
    COUNT(c.id) as num_professores_convocados,
    SUM(CASE WHEN c.presenca = 'Presente' THEN 1 ELSE 0 END) as professores_presentes,
    se.observacoes
FROM sessao_exame se
JOIN exame e ON e.id = se.exame_id
LEFT JOIN convocatoria c ON c.sessao_exame_id = se.id
WHERE e.tipo_prova = 'Verificação Calculadoras'
    AND se.data_exame >= CURDATE()
GROUP BY se.id
ORDER BY se.data_exame, se.hora_exame;
```

### Ver professores convocados para verificação numa data específica
```sql
SELECT 
    c.id,
    u.name as professor,
    u.email,
    se.hora_exame,
    c.presenca,
    c.estado_confirmacao,
    c.observacoes,
    s.codigo_sala as local_verificacao
FROM convocatoria c
JOIN user u ON u.id = c.user_id
JOIN sessao_exame se ON se.id = c.sessao_exame_id
JOIN exame e ON e.id = se.exame_id
LEFT JOIN sessao_exame_sala ses ON ses.sessao_exame_id = se.id
LEFT JOIN salas s ON s.id = ses.sala_id
WHERE e.tipo_prova = 'Verificação Calculadoras'
    AND se.data_exame = '2026-06-15'
ORDER BY se.hora_exame, u.name;
```

### Estatísticas de verificação por professor
```sql
SELECT 
    u.name as professor,
    COUNT(c.id) as total_verificacoes,
    SUM(CASE WHEN c.presenca = 'Presente' THEN 1 ELSE 0 END) as presencas,
    SUM(CASE WHEN c.presenca = 'Falta' THEN 1 ELSE 0 END) as faltas
FROM convocatoria c
JOIN user u ON u.id = c.user_id
JOIN sessao_exame se ON se.id = c.sessao_exame_id
JOIN exame e ON e.id = se.exame_id
WHERE e.tipo_prova = 'Verificação Calculadoras'
    AND c.funcao = 'Verificar Calculadoras'
GROUP BY u.id, u.name
ORDER BY total_verificacoes DESC;
```

### Sessões de verificação com poucos professores (alerta)
```sql
SELECT 
    se.id,
    se.data_exame,
    se.hora_exame,
    se.observacoes,
    COUNT(c.id) as num_professores,
    CASE 
        WHEN COUNT(c.id) < 2 THEN 'CRÍTICO'
        WHEN COUNT(c.id) < 3 THEN 'ATENÇÃO'
        ELSE 'OK'
    END as status_equipa
FROM sessao_exame se
JOIN exame e ON e.id = se.exame_id
LEFT JOIN convocatoria c ON c.sessao_exame_id = se.id
WHERE e.tipo_prova = 'Verificação Calculadoras'
    AND se.data_exame >= CURDATE()
GROUP BY se.id
HAVING num_professores < 3
ORDER BY se.data_exame, se.hora_exame;
```

## 🔄 Rollback

Se necessário reverter as alterações:

```sql
-- Remover exames de verificação de calculadoras
DELETE FROM exame WHERE tipo_prova = 'Verificação Calculadoras';

-- Restaurar ENUM (remover 'Verificação Calculadoras')
ALTER TABLE exame 
MODIFY COLUMN tipo_prova ENUM(
    'Exame Nacional', 
    'Prova Final', 
    'MODa', 
    'Suplentes'
) NOT NULL;
```

⚠️ **Atenção**: O rollback apaga todas as sessões e convocatórias associadas devido ao `ON DELETE CASCADE`.

## ✅ Vantagens da Solução

1. **Zero alterações estruturais complexas** - Usa tabelas e funções existentes
2. **Função dedicada** - 'Verificar Calculadoras' já existia no ENUM de funções
3. **Interface familiar** - Usa mesmos formulários que secretariado já conhece
4. **Flexibilidade total** - Número indeterminado de professores, ajustável ad-hoc
5. **Manutenção simples** - Não cria sistemas paralelos
6. **Integração completa** - Todos os recursos disponíveis:
   - ✓ Notificações automáticas por email
   - ✓ Confirmação "Tomei Conhecimento"
   - ✓ Marcação de presenças
   - ✓ Relatórios e estatísticas
   - ✓ Histórico completo
   - ✓ Permissões e controlo de acesso

## 📐 Boas Práticas

### Quantos Professores Convocar?

**Regra Base:**
- Mínimo: 2 professores (redundância)
- Média: 3-5 professores
- Cálculo: `(Nº alunos × 2 minutos) ÷ (tempo disponível × nº professores)`

**Exemplo:**
- 150 alunos
- 60 minutos disponíveis
- Tempo por calculadora: 2 minutos
- Cálculo: (150 × 2) ÷ 60 = 5 professores

### Timing Recomendado

1. **Criar sessão**: 1-2 semanas antes do exame
2. **Convocar professores**: Imediatamente após criar sessão
3. **Enviar notificações**: 3-5 dias antes do exame
4. **Ajustar equipa**: Até ao dia anterior (se houver faltas)

### Organização no Dia

- **30min antes**: Professores chegam e marcam presença
- **Posicionamento**: Distribuir em filas/postos de verificação
- **Check-list**: Cada professor tem lista de calculadoras permitidas
- **Casos dúbios**: Professor responsável resolve

## 🔗 Diferenças vs Suplentes

| Aspecto | Suplentes | Verificação Calculadoras |
|---------|-----------|-------------------------|
| **Função** | Vigilância de substituição | Validação de equipamento |
| **Duração** | Período completo (3-5h) | Pré-exame (1-1.5h) |
| **Local** | Sala de espera | Entrada/Portaria |
| **Timing** | Durante o exame | Antes do exame |
| **Campo funcao** | 'Vigilante' ou 'Suplente' | 'Verificar Calculadoras' |

## 📅 Data de Implementação

**13 de Fevereiro de 2026**

## 📞 Contacto

Para questões técnicas ou sugestões de melhorias, contactar o administrador do sistema.

---

**Desenvolvido por:** GitHub Copilot  
**Baseado em:** Sistema de Suplentes Simplificado  
**Versão:** 1.0  
**Última Atualização:** 13/02/2026
