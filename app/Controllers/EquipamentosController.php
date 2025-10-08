<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EquipamentosModel;
use App\Models\TipoEquipamentosModel;
use App\Models\SalasModel;
use CodeIgniter\API\ResponseTrait;

class EquipamentosController extends BaseController
{
    use ResponseTrait;

    protected $equipamentosModel;
    protected $tipoEquipamentosModel;
    protected $salasModel;

    public function __construct()
    {
        $this->equipamentosModel = new EquipamentosModel();
        $this->tipoEquipamentosModel = new TipoEquipamentosModel();
        $this->salasModel = new SalasModel();
    }

    public function index()
    {
        $data = [
            'page_title' => 'Gestão de Equipamentos',
            'page_subtitle' => 'Listagem e gestão de equipamentos',
            'tipos_equipamento' => $this->tipoEquipamentosModel->findAll(),
            'salas' => $this->salasModel->findAll()
        ];
        return view('equipamentos/equipamentos_index', $data);
    }

    public function getDataTable()
    {
        $equipamentos = $this->equipamentosModel
            ->select('equipamentos.*, salas.codigo_sala as sala_nome, tipos_equipamento.nome as tipo_nome')
            ->join('salas', 'salas.id = equipamentos.sala_id', 'left')
            ->join('tipos_equipamento', 'tipos_equipamento.id = equipamentos.tipo_id', 'left')
            ->findAll();

        // Adapta os dados para o DataTable
        $data = [];
        foreach ($equipamentos as $eq) {
            $data[] = [
                'id' => $eq['id'],
                'sala_nome' => $eq['sala_nome'] ?? '',
                'tipo_nome' => $eq['tipo_nome'] ?? '',
                'marca' => $eq['marca'],
                'modelo' => $eq['modelo'],
                'numero_serie' => $eq['numero_serie'],
                'estado' => $eq['estado'],
                'data_aquisicao' => $eq['data_aquisicao'],
                'observacoes' => $eq['observacoes'],
            ];
        }

        return $this->respond(['data' => $data]);
    }

    public function getEquipamento($id = null)
    {
        $eq = $this->equipamentosModel
            ->select('equipamentos.*, salas.codigo_sala as sala_nome, tipos_equipamento.nome as tipo_nome')
            ->join('salas', 'salas.id = equipamentos.sala_id', 'left')
            ->join('tipos_equipamento', 'tipos_equipamento.id = equipamentos.tipo_id', 'left')
            ->find($id);

        if ($eq) {
            return $this->respond($eq);
        } else {
            return $this->failNotFound('Equipamento não encontrado.');
        }
    }

    public function create()
    {
        $rules = [
            'sala_id'        => 'permit_empty|is_natural',
            'tipo_id'        => 'required|is_natural_no_zero',
            'marca'          => 'permit_empty|max_length[100]',
            'modelo'         => 'permit_empty|max_length[100]',
            'numero_serie'   => 'permit_empty|max_length[255]|is_unique[equipamentos.numero_serie]',
            'estado'         => 'required|in_list[ativo,inativo,pendente]',
            'data_aquisicao' => 'permit_empty|valid_date',
            'observacoes'    => 'permit_empty|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'sala_id'        => $this->request->getPost('sala_id'),
            'tipo_id'        => $this->request->getPost('tipo_id'),
            'marca'          => $this->request->getPost('marca'),
            'modelo'         => $this->request->getPost('modelo'),
            'numero_serie'   => $this->request->getPost('numero_serie'),
            'estado'         => $this->request->getPost('estado'),
            'data_aquisicao' => $this->request->getPost('data_aquisicao'),
            'observacoes'    => $this->request->getPost('observacoes')
        ];

        if ($this->equipamentosModel->insert($data)) {
            return $this->respondCreated(['message' => 'Equipamento criado com sucesso.']);
        } else {
            return $this->failServerError('Não foi possível criar o equipamento.');
        }
    }

    public function update($id = null)
    {
        $rules = [
            'sala_id'        => 'permit_empty|is_natural',
            'tipo_id'        => 'required|is_natural_no_zero',
            'marca'          => 'permit_empty|max_length[100]',
            'modelo'         => 'permit_empty|max_length[100]',
            'numero_serie'   => 'permit_empty|max_length[255]|is_unique[equipamentos.numero_serie,id,' . $id . ']',
            'estado'         => 'required|in_list[ativo,inativo,pendente]',
            'data_aquisicao' => 'permit_empty|valid_date',
            'observacoes'    => 'permit_empty|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'sala_id'        => $this->request->getPost('sala_id'),
            'tipo_id'        => $this->request->getPost('tipo_id'),
            'marca'          => $this->request->getPost('marca'),
            'modelo'         => $this->request->getPost('modelo'),
            'numero_serie'   => $this->request->getPost('numero_serie'),
            'estado'         => $this->request->getPost('estado'),
            'data_aquisicao' => $this->request->getPost('data_aquisicao'),
            'observacoes'    => $this->request->getPost('observacoes')
        ];

        if ($this->equipamentosModel->update($id, $data)) {
            return $this->respond(['message' => 'Equipamento atualizado com sucesso.']);
        } else {
            return $this->failServerError('Não foi possível atualizar o equipamento.');
        }
    }

    public function delete($id = null)
    {
        if ($this->equipamentosModel->delete($id)) {
            return $this->respondDeleted(['message' => 'Equipamento eliminado com sucesso.']);
        } else {
            return $this->failServerError('Não foi possível eliminar o equipamento.');
        }
    }

    public function getStatistics()
    {
        $totalEquipamentos = $this->equipamentosModel->countAllResults();
        $equipamentosPorEstado = $this->equipamentosModel
            ->select('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->findAll();
        $equipamentosPorTipo = $this->equipamentosModel
            ->select('tipo_id, COUNT(*) as total')
            ->groupBy('tipo_id')
            ->findAll();

        return $this->respond([
            'total_equipamentos' => $totalEquipamentos,
            'por_estado'         => $equipamentosPorEstado,
            'por_tipo'           => $equipamentosPorTipo
        ]);
    }
        public function getAll()
    {
        $equipamentos = $this->equipamentosModel->findAll();
        return $this->respond($equipamentos);
    }
}

