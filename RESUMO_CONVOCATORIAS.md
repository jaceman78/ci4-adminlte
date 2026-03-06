# 📋 RESUMO - Sistema de Convocatórias para Exames

## ✅ Ficheiros Criados

### 🗄️ Migrations (Base de Dados)
1. **2026-01-30-100001_CreateExameTable.php**
   - Tabela `exame` com códigos oficiais das provas
   - Campos: código, nome, tipo (Exame Nacional/Prova Final/MODa), ano escolaridade

2. **2026-01-30-100002_CreateSessaoExameTable.php**
   - Tabela `sessao_exame` para sessões específicas (1ª Fase, 2ª Fase, etc.)
   - Campos: data, hora, duração, tolerância, número de alunos

3. **2026-01-30-100003_CreateConvocatoriaTable.php**
   - Tabela `convocatoria` para ligar professores às sessões
   - Campos: professor, sala, função, estado de confirmação

### 🌱 Seeders (Dados Iniciais)
4. **ExameSeeder.php**
   - Popula a tabela `exame` com todos os códigos oficiais:
     - 6 Provas Finais (4º, 6º, 9º anos)
     - 8 Exames Nacionais (11º ano)
     - 11 Exames Nacionais (12º ano)
     - 4 Provas MODa

### 🎯 Models (Lógica de Negócio)
5. **ExameModel.php**
   - Métodos: getByTipo(), getByAnoEscolaridade(), getByCodigo(), countByTipo()

6. **SessaoExameModel.php**
   - Métodos: getWithExame(), getByData(), getByPeriodo(), getSessoesFuturas()
   - Validação de conflitos de horário
   - Cálculo automático de vigilantes necessários

7. **ConvocatoriaModel.php**
   - Métodos: getByProfessor(), getBySessao(), getPendentes()
   - Métodos: confirmar(), rejeitar()
   - Validação de conflitos de horário do professor
   - Estatísticas de confirmações

### 📖 Documentação
8. **IMPLEMENTACAO_CONVOCATORIAS_EXAMES.md**
   - Documentação completa do sistema
   - Estrutura das tabelas
   - Lista completa de códigos oficiais
   - Exemplos de uso
   - Sugestões de próximos passos

9. **CREATE_SISTEMA_CONVOCATORIAS_EXAMES.sql**
   - Script SQL manual (alternativa às migrations)
   - Inclui criação de todas as tabelas
   - Inclui inserção de todos os códigos oficiais
   - Inclui 2 views úteis (vw_sessoes_exames_completo, vw_convocatorias_completo)

## 🚀 Como Usar

### Instalação Rápida (Via CodeIgniter)
```bash
# 1. Executar migrations
php spark migrate

# 2. Executar seeder
php spark db:seed ExameSeeder
```

### Instalação Manual (Via SQL)
```sql
-- Executar no phpMyAdmin ou MySQL Workbench:
SOURCE CREATE_SISTEMA_CONVOCATORIAS_EXAMES.sql
```

## 📊 Códigos Oficiais Incluídos

### Provas Finais do Ensino Básico
- **21** - Português 4º ano
- **22** - Matemática 4º ano
- **81** - Português 6º ano
- **82** - Matemática 6º ano
- **91** - Português 9º ano
- **92** - Matemática 9º ano

### Exames Nacionais 11º Ano
708, 712, 715, 719, 723, 724, 732, 835

### Exames Nacionais 12º Ano
502, 517, 547, 550, 635, 639, 702, 706, 710, 714, 735

### Provas MODa
310, 311, 312, 323

## 🎯 Funcionalidades Principais

### ✅ Gestão de Exames
- Catálogo completo de provas oficiais
- Filtros por tipo e ano de escolaridade
- Sistema de ativação/desativação

### ✅ Gestão de Sessões
- Múltiplas sessões por exame (fases)
- Controlo de data, hora, duração
- Cálculo automático de vigilantes
- Deteção de conflitos

### ✅ Gestão de Convocatórias
- 6 tipos de funções:
  - Vigilante
  - Suplente
  - Coadjuvante
  - Júri
  - Verificar Calculadoras
  - Apoio TIC
- Sistema de confirmação (Pendente/Confirmado/Rejeitado)
- Deteção de conflitos de horário do professor
- Estatísticas e relatórios

## 🔧 Melhorias Implementadas

Além da estrutura solicitada, foram adicionados:

1. ✅ Campos de auditoria (created_at, updated_at)
2. ✅ Sistema de soft-delete (campo ativo)
3. ✅ Campo para número de alunos
4. ✅ Campo para observações
5. ✅ Separação entre duração e tolerância
6. ✅ Estado "Rejeitado" em confirmações
7. ✅ Índices otimizados
8. ✅ Foreign keys com CASCADE/SET NULL
9. ✅ Validações completas nos Models
10. ✅ Métodos auxiliares para operações comuns
11. ✅ Views SQL para consultas complexas
12. ✅ Cálculo automático de hora de fim
13. ✅ Contagens de convocatórias por sessão

## 📝 Exemplo de Utilização

```php
// 1. Criar sessão de exame
$sessaoModel = new SessaoExameModel();
$sessaoId = $sessaoModel->insert([
    'exame_id' => 12, // Matemática A
    'fase' => '1ª Fase',
    'data_exame' => '2026-06-18',
    'hora_exame' => '09:30:00',
    'duracao_minutos' => 150,
    'tolerancia_minutos' => 30,
    'num_alunos' => 45
]);

// 2. Criar convocatórias
$convocatoriaModel = new ConvocatoriaModel();
$convocatoriaModel->insert([
    'sessao_exame_id' => $sessaoId,
    'user_id' => 15,
    'sala_id' => 8,
    'funcao' => 'Vigilante'
]);

// 3. Listar minhas convocatórias
$minhas = $convocatoriaModel->getByProfessor(session('user_id'));

// 4. Confirmar convocatória
$convocatoriaModel->confirmar(5, 'Confirmo presença');
```

## 🔄 Próximos Passos Recomendados

### 1. Controllers
- ExameController
- SessaoExameController
- ConvocatoriaController

### 2. Views
- Dashboard com calendário
- Formulários CRUD
- Área do professor

### 3. Funcionalidades
- Notificações por email
- Exportação de mapas (PDF)
- Relatórios estatísticos
- Integração com horários

### 4. Permissões
- Coordenadores: criar sessões
- Professores: confirmar convocatórias
- Admin: acesso total

## 📞 Suporte

Para mais informações, consulte:
- **IMPLEMENTACAO_CONVOCATORIAS_EXAMES.md** - Documentação completa

---

**Data:** 30/01/2026  
**Status:** ✅ Estrutura base completa e pronta a usar
