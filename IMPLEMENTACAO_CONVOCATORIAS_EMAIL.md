# Sistema de Envio de Convocatórias por Email

## Funcionalidades Implementadas

### 1. Envio de Convocatórias
O sistema permite enviar convocatórias de vigilância de exames para os professores de duas formas:

- **Envio Individual**: Enviar convocatória para um vigilante específico
- **Envio em Massa**: Enviar convocatórias para todos os vigilantes de uma sessão de exame

### 2. Confirmação via Email
Cada email de convocatória contém um botão de confirmação que permite ao professor confirmar sua presença com apenas um clique. O link de confirmação:
- É único e seguro
- Válido por 30 dias
- Atualiza automaticamente o estado da convocatória para "Confirmado"

### 3. Interface de Usuário
Na página de detalhes de cada sessão de exame (`/sessoes-exame/detalhes/{id}`), há:
- Botão "Enviar para Todos" no cabeçalho da tabela de convocatórias
- Botão de envelope individual em cada linha da tabela para envio unitário
- Coluna adicional mostrando o email e telefone de cada vigilante

## Arquivos Criados/Modificados

### Controller
- **app/Controllers/SessaoExameController.php**
  - `enviarConvocatoria($convocatoriaId)` - Envia email para um vigilante
  - `enviarConvocatoriasTodas($sessaoId)` - Envia emails para todos os vigilantes
  - `confirmarConvocatoria($token)` - Confirma presença via link
  - `enviarEmailConvocatoria($convocatoria)` - Método auxiliar privado
  - `generateToken($data)` - Gera token de confirmação
  - `decodeToken($token)` - Decodifica e valida token

### Views
1. **app/Views/emails/convocatoria_exame.php**
   - Template HTML profissional para o email
   - Contém todas as informações do exame
   - Botão de confirmação destacado
   - Lembrete sobre antecedência de 30 minutos

2. **app/Views/sessoes_exame/confirmacao_sucesso.php**
   - Página de sucesso após confirmação
   - Mostra detalhes da convocatória confirmada
   - Design responsivo e moderno

3. **app/Views/sessoes_exame/confirmacao_erro.php**
   - Página de erro para links inválidos ou expirados
   - Instruções sobre o que fazer

4. **app/Views/sessoes_exame/detalhes.php** (modificado)
   - Adicionados botões de envio de email
   - JavaScript com SweetAlert2 para confirmações
   - Coluna adicional na tabela com ações

### Rotas
**app/Config/Routes.php** - Adicionadas 3 novas rotas:
```php
$routes->post('enviar-convocatoria/(:num)', 'SessaoExameController::enviarConvocatoria/$1');
$routes->post('enviar-convocatorias-todas/(:num)', 'SessaoExameController::enviarConvocatoriasTodas/$1');
$routes->get('confirmar/(:any)', 'SessaoExameController::confirmarConvocatoria/$1');
```

## Como Usar

### Enviar Convocatória Individual
1. Acesse a página de detalhes da sessão: `/sessoes-exame/detalhes/{id}`
2. Na tabela de convocatórias, clique no botão de envelope (📧) ao lado do vigilante
3. Confirme o envio no popup
4. O sistema envia o email e mostra mensagem de sucesso/erro

### Enviar para Todos
1. Na mesma página de detalhes, clique em "Enviar para Todos" no topo da tabela
2. Confirme o envio em massa
3. O sistema envia emails para todos e mostra estatísticas (enviados/erros)

### Confirmação pelo Vigilante
1. O vigilante recebe o email
2. Clica no botão "CONFIRMAR PRESENÇA"
3. É redirecionado para página de confirmação
4. O estado da convocatória é atualizado para "Confirmado"

## Detalhes Técnicos

### Segurança
- Token de confirmação em Base64
- Validação de expiração (30 dias)
- Verificação de convocatória já confirmada
- CSRF protection em todas as chamadas POST

### Email
- Formato HTML responsivo
- Compatível com diversos clientes de email
- Link de confirmação funciona mesmo se copiado e colado
- Configuração via `app/Config/Email.php`

### Tratamento de Erros
- Validação de email do professor
- Log de erros detalhado
- Mensagens amigáveis ao usuário
- Retry disponível (usuário pode tentar novamente)

### Estado da Convocatória
Estados possíveis:
- **Pendente** (padrão) - Badge amarelo
- **Confirmado** - Badge verde
- **Rejeitado** - Badge vermelho

## Requisitos

### Configuração de Email
Certifique-se de que as seguintes variáveis de ambiente estão configuradas no `.env`:
```env
email.fromEmail = seu-email@exemplo.com
email.fromName = Nome do Remetente
email.protocol = smtp
email.SMTPHost = smtp.gmail.com
email.SMTPUser = seu-email@exemplo.com
email.SMTPPass = sua-senha-ou-app-password
email.SMTPPort = 587
email.SMTPCrypto = tls
email.SMTPAuth = true
email.mailType = html
```

### Dependências
- CodeIgniter 4
- Bootstrap 5
- Bootstrap Icons
- jQuery
- SweetAlert2

## Testes Recomendados

1. **Teste de Envio Individual**
   - Enviar para um professor com email válido
   - Verificar recebimento do email
   - Clicar no link de confirmação
   - Verificar atualização do estado

2. **Teste de Envio em Massa**
   - Criar sessão com múltiplas convocatórias
   - Enviar para todos
   - Verificar estatísticas de envio
   - Confirmar recebimento por alguns vigilantes

3. **Teste de Erros**
   - Tentar enviar para professor sem email
   - Usar link de confirmação expirado
   - Tentar confirmar convocatória já confirmada

## Melhorias Futuras Possíveis

1. Histórico de emails enviados
2. Reenvio de convocatória
3. Notificação ao admin quando vigilante confirma
4. Opção de rejeitar convocatória com motivo
5. Lembretes automáticos X dias antes do exame
6. Integração com SMS para envio paralelo
7. Dashboard com estatísticas de confirmações

## Suporte
Em caso de problemas:
1. Verificar logs em `writable/logs/`
2. Testar configuração de email em `/debug/test-email`
3. Verificar permissões de envio SMTP
4. Confirmar que emails não estão em spam
