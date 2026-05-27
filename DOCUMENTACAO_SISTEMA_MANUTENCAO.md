# Sistema de Manutenção

## Descrição

Sistema de manutenção do site que permite aos administradores (nível 9) colocar o site em modo de manutenção, exibindo uma página de aviso aos utilizadores enquanto são realizadas atualizações ou correções.

## Data de Implementação

19/03/2026

## Funcionalidades

### 1. Controlo de Acesso
- Apenas utilizadores com **nível 9** podem aceder à página de administração do modo de manutenção
- Utilizadores nível 9 podem sempre aceder ao site, mesmo em modo de manutenção
- Todos os outros utilizadores são redirecionados para a página de aviso quando o modo está ativo

### 2. Página de Administração (`/manutencao/admin`)
Localizada no menu lateral **Dashboard > Manutenção** (apenas visível para nível 9)

Permite:
- **Ativar/Desativar** o modo de manutenção
- **Configurar mensagem personalizada** a exibir aos utilizadores
- **Definir data/hora prevista** para fim da manutenção (opcional)
- Visualizar o estado atual do sistema

### 3. Página Pública de Aviso (`/manutencao/aviso`)
- Design responsivo e atrativo
- Exibe a mensagem configurada pelo administrador
- Mostra a data/hora prevista para o retorno (se definida)
- Atualiza automaticamente a cada 5 minutos para verificar se a manutenção foi desativada

### 4. Filtro Global
- Verifica automaticamente em todas as requisições se o site está em modo de manutenção
- Redireciona automaticamente utilizadores (exceto nível 9) para a página de aviso
- Não interfere com páginas de login/logout

## Estrutura de Ficheiros

### Base de Dados
- **Ficheiro SQL**: `CREATE_TABLE_SISTEMA_MANUTENCAO.sql`
- **Tabela**: `sistema_manutencao`
  - `id` - ID único (sempre 1)
  - `ativo` - Estado do modo (0 = desativado, 1 = ativado)
  - `mensagem` - Mensagem personalizada
  - `data_inicio` - Data/hora de início da manutenção
  - `data_fim_prevista` - Data/hora prevista para fim (opcional)
  - `criado_por` - NIF do utilizador que ativou (int(11), FK para `user.NIF`)
  - `criado_em` / `atualizado_em` - Timestamps

### Backend
- **Model**: `app/Models/ManutencaoModel.php`
  - Métodos para verificar, ativar e desativar manutenção
  
- **Controller**: `app/Controllers/ManutencaoController.php`
  - `admin()` - Página de administração
  - `aviso()` - Página pública de aviso
  - `ativar()` - Endpoint AJAX para ativar
  - `desativar()` - Endpoint AJAX para desativar
  - `atualizar()` - Endpoint AJAX para atualizar configuração

- **Filter**: `app/Filters/MaintenanceFilter.php`
  - Filtro global que verifica todas as requisições
  - Bypass automático para utilizadores nível 9
  - Redireciona para página de aviso se manutenção ativa

### Frontend
- **View Admin**: `app/Views/manutencao/admin.php`
  - Interface completa com Bootstrap 5
  - Controlo ativar/desativar com confirmação (SweetAlert2)
  - Formulário de configuração com AJAX
  
- **View Pública**: `app/Views/manutencao/aviso.php`
  - Página standalone (não usa layout master)
  - Design moderno com animação
  - Auto-refresh a cada 5 minutos

### Configuração
- **Rotas**: `app/Config/Routes.php`
  - Grupo `/manutencao` com todas as rotas necessárias
  
- **Filtros**: `app/Config/Filters.php`
  - Filtro `maintenance` registado e ativo globalmente

- **Menu**: `app/Views/layout/partials/sidebar.php`
  - Nova entrada "Manutenção" no menu Dashboard (apenas nível 9)

## Instalação

### 1. Criar a Tabela
Execute o ficheiro SQL no MySQL:
```bash
mysql -u seu_usuario -p sua_base_dados < CREATE_TABLE_SISTEMA_MANUTENCAO.sql
```

Ou execute diretamente na base de dados via phpMyAdmin, HeidiSQL, ou outro cliente MySQL.

### 2. Verificar Ficheiros
Todos os ficheiros já foram criados automaticamente:
- Models, Controllers, Views, Filters
- Configuração de rotas e filtros
- Menu sidebar atualizado

### 3. Testar o Sistema

1. Faça login com um utilizador **nível 9**
2. No menu lateral, vá a **Dashboard > Manutenção**
3. Configure a mensagem desejada
4. Clique em **Ativar Manutenção**
5. Faça logout e tente aceder (ou use outro navegador)
6. Deve ver a página de aviso de manutenção
7. Faça login novamente como nível 9 para desativar

## Como Usar

### Ativar Modo de Manutenção

1. Aceda a `/manutencao/admin` (ou pelo menu Dashboard > Manutenção)
2. Configure a mensagem personalizada (opcional)
3. Defina a data/hora prevista para o retorno (opcional)
4. Clique em **"Ativar Manutenção"**
5. Confirme na janela de diálogo

### Desativar Modo de Manutenção

1. Aceda a `/manutencao/admin`
2. Clique em **"Desativar Manutenção"**
3. Confirme na janela de diálogo

### Atualizar Configuração

1. Altere a mensagem ou data prevista
2. Clique em **"Guardar Configuração"**
3. As alterações são aplicadas imediatamente

## Comportamento do Sistema

### Quando Manutenção está ATIVADA:
- ✅ Utilizadores nível 9: Acesso normal ao site
- ❌ Outros utilizadores: Redirecionados para página de aviso
- ✅ Página de login: Acessível
- ✅ Página de manutenção pública: Acessível

### Quando Manutenção está DESATIVADA:
- ✅ Todos os utilizadores: Acesso normal ao site
- ✅ Página de administração: Acessível apenas para nível 9

## Segurança

- ✅ Apenas utilizadores nível 9 podem ativar/desativar
- ✅ Verificação de nível em todos os endpoints
- ✅ Proteção AJAX contra requisições não autorizadas
- ✅ Logs de ativação/desativação (se helper de logs estiver disponível)

## Notas Importantes

1. **Utilizadores Nível 9** nunca são bloqueados, podendo sempre aceder ao site
2. A **página de aviso** recarrega automaticamente a cada 5 minutos
3. O **filtro** é aplicado globalmente, mas ignora rotas de login/logout/manutencao
4. A tabela `sistema_manutencao` tem sempre **apenas 1 registo** (ID = 1)
5. O sistema usa **SweetAlert2** para confirmações elegantes

## Troubleshooting

### Problema: "Foreign key constraint is incorrectly formed" (Erro 150) ao criar tabela
**Solução**: 
- A tabela foi atualizada para usar `user.NIF` como foreign key.
- **Importante**: O tipo de dados deve corresponder exatamente - `user.NIF` é `int(11)`, portanto `criado_por` também deve ser `int(11)`.
- Se o erro persistir, verifique que a coluna `user.NIF` existe e tem um índice (chave primária ou índice).

### Problema: "Acesso negado" ao tentar aceder à página de administração
**Solução**: Certifique-se de que está logado com um utilizador nível 9

### Problema: O filtro não está a funcionar
**Solução**: Verifique se o filtro está registado em `app/Config/Filters.php` e ativo em `$globals['before']`

### Problema: Erro ao ativar manutenção
**Solução**: Verifique se a tabela `sistema_manutencao` foi criada e tem um registo inicial

### Problema: Página de manutenção não aparece
**Solução**: Limpe o cache do navegador e verifique se o campo `ativo` na tabela está com valor `1`

## Melhorias Futuras (Opcionais)

- [ ] Agendar ativação/desativação automática
- [ ] Notificar utilizadores por email antes da manutenção
- [ ] Histórico de ativações/desativações
- [ ] Permitir lista de IPs que podem aceder durante manutenção
- [ ] Modo de manutenção parcial (apenas certas áreas do site)

## Créditos

Sistema desenvolvido para a plataforma **HardWork550 JB**  
Data: 19/03/2026  
Nível de Acesso Requerido: 9 (Administrador)
