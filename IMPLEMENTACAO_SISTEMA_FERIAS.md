# 📚 SISTEMA DE GESTÃO DE FÉRIAS - DOCUMENTAÇÃO COMPLETA

**Data de Implementação:** 07/03/2026  
**Versão:** 1.0  
**Sistema:** CodeIgniter 4 + AdminLTE

---

## 📋 ÍNDICE

1. [Visão Geral](#visão-geral)
2. [Estrutura de Tabelas](#estrutura-de-tabelas)
3. [Models Criados](#models-criados)
4. [Controller e Rotas](#controller-e-rotas)
5. [Helper de Funções](#helper-de-funções)
6. [Instalação e Configuração](#instalação-e-configuração)
7. [Fluxo de Trabalho](#fluxo-de-trabalho)
8. [Emails e Notificações](#emails-e-notificações)
9. [Geração de PDF](#geração-de-pdf)
10. [Segurança e Permissões](#segurança-e-permissões)
11. [Manutenção e Logs](#manutenção-e-logs)
12. [Referência de Estados](#referência-de-estados)

---

## 🎯 VISÃO GERAL

O Sistema de Gestão de Férias permite que professores marquem períodos de férias dentro do ano letivo ativo, que são posteriormente aprovados pela secretaria. O sistema inclui:

- ✅ **Atribuição de dias** de férias pela secretaria
- ✅ **Marcação de períodos** pelos professores
- ✅ **Cálculo automático** de dias úteis (excluindo fins de semana e feriados)
- ✅ **Aprovação** com geração de PDF
- ✅ **Upload** de documento assinado
- ✅ **Transição** de dias não usados para ano seguinte
- ✅ **Notificações** por email em todas as etapas
- ✅ **Histórico completo** de ações

---

## 🗄️ ESTRUTURA DE TABELAS

### 1. `ferias_atribuicao`

Gestão de dias de férias atribuídos por professor e ano letivo.

```sql
CREATE TABLE ferias_atribuicao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_nif VARCHAR(20) NOT NULL,              -- FK para user.NIF
    anoletivo_id INT UNSIGNED NOT NULL,         -- FK para ano_letivo.id_anoletivo
    dias_base INT NOT NULL DEFAULT 22,          -- Dias base (normalmente 22)
    dias_ajuste INT NOT NULL DEFAULT 0,         -- Ajuste do ano anterior (+ ou -)
    dias_extra INT NOT NULL DEFAULT 0,          -- Dias extra (+ ou -)
    dias_total INT GENERATED ALWAYS AS (dias_base + dias_ajuste + dias_extra) STORED,
    observacoes TEXT NULL,
    atribuido_por INT NULL,                     -- FK para user.id
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_ano (user_nif, anoletivo_id),
    FOREIGN KEY (user_nif) REFERENCES user(NIF) ON DELETE CASCADE,
    FOREIGN KEY (anoletivo_id) REFERENCES ano_letivo(id_anoletivo) ON DELETE CASCADE,
    FOREIGN KEY (atribuido_por) REFERENCES user(id) ON DELETE SET NULL
);
```

**Campos Importante:**
- `dias_total`: Campo calculado automaticamente (dias_base + dias_ajuste + dias_extra)
- `dias_ajuste`: Pode ser negativo (ex: -2 se gastou mais no ano anterior)

### 2. `ferias_pedido`

Pedidos de férias submetidos pelos professores.

```sql
CREATE TABLE ferias_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_nif VARCHAR(20) NOT NULL,
    anoletivo_id INT UNSIGNED NOT NULL,
    estado ENUM('por_preencher','submetido','em_aprovacao','aprovado','rejeitado','cancelado','aguarda_assinatura','concluido') DEFAULT 'por_preencher',
    total_dias INT DEFAULT 0,
    documento_pdf VARCHAR(255) NULL,
    documento_assinado VARCHAR(255) NULL,
    observacoes_professor TEXT NULL,
    observacoes_secretaria TEXT NULL,
    submetido_em DATETIME NULL,
    aprovado_em DATETIME NULL,
    aprovado_por INT NULL,
    rejeitado_em DATETIME NULL,
    rejeitado_por INT NULL,
    upload_assinatura_em DATETIME NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_nif) REFERENCES user(NIF) ON DELETE CASCADE,
    FOREIGN KEY (anoletivo_id) REFERENCES ano_letivo(id_anoletivo) ON DELETE CASCADE
);
```

### 3. `ferias_periodo`

Períodos individuais dentro de um pedido (um pedido pode ter múltiplos períodos).

```sql
CREATE TABLE ferias_periodo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    dias_uteis INT NOT NULL,
    observacoes TEXT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES ferias_pedido(id) ON DELETE CASCADE
);
```

### 4. `ferias_log` (Opcional)

Histórico de todas as ações realizadas.

```sql
CREATE TABLE ferias_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NULL,
    user_nif VARCHAR(20) NULL,
    acao VARCHAR(100) NOT NULL,
    detalhes TEXT NULL,
    realizado_por INT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES ferias_pedido(id) ON DELETE CASCADE,
    FOREIGN KEY (user_nif) REFERENCES user(NIF) ON DELETE CASCADE,
    FOREIGN KEY (realizado_por) REFERENCES user(id) ON DELETE SET NULL
);
```

### 5. `ferias_feriados` (Feriados de Portugal)

```sql
CREATE TABLE ferias_feriados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data DATE NOT NULL UNIQUE,
    descricao VARCHAR(255) NOT NULL,
    tipo ENUM('fixo', 'movel', 'municipal') DEFAULT 'fixo',
    ativo TINYINT(1) DEFAULT 1,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 📊 VIEW: `view_ferias_resumo`

View útil para relatórios:

```sql
CREATE OR REPLACE VIEW view_ferias_resumo AS
SELECT 
    fa.user_nif,
    u.name AS nome_professor,
    fa.anoletivo_id,
    al.anoletivo AS ano,
    fa.dias_total AS dias_disponiveis,
    COALESCE(SUM(CASE WHEN fp.estado IN ('aprovado', 'aguarda_assinatura', 'concluido') 
                      THEN fp.total_dias ELSE 0 END), 0) AS dias_gastos,
    fa.dias_total - COALESCE(SUM(...)) AS dias_restantes
FROM ferias_atribuicao fa
    INNER JOIN user u ON u.NIF = fa.user_nif
    INNER JOIN ano_letivo al ON al.id_anoletivo = fa.anoletivo_id
    LEFT JOIN ferias_pedido fp ON fp.user_nif = fa.user_nif AND fp.anoletivo_id = fa.anoletivo_id
GROUP BY fa.user_nif, fa.anoletivo_id;
```

---

## 📦 MODELS CRIADOS

### 1. FeriasAtribuicaoModel
**Ficheiro:** `app/Models/FeriasAtribuicaoModel.php`

**Métodos principais:**
- `getAtribuicaoProfessor($userNif, $anoLetivoId)` - Obter atribuição de um professor
- `atribuirFerias(...)` - Atribuir/atualizar dias de férias
- `calcularDiasGastos($userNif, $anoLetivoId)` - Calcular dias já gastos
- `calcularSaldo($userNif, $anoLetivoId)` - Retorna saldo completo (total, gastos, disponíveis)
- `getProfessoresSemAtribuicao($anoLetivoId)` - Listar professores sem atribuição
- `copiarAtribuicoesAnoAnterior(...)` - Copiar atribuições para novo ano letivo

### 2. FeriasPedidoModel
**Ficheiro:** `app/Models/FeriasPedidoModel.php`

**Métodos principais:**
- `getPedidosProfessor($userNif, $anoLetivoId)` - Listar pedidos do professor
- `getPedidosPendentes($anoLetivoId)` - Pedidos pendentes de aprovação
- `getPedidoComPeriodos($pedidoId)` - Obter pedido completo com períodos
- `criarPedido(...)` - Criar novo pedido com múltiplos períodos
- `aprovarPedido($pedidoId, $aprovadoPor, $observacoes)` - Aprovar pedido
- `rejeitarPedido($pedidoId, $rejeitadoPor, $motivo)` - Rejeitar pedido
- `marcarAguardaAssinatura($pedidoId, $caminhoDocumento)` - Marcar PDF gerado
- `registrarDocumentoAssinado($pedidoId, $caminhoDocumento)` - Registar assinado
- `getEstatisticas($anoLetivoId)` - Estatísticas de pedidos

### 3. FeriasPeriodoModel
**Ficheiro:** `app/Models/FeriasPeriodoModel.php`

**Métodos principais:**
- `getPeriodosPorPedido($pedidoId)` - Obter períodos de um pedido
- `verificarConflito($userNif, $dataInicio, $dataFim)` - Verificar conflitos de datas
- `getPeriodosPorIntervalo($dataInicio, $dataFim)` - Períodos num intervalo (calendário)

### 4. FeriasLogModel
**Ficheiro:** `app/Models/FeriasLogModel.php`

**Métodos principais:**
- `registrarAcao(...)` - Registar ação no log
- `getHistoricoPedido($pedidoId)` - Histórico de um pedido
- `getHistoricoProfessor($userNif)` - Histórico de um professor

### 5. FeriasFeriadosModel
**Ficheiro:** `app/Models/FeriasFeriadosModel.php`

**Métodos principais:**
- `getFeriadosPorIntervalo($dataInicio, $dataFim)` - Obter feriados num intervalo
- `isFeriado($data)` - Verificar se data é feriado
- `adicionarFeriadosPortugal($ano)` - Popular feriados de um ano

---

## 🎮 CONTROLLER E ROTAS

### FeriasController
**Ficheiro:** `app/Controllers/FeriasController.php`

### Rotas para Professores:
```php
GET  /ferias                    → index()                    // Página principal
GET  /ferias/marcar             → marcar()                   // Página marcar férias
POST /ferias/submeter           → submeterPedido()           // Submeter pedido
GET  /ferias/download/:id       → downloadDocumento()        // Download PDF
POST /ferias/upload-documento/:id → uploadDocumentoAssinado() // Upload assinado
```

### Rotas para Secretaria (Level 3+):
```php
GET  /ferias/secretaria          → secretaria()              // Dashboard
GET  /ferias/atribuir            → atribuir()                // Atribuir dias
POST /ferias/salvar-atribuicao   → salvarAtribuicao()        // Salvar atribuição
POST /ferias/aprovar/:id         → aprovarPedido()           // Aprovar
POST /ferias/rejeitar/:id        → rejeitarPedido()          // Rejeitar
```

### API/AJAX:
```php
POST /ferias/calcular-dias       → calcularDiasUteis()       // Calcular dias úteis
GET  /ferias/feriados/:ano       → getFeriados()             // Obter feriados
```

---

## 🛠️ HELPER DE FUNÇÕES

**Ficheiro:** `app/Helpers/ferias_helper.php`

### Funções Disponíveis:

```php
// Cálculo de dias úteis
calcular_dias_uteis($dataInicio, $dataFim, $incluirFeriados = true)

// Validação de período
validar_periodo_ferias($dataInicio, $dataFim, $anoLetivoAtual = null)

// Verificação de saldo
verificar_saldo_ferias($userNif, $anoLetivoId, $diasSolicitados)

// Formatação de estado (HTML)
formatar_estado_ferias($estado)

// Verificação de conflitos
verificar_conflito_ferias($userNif, $dataInicio, $dataFim, $excluirPedidoId = null)

// Obter períodos do ano
obter_periodos_ferias_ano($anoLetivoId)

// Cálculo de saldo final
calcular_saldo_final_ano($userNif, $anoLetivoId)

// Envio de email
enviar_email_ferias($destinatario, $assunto, $mensagem, $anexos = [])

// Geração de calendário
gerar_array_calendario($mes, $ano, $periodos = [])
```

### Carregar o Helper:

No controller:
```php
helper('ferias');
```

Ou automaticamente em `app/Controllers/BaseController.php`:
```php
protected $helpers = ['ferias'];
```

---

## ⚙️ INSTALAÇÃO E CONFIGURAÇÃO

### PASSO 1: Criar Tabelas

```bash
# Executar no phpMyAdmin ou MySQL Workbench
mysql -u root -p sistema_gestao < CREATE_SISTEMA_FERIAS.sql
```

Ou copiar e colar o conteúdo de `CREATE_SISTEMA_FERIAS.sql` no phpMyAdmin.

### PASSO 2: Verificar Estrutura

```sql
-- Verificar tabelas criadas
SHOW TABLES LIKE 'ferias%';

-- Resultado esperado:
-- ferias_atribuicao
-- ferias_pedido
-- ferias_periodo
-- ferias_log
-- ferias_feriados
```

### PASSO 3: Popular Feriados

Os feriados de 2026 já são inseridos automaticamente pelo script SQL. Para anos futuros:

```php
// No controller ou via terminal
$feriadosModel = new \App\Models\FeriasFeriadosModel();
$feriadosModel->adicionarFeriadosPortugal(2027);
```

### PASSO 4: Configurar Email

Já deve estar configurado. Verificar em `app/Config/Email.php`:

```php
public string $fromEmail = 'noreply@seudominio.pt';
public string $fromName  = 'Sistema de Gestão Escolar';
public string $protocol  = 'smtp';
// ... configurações SMTP
```

### PASSO 5: Instalar DomPDF (para geração de PDF)

```bash
composer require dompdf/dompdf
```

### PASSO 6: Criar Pasta Public

```bash
# A pasta public/<nif> será criada automaticamente
# mas garantir permissões:
chmod 755 public/
```

### PASSO 7: Personalizar Dados da Escola

**NOVO!** O sistema inclui ficheiro de configuração personalizável.

Editar `app/Config/FeriasConfig.php` com os dados da sua escola:

```php
<?php
namespace Config;
use CodeIgniter\Config\BaseConfig;

class FeriasConfig extends BaseConfig
{
    // Dados da escola (aparecem no PDF)
    public string $escola_codigo = '171268';
    public string $escola_nome = 'Agrupamento de Escolas João de Barros';
    public string $escola_endereco = '460050 - Escola Secundária João de Barros, 2855 - 713 Corroios';
    public string $escola_nmec = '212589180';
    public string $escola_tel = '212531167';
    public string $escola_contrib = '600078422';
    
    // Categoria padrão dos professores
    public string $categoria_default = 'Professores do 2º e 3º Ciclos e Sec. - Quadro Escola - Nomeação';
    
    // Parâmetros do sistema
    public int $dias_base_default = 22;
    public int $max_dias_ajuste_positivo = 5;
    public int $max_dias_ajuste_negativo = -5;
    // ... outras configurações
}
```

**Personalização Completa:**
- 📄 Ver guia completo: [PERSONALIZACAO_DOCUMENTO_FERIAS.md](PERSONALIZACAO_DOCUMENTO_FERIAS.md)
- 🎨 Como adicionar logo da escola
- 📝 Como adicionar campos customizados
- 🔧 Como alterar formatação do PDF

---

## 🔄 FLUXO DE TRABALHO

### 1️⃣ ATRIBUIÇÃO (Secretaria)

1. Secretaria acede a `/ferias/atribuir`
2. Seleciona professor e define:
   - **Dias Base**: 22 (padrão)
   - **Dias Ajuste**: do ano anterior (+ ou -)
   - **Dias Extra**: ajuste manual
3. Sistema calcula **Dias Total** automaticamente
4. Professor recebe email de notificação

### 2️⃣ MARCAÇÃO (Professor)

1. Professor acede a `/ferias`
2. Visualiza saldo disponível
3. Clica em "Marcar Período de Férias"
4. Seleciona um ou mais períodos:
   - Data início
   - Data fim
   - Sistema calcula dias úteis automaticamente
5. Adiciona observações (opcional)
6. Submete pedido
7. Sistema valida:
   - ✅ Períodos dentro do ano letivo
   - ✅ Sem conflitos com outros períodos
   - ✅ Saldo suficiente
8. Estado muda para **"submetido"**
9. Secretaria recebe email de notificação

### 3️⃣ APROVAÇÃO (Secretaria)

1. Secretaria acede a `/ferias/secretaria`
2. Visualiza pedidos pendentes
3. Clica em "Aprovar" ou "Rejeitar"

**Se APROVAR:**
4. Estado muda para **"aprovado"**
5. **PDF é gerado automaticamente**
6. Estado muda para **"aguarda_assinatura"**
7. Professor recebe email com PDF anexado

**Se REJEITAR:**
4. Indica motivo da rejeição
5. Estado muda para **"rejeitado"**
6. Professor recebe email com motivo

### 4️⃣ ASSINATURA (Professor)

1. Professor faz **download** do PDF
2. **Imprime e assina** fisicamente
3. **Digitaliza** (scanner/foto)
4. Faz **upload** do documento assinado
5. Estado muda para **"concluido"**
6. Secretaria recebe notificação

---

## 📧 EMAILS E NOTIFICAÇÕES

### Templates de Email

Os templates estão em `app/Views/emails/`:

1. **ferias_atribuicao.php** - Notificação de atribuição
2. **ferias_novo_pedido.php** - Novo pedido submetido
3. **ferias_aprovado.php** - Pedido aprovado (com PDF)
4. **ferias_rejeitado.php** - Pedido rejeitado

### Exemplo de Envio:

```php
// Usando o helper
enviar_email_ferias(
    'professor@escola.pt',
    'Pedido de Férias Aprovado',
    view('emails/ferias_aprovado', ['pedido' => $pedido]),
    [FCPATH . 'public/123456789/ferias_pedido_1.pdf'] // Anexo
);
```

---

## 📄 GERAÇÃO DE PDF

### Template do PDF

**Ficheiro:** `app/Views/ferias/documento_pdf.php`

Contém:
- Dados do professor
- Lista de períodos solicitados
- Total de dias úteis
- Espaço para assinatura

### Geração no Controller:

```php
private function gerarPDFFerias($pedidoId)
{
    $pedido = $this->pedidoModel->getPedidoComPeriodos($pedidoId);
    
    // Gerar HTML
    $html = view('ferias/documento_pdf', ['pedido' => $pedido]);
    
    // Gerar PDF com DomPDF
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    // Salvar
    $caminho = FCPATH . "public/{$pedido['user_nif']}/ferias_pedido_{$pedidoId}.pdf";
    file_put_contents($caminho, $dompdf->output());
    
    return "public/{$pedido['user_nif']}/ferias_pedido_{$pedidoId}.pdf";
}
```

---

## 🔐 SEGURANÇA E PERMISSÕES

### Níveis de Acesso:

| Nível | Papel | Permissões |
|-------|-------|-----------|
| 1 | Professor | Ver próprias férias, marcar períodos, upload assinado |
| 3+ | Secretaria | Atribuir dias, aprovar/rejeitar pedidos, gerar PDF |
| 6+ | Direção | Visualizar relatórios gerais |
| 8+ | Admin | Acesso total |

### Implementação no Controller:

```php
// Verificar nível mínimo
$userData = session()->get('LoggedUserData');
if ($userData['level'] < 3) {
    return redirect()->to('/dashboard')->with('error', 'Acesso negado');
}

// Verificar proprietário (professor só acede aos próprios dados)
if ($userLevel < 3 && $pedido['user_nif'] != $userData['NIF']) {
    return $this->failUnauthorized('Sem permissão');
}
```

---

## 📊 MANUTENÇÃO E LOGS

### Consultas Úteis:

```sql
-- Professores sem atribuição no ano ativo
SELECT u.NIF, u.name
FROM user u
WHERE u.level = 1 AND u.status = 1
  AND u.NIF NOT IN (
    SELECT fa.user_nif 
    FROM ferias_atribuicao fa 
    WHERE fa.anoletivo_id = (SELECT id_anoletivo FROM ano_letivo WHERE status = 1)
  );

-- Pedidos pendentes há mais de 7 dias
SELECT * FROM ferias_pedido
WHERE estado IN ('submetido', 'em_aprovacao')
  AND DATEDIFF(NOW(), submetido_em) > 7;

-- Professores com saldo negativo
SELECT * FROM view_ferias_resumo
WHERE dias_restantes < 0;
```

### Limpar Logs Antigos:

```php
// Manter logs dos últimos 365 dias
$logModel = new \App\Models\FeriasLogModel();
$deletados = $logModel->limparLogsAntigos(365);
```

### Transição de Ano Letivo:

```php
// Ao ativar novo ano letivo (ex: 2027)
$atribuicaoModel = new \App\Models\FeriasAtribuicaoModel();

// Copiar atribuições do ano anterior e aplicar saldo
$anoAnterior = 5;  // ID do ano letivo 2025/2026
$anoNovo = 6;      // ID do ano letivo 2026/2027
$userId = session()->get('user_id');

$copiados = $atribuicaoModel->copiarAtribuicoesAnoAnterior($anoAnterior, $anoNovo, $userId);
```

---

## 📌 REFERÊNCIA DE ESTADOS

### Estados do Pedido:

| Estado | Descrição | Ações Disponíveis |
|--------|-----------|-------------------|
| `por_preencher` | Pedido criado mas não submetido | Professor: Editar, Submeter |
| `submetido` | Pedido submetido aguardando análise | Secretaria: Aprovar, Rejeitar |
| `em_aprovacao` | Em análise pela secretaria | Secretaria: Aprovar, Rejeitar |
| `aprovado` | Aprovado mas PDF não gerado ainda | Sistema: Gerar PDF |
| `rejeitado` | Rejeitado pela secretaria | Professor: Ver motivo |
| `cancelado` | Cancelado pelo professor | - |
| `aguarda_assinatura` | PDF gerado, aguarda assinatura | Professor: Upload documento |
| `concluido` | Documento assinado e processo concluído | - |

### Badge Colors (Bootstrap):

```php
function formatar_estado_ferias($estado) {
    $badges = [
        'por_preencher'       => '<span class="badge bg-secondary">Por Preencher</span>',
        'submetido'           => '<span class="badge bg-info">Submetido</span>',
        'em_aprovacao'        => '<span class="badge bg-warning">Em Aprovação</span>',
        'aprovado'            => '<span class="badge bg-success">Aprovado</span>',
        'rejeitado'           => '<span class="badge bg-danger">Rejeitado</span>',
        'cancelado'           => '<span class="badge bg-dark">Cancelado</span>',
        'aguarda_assinatura'  => '<span class="badge bg-primary">Aguarda Assinatura</span>',
        'concluido'           => '<span class="badge bg-success">✓ Concluído</span>'
    ];
    return $badges[$estado] ?? '<span class="badge bg-secondary">Desconhecido</span>';
}
```

---

## 🧪 TESTES

### Teste de Cálculo de Dias Úteis:

```php
// Testar cálculo
$dias = calcular_dias_uteis('2026-08-01', '2026-08-15', true);
echo "Dias úteis: {$dias}";
// Esperado: 10 dias (excluindo fins de semana e feriado 15/ago)
```

### Teste de Atribuição:

```sql
-- Inserir atribuição de teste
INSERT INTO ferias_atribuicao (user_nif, anoletivo_id, dias_base, dias_ajuste, dias_extra, atribuido_por)
VALUES ('123456789', 5, 22, -2, 0, 1);

-- Verificar cálculo automático
SELECT * FROM ferias_atribuicao WHERE user_nif = '123456789';
-- dias_total deve ser 20 (22 - 2 + 0)
```

---

## 📞 SUPORTE E MANUTENÇÃO

### Problemas Comuns:

❌ **Erro: "Documento não encontrado"**
- Verificar permissões da pasta `public/`
- Verificar se DomPDF está instalado: `composer require dompdf/dompdf`

❌ **Erro: "Cálculo de dias incorreto"**
- Verificar se feriados estão populados: `SELECT * FROM ferias_feriados`
- Popular feriados em falta

❌ **Emails não são enviados**
- Verificar configuração SMTP em `app/Config/Email.php`
- Testar com: `php spark make:email teste@email.pt`

---

## 📝 CHANGELOG

### Versão 1.0 (2026-03-07)
- ✅ Implementação inicial completa
- ✅ Sistema de atribuição
- ✅ Sistema de marcação
- ✅ Sistema de aprovação
- ✅ Geração de PDF
- ✅ Upload de documento assinado
- ✅ Sistema de emails
- ✅ Cálculo de dias úteis com feriados
- ✅ Transição de saldo entre anos
- ✅ Log de ações
- ✅ View de resumo

---

## 👨‍💻 DESENVOLVEDOR

**Sistema desenvolvido para:** Gestão Escolar  
**Framework:** CodeIgniter 4  
**Frontend:** AdminLTE + Bootstrap 5  
**Data:** Março 2026

---

**FIM DA DOCUMENTAÇÃO**
