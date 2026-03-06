<?php

namespace App\Controllers;

class PublicController extends BaseController
{
    /**
     * Página pública inicial (sem login)
     */
    public function home()
    {
        $data = [
            'title' => 'Página Pública',
        ];

        return view('public/home', $data);
    }

    /**
     * Exemplo de página pública adicional
     */
    public function about()
    {
        $data = [
            'title' => 'Sobre',
        ];

        return view('public/about', $data);
    }
}
