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

        // Dados do utilizador
        $userdata = [
            'oauth_id'    => $googleUser->getId(),
            'name'        => $googleUser->getName(),
            'email'       => $email,
            'profile_img' => $googleUser->getPicture(),
            'updated_at'  => date('Y-m-d H:i:s'),
            'level'       => 0,
            'status'      => 1,
        ];

        $existingUser = $this->userModel->where('email', $email)->first();
        if ($existingUser) {
           $this->userModel->update($existingUser['id'], $userdata);
        } else {
            $this->userModel->insertUserData($userdata);
        }

        session()->set('LoggedUserData', $userdata);

        return redirect()->to('layout/dashboard');
    }

    public function profile()
    {
        $userData = session()->get('LoggedUserData');

        if (!$userData) {
            return redirect()->to('/login')->with('error', 'Faça login para continuar.');
        }
           // DEBUG: Mostra dados diretamente para confirmar se estão na sessão
    // echo '<pre>';
    // print_r($userData);
    // echo '</pre>';
    // exit;

        return view('layout/dashboard', ['user' => $userData]);
    }

    public function logout()
    {
        session()->remove(['LoggedUserData']);
        session()->setFlashdata('Success', 'Logout efetuado com sucesso.');
        return redirect()->to('/login');
    }


}
