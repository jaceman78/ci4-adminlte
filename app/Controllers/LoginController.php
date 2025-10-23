<?php 

namespace App\Controllers;

use App\Models\UserModel;
use Google_Client;
use Google_Service_Oauth2;

class LoginController extends BaseController
{
    private $userModel;
    private $googleClient;

    public function __construct()
    {
        helper(['session', 'url']);

        require ROOTPATH . 'vendor/autoload.php';

        $this->userModel = new UserModel();

        // Configuração Google OAuth
        $this->googleClient = new Google_Client();
        $this->googleClient->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $this->googleClient->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
        $this->googleClient->setRedirectUri(getenv('GOOGLE_REDIRECT_URI'));
        $this->googleClient->addScope('email');
        $this->googleClient->addScope('profile');
      

    }

    public function index()
    {
        if (session()->get('LoggedUserData')) {
            
            return redirect()->to('layout/dashboard');
        }

   $googleLoginUrl = $this->googleClient->createAuthUrl();

    $data['googleButton'] = '
        <a href="' . $googleLoginUrl . '" class="btn btn-info w-100">
            <i class="fab fa-google me-2"></i> Entrar com Google
        </a>';

        return view('auth/login', $data);
    }

    public function loginWithGoogle()
    {
        $code = $this->request->getVar('code');
        if (!$code) {
            session()->setFlashdata('Error', 'Código de autenticação não encontrado.');
            return redirect()->to('/login');
        }

        $token = $this->googleClient->fetchAccessTokenWithAuthCode($code);
        if (isset($token['error'])) {
            session()->setFlashdata('Error', 'Erro ao obter token do Google.');
            return redirect()->to('/login');
        }

        $this->googleClient->setAccessToken($token['access_token']);

        $googleService = new Google_Service_Oauth2($this->googleClient);
        $googleUser = $googleService->userinfo->get();

        $email = $googleUser->getEmail();
        $allowedDomains = ['@aejoaodebarros.pt'];
        $domain = substr(strrchr($email, "@"), 1);

        if (!in_array('@' . $domain, $allowedDomains)) {
            session()->setFlashdata('Error', 'Conta de email não autorizada!');
            return redirect()->to('/login');
        }

        $existingUser = $this->userModel->where('email', $email)->first();

        // Safe: se não existir utilizador, usa nível por omissão (0)
        $userlevel = 0;
        if ($existingUser && isset($existingUser['level'])) {
            $userlevel = (int) $existingUser['level'];
        }

        // Dados do utilizador
        $userdata = [
            'oauth_id'    => $googleUser->getId(),
            'name'        => $googleUser->getName(),
            'email'       => $email,
            'profile_img' => $googleUser->getPicture(),
            'updated_at'  => date('Y-m-d H:i:s'),
            'level'       => $userlevel,
            'status'      => 1,
        ];

        if ($existingUser) {
            $this->userModel->update($existingUser['id'], $userdata);
            $userId = $existingUser['id'];
        } else {
            $this->userModel->insert($userdata);
            // garantir o id do registo inserido
            $userId = $this->userModel->getInsertID();
        }

        // Buscar dados completos do usuário da base de dados (incluindo NIF)
        $fullUserData = $this->userModel->find($userId);

        // Definir dados da sessão
        session()->set('user_id', $userId);
        session()->set('isLoggedIn', true);
        session()->set('level', $userlevel);
        session()->set('LoggedUserData', $fullUserData); // Usar dados completos do BD
        session()->set('id', $userId); // Adicionar ID direto na sessão

        return redirect()->to('dashboard');
    }

    public function profile()
    {
        $userData = session()->get('LoggedUserData');

        if (!$userData) {
            return redirect()->to('/login')->with('error', 'Faça login para continuar.');
        }

        // Redirecionar para o dashboard personalizado
        return redirect()->to('dashboard');
    }

    public function logout()
    {
        session()->remove(['LoggedUserData', 'user_id', 'isLoggedIn', 'level']);
        session()->setFlashdata('Success', 'Logout efetuado com sucesso.');
        return redirect()->to('/login');
    }


}
