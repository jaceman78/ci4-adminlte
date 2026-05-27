# Correção: Erro 403 ao Carregar Fotos de Perfil no Servidor

## Problema

Ao tentar alterar a foto de perfil em produção (https://escoladigital.cloud/perfil), o navegador apresentava erro 403 ao tentar carregar a imagem:

```
1773923859_1cf2d3e58809777089a4.png:1 Failed to load resource: the server responded with a status of 403 ()
```

## Causa

O problema tinha **duas causas principais**:

### 1. Filtro de Manutenção Bloqueando Uploads
O `MaintenanceFilter` adicionado recentemente estava configurado para verificar **todas as requisições** globalmente, incluindo o acesso aos ficheiros de upload em `writable/uploads/profiles/`.

### 2. .htaccess Bloqueando Acesso Direto
O arquivo `.htaccess` no diretório `writable/` contém a regra:
```apache
<IfModule authz_core_module>
    Require all denied
</IfModule>
```

Esta regra bloqueia **todo o acesso direto** ao diretório writable, incluindo subdirectórios. No servidor de produção, isto impedia o Apache de servir as imagens mesmo através das rotas do CodeIgniter.

## Solução Implementada

### 1. Atualização do MaintenanceFilter

Adicionado lista de exceções para permitir acesso a rotas de assets e uploads mesmo durante manutenção:

**Arquivo**: `app/Filters/MaintenanceFilter.php`

```php
// Permitir acesso a rotas públicas e assets
$allowedSegments = [
    'manutencao',
    'login',
    'logout',
    'writable',  // Uploads de ficheiros
    'assets',    // Assets estáticos
    'public',    // Páginas públicas
    'adminlte'   // AdminLTE assets
];

if (in_array($segment, $allowedSegments)) {
    return $request;
}
```

### 2. Criação de .htaccess Específicos

Criados arquivos `.htaccess` nos diretórios de uploads que **sobrescrevem** a regra do diretório pai:

**Arquivo**: `writable/uploads/profiles/.htaccess`

```apache
# Permitir acesso aos ficheiros de imagem
<IfModule authz_core_module>
    <FilesMatch "\.(jpg|jpeg|png|gif|webp)$">
        Require all granted
    </FilesMatch>
</IfModule>
<IfModule !authz_core_module>
    <FilesMatch "\.(jpg|jpeg|png|gif|webp)$">
        Order Allow,Deny
        Allow from all
    </FilesMatch>
</IfModule>

# Negar acesso a outros tipos de ficheiros
<FilesMatch "^(?!.*\.(jpg|jpeg|png|gif|webp)$)">
    <IfModule authz_core_module>
        Require all denied
    </IfModule>
    <IfModule !authz_core_module>
        Order Deny,Allow
        Deny from all
    </IfModule>
</FilesMatch>
```

**Arquivo**: `writable/uploads/qrcodes/.htaccess` (preventivo)

Similar ao anterior, mas permite também `.svg`.

## Como Aplicar no Servidor

### 1. Via Git

Se estiver a fazer deploy via Git:

```bash
git pull origin main
```

Os ficheiros `.htaccess` serão atualizados automaticamente.

### 2. Via FTP/SFTP

Se usar FTP/SFTP, certifique-se de:

1. Upload do arquivo atualizado: `app/Filters/MaintenanceFilter.php`
2. Upload dos novos arquivos:
   - `writable/uploads/profiles/.htaccess`
   - `writable/uploads/qrcodes/.htaccess`

⚠️ **Importante**: Configurar cliente FTP para mostrar ficheiros ocultos (que começam com `.`)

### 3. Verificar Permissões

Certifique-se de que o diretório tem permissões corretas no servidor:

```bash
chmod 755 writable/uploads/profiles
chmod 644 writable/uploads/profiles/.htaccess
chmod 644 writable/uploads/profiles/*.png
chmod 644 writable/uploads/profiles/*.jpg
```

## Rotas Envolvidas

O sistema usa a seguinte rota para servir as imagens:

**Arquivo**: `app/Config/Routes.php`

```php
$routes->get('writable/uploads/profiles/(:any)', function($filename) {
    $filepath = WRITEPATH . 'uploads/profiles/' . $filename;
    if (file_exists($filepath)) {
        $mime = mime_content_type($filepath);
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
});
```

Esta rota permite que o CodeIgniter sirva os ficheiros mesmo que o diretório `writable` esteja protegido.

## Fluxo de Upload e Visualização

### Upload (Funciona):
1. Utilizador seleciona imagem → `ProfileController::uploadProfileImage()`
2. Ficheiro guardado em `writable/uploads/profiles/[nome_aleatorio].png`
3. Retorna JSON com `filename` e `url`

### Visualização (Corrigido):
1. Browser solicita: `https://escoladigital.cloud/writable/uploads/profiles/1773923859_1cf2d3e58809777089a4.png`
2. Rota do CodeIgniter intercepta (Routes.php)
3. **OU** Apache serve diretamente (se `.htaccess` permitir)
4. Imagem exibida

## Testes Realizados

- ✅ Upload de foto funciona
- ✅ Foto exibida após upload
- ✅ Foto mantém-se após refresh
- ✅ Filtro de manutenção não bloqueia imagens
- ✅ Utilizadores sem nível 9 ainda conseguem ver imagens mesmo em modo manutenção

## Ficheiros Alterados

```
app/Filters/MaintenanceFilter.php
writable/uploads/profiles/.htaccess (novo)
writable/uploads/qrcodes/.htaccess (novo)
```

## Data da Correção

19/03/2026

## Prevenção de Problemas Similares

Para evitar problemas semelhantes no futuro:

1. **Sempre adicionar exceções** no `MaintenanceFilter` para rotas de assets
2. **Criar `.htaccess` específicos** em diretórios de uploads
3. **Testar em produção** após adicionar filtros globais
4. **Verificar logs do Apache** para erros 403/404

## Monitorização

Após aplicar a correção, monitorizar:
- Console do browser (F12) para erros 403/404
- Logs do Apache: `/var/log/apache2/error.log` ou `/var/log/httpd/error_log`
- Logs do CodeIgniter: `writable/logs/log-[data].log`
