<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\HorarioAulasModel;

class ProfileController extends BaseController
{
    protected $userModel;
    protected $horarioModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->horarioModel = new HorarioAulasModel();
        helper(['url', 'form']);
    }

    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Faça login para continuar.');
        }

        $user = $this->userModel->find($userId);

        if (!$user) {
            session()->destroy();
            return redirect()->to('/login')->with('error', 'Utilizador não encontrado.');
        }

        $disciplinasTurmas = [];

        if (!empty($user['NIF'])) {
            $horario = $this->horarioModel->getHorarioProfessor($user['NIF']);

            foreach ($horario as $linha) {
                $disciplina = $linha['nome_disciplina'] ?? $linha['disciplina_id'] ?? '';
                $turma = $linha['nome_turma'] ?? $linha['codigo_turma'] ?? '';

                if ($disciplina === '' && $turma === '') {
                    continue;
                }

                $key = $disciplina . '|' . $turma;
                if (!isset($disciplinasTurmas[$key])) {
                    $disciplinasTurmas[$key] = [
                        'disciplina' => $disciplina,
                        'turma'      => $turma,
                    ];
                }
            }

            $disciplinasTurmas = array_values($disciplinasTurmas);
        }

        $data = [
            'title'             => 'Perfil',
            'user'              => $user,
            'disciplinasTurmas' => $disciplinasTurmas,
        ];

        return view('perfil/index', $data);
    }

    public function update()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Acesso negado.']);
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Sessão expirada.']);
        }

        $telefone = trim((string) $this->request->getPost('telefone'));
        $profileImg = trim((string) $this->request->getPost('profile_img'));

        $updateData = [];

        if ($telefone !== '') {
            $updateData['telefone'] = $telefone;
        } else {
            $updateData['telefone'] = null;
        }

        if ($profileImg !== '') {
            $updateData['profile_img'] = $profileImg;
        }

        if (empty($updateData)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nenhuma alteração para guardar.']);
        }

        if (!$this->userModel->update($userId, $updateData)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao atualizar perfil.',
            ]);
        }

        $user = $this->userModel->find($userId);
        if ($user) {
            session()->set('LoggedUserData', $user);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso.',
        ]);
    }
}
