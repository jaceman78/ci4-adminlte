<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
    }

    /**
     * Verifica se o utilizador tem permissões para aceder à área de Sec. Exames
     * Apenas níveis 4, 8 e 9
     * 
     * @return bool
     */
    protected function checkSecExamesPermissions()
    {
        $userLevel = session()->get('level') ?? (session()->get('LoggedUserData')['level'] ?? 0);
        return in_array($userLevel, [4, 8, 9]);
    }

    /**
     * Redireciona para dashboard se não tiver permissões para Sec. Exames
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse|null
     */
    protected function requireSecExamesPermissions()
    {
        if (!$this->checkSecExamesPermissions()) {
            return redirect()->to('/dashboard')->with('error', 'Não tem permissões para aceder a esta área.');
        }
        return null;
    }
}
