# Documentação: Sistema de Faltas com Desconto em Férias

## Data: 18/03/2026

## Tipos de Faltas Disponíveis

O sistema de gestão de férias suporta três tipos de faltas que podem ser descontadas nos dias de férias:

### 1. **Artigo 102.º do ECD** (Falta Justificada por Participação)
- **Categoria**: Justificada
- **Legislação**: Artigo 102.º - Estatuto da Carreira Docente
- **Descrição**: Faltas justificadas por participação em atividades previstas na legislação
- **Badge na interface**: Amarelo (warning)
- **Código no sistema**: `artigo_102`

### 2. **Artigo 134.º n.º3 do ECD** (Falta Justificada por Doença)
- **Categoria**: Justificada
- **Legislação**: Artigo 134.º n.º 3 - Estatuto da Carreira Docente
- **Descrição**: Faltas justificadas por motivos de saúde/doença
- **Badge na interface**: Amarelo (warning)
- **Código no sistema**: `artigo_134_n3`

### 3. **Falta Injustificada** (NOVO)
- **Categoria**: Injustificada
- **Legislação**: Falta sem justificação aceite
- **Descrição**: Faltas não justificadas ou cuja justificação não foi aceite
- **Badge na interface**: Vermelho (danger)
- **Código no sistema**: `injustificada`

## Estrutura da Tabela

```sql
CREATE TABLE ferias_faltas_desconto (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_nif INT(11) NOT NULL,
    anoletivo_id_falta INT(11) NOT NULL,
    anoletivo_id_desconto INT(11) NOT NULL,
    data_falta DATE NOT NULL,
    tipo_falta ENUM('artigo_102', 'artigo_134_n3', 'injustificada') NOT NULL,
    dias_desconto DECIMAL(5,2) NOT NULL DEFAULT 1.00,
    motivo TEXT NULL,
    observacoes TEXT NULL,
    registado_por INT(11) UNSIGNED NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Migração da Base de Dados

### Script de Migração
Execute o script: **ALTER_FERIAS_FALTAS_DESCONTO_ADD_INJUSTIFICADA.sql**

```bash
# Via phpMyAdmin:
# 1. Aceda ao phpMyAdmin
# 2. Selecione a base de dados
# 3. Vá a "SQL"
# 4. Cole o conteúdo do ficheiro ALTER_FERIAS_FALTAS_DESCONTO_ADD_INJUSTIFICADA.sql
# 5. Execute

# Via linha de comandos:
mysql -u username -p database_name < ALTER_FERIAS_FALTAS_DESCONTO_ADD_INJUSTIFICADA.sql
```

## Utilização na Interface

### Para Secretaria

#### Registar Nova Falta
1. Aceda a **Férias → Gerir Professor** → Selecione o professor
2. Vá ao separador **"Faltas"**
3. Clique em **"Registar Falta"**
4. Preencha o formulário:
   - **Data da Falta**: Data em que ocorreu
   - **Tipo de Falta**: Escolha entre:
     * Artigo 102.º do ECD (Justificada por participação)
     * Artigo 134.º n.º3 do ECD (Justificada por doença)
     * **Falta Injustificada** ← NOVO
   - **Dias a Descontar**: Normalmente 1.0 (permite meios dias: 0.5, 1.5, etc.)
   - **Ano Letivo do Desconto**: Ano onde será descontado
   - **Motivo/Descrição**: Breve descrição da falta

#### Visualizar Faltas
As faltas aparecem em duas secções na página do professor:

1. **Card "Descontos Aplicados ao Ano Atual"**:
   - Mostra faltas que descontam no ano atual
   - Cor da badge indica categoria (amarelo = justificada, vermelho = injustificada)

2. **Separador "Faltas"**:
   - Lista todas as faltas registadas
   - Badge colorido por categoria
   - Referência legal completa

### Para Professor

#### Visualizar as Suas Faltas
1. Aceda a **Férias** (página principal)
2. Expanda o card **"Cálculo Detalhado da Atribuição de Férias"**
3. Veja as secções:
   - **Faltas Justificadas por participação**: Lista com datas
   - **Faltas Injustificadas**: Lista com datas
   - **Total de faltas a descontar**: Soma de todas

## Métodos do Modelo (FeriasFaltasDescontoModel)

### Métodos Estáticos Disponíveis

```php
// Obter descrição legível do tipo de falta
$descricao = FeriasFaltasDescontoModel::getDescricaoTipoFalta('injustificada');
// Retorna: "Falta Injustificada"

// Verificar se falta é justificada
$isJustificada = FeriasFaltasDescontoModel::isFaltaJustificada('artigo_102');
// Retorna: true

// Obter categoria da falta
$categoria = FeriasFaltasDescontoModel::getCategoriaFalta('injustificada');
// Retorna: "injustificada"

// Obter referência legal
$ref = FeriasFaltasDescontoModel::getReferenciaLegal('artigo_102');
// Retorna: "Artigo 102.º - Estatuto da Carreira Docente"
```

## Cálculo de Dias Disponíveis

O cálculo dos dias disponíveis considera todas as faltas (justificadas e injustificadas):

```
Dias Disponíveis = Dias Base + Ajuste Ano Anterior + Extras - Dias Marcados - Total Faltas
```

Onde:
- **Total Faltas** = Soma de todas as faltas (justificadas + injustificadas)

### Exemplo Prático

```
Dias Base: 23
Ajuste Ano Anterior: +1 (24 atribuídos - 23 gozados)
Extras/Acertos: 0
--------------------------
Total Atribuído: 24

Dias Já Marcados: 0
Faltas Justificadas: 0.5 dias
Faltas Injustificadas: 1.0 dias
Total de Faltas: 1.5 dias
--------------------------

Dias Disponíveis = 24 - 0 - 1.5 = 22.5 dias
```

## Diferenciação nas Interfaces

### Badges de Cor
- **Justificadas**: Badge amarelo (`bg-warning`)
- **Injustificadas**: Badge vermelho (`bg-danger`)

### Agrupamento
As faltas são automaticamente agrupadas por categoria:
- **Faltas Justificadas por participação**: artigo_102 + artigo_134_n3
- **Faltas Injustificadas**: injustificada

### Informação Mostrada
Para cada falta, o sistema mostra:
- Descrição legível do tipo
- Data da ocorrência
- Motivo (se preenchido)
- Referência legal
- Dias a descontar
- Categoria (justificada/injustificada)

## Validação

O sistema valida:
- Tipo de falta deve ser: `artigo_102`, `artigo_134_n3` ou `injustificada`
- Dias de desconto > 0
- Data da falta obrigatória
- NIF do professor obrigatório
- Anos letivos obrigatórios

## Segurança

- Apenas níveis 3 e ≥ 6 podem registar faltas
- Regista quem criou a falta (`registado_por`)
- Timestamps automáticos (`criado_em`, `atualizado_em`)
- Foreign keys garantem integridade referencial

## Relatórios e Logs

Todas as operações com faltas são registadas em `ferias_log`:
- Criação de falta
- Eliminação de falta
- Quem executou a operação
- Data e hora da operação

## Notas Importantes

1. **Retroatividade**: Faltas podem ser registadas para anos anteriores e descontadas em anos futuros
2. **Meios Dias**: O sistema suporta descontos de meio dia (0.5, 1.5, etc.)
3. **Múltiplas Faltas**: Um professor pode ter várias faltas no mesmo ano
4. **Cálculo Automático**: O total é calculado automaticamente pelo sistema
5. **Diferenciação Clara**: Interface distingue visualmente faltas justificadas vs injustificadas

## Resolução de Problemas

### Erro: "Tipo de falta inválido"
**Solução**: Execute o script de migração ALTER_FERIAS_FALTAS_DESCONTO_ADD_INJUSTIFICADA.sql

### Faltas não aparecem na lista
**Solução**: Verifique se o `anoletivo_id_desconto` corresponde ao ano que está a visualizar

### Cálculo errado dos dias
**Solução**: Verifique se todas as faltas têm o campo `dias_desconto` preenchido corretamente

## Changelog

### Versão 2.0 - 18/03/2026
- ✅ Adicionado tipo de falta "injustificada" ao ENUM
- ✅ Criados métodos helper no modelo para categorização
- ✅ Atualizado formulário de registo com optgroup
- ✅ Melhorada apresentação visual com badges coloridos
- ✅ Atualizada documentação completa

### Versão 1.0 - Anterior
- Sistema base com artigo_102 e artigo_134_n3
