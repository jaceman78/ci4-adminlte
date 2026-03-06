# Sistema de Gestão de Reparações Externas

## 📋 Visão Geral

Este módulo permite a gestão completa de reparações de equipamentos enviados para entidades externas. Está integrado no braço **Kit Digital** da aplicação e é exclusivo para utilizadores com nível **7 (Técnico Sénior)** ou superior.

## 🔐 Controlo de Acesso

- **Nível Mínimo Requerido**: 7 (Técnico Sénior)
- **Localização**: Menu Kit Digital → Reparações Externas
- **Posição**: Abaixo do item "Estatísticas"

## 🎯 Funcionalidades Implementadas

### 1. Listagem e Visualização
- ✅ Tabela DataTables com todas as reparações
- ✅ Filtros por Estado, Tipologia e Tipo de Avaria
- ✅ Visualização de detalhes completos em modal
- ✅ Cards estatísticos no topo da página
- ✅ Cálculo automático de dias em reparação

### 2. Gestão de Registos
- ✅ Criar nova reparação através de formulário modal
- ✅ Editar reparações existentes
- ✅ Eliminar reparações (soft delete)
- ✅ Validação de dados obrigatórios

### 3. Importação/Exportação CSV
- ✅ Importação em lote via CSV
- ✅ Exportação de dados filtrados
- ✅ Download de template CSV com exemplo
- ✅ Validação de dados durante importação
- ✅ Relatório de erros na importação

### 4. Estatísticas e Relatórios
- ✅ Estatísticas gerais (total, em reparação, reparados, custo total)
- ✅ Gráficos por tipologia (pizza)
- ✅ Gráficos por tipo de avaria (barras)
- ✅ Tempo médio de reparação
- ✅ Taxa de sucesso das reparações

### 5. Sistema de Logs
- ✅ Registo automático de todas as ações (criar, editar, eliminar, importar, exportar)
- ✅ Rastreabilidade completa através do sistema de logs existente

## 📊 Campos da Tabela `reparacoes_externas`

### Campos Obrigatórios
- `n_serie_equipamento` - Número de série do equipamento
- `tipologia` - Tipo I, II ou III
- `possivel_avaria` - Tipo de avaria identificada
- `data_envio` - Data de envio para reparação
- `estado` - Estado atual da reparação

### Campos Opcionais
- `descricao_avaria` - Descrição detalhada da avaria
- `empresa_reparacao` - Nome da empresa reparadora
- `n_guia` - Número de guia/RMA
- `trabalho_efetuado` - Descrição do trabalho realizado
- `custo` - Custo em euros (decimal)
- `data_recepcao` - Data de receção do equipamento
- `observacoes` - Observações gerais

### Campos Automáticos
- `id_reparacao` - ID único (auto-incremento)
- `id_tecnico` - ID do técnico que registou
- `created_at` - Data de criação do registo
- `updated_at` - Data da última atualização
- `deleted_at` - Data de eliminação (soft delete)

## 📥 Instruções para Importação CSV

### Formato do Ficheiro

O ficheiro CSV deve conter as seguintes colunas **na ordem indicada**:

1. **Nº Série** - Obrigatório
2. **Tipologia** - Valores aceites: `Tipo I`, `Tipo II`, `Tipo III`
3. **Tipo Avaria** - Valores aceites: `Teclado`, `Monitor`, `Bateria`, `Disco`, `Sistema Operativo`, `CUCo`, `Gráfica`, `Outro`
4. **Descrição Avaria** - Texto livre
5. **Data Envio** - Formato: `YYYY-MM-DD` (ex: 2024-01-15)
6. **Empresa Reparação** - Nome da empresa
7. **Nº Guia** - Número de guia ou RMA
8. **Trabalho Efetuado** - Descrição do trabalho
9. **Custo (€)** - Número decimal (ex: 45.50)
10. **Data Receção** - Formato: `YYYY-MM-DD` ou deixar vazio
11. **Observações** - Texto livre
12. **Estado** - Valores aceites: `enviado`, `em_reparacao`, `reparado`, `irreparavel`, `cancelado`

### Exemplo de Linha CSV

```csv
ABC123456,Tipo I,Bateria,Bateria não carrega,2024-01-15,TechRepair Lda,GR2024001,Substituição de bateria,45.50,,Equipamento em garantia,enviado
```

### Codificação do Ficheiro

- **Encoding**: UTF-8 com BOM
- **Separador**: Vírgula (,)
- **Primeira Linha**: Cabeçalho (não será importado)

### Obter Template

1. Aceder à página de Reparações Externas
2. Clicar em **"Download Template"**
3. Abrir o ficheiro em Excel ou editor de texto
4. Preencher com os dados desejados
5. Guardar e importar

### Processo de Importação

1. Clicar no botão **"Importar CSV"**
2. Selecionar o ficheiro CSV
3. Clicar em **"Importar"**
4. Aguardar processamento
5. Verificar relatório de sucesso/erros

### Tratamento de Erros

O sistema valida cada linha e apresenta um relatório com:
- ✅ Número de registos importados com sucesso
- ❌ Número da linha com erro
- ❌ Descrição do erro encontrado

## 📤 Exportação de Dados

### Opções de Exportação

- **Exportar Todos**: Exporta todos os registos
- **Exportar Filtrados**: Exporta apenas registos que correspondem aos filtros ativos

### Formato do Ficheiro Exportado

- Ficheiro CSV com encoding UTF-8 + BOM
- Nome do ficheiro: `reparacoes_externas_YYYYMMDD_HHMMSS.csv`
- Inclui todos os campos da tabela

## 🎨 Interface e Usabilidade

### Cards Estatísticos
- **Total de Reparações**: Contador total
- **Em Reparação**: Reparações ativas
- **Reparados**: Reparações concluídas com sucesso
- **Custo Total**: Somatório de todos os custos

### Filtros Disponíveis
- Estado (Enviado, Em Reparação, Reparado, etc.)
- Tipologia (Tipo I, II, III)
- Tipo de Avaria (Teclado, Monitor, Bateria, etc.)

### Tabela DataTables
- **Paginação**: 25 registos por página (configurável)
- **Ordenação**: Por qualquer coluna
- **Pesquisa**: Global em todos os campos
- **Responsiva**: Adaptável a dispositivos móveis

### Badges de Estado
- 🔵 **Enviado** - Azul
- ⚠️ **Em Reparação** - Amarelo
- ✅ **Reparado** - Verde
- 🔴 **Irreparável** - Vermelho
- ⚫ **Cancelado** - Cinzento

## 🔄 Estados do Processo

1. **Enviado**: Equipamento foi enviado para reparação
2. **Em Reparação**: Reparação em curso
3. **Reparado**: Equipamento reparado com sucesso
4. **Irreparável**: Equipamento sem possibilidade de reparação
5. **Cancelado**: Processo cancelado

## 📈 Gráficos e Estatísticas

### Gráfico de Tipologias (Pizza)
Mostra distribuição de reparações por tipo (I, II, III)

### Gráfico de Avarias (Barras)
Mostra quantidade de cada tipo de avaria

### Métricas Calculadas
- **Tempo Médio**: Dias entre envio e receção
- **Taxa de Sucesso**: % de reparações bem-sucedidas
- **Custo Total**: Somatório de todos os custos

## 🔒 Segurança e Logs

### Controlo de Acesso
- Verificação de nível em todas as rotas
- Redirecionamento automático se acesso negado
- Mensagem de erro apropriada

### Sistema de Logs
Todas as ações são registadas:
- `create` - Criação de nova reparação
- `update` - Atualização de dados
- `delete` - Eliminação de registo
- `export` - Exportação de dados
- `import` - Importação de CSV
- `view_index` - Acesso à página principal

## 🛠️ Tecnologias Utilizadas

- **Backend**: CodeIgniter 4
- **Frontend**: Bootstrap 5, jQuery
- **DataTables**: Tabelas interativas
- **Chart.js**: Gráficos estatísticos
- **SweetAlert2**: Alertas e confirmações elegantes
- **Bootstrap Icons**: Ícones

## 📝 Rotas da API

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/reparacoes-externas` | Página principal |
| GET | `/reparacoes-externas/getData` | Dados para DataTable (AJAX) |
| GET | `/reparacoes-externas/getStats` | Estatísticas (AJAX) |
| POST | `/reparacoes-externas/create` | Criar nova reparação |
| POST | `/reparacoes-externas/update/{id}` | Atualizar reparação |
| GET | `/reparacoes-externas/getDetails/{id}` | Detalhes de uma reparação |
| POST | `/reparacoes-externas/delete/{id}` | Eliminar reparação |
| GET | `/reparacoes-externas/export` | Exportar CSV |
| POST | `/reparacoes-externas/import` | Importar CSV |
| GET | `/reparacoes-externas/downloadTemplate` | Download template CSV |

## 🎓 Notas de Implementação

### Model (`ReparacoesExternasModel`)
- Soft delete ativo
- Timestamps automáticos
- Validação de dados integrada
- Métodos auxiliares para estatísticas
- Suporte para importação CSV

### Controller (`ReparacoesExternasController`)
- Verificação de acesso em todos os métodos
- Logs automáticos de ações
- Respostas JSON para AJAX
- Tratamento de erros apropriado
- Suporte para filtros na exportação

### View (`reparacoes_externas/index.php`)
- Modais para todas as operações
- Validação client-side
- Feedback visual imediato
- Gráficos interativos
- Interface responsiva

## 🚀 Próximas Melhorias Sugeridas

1. **Dashboard Específico**: Painel com visão geral das reparações
2. **Notificações**: Alertas quando reparação demorar muito tempo
3. **Histórico**: Timeline de alterações em cada reparação
4. **Impressão**: Gerar PDF com detalhes da reparação
5. **Anexos**: Permitir upload de fotos/documentos
6. **API Externa**: Integração com sistemas de empresas reparadoras
7. **Relatórios Avançados**: Exportação para Excel com gráficos
8. **Comparação**: Análise de performance entre empresas reparadoras

---

**Desenvolvido para**: Sistema de Gestão Escolar AE João de Barros  
**Módulo**: Kit Digital - Reparações Externas  
**Versão**: 1.0  
**Data**: Janeiro 2026
