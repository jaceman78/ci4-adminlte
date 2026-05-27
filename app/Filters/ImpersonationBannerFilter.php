<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Injeta um banner fixo no HTML quando uma impersonificação está ativa.
 * Aplicado como filtro "after" em rotas autenticadas.
 */
class ImpersonationBannerFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Nada a fazer antes
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $impersonated = session()->get('ImpersonatedUserData');
        if (!$impersonated) {
            return $response;
        }

        // Só injetar em respostas HTML
        $contentType = $response->getHeaderLine('Content-Type');
        if (!empty($contentType) && stripos($contentType, 'text/html') === false) {
            return $response;
        }

        $body = $response->getBody();
        if (empty($body) || stripos($body, '<body') === false) {
            return $response;
        }

        $name  = esc($impersonated['name'] ?? 'Desconhecido');
        $level = (int)($impersonated['level'] ?? 0);
        $since = session()->get('ImpersonatedAt') ?? '';
        $stopUrl = base_url('impersonation/stop');

        $banner = <<<HTML
<div id="impersonation-banner" style="
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 99999;
    background: #dc3545;
    color: #fff;
    font-family: Arial, sans-serif;
    font-size: 13px;
    padding: 6px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 6px rgba(0,0,0,.4);
">
    <span>
        <strong>⚠️ MODO IMPERSONIFICAÇÃO</strong>
        &nbsp;—&nbsp; A ver como: <strong>{$name}</strong> (nível {$level})
        {$since}
    </span>
    <a href="{$stopUrl}" style="
        background:#fff;
        color:#dc3545;
        border-radius:4px;
        padding:3px 10px;
        font-weight:bold;
        text-decoration:none;
        font-size:12px;
        white-space:nowrap;
    ">✕ Terminar</a>
</div>
<style>
  body { padding-top: 36px !important; }
  .app-header { top: 36px !important; }
</style>
HTML;

        // Injetar imediatamente a seguir à tag <body ...>
        $body = preg_replace('/(<body[^>]*>)/i', '$1' . $banner, $body, 1);

        return $response->setBody($body);
    }
}
