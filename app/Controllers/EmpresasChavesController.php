<?php

namespace App\Controllers;

use App\Models\EmpresaChaveAcessoModel;
use CodeIgniter\RESTful\ResourceController;

class EmpresasChavesController extends ResourceController
{
    protected $modelName = 'App\Models\EmpresaChaveAcessoModel';
    protected $format    = 'json';

    public function __construct()
    {
        helper(['log', 'LogHelper']);
    }

    /**
     * Verificar se usuário é super admin
     */
    private function verificarAcesso(): bool
    {
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        return $userLevel >= 9; // Apenas Super Administrador
    }

    /**
     * Página principal de gestão
     */
    public function index()
    {
        if (!$this->verificarAcesso()) {
            return redirect()->to('/')->with('error', 'Acesso negado. Apenas Super Administradores.');
        }

        log_activity(
            'empresas_chaves',
            'view',
            null,
            'Acedeu à página de gestão de chaves de acesso'
        );

        $data = [
            'title' => 'Gestão de Chaves de Acesso - Empresas',
            'stats' => $this->model->getEstatisticas(),
        ];

        return view('empresas_chaves/index', $data);
    }

    /**
     * Obter dados para DataTable via AJAX
     */
    public function getDataTable()
    {
        if (!$this->verificarAcesso()) {
            return $this->failUnauthorized('Acesso negado');
        }

        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Requisição inválida');
        }

        $chaves = $this->model->withDeleted()->findAll();

        $data = [];
        foreach ($chaves as $chave) {
            $statusBadge = $chave['ativo'] == 1 
                ? '<span class="badge bg-success">Ativo</span>' 
                : '<span class="badge bg-danger">Inativo</span>';

            $ultimoAcesso = $chave['ultimo_acesso'] 
                ? date('d/m/Y H:i', strtotime($chave['ultimo_acesso']))
                : '<span class="text-muted">Nunca</span>';

            $actions = '
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-info btn-view" data-id="' . $chave['id'] . '" title="Ver">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-edit" data-id="' . $chave['id'] . '" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-primary btn-regenerate" data-id="' . $chave['id'] . '" title="Regenerar Chave">
                        <i class="bi bi-arrow-repeat"></i>
                    </button>
                    <button class="btn btn-danger btn-delete" data-id="' . $chave['id'] . '" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>';

            $data[] = [
                $chave['id'],
                $chave['empresa_nome'],
                '<code class="chave-acesso">' . substr($chave['chave_acesso'], 0, 20) . '...</code>',
                $statusBadge,
                $chave['total_acessos'],
                $ultimoAcesso,
                $chave['ip_ultimo_acesso'] ?? '-',
                $actions
            ];
        }

        return $this->respond([
            'draw' => intval($this->request->getPost('draw') ?? 1),
            'recordsTotal' => count($chaves),
            'recordsFiltered' => count($chaves),
            'data' => $data
        ]);
    }

    /**
     * Criar nova chave de acesso
     */
    public function create()
    {
        if (!$this->verificarAcesso()) {
            return $this->failUnauthorized('Acesso negado');
        }

        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Requisição inválida');
        }

        $data = $this->request->getPost();
        
        // Gerar chave automaticamente
        $data['chave_acesso'] = $this->model->gerarChaveAcesso();
        $data['ativo'] = 1;

        if ($this->model->insert($data)) {
            $id = $this->model->getInsertID();
            $chaveGerada = $data['chave_acesso'];

            log_activity(
                'empresas_chaves',
                'create',
                $id,
                'Criou nova chave de acesso para empresa: ' . $data['empresa_nome']
            );

            return $this->respondCreated([
                'success' => true,
                'message' => 'Chave de acesso criada com sucesso!',
                'data' => [
                    'id' => $id,
                    'chave_acesso' => $chaveGerada
                ]
            ]);
        }

        return $this->fail([
            'success' => false,
            'message' => 'Erro ao criar chave de acesso',
            'errors' => $this->model->errors()
        ]);
    }

    /**
     * Obter detalhes de uma chave
     */
    public function show($id = null)
    {
        if (!$this->verificarAcesso()) {
            return $this->failUnauthorized('Acesso negado');
        }

        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Requisição inválida');
        }

        $chave = $this->model->withDeleted()->find($id);

        if (!$chave) {
            return $this->failNotFound('Chave não encontrada');
        }

        return $this->respond([
            'success' => true,
            'data' => $chave
        ]);
    }

    /**
     * Atualizar chave de acesso
     */
    public function update($id = null)
    {
        if (!$this->verificarAcesso()) {
            return $this->failUnauthorized('Acesso negado');
        }

        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Requisição inválida');
        }

        $chave = $this->model->find($id);
        if (!$chave) {
            return $this->failNotFound('Chave não encontrada');
        }

        $data = $this->request->getPost();
        
        // Não permitir atualizar a chave de acesso diretamente
        unset($data['chave_acesso']);

        if ($this->model->update($id, $data)) {
            log_activity(
                'empresas_chaves',
                'update',
                $id,
                'Atualizou chave de acesso da empresa: ' . ($data['empresa_nome'] ?? $chave['empresa_nome'])
            );

            return $this->respond([
                'success' => true,
                'message' => 'Chave de acesso atualizada com sucesso!'
            ]);
        }

        return $this->fail([
            'success' => false,
            'message' => 'Erro ao atualizar chave de acesso',
            'errors' => $this->model->errors()
        ]);
    }

    /**
     * Regenerar chave de acesso
     */
    public function regenerate($id = null)
    {
        if (!$this->verificarAcesso()) {
            return $this->failUnauthorized('Acesso negado');
        }

        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Requisição inválida');
        }

        $chave = $this->model->find($id);
        if (!$chave) {
            return $this->failNotFound('Chave não encontrada');
        }

        $novaChave = $this->model->gerarChaveAcesso();

        if ($this->model->update($id, ['chave_acesso' => $novaChave])) {
            log_activity(
                'empresas_chaves',
                'regenerate',
                $id,
                'Regenerou chave de acesso da empresa: ' . $chave['empresa_nome'],
                ['chave_antiga' => substr($chave['chave_acesso'], 0, 10) . '...'],
                ['chave_nova' => substr($novaChave, 0, 10) . '...']
            );

            return $this->respond([
                'success' => true,
                'message' => 'Chave regenerada com sucesso!',
                'data' => ['chave_acesso' => $novaChave]
            ]);
        }

        return $this->fail([
            'success' => false,
            'message' => 'Erro ao regenerar chave de acesso'
        ]);
    }

    /**
     * Eliminar chave de acesso (soft delete)
     */
    public function delete($id = null)
    {
        if (!$this->verificarAcesso()) {
            return $this->failUnauthorized('Acesso negado');
        }

        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Requisição inválida');
        }

        $chave = $this->model->find($id);
        if (!$chave) {
            return $this->failNotFound('Chave não encontrada');
        }

        if ($this->model->delete($id)) {
            log_activity(
                'empresas_chaves',
                'delete',
                $id,
                'Eliminou chave de acesso da empresa: ' . $chave['empresa_nome']
            );

            return $this->respondDeleted([
                'success' => true,
                'message' => 'Chave de acesso eliminada com sucesso!'
            ]);
        }

        return $this->fail([
            'success' => false,
            'message' => 'Erro ao eliminar chave de acesso'
        ]);
    }

    /**
     * Alternar status (ativo/inativo)
     */
    public function toggleStatus($id = null)
    {
        if (!$this->verificarAcesso()) {
            return $this->failUnauthorized('Acesso negado');
        }

        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Requisição inválida');
        }

        $chave = $this->model->find($id);
        if (!$chave) {
            return $this->failNotFound('Chave não encontrada');
        }

        $novoStatus = $chave['ativo'] == 1 ? 0 : 1;

        if ($this->model->update($id, ['ativo' => $novoStatus])) {
            log_activity(
                'empresas_chaves',
                'toggle_status',
                $id,
                'Alterou status da chave de acesso da empresa: ' . $chave['empresa_nome'] . ' para ' . ($novoStatus ? 'Ativo' : 'Inativo')
            );

            return $this->respond([
                'success' => true,
                'message' => 'Status alterado com sucesso!',
                'data' => ['ativo' => $novoStatus]
            ]);
        }

        return $this->fail([
            'success' => false,
            'message' => 'Erro ao alterar status'
        ]);
    }

    /**
     * Obter lista de empresas das reparações externas
     */
    public function getEmpresasReparacoes()
    {
        if (!$this->verificarAcesso()) {
            return $this->failUnauthorized('Acesso negado');
        }

        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Requisição inválida');
        }

        $empresas = $this->model->getEmpresasReparacoes();

        return $this->respond([
            'success' => true,
            'data' => $empresas
        ]);
    }
}
