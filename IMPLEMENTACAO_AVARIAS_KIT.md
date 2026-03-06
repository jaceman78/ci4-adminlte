# Implementação - Sistema de Reporte de Avarias Kit Digital

## 📋 Resumo da Implementação

Foi implementado um sistema completo para reportar avarias do Kit Digital, similar ao sistema de requisições existente.

## 🗄️ Base de Dados

### Criar a Tabela
Execute o arquivo SQL na base de dados:
```bash
CREATE_TABLE_REGISTO_AVARIAS_KIT.sql
```

Ou execute manualmente via phpMyAdmin/MySQL:
```sql
CREATE TABLE IF NOT EXISTS `registo_avarias_kit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_aluno` varchar(5) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `turma` varchar(50) NOT NULL,
  `nif` varchar(9) NOT NULL,
  `email_aluno` varchar(255) NOT NULL,
  `email_ee` varchar(255) NOT NULL,
  `estado` enum('pendente','a analisar','por levantar','rejeitado','anulado','terminado') NOT NULL DEFAULT 'pendente',
  `avaria` text NOT NULL,
  `obs` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `finished_at` datetime DEFAULT NULL,
  `id_ano_letivo` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_numero_aluno` (`numero_aluno`),
  KEY `idx_nif` (`nif`),
  KEY `idx_created_at` (`created_at`),
  KEY `fk_avarias_kit_ano_letivo` (`id_ano_letivo`),
  CONSTRAINT `fk_avarias_kit_ano_letivo` FOREIGN KEY (`id_ano_letivo`) REFERENCES `ano_letivo` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## 📁 Arquivos Criados

### Models
- `app/Models/RegistoAvariasKitModel.php`

### Controllers
- `app/Controllers/AvariasKitController.php` (área pública)
- `app/Controllers/AvariasKitAdminController.php` (área privada)

### Views
- `app/Views/public/reportar_avaria_kit.php` (formulário público)
- `app/Views/avarias_kit_admin/index.php` (gestão privada)

### Rotas Adicionadas
Em `app/Config/Routes.php`:

**Área Pública:**
- `/reportar-avaria-kit` - Formulário de reporte
- `/reportar-avaria-kit/enviar` - Submeter reporte

**Área Privada (nível 5+):**
- `/avarias-kit-admin` - Listagem
- `/avarias-kit-admin/get-data` - DataTable AJAX
- `/avarias-kit-admin/view/{id}` - Ver detalhes
- `/avarias-kit-admin/update-status/{id}` - Atualizar estado
- `/avarias-kit-admin/delete/{id}` - Eliminar
- `/avarias-kit-admin/export` - Exportar CSV
- `/avarias-kit-admin/get-stats` - Estatísticas

## 🎨 Interface

### Menu Público
O menu público agora tem um dropdown "Kit Digital" com:
- **Requisição** - Link para o formulário de requisição (existente)
- **Reportar Avaria** - Link para o novo formulário de avaria

### Menu Privado (Sidebar)
No menu "Kit Digital" foi adicionado:
- Listagem de Pedidos (existente)
- **Avarias Reportadas** (novo)
- Estatísticas (existente)

## ✨ Funcionalidades

### Área Pública
- Formulário com captcha anti-bot
- Validação de NIF português
- Campos: número aluno, nome, turma, NIF, emails, descrição da avaria
- Submissão via AJAX com toasts de feedback
- Email de confirmação automático
- Atribuição automática do ano letivo ativo

### Área Privada (Admin)
- DataTable com pesquisa e paginação server-side
- Filtros por estado (pendente, a analisar, por levantar, terminado, rejeitado, anulado)
- Ver detalhes completos da avaria
- Atualizar estado com observações
- Envio automático de email quando muda estado
- Exportar para CSV (filtrado por estado)
- Estatísticas em tempo real com badges
- Eliminar registos (nível 8+)
- Sistema de logs de atividade integrado

## 📧 Emails Automáticos

O sistema envia emails automaticamente:
1. **Confirmação** - Quando avaria é reportada
2. **Mudança de Estado** - Quando admin atualiza o estado
3. Emails enviados para o aluno e CC para encarregado de educação

## 🔒 Níveis de Acesso

- **Nível 5+**: Acesso à gestão de avarias reportadas
- **Nível 8+**: Pode eliminar registos

## 🧪 Testar a Funcionalidade

### 1. Testar Formulário Público
Aceder a: `http://localhost:8080/reportar-avaria-kit`
- Preencher formulário
- Verificar toast de sucesso
- Verificar email enviado

### 2. Testar Área Privada
Aceder a: `http://localhost:8080/avarias-kit-admin` (com login nível 5+)
- Ver listagem
- Filtrar por estado
- Ver detalhes
- Atualizar estado
- Exportar CSV

## 📝 Notas Importantes

1. **Ano Letivo**: A avaria é automaticamente associada ao ano letivo ativo (status=1 na tabela ano_letivo)
2. **Validação NIF**: Usa algoritmo de validação português
3. **Captcha**: Sistema anti-bot simples com soma matemática
4. **Toasts**: Usa Toastr.js para notificações elegantes
5. **Logs**: Todas as ações são registadas no sistema de logs

## 🔄 Estados Disponíveis

1. **Pendente** (padrão) - Avaria reportada, aguarda análise
2. **A Analisar** - Em análise pela equipa
3. **Por Levantar** - Equipamento pronto para levantamento
4. **Terminado** - Processo concluído
5. **Rejeitado** - Avaria rejeitada
6. **Anulado** - Pedido anulado

## 🎨 UI/UX

- Design responsivo (Bootstrap 5)
- Ícones Bootstrap Icons
- Toasts para feedback
- Modais para confirmações
- DataTables para listagens
- Badges coloridos para estados
- Formulários com validação HTML5 e JavaScript
- Loading states em botões

## ✅ Checklist de Implementação

- [x] Criar tabela SQL
- [x] Criar Model
- [x] Criar Controller público
- [x] Criar Controller admin
- [x] Criar View pública
- [x] Criar View privada
- [x] Atualizar rotas
- [x] Atualizar menu público
- [x] Atualizar sidebar privado
- [x] Sistema de emails
- [x] Validações
- [x] Sistema de logs
- [x] Exportação CSV
- [x] Toasts de feedback

## 🚀 Próximos Passos

1. Executar o SQL para criar a tabela
2. Testar o formulário público
3. Testar a gestão privada
4. Configurar emails (se necessário)
5. Ajustar permissões de utilizadores

---

**Data de Implementação**: 7 de Janeiro de 2026  
**Desenvolvido por**: GitHub Copilot
