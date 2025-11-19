# 🌐 Configuração de Páginas Públicas com Subdomínio

## 📋 Resumo

Este projeto agora suporta **páginas públicas** (sem necessidade de login) que podem ser servidas num **subdomínio separado**.

---

## ✅ O Que Foi Implementado

### 1. **Controller Público**
**Ficheiro**: `app/Controllers/PublicController.php`

```php
public function home()    // Página inicial pública
public function about()   // Página "Sobre"
```

### 2. **Views Públicas**
- `app/Views/public/home.php` - Página inicial pública
- `app/Views/public/about.php` - Página "Sobre"
- Usam o layout: `app/Views/layout/public.php` (sem navbar/sidebar de backend)

### 3. **Rotas Configuradas**
**Ficheiro**: `app/Config/Routes.php`

#### Rotas Locais (com `/public`)
```php
$routes->group('public', function($routes) {
    $routes->get('/', 'PublicController::home');
    $routes->get('about', 'PublicController::about');
});
```

#### Rotas para Subdomínio
```php
// Se PUBLIC_HOST estiver definido no .env, mapeia o subdomínio diretamente
if (! empty($publicHost)) {
    $currentHost = $_SERVER['HTTP_HOST'] ?? '';
    if (strcasecmp($currentHost, $publicHost) === 0) {
        $routes->get('/', 'PublicController::home');
        $routes->get('sobre', 'PublicController::about');
    }
}
```

### 4. **Configuração do Subdomínio**
**Ficheiro**: `.env`

```properties
# Subdomínio público (sem login)
PUBLIC_HOST = public.escoladigital.cloud
```

---

## 🚀 Como Usar

### **Localmente (Desenvolvimento)**

Aceda sem login em:
- http://localhost:8080/public → Página inicial pública
- http://localhost:8080/public/about → Página "Sobre"

### **No Servidor (Produção)**

#### No Subdomínio Público:
- https://public.escoladigital.cloud/ → Página inicial pública
- https://public.escoladigital.cloud/sobre → Página "Sobre"

#### No Domínio Principal (Backend):
- https://escoladigital.cloud → Sistema de gestão (requer login)
- https://escoladigital.cloud/dashboard → Dashboard
- https://escoladigital.cloud/tickets → Tickets
- etc.

---

## ⚙️ Configuração no Hostinger

### **Passo 1: Criar o Subdomínio**

1. Aceda ao painel da Hostinger
2. Vá para **Domínios** → **Subdomínios**
3. Crie o subdomínio: `public.escoladigital.cloud`
4. **Document Root**: Aponte para a **mesma pasta `public/`** do projeto principal
   - Exemplo: `/home/u520317771/domains/escoladigital.cloud/public_html/public`

### **Passo 2: Configurar .env (já feito)**

O `.env` já está configurado com:
```properties
PUBLIC_HOST = public.escoladigital.cloud
```

### **Passo 3: Testar**

Aceda a:
- https://public.escoladigital.cloud/
- https://public.escoladigital.cloud/sobre

✅ Deve ver as páginas públicas **sem pedir login**!

---

## 🎨 Como Adicionar Mais Páginas Públicas

### 1. Adicionar Método no Controller

**Ficheiro**: `app/Controllers/PublicController.php`

```php
public function contactos()
{
    $data = ['title' => 'Contactos'];
    return view('public/contactos', $data);
}
```

### 2. Criar a View

**Ficheiro**: `app/Views/public/contactos.php`

```php
<?= $this->extend('layout/public') ?>
<?= $this->section('title') ?>Contactos<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <h1>Entre em Contacto</h1>
    <p>Formulário de contacto aqui...</p>
</div>
<?= $this->endSection() ?>
```

### 3. Adicionar Rota

**Ficheiro**: `app/Config/Routes.php`

No grupo `public`:
```php
$routes->group('public', function($routes) {
    $routes->get('/', 'PublicController::home');
    $routes->get('about', 'PublicController::about');
    $routes->get('contactos', 'PublicController::contactos'); // ✨ Nova rota
});
```

E no mapeamento do subdomínio:
```php
if (strcasecmp($currentHost, $publicHost) === 0) {
    $routes->get('/', 'PublicController::home');
    $routes->get('sobre', 'PublicController::about');
    $routes->get('contactos', 'PublicController::contactos'); // ✨ Nova rota
}
```

---

## 🔐 Segurança

### ✅ **Separação de Contextos**
- Páginas públicas **não têm acesso** a sessões do backend
- Utilizadores não autenticados **não conseguem** aceder a rotas protegidas
- Backend continua protegido em `escoladigital.cloud`

### ✅ **Sem Conflitos**
- As rotas públicas não interferem com o sistema de gestão
- O layout público é minimalista (sem sidebar/navbar de admin)

---

## 📚 Casos de Uso

### **1. Landing Page Institucional**
- Apresentação da escola/agrupamento
- Informações gerais, contactos, missão

### **2. Formulários Públicos**
- Inscrições abertas
- Pedidos de informação
- Sugestões de visitantes não autenticados

### **3. Documentação Pública**
- FAQs
- Manuais de utilizador
- Políticas (privacidade/termos)

### **4. Portal de Notícias**
- Comunicados
- Eventos
- Galeria de fotos

---

## 🛠️ Troubleshooting

### **Problema: Subdomínio mostra "404 Not Found"**

**Solução**: Verifique se o Document Root no Hostinger aponta para a pasta `public/` correta.

### **Problema: Subdomínio pede login**

**Solução**: Verifique se `PUBLIC_HOST` está correto no `.env` e se o servidor está a ler o ficheiro.

### **Problema: CSS/JS não carregam no subdomínio**

**Solução**: Use `base_url()` nas views públicas para garantir caminhos absolutos:
```php
<link href="<?= base_url('adminlte/dist/css/adminlte.min.css') ?>" rel="stylesheet">
```

---

## ✨ Próximos Passos (Opcionais)

1. **SEO**: Adicionar meta tags nas páginas públicas
2. **Analytics**: Integrar Google Analytics no layout público
3. **Cache**: Configurar cache agressivo para conteúdo público
4. **CDN**: Usar CDN para assets estáticos (CSS/JS/imagens)
5. **Formulários**: Criar formulários públicos com captcha

---

**🎉 Páginas públicas implementadas com sucesso!**

Teste agora:
- **Local**: http://localhost:8080/public
- **Produção**: https://public.escoladigital.cloud
