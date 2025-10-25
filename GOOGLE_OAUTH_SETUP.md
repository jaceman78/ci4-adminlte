# Google OAuth 2.0 - Informações de Configuração

## URLs Públicas Necessárias para o Google OAuth

Para configurar o Google OAuth 2.0 no Google Cloud Console, forneça as seguintes URLs:

### 1. **Política de Privacidade (Privacy Policy)**
```
https://seudominio.com/privacy
```
ou em desenvolvimento:
```
http://localhost:8080/privacy
```

### 2. **Termos de Serviço (Terms of Service)**
```
https://seudominio.com/privacy/terms
```
ou em desenvolvimento:
```
http://localhost:8080/privacy/terms
```

### 3. **URI de Redirecionamento OAuth (Authorized redirect URIs)**
```
https://seudominio.com/login/loginWithGoogle
```
ou em desenvolvimento:
```
http://localhost:8080/login/loginWithGoogle
```

### 4. **Domínios Autorizados (Authorized domains)**
```
seudominio.com
```
ou em desenvolvimento:
```
localhost
```

---

## Passos para Configurar no Google Cloud Console

### 1. Aceder ao Google Cloud Console
1. Vá para [Google Cloud Console](https://console.cloud.google.com/)
2. Selecione ou crie um projeto

### 2. Ativar Google+ API
1. No menu lateral, vá para **APIs & Services** > **Library**
2. Procure por "Google+ API"
3. Clique em **Enable**

### 3. Configurar OAuth Consent Screen
1. Vá para **APIs & Services** > **OAuth consent screen**
2. Escolha **External** ou **Internal** (para organizações G Suite)
3. Preencha os campos obrigatórios:
   - **App name**: Sistema de Gestão Escolar - AEJB
   - **User support email**: antonioneto@aejoaodebarros.pt
   - **App logo**: (opcional - logo da escola)
   - **Application home page**: `https://seudominio.com` ou `http://localhost:8080`
   - **Application privacy policy link**: `https://seudominio.com/privacy`
   - **Application terms of service link**: `https://seudominio.com/privacy/terms`
   - **Authorized domains**: `seudominio.com` (ou `localhost` para dev)
   - **Developer contact information**: antonioneto@aejoaodebarros.pt

4. Clique em **Save and Continue**

### 4. Configurar Scopes
1. Clique em **Add or Remove Scopes**
2. Adicione os seguintes scopes:
   - `userinfo.email`
   - `userinfo.profile`
3. Clique em **Update** e depois **Save and Continue**

### 5. Criar Credenciais OAuth 2.0
1. Vá para **APIs & Services** > **Credentials**
2. Clique em **Create Credentials** > **OAuth client ID**
3. Selecione **Web application**
4. Preencha:
   - **Name**: Sistema Gestão AEJB
   - **Authorized JavaScript origins**:
     - `http://localhost:8080` (desenvolvimento)
     - `https://seudominio.com` (produção)
   - **Authorized redirect URIs**:
     - `http://localhost:8080/login/loginWithGoogle` (desenvolvimento)
     - `https://seudominio.com/login/loginWithGoogle` (produção)
5. Clique em **Create**

### 6. Copiar Credenciais
1. Após criar, copie o **Client ID** e **Client Secret**
2. Cole no ficheiro `.env`:

```env
# Google OAuth 2.0
GOOGLE_CLIENT_ID = "seu-client-id-aqui.apps.googleusercontent.com"
GOOGLE_CLIENT_SECRET = "seu-client-secret-aqui"
GOOGLE_REDIRECT_URI = "http://localhost:8080/login/loginWithGoogle"
```

### 7. Configurar para Produção
Quando colocar em produção (Hostinger ou outro):
1. Adicione o domínio real em **Authorized domains**
2. Adicione a URL de produção em **Authorized redirect URIs**
3. Atualize os links de Privacidade e Termos no OAuth consent screen
4. Atualize o `.env` com a URI de produção

---

## Verificação de Conformidade Google OAuth

O Google OAuth exige que as seguintes páginas estejam acessíveis publicamente (sem login):

✅ **Política de Privacidade** - `/privacy`
- Explica como os dados dos utilizadores são recolhidos, usados e protegidos
- Menciona especificamente o uso do Google OAuth
- Descreve os direitos dos utilizadores (RGPD)

✅ **Termos de Serviço** - `/privacy/terms`
- Define as regras de utilização do sistema
- Explica responsabilidades dos utilizadores
- Menciona direitos de propriedade intelectual

✅ **Links Visíveis**
- Na página de login
- No rodapé de todas as páginas
- Acessíveis sem necessidade de autenticação

---

## Checklist para Aprovação Google OAuth

Antes de submeter para revisão do Google, certifique-se de:

- [ ] Política de Privacidade publicada e acessível
- [ ] Termos de Serviço publicados e acessíveis
- [ ] Links claramente visíveis na página de login
- [ ] OAuth consent screen completamente preenchido
- [ ] Logo da aplicação adicionado (opcional mas recomendado)
- [ ] Domínios autorizados configurados
- [ ] Redirect URIs corretos
- [ ] Scopes mínimos necessários (email e profile)
- [ ] Email de suporte válido e monitorizado
- [ ] Aplicação testada em ambiente de desenvolvimento

---

## URLs para Fornecer ao Google

Quando o Google pedir as URLs públicas, forneça:

**Política de Privacidade:**
- Desenvolvimento: `http://localhost:8080/privacy`
- Produção: `https://seudominio.com/privacy`

**Termos de Serviço:**
- Desenvolvimento: `http://localhost:8080/privacy/terms`
- Produção: `https://seudominio.com/privacy/terms`

**Homepage da Aplicação:**
- Desenvolvimento: `http://localhost:8080`
- Produção: `https://seudominio.com`

---

## Notas Importantes

1. **Ambiente de Desenvolvimento**: Use `localhost` apenas para testes. O Google pode não aprovar aplicações que usem localhost em produção.

2. **HTTPS Obrigatório**: Em produção, o Google OAuth exige HTTPS. Certifique-se de que o Hostinger tem SSL configurado.

3. **Domínio Verificado**: Para evitar o ecrã de aviso "This app isn't verified", pode:
   - Submeter a aplicação para verificação do Google (recomendado)
   - OU limitar a utilizadores da sua organização G Suite (Internal)

4. **RGPD**: As páginas de Privacidade e Termos estão em conformidade com RGPD português e mencionam a CNPD.

5. **Atualizações**: Sempre que alterar como usa os dados do Google OAuth, atualize a Política de Privacidade.

---

## Contacto para Suporte

- **Email**: antonioneto@aejoaodebarros.pt
- **Sistema**: Sistema de Gestão Escolar - AEJB
- **Documentação Google OAuth**: https://developers.google.com/identity/protocols/oauth2

---

**Criado em**: <?= date('d/m/Y') ?>  
**Última Atualização**: <?= date('d/m/Y') ?>
