<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DebugController extends BaseController
{
    public function checkSession()
    {
        $userModel = new \App\Models\UserModel();
        
        $sessionData = [
            'user_id' => session()->get('user_id'),
            'isLoggedIn' => session()->get('isLoggedIn'),
            'level' => session()->get('level'),
            'LoggedUserData' => session()->get('LoggedUserData'),
        ];
        
        $userId = session()->get('user_id');
        $userExists = null;
        if ($userId) {
            $userExists = $userModel->find($userId);
        }
        
        $allUsers = $userModel->findAll();
        
        $data = [
            'sessionData' => $sessionData,
            'userExists' => $userExists,
            'allUsers' => $allUsers,
        ];
        
        return $this->response->setJSON($data);
    }
    
    public function fixSession()
    {
        $userModel = new \App\Models\UserModel();
        
        // Pegar primeiro usuário disponível
        $user = $userModel->first();
        
        if ($user) {
            session()->set('user_id', $user['id']);
            session()->set('isLoggedIn', true);
            session()->set('level', $user['level'] ?? 0);
            session()->set('LoggedUserData', $user);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sessão corrigida',
                'user' => $user
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Nenhum usuário encontrado na base de dados'
        ]);
    }
    
    public function testEmailPage()
    {
        return view('teste_email');
    }
    
    public function testEmail()
    {
        $email = \Config\Services::email();
        
        $to = $this->request->getPost('to_email');
        $subject = $this->request->getPost('subject');
        $message = $this->request->getPost('message');
        
        try {
            $email->setFrom(getenv('email.fromEmail'), getenv('email.fromName'));
            $email->setTo($to);
            $email->setSubject($subject);
            $email->setMessage($message);
            
            if ($email->send()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Email enviado com sucesso para ' . $to
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Falha ao enviar email',
                    'debug' => $email->printDebugger(['headers', 'subject', 'body'])
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage(),
                'debug' => $email->printDebugger(['headers', 'subject', 'body'])
            ]);
        }
    }
}
