<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TipoEquipamentosModel;
use CodeIgniter\API\ResponseTrait;

class TiposEquipamentosController extends BaseController
{
    use ResponseTrait;

    protected $tipoEquipamentosModel;

    public function __construct()
    {
        $this->tipoEquipamentosModel = new TipoEquipamentosModel();
    }

    public function index()
    {
        $data = [
            'page_title' => 'Gestão de Tipos de Equipamento',
            'page_subtitle' => 'Listagem e gestão de tipos de equipamento'
        ];
        return view('tipos_equipamentos/tipos_equipamentos_index', $data);
    }

    public function getDataTable()
    {
        $tipos = $this->tipoEquipamentosModel->findAll();
        return $this->respond(['data' => $tipos]);
    }

    public function getTipoEquipamento($id = null)
    {
        $tipo = $this->tipoEquipamentosModel->find($id);
        if ($tipo) {
            return $this->respond($tipo);
        } else {
            return $this->failNotFound('Tipo de equipamento não encontrado.');
        }
    }

    public function create()
    {
        $rules = [
            'nome' => 'required|min_length[3]|max_length[255]|is_unique[tipos_equipamento.nome]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'nome' => $this->request->getPost('nome'),
            'descricao' => $this->request->getPost('descricao')
        ];

        if ($this->tipoEquipamentosModel->insert($data)) {
            return $this->respondCreated(['message' => 'Tipo de equipamento criado com sucesso.']);
        } else {
            return $this->failServerError('Não foi possível criar o tipo de equipamento.');
        }
    }

    public function update($id = null)
    {
        $rules = [
            'id'   => 'permit_empty|integer',
            'nome' => 'required|min_length[3]|max_length[255]|is_unique[tipos_equipamento.nome,id,' . $id . ']'
        ];

        $data = [
            'id' => $id,
            'nome' => $this->request->getPost('nome'),
            'descricao' => $this->request->getPost('descricao')
        ];

        if (!$this->validate($rules, $data)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        if ($this->tipoEquipamentosModel->update($id, $data)) {
            return $this->respond(['message' => 'Tipo de equipamento atualizado com sucesso.']);
        } else {
            return $this->failServerError('Não foi possível atualizar o tipo de equipamento.');
        }
    }

    public function delete($id = null)
    {
        if ($this->tipoEquipamentosModel->delete($id)) {
            return $this->respondDeleted(['message' => 'Tipo de equipamento eliminado com sucesso.']);
        } else {
            return $this->failServerError('Não foi possível eliminar o tipo de equipamento.');
        }
    }

    public function getStatistics()
    {
        $totalTipos = $this->tipoEquipamentosModel->countAllResults();
        return $this->respond(['total_tipos' => $totalTipos]);
    }
}

