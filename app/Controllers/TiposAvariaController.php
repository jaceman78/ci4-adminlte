<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TiposAvariaModel;
use CodeIgniter\API\ResponseTrait;

class TiposAvariaController extends BaseController
{
    use ResponseTrait;

    protected $tiposAvariaModel;

    public function __construct()
    {
        $this->tiposAvariaModel = new TiposAvariaModel();
    }

    public function index()
    {
        $data = [
            'page_title' => 'Gestão de Tipos de Avaria',
            'page_subtitle' => 'Listagem e gestão de tipos de avaria'
        ];
        return view('tipos_avaria/tipos_avaria_index', $data);
    }

    public function getDataTable()
    {
        $tipos = $this->tiposAvariaModel->findAll();
        return $this->response->setJSON(['data' => $tipos]);
    }

    public function getTipoAvaria($id = null)
    {
        $tipo = $this->tiposAvariaModel->find($id);
        if ($tipo) {
            return $this->respond($tipo);
        } else {
            return $this->failNotFound('Tipo de avaria não encontrado.');
        }
    }

    public function create()
    {
        $rules = [
            'descricao' => 'required|min_length[3]|max_length[150]|is_unique[tipos_avaria.descricao]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'descricao' => $this->request->getPost('descricao')
        ];

        if ($this->tiposAvariaModel->insert($data)) {
            return $this->respondCreated(['message' => 'Tipo de avaria criado com sucesso.']);
        } else {
            return $this->failServerError('Não foi possível criar o tipo de avaria.');
        }
    }

    public function update($id = null)
    {
        $rules = [
            'descricao' => 'required|min_length[3]|max_length[150]|is_unique[tipos_avaria.descricao,id,' . $id . ']'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'descricao' => $this->request->getPost('descricao')
        ];

        if ($this->tiposAvariaModel->update($id, $data)) {
            return $this->respond(['message' => 'Tipo de avaria atualizado com sucesso.']);
        } else {
            return $this->failServerError('Não foi possível atualizar o tipo de avaria.');
        }
    }

    public function delete($id = null)
    {
        if ($this->tiposAvariaModel->delete($id)) {
            return $this->respondDeleted(['message' => 'Tipo de avaria eliminado com sucesso.']);
        } else {
            return $this->failServerError('Não foi possível eliminar o tipo de avaria.');
        }
    }

    public function getStatistics()
    {
        $totalTiposAvaria = $this->tiposAvariaModel->countAllResults();
        return $this->respond(['total_tipos_avaria' => $totalTiposAvaria]);
    }
        public function getAll()
    {
        $tipos = $this->tiposAvariaModel->findAll();
        return $this->respond($tipos);
    }
}