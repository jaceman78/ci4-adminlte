<?php

namespace App\Controllers;

use App\Models\EmpresaChaveAcessoModel;
use App\Models\ReparacoesExternasModel;
use CodeIgniter\Controller;

class EmpresaPortalController extends Controller
{
    protected $chaveModel;
    protected $reparacoesModel;

    public function __construct()
    {
        $this->chaveModel = new EmpresaChaveAcessoModel();
        $this->reparacoesModel = new ReparacoesExternasModel();
        helper(['log', 'LogHelper']);
    }

    /**
     * Página de login
     */
    public function login()
    {
        // Se já está autenticado, redirecionar para dashboard
        if (session()->has('empresa_autenticada')) {
            return redirect()->to(base_url('empresa/dashboard'));
        }

        // Se há chave na URL, tentar autenticar automaticamente
        $chaveUrl = $this->request->getGet('chave');
        if ($chaveUrl) {
            return $this->autenticar($chaveUrl);
        }

        return view('empresa_portal/login');
    }

    /**
     * Processar login
     */
    public function processarLogin()
    {
        $chave = $this->request->getPost('chave_acesso');
        return $this->autenticar($chave);
    }

    /**
     * Autenticar empresa
     */
    private function autenticar($chave)
    {
        if (empty($chave)) {
            return redirect()->to(base_url('empresa/login'))->with('error', 'Chave de acesso não fornecida');
        }

        $empresaChave = $this->chaveModel->verificarChave($chave);

        if (!$empresaChave) {
            log_activity(
                'empresa_portal',
                'login_failed',
                null,
                'Tentativa de login com chave inválida',
                null,
                ['chave_tentada' => substr($chave, 0, 10) . '...', 'ip' => $this->request->getIPAddress()]
            );

            return redirect()->to(base_url('empresa/login'))->with('error', 'Chave de acesso inválida ou inativa');
        }

        // Registrar acesso
        $this->chaveModel->registrarAcesso($empresaChave['id'], $this->request->getIPAddress());

        // Guardar na sessão
        session()->set('empresa_autenticada', [
            'id' => $empresaChave['id'],
            'empresa_nome' => $empresaChave['empresa_nome'],
            'chave_acesso' => $empresaChave['chave_acesso'],
        ]);

        log_activity(
            'empresa_portal',
            'login_success',
            $empresaChave['id'],
            'Empresa autenticada: ' . $empresaChave['empresa_nome'],
            null,
            ['ip' => $this->request->getIPAddress()]
        );

        return redirect()->to(base_url('empresa/dashboard'))->with('success', 'Bem-vindo, ' . $empresaChave['empresa_nome']);
    }

    /**
     * Dashboard da empresa
     */
    public function dashboard()
    {
        if (!session()->has('empresa_autenticada')) {
            return redirect()->to(base_url('empresa/login'))->with('error', 'Necessário autenticação');
        }

        $empresaData = session()->get('empresa_autenticada');
        $empresaNome = $empresaData['empresa_nome'];

        // Buscar dados completos da empresa (incluindo plafond)
        $empresaCompleta = $this->chaveModel->find($empresaData['id']);

        // Buscar reparações da empresa
        $reparacoes = $this->reparacoesModel
            ->where('empresa_reparacao', $empresaNome)
            ->orderBy('data_envio', 'DESC')
            ->findAll();

        // Calcular estatísticas
        $stats = [
            'total' => count($reparacoes),
            'enviado' => 0,
            'em_reparacao' => 0,
            'reparado' => 0,
            'irreparavel' => 0,
            'cancelado' => 0,
            'custo_total' => 0,
        ];

        foreach ($reparacoes as $rep) {
            if (isset($stats[$rep['estado']])) {
                $stats[$rep['estado']]++;
            }
            $stats['custo_total'] += $rep['custo'] ?? 0;
        }

        // Calcular plafond e saldo disponível
        $plafond = $empresaCompleta['plafond_com_iva'] ?? 0;
        $gasto = $stats['custo_total'];
        $disponivel = $plafond - $gasto;

        $data = [
            'empresa' => $empresaData,
            'stats' => $stats,
            'reparacoes' => $reparacoes,
            'plafond' => $plafond,
            'gasto' => $gasto,
            'disponivel' => $disponivel,
        ];

        return view('empresa_portal/dashboard', $data);
    }

    /**
     * Obter dados para DataTable
     */
    public function getDataTable()
    {
        if (!session()->has('empresa_autenticada')) {
            return $this->response->setJSON(['error' => 'Não autenticado'])->setStatusCode(401);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Requisição inválida'])->setStatusCode(403);
        }

        $empresaData = session()->get('empresa_autenticada');
        $empresaNome = $empresaData['empresa_nome'];

        // Buscar reparações da empresa
        $reparacoes = $this->reparacoesModel
            ->where('empresa_reparacao', $empresaNome)
            ->orderBy('data_envio', 'DESC')
            ->findAll();

        $data = [];
        foreach ($reparacoes as $rep) {
            $estadoBadge = $this->getEstadoBadge($rep['estado']);
            $diasReparacao = isset($rep['dias_reparacao']) && $rep['dias_reparacao'] !== null 
                ? $rep['dias_reparacao'] . ' dias' 
                : '-';
            
            $actions = '
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-info btn-view" data-id="' . $rep['id_reparacao'] . '" title="Ver">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-edit" data-id="' . $rep['id_reparacao'] . '" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                </div>';

            $data[] = [
                $rep['n_serie_equipamento'],
                $rep['tipologia'],
                $rep['possivel_avaria'],
                $rep['data_envio'] ? date('d/m/Y', strtotime($rep['data_envio'])) : '-',
                number_format($rep['custo'] ?? 0, 2, ',', '.') . '€',
                $estadoBadge,
                $diasReparacao,
                $actions
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($this->request->getPost('draw') ?? 1),
            'recordsTotal' => count($reparacoes),
            'recordsFiltered' => count($reparacoes),
            'data' => $data
        ]);
    }

    /**
     * Obter detalhes de uma reparação
     */
    public function getReparacao($id)
    {
        if (!session()->has('empresa_autenticada')) {
            return $this->response->setJSON(['error' => 'Não autenticado'])->setStatusCode(401);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Requisição inválida'])->setStatusCode(403);
        }

        $empresaData = session()->get('empresa_autenticada');
        $empresaNome = $empresaData['empresa_nome'];

        $reparacao = $this->reparacoesModel->find($id);

        if (!$reparacao || $reparacao['empresa_reparacao'] !== $empresaNome) {
            return $this->response->setJSON(['error' => 'Reparação não encontrada'])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $reparacao
        ]);
    }

    /**
     * Atualizar reparação (apenas campos permitidos)
     */
    public function atualizarReparacao($id)
    {
        if (!session()->has('empresa_autenticada')) {
            return $this->response->setJSON(['error' => 'Não autenticado'])->setStatusCode(401);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Requisição inválida'])->setStatusCode(403);
        }

        $empresaData = session()->get('empresa_autenticada');
        $empresaNome = $empresaData['empresa_nome'];

        $reparacao = $this->reparacoesModel->find($id);

        if (!$reparacao || $reparacao['empresa_reparacao'] !== $empresaNome) {
            return $this->response->setJSON(['error' => 'Reparação não encontrada'])->setStatusCode(404);
        }

        $post = $this->request->getPost();

        // Apenas campos permitidos
        $dadosPermitidos = [
            'descricao_avaria' => $post['descricao_avaria'] ?? $reparacao['descricao_avaria'],
            'trabalho_efetuado' => $post['trabalho_efetuado'] ?? $reparacao['trabalho_efetuado'],
            'custo' => $post['custo'] ?? $reparacao['custo'],
            'observacoes' => $post['observacoes'] ?? $reparacao['observacoes'],
            'estado' => $post['estado'] ?? $reparacao['estado'],
        ];

        // Calcular dias de reparação se estado for 'reparado' ou 'irreparavel'
        if (in_array($dadosPermitidos['estado'], ['reparado', 'irreparavel']) && $reparacao['data_envio']) {
            $dataEnvio = new \DateTime($reparacao['data_envio']);
            $hoje = new \DateTime();
            $dadosPermitidos['dias_reparacao'] = $dataEnvio->diff($hoje)->days;
        }

        if ($this->reparacoesModel->update($id, $dadosPermitidos)) {
            log_activity(
                'empresa_portal',
                'update_reparacao',
                $id,
                'Empresa ' . $empresaNome . ' atualizou reparação: ' . $reparacao['n_serie_equipamento'],
                ['estado_anterior' => $reparacao['estado']],
                ['estado_novo' => $dadosPermitidos['estado']]
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Reparação atualizada com sucesso!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar reparação',
            'errors' => $this->reparacoesModel->errors()
        ]);
    }

    /**
     * Logout
     */
    public function logout()
    {
        $empresaData = session()->get('empresa_autenticada');
        
        if ($empresaData) {
            log_activity(
                'empresa_portal',
                'logout',
                $empresaData['id'],
                'Empresa desautenticada: ' . $empresaData['empresa_nome']
            );
        }

        session()->remove('empresa_autenticada');
        return redirect()->to(base_url('empresa/login'))->with('success', 'Sessão encerrada com sucesso');
    }

    /**
     * Helper para badge de estado
     */
    private function getEstadoBadge($estado): string
    {
        $badges = [
            'enviado' => '<span class="badge bg-primary">Enviado</span>',
            'em_reparacao' => '<span class="badge bg-warning">Em Reparação</span>',
            'reparado' => '<span class="badge bg-success">Reparado</span>',
            'irreparavel' => '<span class="badge bg-danger">Irreparável</span>',
            'cancelado' => '<span class="badge bg-secondary">Cancelado</span>'
        ];
        return $badges[$estado] ?? $estado;
    }
}
