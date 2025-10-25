<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class PrivacyController extends BaseController
{
    /**
     * Página de Política de Privacidade
     */
    public function index()
    {
        $data = [
            'title' => 'Política de Privacidade'
        ];

        return view('privacy/privacy_policy', $data);
    }

    /**
     * Página de Termos de Serviço
     */
    public function terms()
    {
        $data = [
            'title' => 'Termos de Serviço'
        ];

        return view('privacy/terms_of_service', $data);
    }
}
