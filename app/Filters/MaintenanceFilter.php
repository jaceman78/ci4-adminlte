<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ManutencaoModel;

class MaintenanceFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verificar se o utilizador está logado
        $userData = session()->get('LoggedUserData');
        $userLevel = $userData['level'] ?? 0;

        // Utilizadores nível 9 podem sempre aceder (bypass do modo manutenção)
        if ($userLevel == 9) {
            return $request;
        }

        // Verificar se estamos na página de manutenção ou login (evitar loop)
        $uri = service('uri');
        $segment = $uri->getSegment(1);
        
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

        // Verificar se o site está em manutenção
        $manutencaoModel = new ManutencaoModel();
        if ($manutencaoModel->estaEmManutencao()) {
            // Redirecionar para página de manutenção
            return redirect()->to('/manutencao/aviso');
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Não precisa fazer nada depois
    }
}
