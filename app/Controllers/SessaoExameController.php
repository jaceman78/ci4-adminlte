<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SessaoExameModel;
use App\Models\ExameModel;
use App\Models\ConvocatoriaModel;

class SessaoExameController extends BaseController
{
    protected $sessaoExameModel;
    protected $exameModel;
    protected $convocatoriaModel;

    public function __construct()
    {
        $this->sessaoExameModel = new SessaoExameModel();
        $this->exameModel = new ExameModel();
        $this->convocatoriaModel = new ConvocatoriaModel();
    }

    /**
     * Lista todas as sessões
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

        $data = [
            'title' => 'Sessões de Exame',
            'exames' => $this->exameModel->where('ativo', 1)->findAll()
        ];

        return view('sessoes_exame/index', $data);
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
        
        $orderColumn = 'sessao_exame.id';
        $orderDir = 'desc';
        
        if (isset($request['order'][0])) {
            $columns = ['sessao_exame.id', 'exame.codigo_prova', 'exame.nome_prova', 'sessao_exame.fase', 'sessao_exame.data_exame', 'sessao_exame.hora_exame', 'sessao_exame.ativo'];
            $orderColumnIndex = $request['order'][0]['column'];
            $orderColumn = $columns[$orderColumnIndex] ?? 'sessao_exame.id';
            $orderDir = $request['order'][0]['dir'] ?? 'desc';
        }

        $builder = $this->sessaoExameModel->db->table('sessao_exame');
        $builder->select('sessao_exame.id, sessao_exame.exame_id, sessao_exame.fase, 
                          sessao_exame.data_exame, sessao_exame.hora_exame, sessao_exame.duracao_minutos, 
                          sessao_exame.tolerancia_minutos, sessao_exame.num_alunos, 
                          sessao_exame.observacoes, sessao_exame.ativo,
                          exame.codigo_prova, exame.nome_prova, exame.tipo_prova')
                ->join('exame', 'exame.id = sessao_exame.exame_id', 'left');

        if ($search) {
            $builder->groupStart()
                    ->like('exame.codigo_prova', $search)
                    ->orLike('exame.nome_prova', $search)
                    ->orLike('sessao_exame.fase', $search)
                    ->groupEnd();
        }

        $totalRecords = $this->sessaoExameModel->countAll();
        $recordsFiltered = $builder->countAllResults(false);

        $sessoes = $builder->orderBy($orderColumn, $orderDir)
                          ->limit($length, $start)
                          ->get()
                          ->getResultArray();

        $data = [];
        foreach ($sessoes as $sessao) {
            $badge = $sessao['ativo'] == 1 
                ? '<span class="badge bg-success">Ativo</span>' 
                : '<span class="badge bg-secondary">Cancelado</span>';

            // Contar convocatórias
            $numConvocatorias = $this->convocatoriaModel->where('sessao_exame_id', $sessao['id'])->countAllResults();

            $actions = '
                <div class="btn-group" role="group">
                    <a href="' . base_url('sessoes-exame/detalhes/' . $sessao['id']) . '" class="btn btn-sm btn-info" title="Ver Detalhes">
                        <i class="bi bi-eye"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-primary" onclick="editSessao(' . $sessao['id'] . ')" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteSessao(' . $sessao['id'] . ')" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>';
            
            $data[] = [
                $sessao['id'],
                '<a href="' . base_url('sessoes-exame/detalhes/' . $sessao['id']) . '" class="text-decoration-none fw-bold">' . ($sessao['codigo_prova'] ?? '-') . '</a>',
                '<a href="' . base_url('sessoes-exame/detalhes/' . $sessao['id']) . '" class="text-decoration-none">' . ($sessao['nome_prova'] ?? '-') . '</a>',
                $sessao['fase'],
                date('d/m/Y', strtotime($sessao['data_exame'])),
                date('H:i', strtotime($sessao['hora_exame'])),
                $sessao['duracao_minutos'] . ' min',
                '<span class="badge bg-info">' . $numConvocatorias . '</span>',
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
     * Mostra detalhes de uma sessão
     */
    public function detalhes($id)
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/login');
        }

        $sessao = $this->sessaoExameModel->getWithExame($id);

        if (!$sessao) {
            return redirect()->to('sessoes-exame')->with('error', 'Sessão não encontrada');
        }

        // Buscar convocatórias
        $convocatorias = $this->convocatoriaModel->getBySessao($id);

        // Calcular vigilantes necessários baseado nas salas alocadas
        $sessaoExameSalaModel = new \App\Models\SessaoExameSalaModel();
        $totalVigilantesNecessarios = $sessaoExameSalaModel->getTotalVigilantesNecessarios($id);
        
        // Verificar se é sessão especial (suplentes, verificação calculadoras ou apoio TIC)
        $isSessaoEspecial = in_array($sessao['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC']);
        
        // Contar convocações por função
        $vigilantesConvocados = 0;
        $suplentesConvocados = 0;
        
        if ($isSessaoEspecial) {
            // Para sessões especiais, todos os convocados contam como especiais
            $suplentesConvocados = count($convocatorias);
        } else {
            // Para sessões normais, contar por função
            foreach ($convocatorias as $conv) {
                if ($conv['funcao'] === 'Vigilante') {
                    $vigilantesConvocados++;
                } elseif ($conv['funcao'] === 'Suplente') {
                    $suplentesConvocados++;
                }
            }
        }

        $data = [
            'title' => 'Detalhes da Sessão de Exame',
            'sessao' => $sessao,
            'convocatorias' => $convocatorias,
            'vigilantesNecessarios' => $totalVigilantesNecessarios,
            'vigilantesConvocados' => $vigilantesConvocados,
            'suplentesConvocados' => $suplentesConvocados
        ];

        return view('sessoes_exame/detalhes', $data);
    }

    /**
     * Retorna dados de uma sessão para edição
     */
    public function get($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('sessoes-exame');
        }

        $sessao = $this->sessaoExameModel->find($id);

        if (!$sessao) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sessão não encontrada'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $sessao
        ]);
    }

    /**
     * Guarda uma nova sessão
     */
    public function store()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('sessoes-exame');
        }

        $data = [
            'exame_id' => $this->request->getPost('exame_id'),
            'fase' => $this->request->getPost('fase'),
            'data_exame' => $this->request->getPost('data_exame'),
            'hora_exame' => $this->request->getPost('hora_exame'),
            'duracao_minutos' => $this->request->getPost('duracao_minutos'),
            'tolerancia_minutos' => $this->request->getPost('tolerancia_minutos') ?? 0,
            'num_alunos' => $this->request->getPost('num_alunos'),
            'observacoes' => $this->request->getPost('observacoes'),
            'ativo' => 1,
        ];

        if ($this->sessaoExameModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sessão criada com sucesso!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao criar sessão',
            'errors' => $this->sessaoExameModel->errors()
        ]);
    }

    /**
     * Atualiza uma sessão
     */
    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('sessoes-exame');
        }

        $data = [
            'exame_id' => $this->request->getPost('exame_id'),
            'fase' => $this->request->getPost('fase'),
            'data_exame' => $this->request->getPost('data_exame'),
            'hora_exame' => $this->request->getPost('hora_exame'),
            'duracao_minutos' => $this->request->getPost('duracao_minutos'),
            'tolerancia_minutos' => $this->request->getPost('tolerancia_minutos') ?? 0,
            'num_alunos' => $this->request->getPost('num_alunos'),
            'observacoes' => $this->request->getPost('observacoes'),
        ];

        if ($this->sessaoExameModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sessão atualizada com sucesso!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar sessão',
            'errors' => $this->sessaoExameModel->errors()
        ]);
    }

    /**
     * Elimina uma sessão
     */
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('sessoes-exame');
        }

        if ($this->sessaoExameModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sessão eliminada com sucesso!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao eliminar sessão'
        ]);
    }

    /**
     * API: Calcular vigilantes necessários
     */
    public function calcularVigilantes($sessaoId)
    {
        $resultado = $this->sessaoExameModel->getVigilantesNecessarios($sessaoId);
        return $this->response->setJSON([
            'success' => true,
            'data' => $resultado
        ]);
    }

    /**
     * Calendário de Sessões de Exame
     */
    public function calendario()
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/login')->with('error', 'Por favor, faça login.');
        }

        // Verificar permissões - apenas níveis 4, 8 e 9
        if ($redirect = $this->requireSecExamesPermissions()) {
            return $redirect;
        }

        $data = [
            'title' => 'Calendário de Exames'
        ];

        return view('sessoes_exame/calendario', $data);
    }

    /**
     * API: Obter eventos do calendário
     */
    public function getCalendarioEventos()
    {
        $sessoes = $this->sessaoExameModel
            ->select('sessao_exame.*, exame.codigo_prova, exame.nome_prova, exame.tipo_prova')
            ->join('exame', 'exame.id = sessao_exame.exame_id', 'left')
            ->where('sessao_exame.ativo', 1)
            ->findAll();

        $eventos = [];
        foreach ($sessoes as $sessao) {
            $dataHora = $sessao['data_exame'] . ' ' . $sessao['hora_exame'];
            
            // Cor baseada no tipo de prova (case-insensitive)
            $tipoProva = strtolower(trim($sessao['tipo_prova'] ?? ''));
            $cor = '#3788d8'; // Azul padrão (Prova Final)
            
            if ($tipoProva === 'moda') {
                $cor = '#28a745'; // Verde
            } elseif ($tipoProva === 'exame nacional') {
                $cor = '#dc3545'; // Vermelho
            } elseif ($tipoProva === 'prova final') {
                $cor = '#3788d8'; // Azul claro
            }

            $eventos[] = [
                'id' => $sessao['id'],
                'title' => $sessao['codigo_prova'] . ' - ' . $sessao['nome_prova'],
                'start' => $dataHora,
                'url' => base_url('sessoes-exame/detalhes/' . $sessao['id']),
                'backgroundColor' => $cor,
                'borderColor' => $cor,
                'extendedProps' => [
                    'fase' => $sessao['fase'],
                    'tipo' => $sessao['tipo_prova'],
                    'duracao' => $sessao['duracao_minutos'],
                    'alunos' => $sessao['num_alunos']
                ]
            ];
        }

        return $this->response->setJSON($eventos);
    }

    /**
     * Enviar convocatória por email para um vigilante específico
     */
    public function enviarConvocatoria($convocatoriaId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('sessoes-exame');
        }

        try {
            $convocatoria = $this->convocatoriaModel->getWithDetails($convocatoriaId);
            
            if (!$convocatoria) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Convocatória não encontrada'
                ]);
            }

            $result = $this->enviarEmailConvocatoria($convocatoria);

            return $this->response->setJSON([
                'success' => $result['success'],
                'message' => $result['message']
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao enviar convocatória: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao enviar email: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Enviar convocatórias para todos os vigilantes de uma sessão
     */
    public function enviarConvocatoriasTodas($sessaoId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('sessoes-exame');
        }

        try {
            $convocatorias = $this->convocatoriaModel->getBySessao($sessaoId);
            
            if (empty($convocatorias)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Não há convocatórias para enviar'
                ]);
            }

            $enviados = 0;
            $erros = 0;
            $mensagensErro = [];

            foreach ($convocatorias as $conv) {
                $result = $this->enviarEmailConvocatoria($conv);
                if ($result['success']) {
                    $enviados++;
                } else {
                    $erros++;
                    $mensagensErro[] = $conv['professor_nome'] . ': ' . $result['message'];
                }
            }

            $message = "Enviados: $enviados emails";
            if ($erros > 0) {
                $message .= " | Erros: $erros";
                if (!empty($mensagensErro)) {
                    $message .= " (" . implode('; ', array_slice($mensagensErro, 0, 3)) . ")";
                }
            }

            return $this->response->setJSON([
                'success' => $erros === 0,
                'message' => $message,
                'enviados' => $enviados,
                'erros' => $erros
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao enviar convocatórias: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao enviar emails: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Confirmar convocatória via link do email
     */
    public function confirmarConvocatoria($token)
    {
        try {
            // Decodificar token
            $data = $this->decodeToken($token);
            
            if (!$data || !isset($data['convocatoria_id'])) {
                return view('sessoes_exame/confirmacao_erro', [
                    'title' => 'Link Inválido',
                    'message' => 'O link de confirmação é inválido ou expirou.'
                ]);
            }

            $convocatoriaId = $data['convocatoria_id'];
            $convocatoria = $this->convocatoriaModel->find($convocatoriaId);

            if (!$convocatoria) {
                return view('sessoes_exame/confirmacao_erro', [
                    'title' => 'Convocatória não encontrada',
                    'message' => 'A convocatória não foi encontrada no sistema.'
                ]);
            }

            // Verificar se já foi confirmada
            if ($convocatoria['estado_confirmacao'] === 'Confirmado') {
                $convocatoriaDetalhes = $this->convocatoriaModel->getWithDetails($convocatoriaId);
                return view('sessoes_exame/confirmacao_sucesso', [
                    'title' => 'Já Confirmado',
                    'message' => 'Esta convocatória já foi confirmada anteriormente.',
                    'convocatoria' => $convocatoriaDetalhes,
                    'ja_confirmada' => true
                ]);
            }

            // Atualizar estado
            $this->convocatoriaModel->update($convocatoriaId, [
                'estado_confirmacao' => 'Confirmado',
                'data_confirmacao' => date('Y-m-d H:i:s')
            ]);

            $convocatoriaDetalhes = $this->convocatoriaModel->getWithDetails($convocatoriaId);

            return view('sessoes_exame/confirmacao_sucesso', [
                'title' => 'Confirmação Realizada',
                'message' => 'A sua presença foi confirmada com sucesso!',
                'convocatoria' => $convocatoriaDetalhes,
                'ja_confirmada' => false
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao confirmar convocatória: ' . $e->getMessage());
            return view('sessoes_exame/confirmacao_erro', [
                'title' => 'Erro',
                'message' => 'Ocorreu um erro ao processar a confirmação.'
            ]);
        }
    }

    /**
     * Gerar PDF com convocatórias para afixação
     */
    public function gerarPdfConvocatorias($sessaoId)
    {
        // Aumentar tempo de execução e memória para gerar PDF
        set_time_limit(180);
        ini_set('memory_limit', '512M');
        
        try {
            $sessao = $this->sessaoExameModel->getWithExame($sessaoId);

            if (!$sessao) {
                return redirect()->to('sessoes-exame')->with('error', 'Sessão não encontrada');
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
            $html = view('sessoes_exame/pdf_convocatorias', $data);

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
            $filename = 'Convocatorias_' . $sessao['codigo_prova'] . '_' . $sessao['fase'] . '_' . date('d-m-Y') . '.pdf';

            // Output do PDF
            $dompdf->stream($filename, ['Attachment' => true]);
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Erro ao gerar PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao gerar PDF: ' . $e->getMessage());
        }
    }

    /**
     * Função auxiliar para enviar email de convocatória
     */
    private function enviarEmailConvocatoria($convocatoria)
    {
        try {
            if (empty($convocatoria['professor_email'])) {
                return [
                    'success' => false,
                    'message' => 'Professor sem email cadastrado'
                ];
            }

            $email = \Config\Services::email();
            
            // Log de debug
            $fromEmailConfig = getenv('email.fromEmail') ?: 'escoladigitaljb@aejoaodebarros.pt';
            $smtpUserConfig = getenv('email.SMTPUser');
            log_message('info', 'Email Config - fromEmail: ' . $fromEmailConfig . ', SMTPUser: ' . $smtpUserConfig);
            
            // Definir remetente explicitamente
            $email->setFrom(
                $fromEmailConfig,
                getenv('email.fromName') ?: 'Escola Digital - AE João de Barros'
            );
            
            // Gerar token de confirmação
            $token = $this->generateToken([
                'convocatoria_id' => $convocatoria['id'],
                'timestamp' => time()
            ]);

            $confirmUrl = base_url('sessoes-exame/confirmar/' . $token);

            // Preparar dados para o template
            $emailData = [
                'convocatoria' => $convocatoria,
                'confirmUrl' => $confirmUrl
            ];

            $message = view('emails/convocatoria_exame', $emailData);

            $email->setTo($convocatoria['professor_email']);
            $email->setSubject('Convocatória - ' . $convocatoria['nome_prova'] . ' (' . $convocatoria['fase'] . ')');
            $email->setMessage($message);

            if ($email->send()) {
                // Registrar o envio do email na base de dados usando query builder
                try {
                    $db = \Config\Database::connect();
                    $updated = $db->table('convocatoria')
                       ->where('id', $convocatoria['id'])
                       ->update([
                           'email_enviado' => 1,
                           'data_envio_email' => date('Y-m-d H:i:s')
                       ]);
                    
                    log_message('info', 'Convocatoria ID ' . $convocatoria['id'] . ' atualizada. Linhas afetadas: ' . $updated);
                } catch (\Exception $e) {
                    log_message('error', 'Erro ao atualizar convocatoria: ' . $e->getMessage());
                }
                
                return [
                    'success' => true,
                    'message' => 'Email enviado com sucesso'
                ];
            } else {
                $error = $email->printDebugger(['headers']);
                log_message('error', 'Erro ao enviar email: ' . $error);
                return [
                    'success' => false,
                    'message' => 'Falha ao enviar email'
                ];
            }
        } catch (\Exception $e) {
            log_message('error', 'Exceção ao enviar email: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Gerar token de confirmação
     */
    private function generateToken($data)
    {
        $json = json_encode($data);
        $base64 = base64_encode($json);
        // Converter para Base64 URL-safe e remover padding
        return rtrim(strtr($base64, '+/', '-_'), '=');
    }

    /**
     * Decodificar token de confirmação
     */
    private function decodeToken($token)
    {
        try {
            // Converter de Base64 URL-safe para Base64 normal
            $base64 = strtr($token, '-_', '+/');
            // Adicionar padding se necessário
            $base64 .= str_repeat('=', (4 - strlen($base64) % 4) % 4);
            $json = base64_decode($base64);
            $data = json_decode($json, true);
            
            // Verificar se o token não expirou (válido por 30 dias)
            if (isset($data['timestamp']) && (time() - $data['timestamp']) > (30 * 24 * 60 * 60)) {
                return null;
            }
            
            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }
}
