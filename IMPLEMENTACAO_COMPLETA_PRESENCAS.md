# Sistema de Marcação de Presenças - Implementação Completa

## 📋 Visão Geral

Sistema implementado para permitir que o secretariado de exames (níveis 4, 8, 9) marque a presença/falta dos professores vigilantes nas sessões de exame e gere relatórios em PDF.

## ✅ Componentes Implementados

### 1. Base de Dados
- ✅ Migração: `ALTER_CONVOCATORIA_ADD_PRESENCA.sql`
- ✅ Campo: `presenca` ENUM('Pendente', 'Presente', 'Falta', 'Falta Justificada')
- ✅ Índice: `idx_presenca` para otimização
- ✅ Valor padrão: 'Pendente'

### 2. Model - ConvocatoriaModel.php
**Métodos Adicionados:**

```php
// 1. Buscar todas as sessões com convocatórias
public function getSessionsComConvocatorias()
// Retorna: Lista de sessões com código, nome, data, hora, tipo, fase

// 2. Buscar convocatórias de uma sessão com dados do professor
public function getConvocatoriasBySessaoComProfessores($sessaoId)
// Retorna: Convocatórias com nome professor, função, sala, presença

// 3. Atualizar presença de uma convocatória
public function atualizarPresenca($convocatoriaId, $presenca)
// Valida: ENUM values, retorna boolean

// 4. Buscar faltas de uma sessão
public function getFaltasBySessao($sessaoId)
// Retorna: Apenas 'Falta' e 'Falta Justificada' com dados do professor

// 5. Estatísticas de presenças
public function getEstatisticasPresencas($sessaoId)
// Retorna: Contadores por estado (total, presentes, faltas, etc.)
```

**Campo Adicionado:**
- `presenca` em `$allowedFields`

### 3. Controller - ConvocatoriaController.php
**Métodos Adicionados:**

```php
// 1. Página principal de marcação
public function marcarPresencas()
// Rota: GET /convocatorias/marcar-presencas
// View: convocatorias/marcar_presencas.php
// Permissões: Níveis 4, 8, 9

// 2. Buscar convocatórias (AJAX)
public function getConvocatoriasSessao($sessaoId)
// Rota: GET /convocatorias/get-convocatorias-sessao/{id}
// Retorna: JSON agrupado por função (vigilantes, suplentes, coadjuvantes, outros)

// 3. Atualizar presença individual (AJAX)
public function atualizarPresenca($convocatoriaId)
// Rota: POST /convocatorias/atualizar-presenca/{id}
// Body: { presenca: 'Presente|Falta|Falta Justificada|Pendente' }
// Retorna: JSON com success/message

// 4. Atualizar múltiplas presenças (AJAX)
public function atualizarPresencasSessao($sessaoId)
// Rota: POST /convocatorias/atualizar-presencas-sessao/{id}
// Body: { presencas: { conv_id: presenca, ... } }
// Retorna: JSON com success/message/updated_count

// 5. Gerar PDF de faltas
public function gerarPdfFaltas($sessaoId)
// Rota: GET /convocatorias/gerar-pdf-faltas/{id}
// View: convocatorias/pdf_faltas.php
// Output: PDF download
```

### 4. Views

#### A) marcar_presencas.php
**Características:**
- Cardboxes expansíveis por sessão (accordion Bootstrap 5)
- Filtros: Data e Tipo de Prova
- Carregamento AJAX dinâmico ao expandir
- Tabelas agrupadas por função:
  - Vigilantes (com sala)
  - Suplentes
  - Coadjuvantes
  - Outros (com função)
- Dropdowns de presença com cores:
  - Pendente: branco
  - Presente: verde (#d4edda)
  - Falta: vermelho (#f8d7da)
  - Falta Justificada: amarelo (#fff3cd)
- Botões:
  - "Guardar Presença" individual (por linha)
  - "Guardar Todas as Presenças" (bulk update)
  - "Gerar PDF de Faltas" (abre em nova tab)

#### B) pdf_faltas.php
**Estrutura:**
- Header: Logos ESJB + RP (otimizados)
- Título: "Relatório de Faltas - Vigilância de Exames"
- Data em destaque: fundo amarelo, texto vermelho grande
- Info box: Dados da sessão (código, nome, tipo, fase, duração)
- Estatísticas: Total, Presentes, Faltas, Faltas Justificadas, Pendentes
- Tabela de faltas:
  - Colunas: Nome, Função, Tipo de Falta
  - Cores: Falta (vermelho), Falta Justificada (amarelo)
- Mensagem "sem faltas" se não houver
- Assinatura do diretor: António de Carvalho
- Footer: Dados da escola + data/hora de geração

### 5. Routes - Routes.php
```php
$routes->group('convocatorias', function($routes) {
    // ... rotas existentes ...
    
    // Sistema de Marcação de Presenças
    $routes->get('marcar-presencas', 'ConvocatoriaController::marcarPresencas');
    $routes->get('get-convocatorias-sessao/(:num)', 'ConvocatoriaController::getConvocatoriasSessao/$1');
    $routes->post('atualizar-presenca/(:num)', 'ConvocatoriaController::atualizarPresenca/$1');
    $routes->post('atualizar-presencas-sessao/(:num)', 'ConvocatoriaController::atualizarPresencasSessao/$1');
    $routes->get('gerar-pdf-faltas/(:num)', 'ConvocatoriaController::gerarPdfFaltas/$1');
});
```

### 6. Menu - sidebar.php
**Item Adicionado:**
```php
<li class="nav-item">
  <a href="<?= base_url('convocatorias/marcar-presencas') ?>" class="nav-link">
    <i class="nav-icon bi bi-clipboard-check"></i>
    <p>Marcar Presenças</p>
  </a>
</li>
```
**Posição:** Logo após "Convocatórias/Vigilâncias"  
**Visibilidade:** Níveis 4, 8, 9 (através de verificação em Sec. Exames)

## 🔒 Segurança

### Controle de Acesso
- **Níveis Permitidos:** 4, 8, 9 (Secretariado de Exames)
- **Validação:** `requireSecExamesPermissions()` em BaseController
- **AJAX:** Verificação com `isAJAX()` + validação de dados
- **CSRF:** Proteção automática do CodeIgniter 4

### Validação de Dados
```php
// Valores permitidos para presença
$validPresencas = ['Pendente', 'Presente', 'Falta', 'Falta Justificada'];

// Validação no Model
if (!in_array($presenca, $validPresencas)) {
    return false;
}
```

## 📊 Fluxo de Dados

### 1. Carregar Página
```
Usuário → marcarPresencas() → getSessionsComConvocatorias() → View com lista de sessões
```

### 2. Expandir Cardbox (AJAX)
```
Click → JS carregarConvocatorias() → getConvocatoriasSessao() 
  → getConvocatoriasBySessaoComProfessores() → JSON agrupado → Renderizar tabelas
```

### 3. Salvar Presença Individual (AJAX)
```
Dropdown change + Click → salvarPresencaIndividual() → atualizarPresenca() 
  → UPDATE convocatoria → SweetAlert2 success → Atualizar cor dropdown
```

### 4. Salvar Todas as Presenças (AJAX)
```
Click botão → salvarTodasPresencas() → atualizarPresencasSessao() 
  → Múltiplos UPDATE → SweetAlert2 success → Atualizar cores
```

### 5. Gerar PDF
```
Click botão → gerarPdfFaltas() → getFaltasBySessao() + getEstatisticasPresencas() 
  → DomPDF render → Download PDF
```

## 🎨 Interface

### Cores e Estados
| Estado | Background | Uso |
|--------|-----------|-----|
| Pendente | #ffffff (branco) | Padrão inicial |
| Presente | #d4edda (verde claro) | Confirmação |
| Falta | #f8d7da (vermelho claro) | Ausência |
| Falta Justificada | #fff3cd (amarelo claro) | Ausência com justificação |

### Badges de Sessão
| Tipo | Cor | Classe |
|------|-----|--------|
| Tipo Prova | Cinza | bg-secondary |
| Data/Hora | Azul | bg-info |
| Fase | Azul escuro | bg-primary |

### Ícones Bootstrap Icons
- `bi-clipboard-check` - Menu e título
- `bi-calendar-event` - Sessões
- `bi-people-fill` - Vigilantes
- `bi-people` - Suplentes
- `bi-person-badge` - Coadjuvantes
- `bi-person-gear` - Outros
- `bi-check` - Salvar individual
- `bi-save` - Salvar todas
- `bi-file-pdf` - Gerar PDF

## 🔧 Configuração PDF

### DomPDF
```php
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('isFontSubsettingEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);
$dompdf->setPaper('A4', 'portrait');
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '180');
```

### Imagens Otimizadas
- `esjb_logo_pdf.png` - 200x57px, 13KB
- `RP_Edu_pdf.png` - 200x94px, 10KB

## 📝 Próximos Passos (Testes)

### 1. Teste Funcional
- [ ] Acessar `/convocatorias/marcar-presencas`
- [ ] Verificar carregamento das sessões
- [ ] Testar filtros (data e tipo)
- [ ] Expandir cardbox e verificar carregamento AJAX
- [ ] Alterar dropdown e salvar presença individual
- [ ] Marcar várias presenças e usar "Guardar Todas"
- [ ] Gerar PDF sem faltas
- [ ] Marcar faltas e gerar PDF com listagem

### 2. Teste de Permissões
- [ ] Login com nível < 4: deve redirecionar
- [ ] Login com níveis 4, 8, 9: deve acessar
- [ ] Tentar AJAX sem autenticação: deve retornar erro

### 3. Teste de Edge Cases
- [ ] Sessão sem convocatórias: deve mostrar mensagem
- [ ] Alterar presença para valor inválido: deve validar
- [ ] PDF com sessão inexistente: deve retornar 404
- [ ] Múltiplas atualizações simultâneas: verificar race conditions

### 4. Teste de Performance
- [ ] Sessão com 50+ convocatórias: tempo de carregamento
- [ ] Bulk update com 50+ registros: tempo de resposta
- [ ] Geração de PDF com muitas faltas: memória/timeout

## 📚 Documentação Relacionada

- `IMPLEMENTACAO_MARCACAO_PRESENCAS.md` - Plano completo de implementação
- `IMPLEMENTACAO_CONVOCATORIAS_EXAMES.md` - Sistema de convocatórias
- `ALTER_CONVOCATORIA_ADD_PRESENCA.sql` - Migração de banco de dados

## 🎯 Resumo da Implementação

**Status:** ✅ Completo (Backend + Frontend + Rotas + Menu)

**Arquivos Criados/Modificados:**
1. ✅ `app/Models/ConvocatoriaModel.php` - 5 novos métodos + allowedFields
2. ✅ `app/Controllers/ConvocatoriaController.php` - 5 novos métodos
3. ✅ `app/Views/convocatorias/marcar_presencas.php` - Interface principal
4. ✅ `app/Views/convocatorias/pdf_faltas.php` - Template PDF
5. ✅ `app/Config/Routes.php` - 5 novas rotas
6. ✅ `app/Views/layout/partials/sidebar.php` - Item de menu
7. ✅ `ALTER_CONVOCATORIA_ADD_PRESENCA.sql` - Migração DB

**Tecnologias Utilizadas:**
- PHP 8.1+ (CodeIgniter 4.6.1)
- MySQL/MariaDB
- Bootstrap 5 + AdminLTE
- jQuery 3.7.1
- SweetAlert2
- DomPDF
- Bootstrap Icons

**Resultado Final:**
Sistema funcional que permite marcação de presenças com interface intuitiva, atualizações AJAX em tempo real, e geração de relatórios PDF profissionais para comunicação com a secretaria da escola.
