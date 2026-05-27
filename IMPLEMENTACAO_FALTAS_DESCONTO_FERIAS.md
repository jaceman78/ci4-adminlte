# Implementação do Sistema de Faltas que Descontam em Férias

## Objetivo
Implementar um sistema para registar faltas de professores que, ao abrigo do **Estatuto da Carreira Docente (ECD)**, podem ser descontadas nos dias de férias, seja no próprio ano letivo ou no ano seguinte.

## Enquadramento Legal
- **Artigo 102.º do ECD** - Faltas injustificadas ou por motivos não previstos
- **Artigo 134.º n.º3 do ECD** - Faltas que excedem limites estabelecidos

## Componentes Implementados

### 1. Base de Dados

#### Tabela: `ferias_faltas_desconto`
```sql
CREATE TABLE ferias_faltas_desconto (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_nif INT NOT NULL,
    anoletivo_id_falta INT UNSIGNED NOT NULL,
    anoletivo_id_desconto INT UNSIGNED NOT NULL,
    data_falta DATE NOT NULL,
    tipo_falta ENUM('artigo_102', 'artigo_134_n3') NOT NULL,
    dias_desconto DECIMAL(5,2) NOT NULL DEFAULT 1.00,
    motivo TEXT,
    observacoes TEXT,
    registado_por INT UNSIGNED,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Foreign Keys
    FOREIGN KEY (user_nif) REFERENCES user(NIF) ON DELETE CASCADE,
    FOREIGN KEY (anoletivo_id_falta) REFERENCES ano_letivo(id_anoletivo) ON DELETE CASCADE,
    FOREIGN KEY (anoletivo_id_desconto) REFERENCES ano_letivo(id_anoletivo) ON DELETE CASCADE,
    FOREIGN KEY (registado_por) REFERENCES user(id) ON DELETE SET NULL,
    -- Indexes
    INDEX idx_user_ano (user_nif, anoletivo_id_desconto),
    INDEX idx_data_falta (data_falta)
);
```

**Campos-chave:**
- `user_nif` - NIF do professor que teve a falta
- `anoletivo_id_falta` - Ano letivo em que a falta ocorreu
- `anoletivo_id_desconto` - Ano letivo onde o desconto será aplicado (pode ser diferente)
- `data_falta` - Data específica da falta
- `tipo_falta` - Base legal (artigo 102 ou 134 n.3)
- `dias_desconto` - Número de dias a descontar (suporta meio dia: 0.5, 1.5, etc.)

### 2. Model: `FeriasFaltasDescontoModel.php`

**Métodos principais:**
- `getFaltasPorProfessorEAno($userNif, $anoLetivoIdDesconto)` - Lista faltas de um professor para um ano
- `calcularTotalDescontoPorAno($userNif, $anoLetivoIdDesconto)` - Retorna total de dias a descontar
- `registarFalta($data)` - Cria novo registo de falta
- `getAllFaltas()` - Lista todas as faltas (para secretaria)
- `getDescricaoTipoFalta($tipoFalta)` - Retorna descrição legível do tipo (static)
- `getResumoFaltasPorAno($anoLetivoIdDesconto)` - Resumo agrupado por professor

### 3. Controller: `FeriasController.php`

**Novos endpoints implementados:**
- `GET /ferias/faltas` - Página de gestão de faltas (secretaria)
- `GET /ferias/faltas/listar` - Listar todas as faltas (AJAX)
- `GET /ferias/faltas/obter/:id` - Obter falta específica (AJAX)
- `POST /ferias/faltas/registar` - Criar nova falta (AJAX)
- `POST /ferias/faltas/atualizar/:id` - Atualizar falta (AJAX)
- `DELETE /ferias/faltas/eliminar/:id` - Eliminar falta (AJAX)

**Endpoints de suporte adicionados:**
- `GET /api/anos-letivos` - Lista anos letivos para selects
- `GET /api/professores` - Lista professores ativos para selects

### 4. View: `secretaria_faltas.php`

**Funcionalidades da interface:**
1. **Alertas informativos** com enquadramento legal (Artigos 102 e 134 n.3 do ECD)
2. **Botão para registar nova falta** - Abre modal com formulário completo
3. **Tabela de faltas registadas** com:
   - Dados do professor
   - Ano da falta e ano do desconto
   - Data da falta
   - Tipo de falta (badge colorido)
   - Dias a descontar
   - Motivo e observações
   - Quem registou e quando
   - Botões para editar/eliminar

4. **Pesquisa em tempo real** para filtrar faltas por professor

5. **Modal de registo/edição** com campos:
   - Select de professor (NIF e nome)
   - Select do ano letivo da falta
   - Date picker para data da falta
   - Select do tipo de falta (Artigo 102 ou 134 n.3)
   - Input numérico para dias a descontar (permite 0.5)
   - Select do ano letivo onde será descontado
   - Textarea para motivo (obrigatório)
   - Textarea para observações (opcional)

### 5. Integração com Cálculo de Saldo

#### FeriasAtribuicaoModel::calcularSaldo() - MODIFICADO
Agora inclui o desconto de faltas no cálculo:
```php
$diasDescontoFaltas = $faltasModel->calcularTotalDescontoPorAno($userNif, $anoLetivoId);
$diasDisponiveis = $diasTotal - $diasGastos - $diasDescontoFaltas;

return [
    'dias_total' => $diasTotal,
    'dias_gastos' => $diasGastos,
    'dias_desconto_faltas' => $diasDescontoFaltas,  // NOVO
    'dias_disponiveis' => $diasDisponiveis,
    // ...
];
```

#### View marcar.php - ATUALIZADA
O breakdown de dias atribuídos agora mostra:
- Dias base (deste ano)
- Dias de anos anteriores (ajustes)
- **Desconto por faltas** (NOVO) - apenas se > 0, em vermelho com ícone

### 6. Menu de Navegação

Adicionada opção no menu "Ações Rápidas" da secretaria:
```
📋 Ações Rápidas
  ├─ Atribuir Dias de Férias
  ├─ Pedidos Pendentes
  ├─ ⚠️ Faltas (Desconto em Férias) [NOVO]
  ├─ Calendário de Férias
  └─ Relatórios
```

## Fluxo de Uso

### Para a Secretaria:
1. **Aceder** a "Faltas (Desconto em Férias)" no menu
2. **Clicar** em "Registar Nova Falta"
3. **Preencher formulário**:
   - Selecionar professor
   - Indicar quando ocorreu a falta (ano + data)
   - Escolher tipo legal (Artigo 102 ou 134 n.3)
   - Definir quantos dias descontar (pode ser 0.5)
   - Indicar em que ano será descontado
   - Descrever motivo
4. **Submeter** - O registo fica guardado
5. **Editar/Eliminar** faltas se necessário

### Para o Professor:
Ao aceder à página de marcação de férias:
- Vê automaticamente o desconto aplicado no breakdown de "Dias Atribuídos"
- O cálculo de "Dias Disponíveis" já desconta as faltas
- **Não pode marcar mais dias do que o saldo disponível** (já com desconto aplicado)

## Validações Implementadas

### Backend (Controller):
- Apenas secretaria pode aceder (level >= 3, exceto 4-5)
- Campos obrigatórios: professor, anos letivos, data, tipo, dias, motivo
- Tipo de falta deve ser 'artigo_102' ou 'artigo_134_n3'
- Dias a descontar entre 0.5 e 365

### Frontend (JavaScript):
- Formulário HTML5 validation
- SweetAlert2 para confirmações e feedback
- Validação antes de submeter (checkValidity)

### Database:
- Foreign keys garantem integridade referencial
- ENUM tipo_falta garante valores válidos
- DECIMAL(5,2) para dias_desconto permite meio dia

## Características Especiais

1. **Flexibilidade temporal**: A falta pode ocorrer num ano e ser descontada noutro
   - Exemplo: Falta em 2024/2025, desconto em 2025/2026

2. **Suporte a meio dia**: Campo dias_desconto aceita decimais (0.5, 1.5, 2.5, etc.)

3. **Auditoria**: Registo de quem criou/modificou e quando (criado_em, atualizado_em, registado_por)

4. **Integração automática**: O desconto é automaticamente aplicado ao calcular saldo disponível

5. **Interface intuitiva**: Badges coloridos para tipos de faltas, pesquisa em tempo real, modais responsivos

## Segurança

- **Autenticação**: Apenas utilizadores autenticados com níveis >= 3 (exceto 4-5)
- **Autorização**: Validação em cada endpoint (controller)
- **CSRF Protection**: CodeIgniter 4 built-in
- **SQL Injection**: Uso de Query Builder e prepared statements
- **XSS**: Escapamento automático nas views do CI4

## Performance

- **Indexes** na tabela para queries rápidas:
  - `idx_user_ano` para lookups por professor e ano
  - `idx_data_falta` para ordenação por data
  
- **JOINs otimizados** no model para evitar N+1 queries

## Próximas Melhorias (Opcional)

1. ✅ **Relatório de faltas** - Dashboard com estatísticas
2. ✅ **Notificações ao professor** - Email quando falta é registada
3. ✅ **Histórico de alterações** - Log de edições e eliminações
4. ✅ **Exportação para Excel** - Lista de faltas para arquivo
5. ✅ **Bulk import** - Importar múltiplas faltas via CSV
6. ✅ **PDF de comprovativo** - Documento oficial do registo

## Arquivos Criados/Modificados

### ✅ Criados:
- `CREATE_TABLE_FERIAS_FALTAS_DESCONTO.sql` - Migration table
- `app/Models/FeriasFaltasDescontoModel.php` - Model com lógica de negócio
- `app/Views/ferias/secretaria_faltas.php` - Interface de gestão
- `IMPLEMENTACAO_FALTAS_DESCONTO_FERIAS.md` - Esta documentação

### ✅ Modificados:
- `app/Controllers/FeriasController.php` - Adicionado $faltasDescontoModel e 8 métodos novos
- `app/Models/FeriasAtribuicaoModel.php` - calcularSaldo() inclui desconto de faltas
- `app/Views/ferias/marcar.php` - Breakdown mostra desconto de faltas
- `app/Views/ferias/secretaria_index.php` - Menu com link para faltas
- `app/Config/Routes.php` - 6 rotas de faltas + 2 APIs

## Status: ✅ IMPLEMENTADO E FUNCIONAL

Data de conclusão: 16 de Março de 2025
Desenvolvido por: Copilot (Claude Sonnet 4.5)
