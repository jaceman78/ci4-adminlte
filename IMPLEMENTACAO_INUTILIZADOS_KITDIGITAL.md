# 🖥️ Implementação: Gestão de Equipamentos Inutilizados

## 📋 Descrição
Sistema para gestão de equipamentos (computadores portáteis) inutilizados destinados à "canibalização" de componentes para reparação de outros equipamentos.

## 🎯 Funcionalidades Implementadas

### 1. **Gestão de Equipamentos**
- ✅ Registo de equipamentos inutilizados
- ✅ Edição de informações
- ✅ Eliminação (soft delete)
- ✅ Listagem com DataTables
- ✅ Pesquisa e filtragem

### 2. **Controlo de Componentes**
Cada equipamento pode ter os seguintes componentes:
- 🔹 **RAM** - Memória RAM
- 🔹 **Disco** - Disco rígido/SSD
- 🔹 **Teclado** - Teclado
- 🔹 **Ecrã** - Display/Monitor
- 🔹 **Bateria** - Bateria
- 🔹 **Caixa** - Carcaça/estrutura
- 🔹 **Outros** - Campo livre para outros componentes

**Estados dos componentes:**
- ✅ Disponível (1) - Componente ainda pode ser utilizado
- ❌ Utilizado (0) - Componente já foi canibalizado

### 3. **QR Codes**
- ✅ Geração automática de QR Code para cada equipamento
- ✅ QR Code contém:
  - ID do equipamento
  - Número de série
  - Marca e modelo
  - Estado de cada componente
  - Link direto para visualização
- ✅ Visualização do QR Code
- ✅ Download do QR Code em PNG
- ✅ Página para impressão do QR Code
- 🎯 **Utilização**: Colar o QR Code no equipamento físico para identificação rápida

### 4. **Estados do Equipamento**
- 🟢 **Ativo** - Equipamento disponível com componentes para canibalizar
- 🟡 **Esgotado** - Todos os componentes já foram utilizados
- ⚫ **Descartado** - Equipamento descartado/eliminado

### 5. **Estatísticas e Dashboard**
- Total de equipamentos registados
- Equipamentos por estado (ativo/esgotado/descartado)
- Contagem de componentes disponíveis por tipo
- Estatísticas por marca
- Atualização em tempo real

### 6. **Controlo de Acesso**
- 🔒 **Acesso restrito a nível 7+** (Técnico Sénior)
- Verificação de permissões em todas as operações
- Log de atividades (criação, edição, eliminação)

## 📁 Estrutura de Ficheiros

### Base de Dados
```
app/Database/Migrations/
  └── 2026-01-26-000001_CreateInutilizadosKitdigitalTable.php
```

### Modelo
```
app/Models/
  └── InutilizadosKitdigitalModel.php
```

### Controlador
```
app/Controllers/
  └── InutilizadosKitdigitalController.php
```

### Views
```
app/Views/inutilizados_kitdigital/
  ├── index.php       # Listagem principal
  ├── view.php        # Visualização detalhada
  └── qrcode.php      # Visualização do QR Code
```

### Configuração
```
app/Config/
  └── Routes.php      # Rotas adicionadas
app/Views/layout/partials/
  └── sidebar.php     # Menu adicionado
```

## 🗄️ Estrutura da Tabela

```sql
CREATE TABLE inutilizados_kitdigital (
  id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  n_serie VARCHAR(255) NOT NULL,
  marca VARCHAR(100) NOT NULL,
  modelo VARCHAR(100) NULL,
  ram TINYINT(1) DEFAULT 1,
  disco TINYINT(1) DEFAULT 1,
  teclado TINYINT(1) DEFAULT 1,
  ecra TINYINT(1) DEFAULT 1,
  bateria TINYINT(1) DEFAULT 1,
  caixa TINYINT(1) DEFAULT 1,
  outros TEXT NULL,
  observacoes TEXT NULL,
  qr_code VARCHAR(255) NULL,
  id_tecnico INT(11) UNSIGNED NULL,
  estado VARCHAR(50) DEFAULT 'ativo',
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  deleted_at DATETIME NULL
);
```

## 🚀 Instalação e Configuração

### 1. Instalar Dependências
```bash
composer require endroid/qr-code
```
ou
```bash
composer update
```

### 2. Executar Migration
```bash
php spark migrate
```

**OU** executar o SQL diretamente:
```bash
mysql -u [usuario] -p [nome_base_dados] < CREATE_TABLE_INUTILIZADOS_KITDIGITAL.sql
```

### 3. Criar Diretório para QR Codes
O sistema cria automaticamente, mas pode criar manualmente:
```bash
mkdir -p writable/uploads/qrcodes
chmod 777 writable/uploads/qrcodes
```

### 4. Verificar Permissões
- Utilizador deve ter **nível 7 ou superior** (Técnico Sénior)
- Verificar se está logado no sistema

## 🔗 Rotas Disponíveis

```php
// Listagem principal
GET /inutilizados-kitdigital

// Obter dados para DataTable (AJAX)
GET /inutilizados-kitdigital/getData

// Obter estatísticas (AJAX)
GET /inutilizados-kitdigital/getStats

// Criar novo equipamento
POST /inutilizados-kitdigital/create

// Atualizar equipamento
POST /inutilizados-kitdigital/update/{id}

// Obter detalhes de um equipamento
GET /inutilizados-kitdigital/getDetails/{id}

// Eliminar equipamento
POST /inutilizados-kitdigital/delete/{id}

// Ver página de detalhes
GET /inutilizados-kitdigital/view/{id}

// Ver QR Code
GET /inutilizados-kitdigital/viewQRCode/{id}

// Download QR Code
GET /inutilizados-kitdigital/downloadQRCode/{id}

// Buscar por componente
GET /inutilizados-kitdigital/buscarPorComponente?componente=ram
```

## 📍 Localização no Menu

```
Kit Digital
  └── Equipamentos Inutilizados  (⚠️ Visível apenas para nível 7+)
```

O menu aparece logo abaixo de "Reparações Externas" dentro da secção "Kit Digital".

## 🎨 Interface

### Dashboard Principal
- Cards de estatísticas gerais
- Cards de componentes disponíveis
- Tabela interativa com DataTables
- Botões de ação: Ver QR, Editar, Eliminar

### Modal de Criação/Edição
- Campos: N/Série, Marca, Modelo, Estado
- Switches para cada componente
- Campo de texto para outros componentes
- Campo de observações

### Página de Visualização
- Informações completas do equipamento
- Estado de cada componente com ícones
- QR Code incorporado
- Botões de ação rápida

### Página do QR Code
- QR Code grande para impressão
- Informações do equipamento
- Botão de download
- Botão de impressão
- CSS otimizado para impressão

## 📊 Fluxo de Trabalho

1. **Registar Equipamento**
   - Técnico regista equipamento inutilizado
   - Sistema gera QR Code automaticamente
   - Estado inicial: "Ativo"

2. **Imprimir QR Code**
   - Aceder à página do equipamento
   - Imprimir ou fazer download do QR Code
   - Colar no equipamento físico

3. **Canibalizar Componente**
   - Quando um componente é retirado
   - Editar equipamento
   - Desmarcar componente utilizado
   - Sistema atualiza automaticamente

4. **Estado Esgotado**
   - Quando todos componentes marcados como utilizados
   - Sistema pode marcar automaticamente como "esgotado"

5. **Descartar**
   - Equipamento sem mais utilidade
   - Alterar estado para "descartado"
   - Ou eliminar definitivamente

## 🔍 Funcionalidades Adicionais

### Pesquisa por Componente
```javascript
GET /inutilizados-kitdigital/buscarPorComponente?componente=ram
```
Retorna todos os equipamentos com o componente específico disponível.

### Log de Atividades
Todas as ações são registadas:
- Criação de equipamento
- Edição de equipamento
- Eliminação de equipamento

### Validações
- Número de série obrigatório (mín. 3 caracteres)
- Marca obrigatória (mín. 2 caracteres)
- Estado deve ser: ativo, esgotado ou descartado
- Componentes devem ser 0 ou 1

## 🛠️ Manutenção

### Limpeza de QR Codes Antigos
```bash
# Limpar QR Codes de equipamentos eliminados há mais de 30 dias
find writable/uploads/qrcodes -name "*.png" -mtime +30 -delete
```

### Regenerar QR Code
O sistema regenera automaticamente se o ficheiro não existir.

## 📱 Compatibilidade

- ✅ Responsivo (mobile-friendly)
- ✅ DataTables com paginação
- ✅ Bootstrap 5
- ✅ Bootstrap Icons
- ✅ SweetAlert2 para confirmações

## 🔐 Segurança

- Verificação de nível de acesso em todas as rotas
- Proteção contra SQL injection (uso de Model)
- Validação de dados no servidor
- CSRF protection (CodeIgniter 4)
- Soft delete (recuperação possível)

## 📝 Notas Importantes

1. **QR Code Library**: Requer `endroid/qr-code` instalado via Composer
2. **Permissões**: Apenas utilizadores nível 7+ têm acesso
3. **Diretório**: writable/uploads/qrcodes deve ter permissões de escrita
4. **Backup**: Fazer backup regular da tabela e dos QR Codes

## 🐛 Troubleshooting

### QR Code não é gerado
- Verificar se `endroid/qr-code` está instalado
- Verificar permissões do diretório writable/uploads/qrcodes
- Ver logs em writable/logs/

### Acesso negado
- Verificar nível do utilizador (deve ser 7+)
- Verificar sessão ativa

### Erro ao salvar
- Verificar campos obrigatórios
- Ver mensagens de validação
- Consultar logs do servidor

## 📞 Suporte

Para questões ou problemas, consultar:
- Logs: `writable/logs/log-[data].log`
- Base de dados: tabela `inutilizados_kitdigital`
- Logs de atividade: função `log_activity()`

---

**Versão**: 1.0  
**Data**: 26 de Janeiro de 2026  
**Autor**: Sistema de Gestão Escolar
