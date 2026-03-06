<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ExameModel;

class ExameController extends BaseController
{
    protected $exameModel;

    public function __construct()
    {
        $this->exameModel = new ExameModel();
    }

    /**
     * Lista todos os exames
     */
    public function index()
    {
        // Verificar autenticação
        if (!session()->has('user_id')) {
            return redirect()->to('/login')->with('error', 'Por favor, faça login.');
        }

        // Verificar permissões - apenas níveis 4, 8 e 9
        if ($redirect = $this->requireSecExamesPermissions()) {
            return $redirect;
        }

        $data = [
            'title' => 'Exames/Provas',
            'exames' => $this->exameModel->orderBy('tipo_prova', 'ASC')
                                         ->orderBy('ano_escolaridade', 'ASC')
                                         ->orderBy('nome_prova', 'ASC')
                                         ->findAll()
        ];

        return view('exames/index', $data);
    }

    /**
     * DataTable Ajax
     */
    public function getDataTable()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $request = $this->request->getPost();
        
        $start = $request['start'] ?? 0;
        $length = $request['length'] ?? 10;
        $search = $request['search']['value'] ?? '';
        
        $orderColumn = 'id';
        $orderDir = 'desc';
        
        if (isset($request['order'][0])) {
            $columns = ['id', 'codigo_prova', 'nome_prova', 'tipo_prova', 'ano_escolaridade', 'ativo'];
            $orderColumnIndex = $request['order'][0]['column'];
            $orderColumn = $columns[$orderColumnIndex] ?? 'id';
            $orderDir = $request['order'][0]['dir'] ?? 'desc';
        }

        $builder = $this->exameModel->builder();

        // Contar total de registos
        $totalRecords = $this->exameModel->countAll();

        // Aplicar pesquisa se existir
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('codigo_prova', $search)
                    ->orLike('nome_prova', $search)
                    ->orLike('tipo_prova', $search)
                    ->orLike('ano_escolaridade', $search)
                    ->groupEnd();
        }

        // Contar registos filtrados
        $recordsFiltered = $builder->countAllResults(false);

        // Buscar dados com paginação
        $exames = $builder->orderBy($orderColumn, $orderDir)
                         ->limit($length, $start)
                         ->get()
                         ->getResultArray();

        $data = [];
        foreach ($exames as $exame) {
            $badge = $exame['ativo'] == 1 
                ? '<span class="badge bg-success">Ativo</span>' 
                : '<span class="badge bg-secondary">Inativo</span>';

            $tipoBadge = match($exame['tipo_prova']) {
                'Exame Nacional' => 'bg-primary',
                'Prova Final' => 'bg-info',
                'MODa' => 'bg-warning',
                default => 'bg-secondary'
            };

            $actions = '
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-primary" onclick="editExame(' . $exame['id'] . ')" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteExame(' . $exame['id'] . ')" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>';
            
            $data[] = [
                $exame['id'],
                $exame['codigo_prova'],
                $exame['nome_prova'],
                '<span class="badge ' . $tipoBadge . '">' . $exame['tipo_prova'] . '</span>',
                $exame['ano_escolaridade'] . 'º ano',
                $badge,
                $actions
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($request['draw'] ?? 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    /**
     * Retorna dados de um exame para edição
     */
    public function get($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('exames');
        }

        $exame = $this->exameModel->find($id);

        if (!$exame) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Exame não encontrado'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $exame
        ]);
    }

    /**
     * Guarda um novo exame
     */
    public function store()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('exames');
        }

        $data = [
            'codigo_prova' => $this->request->getPost('codigo_prova'),
            'nome_prova' => $this->request->getPost('nome_prova'),
            'tipo_prova' => $this->request->getPost('tipo_prova'),
            'ano_escolaridade' => $this->request->getPost('ano_escolaridade'),
            'ativo' => $this->request->getPost('ativo') ?? 1,
        ];

        if ($this->exameModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Exame criado com sucesso!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao criar exame',
            'errors' => $this->exameModel->errors()
        ]);
    }

    /**
     * Atualiza um exame
     */
    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('exames');
        }

        $data = [
            'id' => $id,
            'codigo_prova' => $this->request->getPost('codigo_prova'),
            'nome_prova' => $this->request->getPost('nome_prova'),
            'tipo_prova' => $this->request->getPost('tipo_prova'),
            'ano_escolaridade' => $this->request->getPost('ano_escolaridade'),
            'ativo' => $this->request->getPost('ativo') ?? 1,
        ];

        if ($this->exameModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Exame atualizado com sucesso!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar exame',
            'errors' => $this->exameModel->errors()
        ]);
    }

    /**
     * Elimina um exame
     */
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('exames');
        }

        if ($this->exameModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Exame eliminado com sucesso!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao eliminar exame'
        ]);
    }

    /**
     * API: Buscar exames por tipo
     */
    public function getByTipo($tipo)
    {
        $exames = $this->exameModel->getByTipo($tipo);
        return $this->response->setJSON([
            'success' => true,
            'data' => $exames
        ]);
    }

    /**
     * API: Buscar exames por ano
     */
    public function getByAno($ano)
    {
        $exames = $this->exameModel->getByAnoEscolaridade($ano);
        return $this->response->setJSON([
            'success' => true,
            'data' => $exames
        ]);
    }
}
