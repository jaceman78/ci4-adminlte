# Sistema de Marcação de Presenças - Convocatórias

## Data: 2026-02-11

## 1. Alteração na Base de Dados

### Tabela: `convocatoria`
**Novo campo adicionado:**
- `presenca` ENUM('Pendente', 'Presente', 'Falta', 'Falta Justificada') DEFAULT 'Pendente'
- Índice: `idx_presenca`

**Estados possíveis:**
- **Pendente**: Estado inicial (antes do exame)
- **Presente**: Professor compareceu
- **Falta**: Professor faltou sem justificação
- **Falta Justificada**: Professor faltou com justificação

---

## 2. Alterações na Interface

### 2.1. Página: `/convocatorias` (app/Views/convocatorias/index.php)

**Estrutura Atual:** Lista simples de convocatórias

**Nova Estrutura:**
```
┌─────────────────────────────────────────┐
│ [▼] Sessão de Exame - 706 - Desenho A  │  ← Cardbox expansível (collapse)
│     Data: 15/04/2026 | Hora: 09:00     │
│     Tipo: Exame Nacional | Fase: 1ªfase│
├─────────────────────────────────────────┤
│ Vigilantes:                              │
│ ┌───────────────────────────────────┐   │
│ │ Nome         │ Sala │ Presença    │   │
│ ├───────────────────────────────────┤   │
│ │ João Silva   │ A101 │ [Dropdown] │   │
│ │ Maria Santos │ A102 │ [Dropdown] │   │
│ └───────────────────────────────────┘   │
│                                          │
│ Suplentes:                               │
│ ┌───────────────────────────────────┐   │
│ │ Nome         │ Presença          │   │
│ ├───────────────────────────────────┤   │
│ │ Ana Costa    │ [Dropdown]        │   │
│ └───────────────────────────────────┘   │
│                                          │
│ [Guardar Presenças] [Gerar PDF Faltas] │
└─────────────────────────────────────────┘
```

**Funcionalidades:**
- Cardboxes agrupadas por sessão de exame
- Cada cardbox mostra: código prova, nome, data, hora, tipo, fase
- Lista de professores por função (Vigilantes, Suplentes, Coadjuvantes, etc.)
- Dropdown para cada professor com opções: Pendente, Presente, Falta, Falta Justificada
- Botão "Guardar Presenças" (AJAX)
- Botão "Gerar PDF Faltas" (apenas se houver faltas)

---

## 3. Implementação Backend

### 3.1. Controller: `ConvocatoriaController.php`

**Novos métodos:**

```php
/**
 * Página de marcação de presenças
 * Agrupa convocatórias por sessão
 */
public function marcarPresencas()

/**
 * Atualizar presença de um professor
 * POST /convocatorias/atualizar-presenca/{id}
 */
public function atualizarPresenca($convocatoriaId)

/**
 * Atualizar múltiplas presenças de uma sessão
 * POST /convocatorias/atualizar-presencas-sessao/{sessaoId}
 */
public function atualizarPresencasSessao($sessaoId)

/**
 * Gerar PDF com relatório de faltas
 * GET /convocatorias/gerar-pdf-faltas/{sessaoId}
 */
public function gerarPdfFaltas($sessaoId)
```

### 3.2. Model: `ConvocatoriaModel.php`

**Novos métodos:**

```php
/**
 * Buscar convocatórias agrupadas por sessão
 */
public function getBySessaoGrouped($filtros = [])

/**
 * Atualizar presença de convocatória
 */
public function atualizarPresenca($convocatoriaId, $presenca)

/**
 * Buscar faltas de uma sessão
 */
public function getFaltasBySessao($sessaoId)

/**
 * Estatísticas de presenças
 */
public function getEstatisticasPresencas($sessaoId)
```

---

## 4. PDF de Faltas

### Estrutura do PDF:

```
┌────────────────────────────────────────────┐
│  Logo ESJB    RELATÓRIO DE FALTAS    Logo RP│
├────────────────────────────────────────────┤
│ Sessão de Exame: 706 - Desenho A          │
│ Data: 15/04/2026 | Hora: 09:00            │
│ Tipo: Exame Nacional | Fase: 1ª Fase      │
├────────────────────────────────────────────┤
│                                            │
│ PROFESSORES COM FALTA                      │
│ ┌────────────────────────────────────────┐│
│ │ Nome           │ Função    │ Tipo Falta││
│ ├────────────────────────────────────────┤│
│ │ João Silva     │ Vigilante │ Falta     ││
│ │ Maria Santos   │ Suplente  │ Falta Just││
│ └────────────────────────────────────────┘│
│                                            │
│ RESUMO:                                    │
│ - Total Convocados: 15                     │
│ - Presentes: 13                            │
│ - Faltas: 1                                │
│ - Faltas Justificadas: 1                   │
│                                            │
│        Corroios, 15 de abril de 2026      │
│                                            │
│              O Diretor                     │
│        ________________________            │
│        ( António de Carvalho )             │
├────────────────────────────────────────────┤
│ Agrupamento de Escolas João de Barros     │
│ Documento gerado em: 15/04/2026            │
└────────────────────────────────────────────┘
```

---

## 5. Rotas a Adicionar

```php
// app/Config/Routes.php

$routes->group('convocatorias', ['filter' => 'auth'], function($routes) {
    // Página de marcação de presenças
    $routes->get('marcar-presencas', 'ConvocatoriaController::marcarPresencas');
    
    // Atualizar presença individual
    $routes->post('atualizar-presenca/(:num)', 'ConvocatoriaController::atualizarPresenca/$1');
    
    // Atualizar múltiplas presenças de uma sessão
    $routes->post('atualizar-presencas-sessao/(:num)', 'ConvocatoriaController::atualizarPresencasSessao/$1');
    
    // Gerar PDF de faltas
    $routes->get('gerar-pdf-faltas/(:num)', 'ConvocatoriaController::gerarPdfFaltas/$1');
});
```

---

## 6. Permissões

**Quem pode marcar presenças:**
- Secretariado de Exames (níveis 4, 8, 9)
- Direção (níveis 8, 9)

**Validações:**
- Apenas pode marcar presença em sessões que já ocorreram (data passada)
- Pode atualizar presenças até X dias após o exame (configurável)

---

## 7. Notificações (Futuro)

**Quando marcar falta:**
- Enviar email ao professor notificando a falta
- Enviar notificação à direção

**Quando marcar falta justificada:**
- Registar justificação em observações
- Anexar documento de justificação (futuro)

---

## 8. Ordem de Implementação

1. ✅ Criar campo `presenca` na tabela
2. ✅ Atualizar migration principal
3. ⏳ Criar view `marcar_presencas.php` com cardboxes expansíveis
4. ⏳ Implementar métodos no `ConvocatoriaController`
5. ⏳ Implementar métodos no `ConvocatoriaModel`
6. ⏳ Criar view do PDF de faltas
7. ⏳ Adicionar rotas
8. ⏳ Testar funcionalidade completa
9. ⏳ Adicionar item no menu lateral

---

## 9. Item do Menu

Adicionar em `app/Views/layout/partials/sidebar.php`:

```php
<!-- Marcação de Presenças (Secretariado) -->
<?php if (in_array(session()->get('nivel_acesso'), [4, 8, 9])): ?>
    <li class="nav-item">
        <a href="<?= base_url('convocatorias/marcar-presencas') ?>" 
           class="nav-link <?= (current_url() == base_url('convocatorias/marcar-presencas')) ? 'active' : '' ?>">
            <i class="nav-icon bi bi-clipboard-check"></i>
            <p>Marcar Presenças</p>
        </a>
    </li>
<?php endif; ?>
```

---

## 10. Melhorias Futuras

- Dashboard com estatísticas de assiduidade
- Gráficos de presenças/faltas por professor
- Export Excel de relatórios de faltas
- Histórico de presenças por professor
- Sistema de justificações anexadas
- Alertas automáticos para professores com muitas faltas
