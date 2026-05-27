# 📄 GUIA DE PERSONALIZAÇÃO - DOCUMENTO DE FÉRIAS PDF

## 📋 VISÃO GERAL

O documento de férias foi criado seguindo **exatamente** o formato oficial português utilizado pelas escolas, com todos os campos necessários para conformidade legal e assinatura.

---

## 🎨 PERSONALIZAR DADOS DA ESCOLA

### Ficheiro de Configuração: `app/Config/FeriasConfig.php`

Todos os dados da escola podem ser personalizados editando este ficheiro:

```php
<?php
namespace Config;
use CodeIgniter\Config\BaseConfig;

class FeriasConfig extends BaseConfig
{
    // Código da escola (ex: 171268)
    public string $escola_codigo = '171268';
    
    // Nome completo do agrupamento
    public string $escola_nome = 'Agrupamento de Escolas João de Barros';
    
    // Morada completa
    public string $escola_endereco = '460050 - Escola Secundária João de Barros, 2855 - 713 Corroios';
    
    // Número Mecanográfico
    public string $escola_nmec = '212589180';
    
    // Telefone
    public string $escola_tel = '212531167';
    
    // Contribuinte
    public string $escola_contrib = '600078422';
    
    // Nome completo da escola (para campo "Escola")
    public string $escola_completo = 'Escola Secundária João de Barros, Corroios, Seixal';
    
    // Categoria padrão que aparece no documento
    public string $categoria_default = 'Professores do 2º e 3º Ciclos e Sec. - Quadro Escola - Nomeação';
    
    // Rodapé do documento
    public string $rodape_sistema = 'Processado por computador pelo software Sistema de Gestão Escolar • Copyright©';
}
```

### ✏️ Como Personalizar:

1. **Abrir ficheiro**: `app/Config/FeriasConfig.php`

2. **Editar dados da sua escola**:
   ```php
   public string $escola_codigo = '123456';
   public string $escola_nome = 'Agrupamento de Escolas Sua Escola';
   public string $escola_endereco = 'Rua da Escola, 1234-567 Cidade';
   public string $escola_nmec = '123456789';
   public string $escola_tel = '123456789';
   public string $escola_contrib = '123456789';
   public string $escola_completo = 'Escola Secundária Sua Escola, Cidade';
   ```

3. **Guardar ficheiro**

4. **Testar**: Gerar novo PDF de férias

✅ **Todas as mudanças aplicam-se automaticamente!**

---

## 🖼️ ADICIONAR LOGO DA ESCOLA

### Passo 1: Preparar Logo

- **Formato**: PNG ou JPG
- **Tamanho**: 200x80 pixels (aproximado)
- **Fundo**: Transparente ou branco

### Passo 2: Colocar Logo

```bash
# Copiar logo para:
public/img/logo_escola.png
```

### Passo 3: Ativar Logo

Editar `app/Config/FeriasConfig.php`:

```php
// Caminho relativo ao logo
public string $logo_escola = 'public/img/logo_escola.png';

// Ativar uso do logo
public bool $usar_logo = true;  // Mudar para true
```

### Passo 4: Modificar Template PDF

Editar `app/Views/ferias/documento_pdf.php`:

Adicionar no cabeçalho (antes de "REPÚBLICA PORTUGUESA"):

```php
<?php if (!empty($pedido['usar_logo']) && $pedido['usar_logo'] && file_exists(FCPATH . $pedido['logo_escola'])): ?>
<div style="text-align: center; margin-bottom: 10px;">
    <img src="<?= FCPATH . $pedido['logo_escola'] ?>" alt="Logo" style="max-height: 60px;">
</div>
<?php endif; ?>
```

---

## 📝 CAMPOS DO DOCUMENTO

### Estrutura Atual:

#### 1. **Cabeçalho**
- Logo República Portuguesa
- Educação, Ciência e Inovação
- Código da escola
- Nome da escola
- Morada, NMec, Tel, Contribuinte

#### 2. **Título**
- "Licença para Férias / Ano 2026"

#### 3. **Dados do Professor**
- Nome
- Nº Funcionário
- Categoria
- Escola

#### 4. **Cálculo de Dias**
- Dias direito ano anterior
- Dias gozados ano anterior
- Dias direito atual (com nota de rodapé se há acumulação)
- Faltas justificadas por participação
- Faltas injustificadas
- Total faltas a descontar
- **Dias a conceder** (valor final)

#### 5. **Assinatura Coordenador Técnico/CSAE**

#### 6. **Tabela: Períodos Pretendidos**
- Data início / Data fim / Dias úteis
- Total de dias úteis

#### 7. **Residência durante Férias**
- Campo texto livre

#### 8. **Data e Assinatura do Trabalhador**

#### 9. **Despacho**
- Campo para observações da secretaria
- Marcação de APROVADO (se aplicável)
- Data e assinatura

#### 10. **Notas de Rodapé**
- Explicação campo (a)
- Nota sobre dias acumulados (se aplicável)

#### 11. **Rodapé Sistema**
- Software utilizado
- Número do documento
- Data/hora de geração

---

## 🎨 PERSONALIZAR ESTILO DO PDF

### Ficheiro: `app/Views/ferias/documento_pdf.php`

#### Alterar Fonte:

```css
body {
    font-family: Arial, Helvetica, sans-serif;  /* Mudar para: Times, Courier, etc. */
    font-size: 10pt;  /* Ajustar tamanho */
}
```

#### Alterar Margens:

```css
@page {
    margin: 15mm;  /* Ajustar: 10mm, 20mm, etc. */
}
```

#### Alterar Bordas das Caixas:

```css
.despacho {
    border: 1px solid #000;  /* Alterar espessura: 2px, cor: #ccc */
}
```

#### Alterar Tamanho dos Campos:

```css
.campo-pequeno {
    min-width: 40px;  /* Aumentar/diminuir */
    padding: 3px 8px;  /* Ajustar espaçamento interno */
}
```

---

## 📚 DADOS DINÂMICOS DISPONÍVEIS

O template recebe a variável `$pedido` com todos os dados:

### Dados do Pedido:
```php
$pedido['id']                     // ID do pedido
$pedido['user_nif']               // NIF do professor
$pedido['nome_professor']         // Nome completo
$pedido['email_professor']        // Email
$pedido['ano']                    // Ano letivo (2026)
$pedido['anoletivo_id']           // ID ano letivo
$pedido['estado']                 // Estado atual
$pedido['total_dias']             // Total dias solicitados
$pedido['submetido_em']           // Data submissão
$pedido['aprovado_em']            // Data aprovação
$pedido['observacoes_professor']  // Observações professor
$pedido['observacoes_secretaria'] // Observações secretaria
$pedido['periodos']               // Array de períodos
```

### Dados de Cálculo:
```php
$pedido['dias_base']              // Dias base (22)
$pedido['dias_ajuste']            // Ajuste (-5 a +5)
$pedido['dias_extra']             // Dias extra
$pedido['dias_ano_anterior']      // Dias que teve direito ano anterior
$pedido['dias_gozados_anterior']  // Dias que gozou ano anterior
$pedido['dias_concedidos']        // Dias finais a conceder
$pedido['faltas_justificadas']    // Nº faltas justificadas
$pedido['faltas_injustificadas']  // Nº faltas injustificadas
$pedido['data_falta_justificada'] // Data da falta (se houver)
```

### Dados da Escola (da config):
```php
$pedido['escola_codigo']          // 171268
$pedido['escola_nome']            // Nome agrupamento
$pedido['escola_endereco']        // Morada completa
$pedido['escola_nmec']            // Número mecanográfico
$pedido['escola_tel']             // Telefone
$pedido['escola_contrib']         // Contribuinte
$pedido['escola_completo']        // Nome completo para campo "Escola"
$pedido['categoria_default']      // Categoria do professor
$pedido['rodape_sistema']         // Texto rodapé
$pedido['usar_logo']              // Usar logo (true/false)
$pedido['logo_escola']            // Caminho do logo
```

### Dados do Professor:
```php
$pedido['numero_funcionario']     // Nº funcionário
$pedido['categoria']              // Categoria específica
$pedido['residencia_ferias']      // Residência durante férias
```

### Array de Períodos:
```php
foreach ($pedido['periodos'] as $periodo) {
    $periodo['data_inicio']       // 2026-08-01
    $periodo['data_fim']          // 2026-08-10
    $periodo['dias_uteis']        // 7
}
```

---

## 🔧 EXEMPLOS DE CUSTOMIZAÇÃO

### Exemplo 1: Adicionar Campo "Departamento"

**1. Na tabela `user` (SQL):**
```sql
ALTER TABLE user ADD COLUMN departamento VARCHAR(100) DEFAULT NULL;
UPDATE user SET departamento = 'Matemática' WHERE NIF = '123456789';
```

**2. No Controller** (`FeriasController.php`, método `gerarPDFFerias`):
```php
// Buscar professor
$professor = $this->userModel->where('NIF', $pedido['user_nif'])->first();

// Adicionar departamento
$pedido['departamento'] = $professor['departamento'] ?? 'Não especificado';
```

**3. No Template PDF** (`documento_pdf.php`):
```php
<div class="campo">
    <span class="campo-label">Departamento :</span>
    <span class="campo-valor"><?= esc($pedido['departamento']) ?></span>
</div>
```

---

### Exemplo 2: Alterar Cálculo de Faltas

**No Controller** (`gerarPDFFerias`):

```php
// Buscar faltas de uma hipotética tabela faltas
$faltasModel = new \App\Models\FaltasModel();
$faltas = $faltasModel->where('user_nif', $pedido['user_nif'])
                      ->where('ano', $pedido['ano'])
                      ->findAll();

$justificadas = 0;
$injustificadas = 0;
$dataFalta = null;

foreach ($faltas as $falta) {
    if ($falta['justificada']) {
        $justificadas++;
        if (!$dataFalta) $dataFalta = $falta['data'];
    } else {
        $injustificadas++;
    }
}

$pedido['faltas_justificadas'] = $justificadas;
$pedido['faltas_injustificadas'] = $injustificadas;
$pedido['data_falta_justificada'] = $dataFalta;
```

---

### Exemplo 3: Alterar Formato de Data

**No Template PDF** (trocar `dd/mm/yyyy` por `yyyy-mm-dd`):

```php
// De:
<?= date('d/m/Y', strtotime($periodo['data_inicio'])) ?>

// Para:
<?= date('Y-m-d', strtotime($periodo['data_inicio'])) ?>

// Ou formato extenso:
<?= strftime('%d de %B de %Y', strtotime($periodo['data_inicio'])) ?>
// Ex: "01 de agosto de 2026"
```

---

### Exemplo 4: Adicionar QR Code ao Documento

**1. Instalar biblioteca:**
```bash
composer require endroid/qr-code
```

**2. No Controller** (`gerarPDFFerias`):
```php
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Gerar QR Code com URL de validação
$qrCode = new QrCode(base_url("ferias/validar/{$pedidoId}"));
$writer = new PngWriter();
$result = $writer->write($qrCode);

// Salvar imagem temporária
$qrPath = FCPATH . "temp/qr_{$pedidoId}.png";
$result->saveToFile($qrPath);

$pedido['qr_code_path'] = $qrPath;
```

**3. No Template PDF:**
```php
<?php if (!empty($pedido['qr_code_path']) && file_exists($pedido['qr_code_path'])): ?>
<div style="text-align: right; margin-top: 20px;">
    <img src="<?= $pedido['qr_code_path'] ?>" alt="QR Code" style="width: 80px; height: 80px;">
    <br><small>Código de validação</small>
</div>
<?php endif; ?>
```

---

## 📊 VALIDAÇÃO E TESTES

### Teste 1: Verificar Dados Gerados

Criar ficheiro `teste_documento_ferias.php`:

```php
<?php
require_once 'vendor/autoload.php';

$pedidoModel = new \App\Models\FeriasPedidoModel();
$pedido = $pedidoModel->getPedidoComPeriodos(1); // ID do pedido

echo "<pre>";
print_r($pedido);
echo "</pre>";

// Testar template
$html = view('ferias/documento_pdf', ['pedido' => $pedido]);
echo $html;
```

```bash
php teste_documento_ferias.php > teste.html
# Abrir teste.html no navegador
```

---

### Teste 2: Validar Cálculos

```php
<?php
helper('ferias');

$dataInicio = '2026-08-01';
$dataFim = '2026-08-31';

$diasUteis = calcular_dias_uteis($dataInicio, $dataFim, true);
echo "Dias úteis entre {$dataInicio} e {$dataFim}: {$diasUteis}\n";

// Exemplo: Agosto 2026 tem ~21 dias úteis (31 dias - sábados/domingos - feriados)
```

---

## 🆘 RESOLUÇÃO DE PROBLEMAS

### Problema 1: "Dados da escola não aparecem"

**Solução:**
1. Verificar se `FeriasConfig.php` existe
2. Limpar cache: `php spark cache:clear`
3. Verificar se Controller carrega config: `$config = config('FeriasConfig');`

---

### Problema 2: "PDF não gera"

**Solução:**
1. Verificar se DomPDF está instalado: `composer show dompdf/dompdf`
2. Verificar permissões da pasta: `chmod 755 public/`
3. Verificar logs: `tail -f writable/logs/log-*.log`

---

### Problema 3: "Fonte não aparece correta"

**Solução:**
```php
// No template PDF, adicionar fallbacks:
body {
    font-family: Arial, Helvetica, sans-serif, 'DejaVu Sans'; /* DejaVu é fallback */
}
```

---

### Problema 4: "Imagem/Logo não aparece no PDF"

**Solução:**
```php
// Usar caminho absoluto em vez de relativo:
<img src="<?= FCPATH . 'public/img/logo_escola.png' ?>" alt="Logo">
```

---

## 📚 RECURSOS ADICIONAIS

### Documentação DomPDF:
- https://github.com/dompdf/dompdf
- Suporta: HTML, CSS (limitado), imagens PNG/JPG

### CSS Suportado por DomPDF:
- ✅ Margens, padding, bordas
- ✅ Cores, backgrounds
- ✅ Fontes básicas (Arial, Times, Courier)
- ❌ Flexbox, Grid (não suportados)
- ❌ JavaScript, CSS avançado

### Fontes Customizadas:
```php
// Instalar fonte custom no DomPDF
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);
```

---

## ✅ CHECKLIST FINAL

Após personalizar o documento:

- [ ] Dados da escola atualizados em `FeriasConfig.php`
- [ ] Logo adicionado (se aplicável)
- [ ] Template PDF testado com dados reais
- [ ] PDF gerado sem erros
- [ ] Todos os campos exibidos corretamente
- [ ] Cálculos de dias corretos
- [ ] Formatação adequada para impressão
- [ ] Rodapé personalizado
- [ ] QR Code/assinatura digital (se aplicável)

---

**Documento personalizado e pronto para produção! 🎉**
