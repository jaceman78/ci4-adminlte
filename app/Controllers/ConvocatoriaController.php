<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ConvocatoriaModel;
use App\Models\SessaoExameModel;
use App\Models\SessaoExameSalaModel;
use App\Models\UserModel;
use App\Models\SalasModel;

class ConvocatoriaController extends BaseController
{
    protected $convocatoriaModel;
    protected $sessaoExameModel;
    protected $sessaoExameSalaModel;
    protected $userModel;
    protected $salasModel;

    public function __construct()
    {
        $this->convocatoriaModel = new ConvocatoriaModel();
        $this->sessaoExameModel = new SessaoExameModel();
        $this->sessaoExameSalaModel = new SessaoExameSalaModel();
        $this->userModel = new UserModel();
        $this->salasModel = model('SalasModel');
    }

    /**
     * Lista todas as convocatórias
     */
    public function index()
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/login')->with('error', 'Por favor, faça login.');
        }

        // Verificar permissões - apenas níveis 4, 8 e 9
        if ($redirect = $this->requireSecExamesPermissions()) {
            return $redirect;
        }

        // Carregar permutas com informações completas
        $permutasModel = new \App\Models\PermutasVigilanciaModel();
        $permutas = $permutasModel->select('permutas_vigilancia.*,
                u_orig.name as nome_original, u_orig.email as email_original,
                u_subst.name as nome_substituto, u_subst.email as email_substituto,
                u_valid.name as nome_validador,
                ex.codigo_prova, ex.nome_prova,
                se.data_exame, se.hora_exame, se.fase,
                sala.codigo_sala,
                conv.funcao
            ')
            ->join('convocatoria conv', 'conv.id = permutas_vigilancia.convocatoria_id')
            ->join('user u_orig', 'u_orig.id = permutas_vigilancia.user_original_id')
            ->join('user u_subst', 'u_subst.id = permutas_vigilancia.user_substituto_id')
            ->join('user u_valid', 'u_valid.id = permutas_vigilancia.validado_por', 'left')
            ->join('sessao_exame se', 'se.id = conv.sessao_exame_id')
            ->join('exame ex', 'ex.id = se.exame_id')
            ->join('sessao_exame_sala ses', 'ses.id = conv.sessao_exame_sala_id', 'left')
            ->join('salas sala', 'sala.id = ses.sala_id', 'left')
            ->orderBy('permutas_vigilancia.criado_em', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Permutas de Vigilâncias',
            'permutas' => $permutas
        ];

        return view('convocatorias/index', $data);
    }

    /**
     * Página de Estatísticas
     */
    public function estatisticas()
    {
        // Calcular estatísticas de vigilâncias
        $db = \Config\Database::connect();
        
        // Estatísticas por professor
        $query = $db->query("
            SELECT 
                u.id,
                u.name as nome_professor,
                COUNT(DISTINCT c.id) as total_vigilancias,
                SUM(CASE WHEN e.tipo_prova = 'Suplentes' THEN 1 ELSE 0 END) as total_suplencias,
                SUM(CASE WHEN c.presenca = 'Falta' THEN 1 ELSE 0 END) as total_faltas,
                SUM(CASE WHEN c.presenca = 'Presente' THEN 1 ELSE 0 END) as total_presencas,
                SUM(CASE WHEN c.presenca = 'Falta Justificada' THEN 1 ELSE 0 END) as total_faltas_justificadas,
                SUM(CASE WHEN c.estado_confirmacao = 'Confirmado' THEN 1 ELSE 0 END) as total_confirmados,
                COALESCE(SUM(se.duracao_minutos), 0) as total_minutos
            FROM convocatoria c
            INNER JOIN user u ON u.id = c.user_id
            INNER JOIN sessao_exame se ON se.id = c.sessao_exame_id
            INNER JOIN exame e ON e.id = se.exame_id
            WHERE c.funcao = 'Vigilante'
            GROUP BY u.id, u.name
            ORDER BY total_vigilancias DESC, u.name ASC
        ");
        $estatisticasProfessores = $query->getResultArray();
        
        // Estatísticas gerais
        $queryGeral = $db->query("
            SELECT 
                COUNT(DISTINCT c.id) as total_convocatorias,
                COUNT(DISTINCT c.user_id) as total_professores,
                SUM(CASE WHEN c.presenca = 'Falta' THEN 1 ELSE 0 END) as total_faltas_geral,
                SUM(CASE WHEN c.presenca = 'Presente' THEN 1 ELSE 0 END) as total_presencas_geral,
                COALESCE(SUM(se.duracao_minutos), 0) as total_minutos_geral,
                COUNT(DISTINCT CASE WHEN e.tipo_prova = 'Suplentes' THEN c.id END) as total_suplencias_geral
            FROM convocatoria c
            INNER JOIN sessao_exame se ON se.id = c.sessao_exame_id
            INNER JOIN exame e ON e.id = se.exame_id
            WHERE c.funcao = 'Vigilante'
        ");
        $estatisticasGerais = $queryGeral->getRowArray();
        
        // Calcular taxa de presença
        $totalComPresenca = ($estatisticasGerais['total_presencas_geral'] ?? 0) + ($estatisticasGerais['total_faltas_geral'] ?? 0);
        $estatisticasGerais['taxa_presenca'] = $totalComPresenca > 0 
            ? round(($estatisticasGerais['total_presencas_geral'] / $totalComPresenca) * 100, 1) 
            : 0;

        $data = [
            'title' => 'Estatísticas de Vigilâncias',
            'estatisticas_professores' => $estatisticasProfessores,
            'estatisticas_gerais' => $estatisticasGerais
        ];

        return view('convocatorias/estatisticas', $data);
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
        
        $builder = $this->convocatoriaModel->builder();
        $builder->select('
            convocatoria.*,
            user.name as professor_nome,
            salas.codigo_sala,
            salas.codigo_sala as sala_nome,
            sessao_exame.data_exame,
            sessao_exame.hora_exame,
            sessao_exame.fase,
            exame.codigo_prova,
            exame.nome_prova
        ')
        ->join('user', 'user.id = convocatoria.user_id', 'left')
        ->join('sessao_exame_sala', 'sessao_exame_sala.id = convocatoria.sessao_exame_sala_id', 'left')
        ->join('salas', 'salas.id = sessao_exame_sala.sala_id', 'left')
        ->join('sessao_exame', 'sessao_exame.id = convocatoria.sessao_exame_id', 'left')
        ->join('exame', 'exame.id = sessao_exame.exame_id', 'left')
        ->where('sessao_exame.ativo', 1);

        if ($search) {
            $builder->groupStart()
                    ->like('user.name', $search)
                    ->orLike('exame.nome_prova', $search)
                    ->orLike('convocatoria.funcao', $search)
                    ->groupEnd();
        }

        $totalRecords = $this->convocatoriaModel->countAll();
        $recordsFiltered = $builder->countAllResults(false);

        $convocatorias = $builder->orderBy('sessao_exame.data_exame', 'DESC')
                                ->limit($length, $start)
                                ->get()
                                ->getResultArray();

        $data = [];
        foreach ($convocatorias as $conv) {
            $estadoBadge = match($conv['estado_confirmacao']) {
                'Confirmado' => '<span class="badge bg-success">Confirmado</span>',
                'Pendente' => '<span class="badge bg-warning">Pendente</span>',
                'Rejeitado' => '<span class="badge bg-danger">Rejeitado</span>',
                default => '<span class="badge bg-secondary">N/A</span>'
            };

            $funcaoBadge = match($conv['funcao']) {
                'Vigilante' => 'bg-primary',
                'Suplente' => 'bg-info',
                'Coadjuvante' => 'bg-secondary',
                'Júri' => 'bg-warning',
                default => 'bg-dark'
            };

            $actions = '
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteConvocatoria(' . $conv['id'] . ')" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>';
            
            $data[] = [
                $conv['id'],
                date('d/m/Y', strtotime($conv['data_exame'])),
                date('H:i', strtotime($conv['hora_exame'])),
                $conv['codigo_prova'] . ' - ' . $conv['nome_prova'],
                $conv['fase'],
                $conv['professor_nome'],
                '<span class="badge ' . $funcaoBadge . '">' . $conv['funcao'] . '</span>',
                $conv['codigo_sala'] ?? '<em>Suplente</em>',
                $estadoBadge,
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
     * Mostra formulário para criar convocatórias
     */
    public function criar($sessaoExameSalaId = null)
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/login');
        }

        // Buscar professores ativos
        $professores = $this->userModel->where('status', 1)
                                      ->where('level >', 0)
                                      ->orderBy('name', 'ASC')
                                      ->findAll();

        // Buscar sessões futuras
        $sessoes = $this->sessaoExameModel->getSessoesFuturas();

        // Se foi passado sessaoExameSalaId, buscar info da sala
        $salaInfo = null;
        if ($sessaoExameSalaId) {
            $salaInfo = $this->sessaoExameSalaModel->getSalaComVigilantes($sessaoExameSalaId);
        }

        $data = [
            'title' => 'Criar Convocatória',
            'professores' => $professores,
            'sessoes' => $sessoes,
            'sessaoExameSalaId' => $sessaoExameSalaId,
            'salaInfo' => $salaInfo
        ];

        return view('convocatorias/form', $data);
    }

    /**
     * Guarda uma nova convocatória
     */
    public function store()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('convocatorias');
        }

        $sessaoExameSalaId = $this->request->getPost('sessao_exame_sala_id') ?: null;
        $sessaoExameId = $this->request->getPost('sessao_exame_id');
        
        $data = [
            'sessao_exame_id' => $sessaoExameId,
            'sessao_exame_sala_id' => $sessaoExameSalaId,
            'user_id' => $this->request->getPost('user_id'),
            'funcao' => $this->request->getPost('funcao'),
            'observacoes' => $this->request->getPost('observacoes'),
            'estado_confirmacao' => 'Pendente',
        ];

        // Verificar conflito de horário
        if ($this->convocatoriaModel->hasConflitoHorario($data['user_id'], $data['sessao_exame_id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Conflito de horário! Este professor já tem outra convocatória no mesmo horário.'
            ]);
        }

        if ($this->convocatoriaModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Convocatória criada com sucesso!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao criar convocatória',
            'errors' => $this->convocatoriaModel->errors()
        ]);
    }

    /**
     * Cria múltiplas convocatórias de uma vez
     */
    public function criarMultiplas()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('convocatorias');
        }

        $sessaoId = $this->request->getPost('sessao_exame_id');
        $professores = $this->request->getPost('professores'); // Array de [user_id, funcao, sala_id]

        $sucesso = 0;
        $erros = [];

        foreach ($professores as $prof) {
            $data = [
                'sessao_exame_id' => $sessaoId,
                'user_id' => $prof['user_id'],
                'sala_id' => $prof['sala_id'] ?: null,
                'funcao' => $prof['funcao'],
                'estado_confirmacao' => 'Pendente',
            ];

            // Verificar conflito
            if ($this->convocatoriaModel->hasConflitoHorario($data['user_id'], $sessaoId)) {
                $professor = $this->userModel->find($prof['user_id']);
                $erros[] = 'Conflito de horário: ' . $professor['name'];
                continue;
            }

            if ($this->convocatoriaModel->insert($data)) {
                $sucesso++;
            }
        }

        return $this->response->setJSON([
            'success' => $sucesso > 0,
            'message' => "$sucesso convocatória(s) criada(s) com sucesso!",
            'errors' => $erros
        ]);
    }

    /**
     * Elimina uma convocatória
     */
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('convocatorias');
        }

        if ($this->convocatoriaModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Convocatória eliminada com sucesso!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao eliminar convocatória'
        ]);
    }

    /**
     * Lista convocatórias de uma sessão específica
     */
    public function porSessao($sessaoId)
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/login');
        }

        $sessao = $this->sessaoExameModel->getWithExame($sessaoId);
        
        if (!$sessao) {
            return redirect()->to('convocatorias')->with('error', 'Sessão não encontrada');
        }

        $convocatorias = $this->convocatoriaModel->getBySessao($sessaoId);

        $data = [
            'title' => 'Convocatórias da Sessão',
            'sessao' => $sessao,
            'convocatorias' => $convocatorias
        ];

        return view('convocatorias/por_sessao', $data);
    }

    /**
     * API: Professores disponíveis numa data/hora
     */
    public function getProfessoresDisponiveis()
    {
        $sessaoId = $this->request->getGet('sessao_id');
        
        if (!$sessaoId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sessão não especificada'
            ]);
        }

        $sessao = $this->sessaoExameModel->find($sessaoId);
        
        if (!$sessao) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sessão não encontrada'
            ]);
        }

        // Buscar professores que NÃO têm convocatória nesse horário
        $professoresDisponiveis = [];
        $todosProfessores = $this->userModel->where('status', 1)
                                           ->where('level >', 0)
                                           ->orderBy('name', 'ASC')
                                           ->findAll();

        foreach ($todosProfessores as $prof) {
            if (!$this->convocatoriaModel->hasConflitoHorario($prof['id'], $sessaoId)) {
                $professoresDisponiveis[] = $prof;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $professoresDisponiveis
        ]);
    }

    /**
     * Página de marcação de presenças
     */
    public function marcarPresencas()
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/login')->with('error', 'Por favor, faça login.');
        }

        // Verificar permissões - apenas níveis 4, 8 e 9
        if ($redirect = $this->requireSecExamesPermissions()) {
            return $redirect;
        }

        // Buscar todas as sessões com convocatórias
        $sessoes = $this->convocatoriaModel->getSessionsComConvocatorias();

        $data = [
            'title' => 'Marcar Presenças',
            'sessoes' => $sessoes
        ];

        return view('convocatorias/marcar_presencas', $data);
    }

    /**
     * Buscar convocatórias de uma sessão (AJAX)
     */
    public function getConvocatoriasSessao($sessaoId)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
            }

            $convocatorias = $this->convocatoriaModel->getConvocatoriasBySessaoComProfessores($sessaoId);

            // Agrupar por função
            $agrupadas = [
                'vigilantes' => [],
                'suplentes' => [],
                'coadjuvantes' => [],
                'outros' => []
            ];

            foreach ($convocatorias as $conv) {
                if ($conv['funcao'] === 'Vigilante') {
                    $agrupadas['vigilantes'][] = $conv;
                } elseif ($conv['funcao'] === 'Suplente') {
                    $agrupadas['suplentes'][] = $conv;
                } elseif ($conv['funcao'] === 'Coadjuvante') {
                    $agrupadas['coadjuvantes'][] = $conv;
                } else {
                    $agrupadas['outros'][] = $conv;
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $agrupadas
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar convocatórias: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao buscar convocatórias: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Atualizar presença de uma convocatória (AJAX)
     */
    public function atualizarPresenca($convocatoriaId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        // Verificar permissões
        if ($redirect = $this->requireSecExamesPermissions()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão']);
        }

        $presenca = $this->request->getPost('presenca');

        if (!in_array($presenca, ['Pendente', 'Presente', 'Falta', 'Falta Justificada'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Estado de presença inválido']);
        }

        $success = $this->convocatoriaModel->atualizarPresenca($convocatoriaId, $presenca);

        if ($success) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Presença atualizada com sucesso'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar presença'
        ]);
    }

    /**
     * Atualizar múltiplas presenças de uma sessão (AJAX)
     */
    public function atualizarPresencasSessao($sessaoId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        // Verificar permissões
        if ($redirect = $this->requireSecExamesPermissions()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão']);
        }

        $presencas = $this->request->getPost('presencas'); // Array [convocatoria_id => presenca]

        if (empty($presencas) || !is_array($presencas)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dados inválidos']);
        }

        $erros = 0;
        foreach ($presencas as $convId => $presenca) {
            if (!in_array($presenca, ['Pendente', 'Presente', 'Falta', 'Falta Justificada'])) {
                continue;
            }
            
            if (!$this->convocatoriaModel->atualizarPresenca($convId, $presenca)) {
                $erros++;
            }
        }

        if ($erros > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Erro ao atualizar {$erros} presença(s)"
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Todas as presenças foram atualizadas com sucesso'
        ]);
    }

    /**
     * Gerar PDF com folha de presenças (Início/Fim)
     */
    public function gerarPdfPresencas($sessaoId)
    {
        // Verificar permissões
        if ($redirect = $this->requireSecExamesPermissions()) {
            return $redirect;
        }

        try {
            $sessao = $this->sessaoExameModel->getWithExame($sessaoId);

            if (!$sessao) {
                return redirect()->to('convocatorias/marcar-presencas')->with('error', 'Sessão não encontrada');
            }

            $convocatorias = $this->convocatoriaModel->getBySessao($sessaoId);

            if (empty($convocatorias)) {
                return redirect()->back()->with('error', 'Não há convocatórias para gerar PDF');
            }

            // Agrupar por função
            $vigilantes = [];
            $suplentes = [];
            $coadjuvantes = [];
            $outros = [];

            foreach ($convocatorias as $conv) {
                switch ($conv['funcao']) {
                    case 'Vigilante':
                        $vigilantes[] = $conv;
                        break;
                    case 'Suplente':
                        $suplentes[] = $conv;
                        break;
                    case 'Coadjuvante':
                        $coadjuvantes[] = $conv;
                        break;
                    default:
                        $outros[] = $conv;
                        break;
                }
            }

            $data = [
                'sessao' => $sessao,
                'vigilantes' => $vigilantes,
                'suplentes' => $suplentes,
                'coadjuvantes' => $coadjuvantes,
                'outros' => $outros
            ];

            // Gerar HTML
            $html = view('sessoes_exame/pdf_presencas', $data);

            // Gerar PDF usando Dompdf
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isRemoteEnabled', true);
            $options->set('chroot', FCPATH);
            
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Nome do arquivo
            $filename = 'Folha_Presencas_' . $sessao['codigo_prova'] . '_' . $sessao['fase'] . '_' . date('d-m-Y') . '.pdf';

            // Output do PDF
            $dompdf->stream($filename, ['Attachment' => true]);
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Erro ao gerar PDF de presenças: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao gerar PDF: ' . $e->getMessage());
        }
    }

    /**
     * Gerar PDF com relatório de faltas
     */
    public function gerarPdfFaltas($sessaoId)
    {
        // Verificar permissões
        if ($redirect = $this->requireSecExamesPermissions()) {
            return $redirect;
        }

        // Aumentar tempo de execução e memória
        set_time_limit(180);
        ini_set('memory_limit', '512M');

        // Limpar qualquer output buffer anterior
        if (ob_get_level()) {
            ob_end_clean();
        }

        try {
            $sessao = $this->sessaoExameModel->getWithExame($sessaoId);

            if (!$sessao) {
                // Não pode usar redirect aqui pois já limpamos o buffer
                echo '<html><body>';
                echo '<h1>Erro</h1>';
                echo '<p>Sessão não encontrada.</p>';
                echo '<p><a href="' . base_url('convocatorias/marcar-presencas') . '">Voltar</a></p>';
                echo '</body></html>';
                exit;
            }

            $faltas = $this->convocatoriaModel->getFaltasBySessao($sessaoId);
            $estatisticas = $this->convocatoriaModel->getEstatisticasPresencas($sessaoId);

            $data = [
                'sessao' => $sessao,
                'faltas' => $faltas,
                'estatisticas' => $estatisticas,
                'fcpath' => FCPATH // Passar o caminho base para a view
            ];

            // Gerar HTML (usando renderização de view sem buffer do CI4)
            $html = view('convocatorias/pdf_faltas', $data);

            // Verificar se o HTML foi gerado
            if (empty($html)) {
                throw new \Exception('Erro ao gerar HTML da view');
            }

            // Gerar PDF usando Dompdf
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', false); // Desabilitar para segurança
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isRemoteEnabled', true);
            $options->set('chroot', FCPATH);

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Nome do arquivo
            $filename = 'Relatorio_Faltas_' . $sessao['codigo_prova'] . '_' . $sessao['fase'] . '_' . date('d-m-Y') . '.pdf';

            // Limpar qualquer saída e enviar headers corretos
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // Enviar headers
            $this->response->setHeader('Content-Type', 'application/pdf');
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            $this->response->setHeader('Cache-Control', 'private, max-age=0, must-revalidate');
            $this->response->setHeader('Pragma', 'public');
            
            // Enviar o PDF
            $this->response->setBody($dompdf->output());
            return $this->response;

        } catch (\Exception $e) {
            log_message('error', 'Erro ao gerar PDF de faltas: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Limpar buffer se houver
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // Exibir erro na tela para debug
            echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Erro ao Gerar PDF</title>';
            echo '<style>body{font-family:Arial,sans-serif;padding:20px;} .error{background:#f8d7da;border:1px solid #f5c6cb;padding:15px;border-radius:5px;} pre{background:#f4f4f4;padding:10px;overflow:auto;}</style>';
            echo '</head><body>';
            echo '<div class="error">';
            echo '<h1>Erro ao gerar PDF</h1>';
            echo '<p><strong>Mensagem:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><strong>Arquivo:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
            echo '<details><summary>Stack Trace</summary><pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre></details>';
            echo '</div>';
            echo '<p style="margin-top:20px;"><a href="' . base_url('convocatorias/marcar-presencas') . '" style="padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;display:inline-block;">Voltar</a></p>';
            echo '</body></html>';
            exit;
        }
    }
}
