<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TicketsModel;
use App\Models\EquipamentosModel;
use App\Models\SalasModel;
use App\Models\TiposAvariaModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Email\Email;

class TicketsController extends BaseController
{
    use ResponseTrait;

    protected $ticketsModel;
    protected $equipamentosModel;
    protected $salasModel;
    protected $escolasModel;
    protected $tiposAvariaModel;
    protected $tipoEquipamentosModel;
    protected $userModel;
    protected $equipamentosSalaModel;
    protected $email;

    public function __construct()
    {
        $this->ticketsModel = new TicketsModel();
        $this->equipamentosModel = new EquipamentosModel();
        $this->salasModel = new SalasModel();
        $this->escolasModel = new \App\Models\EscolasModel();
        $this->tiposAvariaModel = new TiposAvariaModel();
        $this->tipoEquipamentosModel = new \App\Models\TipoEquipamentosModel();
        $this->userModel = new UserModel();
        $this->equipamentosSalaModel = new \App\Models\EquipamentosSalaModel();
        $this->email = new Email();
        
        // Carregar helper de estados
        helper(['estado']);
    }

    // --- Vistas --- //

    public function novoTicket()
    {
        // Apenas utilizadores autenticados podem criar tickets
        // Aceita tanto a flag 'isLoggedIn' quanto 'LoggedUserData' (consistência com LoginController)
        if (! session()->get('isLoggedIn') && ! session()->get('LoggedUserData')) {
            return redirect()->to("/login");
        }

        $data = [
            'equipamentos'       => $this->equipamentosModel->findAll(),
            'escolas'            => $this->escolasModel->orderBy('nome', 'ASC')->findAll(),
            'salas'              => $this->salasModel->findAll(),
            'tiposAvaria'        => $this->tiposAvariaModel->findAll(),
            'tipos_equipamento'  => $this->tipoEquipamentosModel->findAll(),
            'title'              => 'Criar Novo Ticket'
        ];
        return view('tickets/novo_ticket', $data);
    }

    public function meusTickets()
    {
        // Apenas utilizadores autenticados podem ver os seus tickets
        if (!session()->get("isLoggedIn")) {
            return redirect()->to("/login");
        }

        $userId = session()->get('user_id'); // ID do utilizador logado
        $data = [
            'title' => 'Meus Tickets'
        ];
        return view('tickets/meus_tickets', $data);
    }

    public function tratamentoTickets()
    {
        // Apenas utilizadores de nível 5 ou superior
        if (!session()->get("isLoggedIn") || session()->get('level') < 5) {
            return redirect()->to("/dashboard")->with('error', 'Acesso não autorizado.');
        }

        $data = [
            'utilizadoresTecnicos' => $this->userModel->where('level >=', 5)->findAll(),
            'title'                => 'Tratamento de Tickets'
        ];
        return view('tickets/tratamento_tickets', $data);
    }

    public function todosTickets()
    {
        // Apenas utilizadores de nível 8 ou superior (Administrador e Super Admin)
        if (!session()->get("isLoggedIn") || session()->get('level') < 8) {
            return redirect()->to("/dashboard")->with('error', 'Acesso não autorizado.');
        }

        $data = [
            'title' => 'Todos os Tickets'
        ];
        return view('tickets/tickets', $data);
    }

    public function viewTicket($id = null)
    {
        // Utilizadores autenticados podem ver tickets
        if (!session()->get("isLoggedIn")) {
            return redirect()->to("/login");
        }

        $ticket = $this->ticketsModel->getTicketDetails($id);
        
        if (!$ticket) {
            return redirect()->to("/tickets/meus")->with('error', 'Ticket não encontrado.');
        }

        // Verificar se o utilizador tem permissão para ver este ticket
        $userId = session()->get('user_id');
        $userLevel = session()->get('level');
        
        // Pode ver se: é o criador, é o atribuído, ou é técnico/admin (level >= 5)
        if ($ticket['user_id'] != $userId && 
            $ticket['atribuido_user_id'] != $userId && 
            $userLevel < 5) {
            return redirect()->to("/tickets/meus")->with('error', 'Não tem permissão para ver este ticket.');
        }

        // Carregar lista de técnicos para atribuição (se user level >= 8)
        $tecnicos = [];
        if ($userLevel >= 8) {
            $userModel = new \App\Models\UserModel();
            $tecnicos = $userModel->where('level >=', 5)->where('status', 1)->findAll();
        }

        $data = [
            'ticket' => $ticket,
            'tecnicos' => $tecnicos,
            'title' => 'Detalhes do Ticket #' . $ticket['id']
        ];
        
        return view('tickets/view_ticket', $data);
    }

    // --- Métodos AJAX (CRUD) --- //

    public function create()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        // Log dos dados recebidos para debug
        log_message('debug', 'Dados POST recebidos: ' . json_encode($this->request->getPost()));

        $rules = [
            'equipamento_id' => 'required|integer',
            'sala_id'        => 'required|integer',
            'tipo_avaria_id' => 'required|integer',
            'descricao'      => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            log_message('error', 'Erros de validação: ' . json_encode($this->validator->getErrors()));
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Obter ID do utilizador da sessão (compatível com LoginController)
        $userId = session()->get('user_id');
        if (!$userId) {
            log_message('error', 'Tentativa de criar ticket sem autenticação');
            return $this->failUnauthorized('Utilizador não autenticado. Por favor, faça login novamente.');
        }

        // Verificar se o utilizador existe na base de dados
        $user = $this->userModel->find($userId);
        if (!$user) {
            log_message('error', "User ID {$userId} da sessão não existe na tabela user");
            session()->destroy();
            return $this->failUnauthorized('Sessão inválida. Por favor, faça login novamente.');
        }

        $data = [
            'equipamento_id' => $this->request->getPost('equipamento_id'),
            'sala_id'        => $this->request->getPost('sala_id'),
            'tipo_avaria_id' => $this->request->getPost('tipo_avaria_id'),
            'user_id'        => $userId,
            'descricao'      => $this->request->getPost('descricao'),
            'estado'         => 'novo', // Estado inicial
            'prioridade'     => 'media' // Prioridade inicial
        ];

        log_message('debug', 'Dados preparados para inserção: ' . json_encode($data));

        if ($this->ticketsModel->insert($data)) {
            $ticketId = $this->ticketsModel->getInsertID();
            log_message('info', "Ticket criado com sucesso. ID: {$ticketId}");
            
            // Alocar o equipamento à sala automaticamente
            $this->allocateEquipmentToRoom($data['equipamento_id'], $data['sala_id'], $userId);
            
            try {
                $ticketDetails = $this->ticketsModel->getTicketDetails($ticketId);
                log_message('debug', 'Detalhes do ticket obtidos: ' . json_encode($ticketDetails));
                
                // Tentar enviar email de confirmação (não bloqueia se falhar)
                try {
                    $this->sendTicketConfirmationEmail($ticketDetails);
                    log_message('info', 'Email de confirmação enviado para ticket #' . $ticketId);
                } catch (\Exception $e) {
                    log_message('warning', 'Falha ao enviar email de confirmação para ticket #' . $ticketId . ': ' . $e->getMessage());
                }
                
                return $this->respondCreated(['message' => 'Ticket criado com sucesso!', 'ticketId' => $ticketId]);
                
            } catch (\Exception $e) {
                log_message('error', 'Erro ao processar ticket após inserção: ' . $e->getMessage());
                // Mesmo com erro no processamento, o ticket foi criado
                return $this->respondCreated([
                    'message' => 'Ticket criado, mas ocorreu um erro no processamento. Por favor, contacte o administrador.',
                    'ticketId' => $ticketId,
                    'warning' => true
                ]);
            }
        } else {
            $errors = $this->ticketsModel->errors();
            log_message('error', 'Erro ao inserir ticket: ' . json_encode($errors));
            return $this->failServerError('Não foi possível criar o ticket. ' . json_encode($errors));
        }
    }

    public function update($id = null)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->failUnauthorized('Acesso não autorizado.');
            }

            $ticket = $this->ticketsModel->find($id);
            if (!$ticket) {
                return $this->failNotFound('Ticket não encontrado.');
            }

            // Apenas o criador pode editar se o estado for 'novo', ou admins (nível 8+)
            $userId = session()->get('user_id');
            $userLevel = (int) session()->get('level') ?? 0;
            
            $isOwner = $ticket['user_id'] == $userId && $ticket['estado'] == 'novo';
            $isAdmin = $userLevel >= 8;
            
            if (!$isOwner && !$isAdmin) {
                return $this->failUnauthorized('Não tem permissão para editar este ticket.');
            }

            $rules = [
                'equipamento_id' => 'permit_empty|integer',
                'sala_id'        => 'permit_empty|integer',
                'tipo_avaria_id' => 'required|integer',
                'descricao'      => 'required|min_length[10]'
            ];

            if (!$this->validate($rules)) {
                return $this->failValidationErrors($this->validator->getErrors());
            }

            $data = [
                'tipo_avaria_id' => $this->request->getPost('tipo_avaria_id'),
                'descricao'      => $this->request->getPost('descricao'),
            ];

            // Adicionar campos opcionais apenas se foram enviados
            if ($this->request->getPost('equipamento_id')) {
                $data['equipamento_id'] = $this->request->getPost('equipamento_id');
            }
            if ($this->request->getPost('sala_id')) {
                $data['sala_id'] = $this->request->getPost('sala_id');
            }
            if ($this->request->getPost('estado')) {
                $data['estado'] = $this->request->getPost('estado');
            }
            if ($this->request->getPost('prioridade')) {
                $data['prioridade'] = $this->request->getPost('prioridade');
            }
            if ($this->request->getPost('atribuido_user_id') !== null) {
                $data['atribuido_user_id'] = $this->request->getPost('atribuido_user_id') ?: null;
            }

            if ($this->ticketsModel->update($id, $data)) {
                $ticketDetails = $this->ticketsModel->getTicketDetails($id);
                
                // Tentar enviar email de atualização (não bloqueia se falhar)
                try {
                    $this->sendTicketUpdateEmail($ticketDetails);
                } catch (\Exception $e) {
                    log_message('warning', 'Falha ao enviar email de atualização: ' . $e->getMessage());
                }
                
                return $this->respond([
                    'status' => 200,
                    'message' => 'Ticket atualizado com sucesso!'
                ]);
            } else {
                return $this->failServerError('Não foi possível atualizar o ticket.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro no update: ' . $e->getMessage());
            return $this->fail('Erro ao atualizar ticket: ' . $e->getMessage(), 500);
        }
    }

    public function delete($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        $ticket = $this->ticketsModel->find($id);
        if (!$ticket) {
            return $this->failNotFound('Ticket não encontrado.');
        }

        // Apenas o criador pode apagar se o estado for 'novo', ou admins (nível 8+)
        $userId = session()->get('user_id');
        $userLevel = session()->get('level') ?? 0;
        
        $isOwner = $ticket['user_id'] == $userId && $ticket['estado'] == 'novo';
        $isAdmin = $userLevel >= 8;
        
        if (!$isOwner && !$isAdmin) {
            return $this->failUnauthorized('Não tem permissão para apagar este ticket.');
        }

        if ($this->ticketsModel->delete($id)) {
            // Tentar enviar email de notificação (não bloqueia se falhar)
            try {
                $this->sendTicketDeletionEmail($ticket);
            } catch (\Exception $e) {
                log_message('warning', 'Falha ao enviar email de eliminação: ' . $e->getMessage());
            }
            
            return $this->respondDeleted(['message' => 'Ticket apagado com sucesso!']);
        } else {
            return $this->failServerError('Não foi possível apagar o ticket.');
        }
    }

    public function get($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        $ticket = $this->ticketsModel->find($id);
        if (!$ticket) {
            return $this->failNotFound('Ticket não encontrado.');
        }

        // Verificar se o utilizador tem permissão para ver este ticket
        $userId = session()->get('user_id');
        $userLevel = session()->get('level');
        
        // Pode ver se: é o criador, é o atribuído, ou é técnico/admin (level >= 5)
        if ($ticket['user_id'] != $userId && 
            $ticket['atribuido_user_id'] != $userId && 
            $userLevel < 5) {
            return $this->failUnauthorized('Não tem permissão para ver este ticket.');
        }

        // Retornar no formato esperado pelo JavaScript
        return $this->respond([
            'status' => 200,
            'data' => $ticket,
            'message' => 'Ticket encontrado com sucesso.'
        ]);
    }

    // --- Métodos AJAX para DataTables --- //

    public function getMyTicketsDataTable()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->failUnauthorized('Utilizador não autenticado.');
        }

        $tickets = $this->ticketsModel->getMyTickets($userId);

        $data = [];
        foreach ($tickets as $ticket) {
            $row = [];
            
            // Formatar nome do equipamento com tipo, marca, modelo e nº série
            $equipamento = '';
            if (!empty($ticket['equipamento_tipo'])) {
                $equipamento .= '<strong>' . $ticket['equipamento_tipo'] . '</strong><br>';
            }
            $equipamento .= $ticket['equipamento_marca'] . ' ' . $ticket['equipamento_modelo'];
            if (!empty($ticket['equipamento_nserie'])) {
                $equipamento .= '<br><small class="text-muted">S/N: ' . $ticket['equipamento_nserie'] . '</small>';
            }
            
            // Adicionar badge se o ticket foi atribuído ao utilizador
            if ($ticket['atribuido_user_id'] == $userId && $ticket['user_id'] != $userId) {
                $equipamento .= '<br><span class="badge bg-info text-white mt-1"><i class="fas fa-user-tag"></i> Atribuído a mim</span>';
            }
            
            $row[] = $equipamento;
            $row[] = $ticket['codigo_sala'];
            $row[] = $ticket['tipo_avaria_descricao'];
            $row[] = $ticket['descricao'];
            $row[] = getEstadoBadge($ticket['estado'], true); // Badge dinâmico
            $row[] = $ticket['prioridade'];
            $row[] = date('d/m/Y H:i', strtotime($ticket['created_at']));
            $row[] = date('d/m/Y H:i', strtotime($ticket['updated_at']));
            
            $options = '';
            // Botão para ver detalhes (sempre visível)
            $options .= '<a href="' . base_url('tickets/view/' . $ticket['id']) . '" class="btn btn-sm btn-info" title="Ver Detalhes"><i class="fas fa-eye"></i></a> ';
            
            // Apenas o criador pode editar/apagar se o estado for 'novo'
            if ($ticket['user_id'] == $userId && $ticket['estado'] == 'novo') {
                $options .= '<button class="btn btn-sm btn-warning edit-ticket" data-id="' . $ticket['id'] . '" title="Editar"><i class="fas fa-edit"></i></button> ';
                $options .= '<button class="btn btn-sm btn-danger delete-ticket" data-id="' . $ticket['id'] . '" title="Apagar"><i class="fas fa-trash"></i></button>';
            }
            $row[] = $options;
            $row[] = $ticket['id']; // ID do ticket (coluna oculta)
            $row[] = $ticket['estado']; // Código do estado (coluna oculta para JS)
            $row[] = isEstadoFinal($ticket['estado']) ? 1 : 0; // Flag se é estado final (coluna oculta)
            $data[] = $row;
        }

        return $this->respond(['data' => $data]);
    }

    public function getTicketsForTreatmentDataTable()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        // Nível de acesso já verificado na função tratamentoTickets()

        $tickets = $this->ticketsModel->getTicketsForTreatment();

        $data = [];
        foreach ($tickets as $ticket) {
            $row = [];
            $row[] = $ticket['equipamento_marca'] . ' ' . $ticket['equipamento_modelo'];
            $row[] = $ticket['codigo_sala'];
            $row[] = $ticket['tipo_avaria_descricao'];
            $row[] = $ticket['descricao'];
            $row[] = getEstadoBadge($ticket['estado'], true); // Badge dinâmico
            $row[] = $ticket['prioridade'];
            $row[] = $ticket['created_at'];
            $row[] = $ticket['user_nome'];
            $row[] = $ticket['atribuido_user_nome'] ?? 'Não Atribuído';

            // Botão para ver detalhes (link direto)
            $options = '<a href="' . base_url('tickets/view/' . $ticket['id']) . '" class="btn btn-sm btn-info" title="Ver Detalhes"><i class="fas fa-eye"></i></a> ';
            if (session()->get('level') >= 5) { // Apenas técnicos podem atribuir/alterar estado
                $options .= '<button class="btn btn-sm btn-primary assign-ticket" data-id="' . $ticket['id'] . '" title="Atribuir/Estado"><i class="fas fa-user-cog"></i></button>';
            }
            $row[] = $options;
            $row[] = $ticket['id']; // ID do ticket (coluna oculta)
            $row[] = $ticket['estado']; // Código do estado (coluna oculta)
            $data[] = $row;
        }

        return $this->respond(['data' => $data]);
    }

    public function getAllTicketsDataTable()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        // Nível de acesso já verificado na função todosTickets()
        
        $userLevel = (int) session()->get('level');

        $tickets = $this->ticketsModel->getAllTicketsOrdered();

        $data = [];
        foreach ($tickets as $ticket) {
            $row = [];
            $row[] = $ticket['id'];
            $row[] = $ticket['equipamento_marca'] . ' ' . $ticket['equipamento_modelo'];
            $row[] = $ticket['codigo_sala'];
            $row[] = $ticket['tipo_avaria_descricao'];
            $row[] = $ticket['descricao'];
            $row[] = getEstadoBadge($ticket['estado'], true); // Badge dinâmico
            $row[] = $ticket['prioridade'];
            $row[] = $ticket['created_at'];
            $row[] = $ticket['user_nome'];
            $row[] = $ticket['atribuido_user_nome'] ?? 'Não Atribuído';
            $row[] = $ticket['ticket_aceite'] ? 'Sim' : 'Não';
            
            // Botão para ver detalhes (link direto)
            $options = '<a href="' . base_url('tickets/view/' . $ticket['id']) . '" class="btn btn-sm btn-info" title="Ver Detalhes"><i class="fas fa-eye"></i></a> ';
            
            if ($userLevel >= 8) { // Admins (8) e Super Admins (9) podem editar e apagar
                $options .= '<button class="btn btn-sm btn-warning edit-ticket" data-id="' . $ticket['id'] . '" title="Editar"><i class="fas fa-edit"></i></button> ';
                $options .= '<button class="btn btn-sm btn-danger delete-ticket" data-id="' . $ticket['id'] . '" title="Apagar"><i class="fas fa-trash"></i></button>';
            }
            $row[] = $options;
            $row[] = $ticket['estado']; // Código do estado (coluna oculta)
            $row[] = isEstadoFinal($ticket['estado']) ? 1 : 0; // Flag estado final (coluna oculta)
            $data[] = $row;
        }

        return $this->respond(['data' => $data]);
    }

    // --- Métodos de Ação para Tratamento de Tickets --- //

    public function assignTicket()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        // Nível de acesso já verificado na função tratamentoTickets()
        if (session()->get('level') < 5) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        // Log dos dados recebidos para debug
        log_message('debug', 'assignTicket - Dados recebidos: ' . json_encode($this->request->getPost()));

        $rules = [
            'ticket_id'         => 'required|integer',
            'atribuido_user_id' => 'permit_empty|integer',
            'estado'            => 'required|validar_estado_ticket'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            log_message('error', 'assignTicket - Erros de validação: ' . json_encode($errors));
            return $this->fail($errors, 400);
        }

        $ticketId = $this->request->getPost('ticket_id');
        $atribuidoUserId = $this->request->getPost('atribuido_user_id');
        $estado = $this->request->getPost('estado');

        // Se o estado recebido for 'novo', alterar automaticamente para 'em_resolucao' ao atribuir
        if ($estado === 'novo') {
            $estado = 'em_resolucao';
        }

        $data = [
            'atribuido_user_id' => $atribuidoUserId,
            'estado'            => $estado
        ];

        if ($this->ticketsModel->update($ticketId, $data)) {
            $ticketDetails = $this->ticketsModel->getTicketDetails($ticketId);
            
            // Tentar enviar email de atribuição (não bloqueia se falhar)
            try {
                $this->sendTicketAssignmentEmail($ticketDetails);
            } catch (\Exception $e) {
                log_message('warning', 'Falha ao enviar email de atribuição: ' . $e->getMessage());
            }
            
            return $this->respondUpdated(['message' => 'Ticket atribuído e estado atualizado com sucesso!']);
        } else {
            return $this->failServerError('Não foi possível atribuir o ticket ou atualizar o estado.');
        }
    }

    public function acceptTicket($ticketId = null)
    {
        // Este método pode ser chamado via GET (link no email) ou AJAX
        // Para simplificar, vamos assumir que é um link no email e o utilizador já está logado
        // Em um cenário real, seria necessário um token seguro para aceitação via link.

        if (!session()->get("isLoggedIn")) {
            return redirect()->to("/login")->with('error', 'Por favor, faça login para aceitar o ticket.');
        }

        $ticket = $this->ticketsModel->find($ticketId);
        if (!$ticket) {
            return redirect()->to("/dashboard")->with('error', 'Ticket não encontrado.');
        }

        // Verificar se o utilizador logado é o atribuído
        if ($ticket['atribuido_user_id'] != session()->get('user_id')) {
            return redirect()->to("/dashboard")->with('error', 'Não tem permissão para aceitar este ticket.');
        }

        // Apenas aceitar se o estado for 'novo' ou 'em_resolucao' (se já foi atribuído mas ainda não aceitou)
        if ($ticket['estado'] == 'novo' || $ticket['estado'] == 'aguarda_peca') {
            $data = [
                'ticket_aceite' => true,
                'estado'        => 'em_resolucao'
            ];

            if ($this->ticketsModel->update($ticketId, $data)) {
                $ticketDetails = $this->ticketsModel->getTicketDetails($ticketId);
                
                // Tentar enviar email de aceitação (não bloqueia se falhar)
                try {
                    $this->sendTicketAcceptedEmail($ticketDetails);
                } catch (\Exception $e) {
                    log_message('warning', 'Falha ao enviar email de aceitação: ' . $e->getMessage());
                }
                
                return redirect()->to("/tickets/tratamento")->with('success', 'Ticket aceite e estado atualizado para Em Resolução!');
            } else {
                return redirect()->to("/dashboard")->with('error', 'Não foi possível aceitar o ticket.');
            }
        }

        return redirect()->to("/dashboard")->with('info', 'O ticket já foi aceite ou está num estado que não permite aceitação.');
    }

    // --- Métodos de E-mail --- //

    private function sendEmail($to, $subject, $message)
    {
        // Carregar configurações de e-mail do .env com conversão de tipos adequada
        $config = [
            'protocol'     => getenv('email.protocol') ?: 'smtp',
            'SMTPHost'     => getenv('email.SMTPHost') ?: 'smtp.gmail.com',
            'SMTPPort'     => (int)(getenv('email.SMTPPort') ?: 587),
            'SMTPUser'     => getenv('email.SMTPUser') ?: '',
            'SMTPPass'     => getenv('email.SMTPPass') ?: '',
            'SMTPCrypto'   => getenv('email.SMTPCrypto') ?: 'tls',
            'SMTPAuth'     => true,
            'SMTPTimeout'  => (int)(getenv('email.SMTPTimeout') ?: 10),
            'mailType'     => 'html',
            'charset'      => 'UTF-8',
            'newline'      => "\r\n",
            'CRLF'         => "\r\n"
        ];

        $this->email->initialize($config);

        $this->email->setFrom(getenv('email.fromEmail') ?: 'antonioneto@aejoaodebarros.pt', 
                              getenv('email.fromName') ?: 'António Neto - Escola Digital JB');
        $this->email->setTo($to);
        $this->email->setSubject($subject);
        $this->email->setMessage($message);

        if ($this->email->send()) {
            log_message('info', 'Email enviado com sucesso para: ' . $to);
            return true;
        } else {
            log_message('error', 'Falha no envio de email para: ' . $to . ' Erro: ' . $this->email->printDebugger(['headers', 'subject', 'body']));
            return false;
        }
    }

    /**
     * Aloca automaticamente o equipamento à sala especificada
     * Se o equipamento já estiver em outra sala, faz a movimentação
     */
    private function allocateEquipmentToRoom($equipamentoId, $salaId, $userId)
    {
        try {
            // Verificar se o equipamento já está alocado a alguma sala
            $alocacaoAtual = $this->equipamentosSalaModel
                ->where('equipamento_id', $equipamentoId)
                ->where('data_saida IS NULL')
                ->first();

            if ($alocacaoAtual) {
                // Se já está na mesma sala, não fazer nada
                if ($alocacaoAtual['sala_id'] == $salaId) {
                    log_message('info', "Equipamento {$equipamentoId} já está alocado à sala {$salaId}");
                    return;
                }

                // Se está em outra sala, registrar saída
                $this->equipamentosSalaModel->update($alocacaoAtual['id'], [
                    'data_saida' => date('Y-m-d H:i:s'),
                    'motivo_movimentacao' => 'Movimentação automática via criação de ticket',
                    'observacoes' => 'Equipamento movido automaticamente ao criar ticket de avaria'
                ]);
                
                log_message('info', "Equipamento {$equipamentoId} movido da sala {$alocacaoAtual['sala_id']} para sala {$salaId}");
            }

            // Criar nova alocação
            $novaAlocacao = [
                'equipamento_id' => $equipamentoId,
                'sala_id' => $salaId,
                'data_entrada' => date('Y-m-d H:i:s'),
                'data_saida' => null,
                'motivo_movimentacao' => 'Alocação automática via criação de ticket',
                'user_id' => $userId,
                'observacoes' => 'Equipamento alocado automaticamente ao criar ticket de avaria'
            ];

            if ($this->equipamentosSalaModel->insert($novaAlocacao)) {
                log_message('info', "Equipamento {$equipamentoId} alocado com sucesso à sala {$salaId}");
                
                // Atualizar o campo sala_id na tabela equipamentos também (se existir)
                $this->equipamentosModel->update($equipamentoId, ['sala_id' => $salaId]);
            } else {
                log_message('error', "Erro ao alocar equipamento {$equipamentoId} à sala {$salaId}");
            }

        } catch (\Exception $e) {
            log_message('error', 'Erro ao alocar equipamento à sala: ' . $e->getMessage());
            // Não bloquear a criação do ticket mesmo se a alocação falhar
        }
    }

    private function sendTicketConfirmationEmail($ticketDetails)
    {
        $user = $this->userModel->find($ticketDetails['user_id']);
        if (!$user) return;

        $subject = 'Confirmação de Criação de Ticket #' . $ticketDetails['id'];
        $message = view('emails/ticket_confirmation', ['ticket' => $ticketDetails, 'user' => $user]);
        $this->sendEmail($user['email'], $subject, $message);
    }

    private function sendTicketUpdateEmail($ticketDetails)
    {
        $user = $this->userModel->find($ticketDetails['user_id']);
        if (!$user) return;

        $subject = 'Atualização do Ticket #' . $ticketDetails['id'];
        $message = view('emails/ticket_update', ['ticket' => $ticketDetails, 'user' => $user]);
        $this->sendEmail($user['email'], $subject, $message);
    }

    private function sendTicketDeletionEmail($ticketDetails)
    {
        $user = $this->userModel->find($ticketDetails['user_id']);
        if (!$user) return;

        $subject = 'Ticket #' . $ticketDetails['id'] . ' Eliminado';
        $message = view('emails/ticket_deletion', ['ticket' => $ticketDetails, 'user' => $user]);
        $this->sendEmail($user['email'], $subject, $message);
    }

    private function sendTicketAssignmentEmail($ticketDetails)
    {
        $assignedUser = $this->userModel->find($ticketDetails['atribuido_user_id']);
        if (!$assignedUser) return;

        $subject = 'Ticket #' . $ticketDetails['id'] . ' Atribuído a Você';
        $message = view('emails/ticket_assignment', ['ticket' => $ticketDetails, 'assignedUser' => $assignedUser]);
        $this->sendEmail($assignedUser['email'], $subject, $message);
    }

    private function sendTicketAcceptedEmail($ticketDetails)
    {
        $creatorUser = $this->userModel->find($ticketDetails['user_id']);
        $assignedUser = $this->userModel->find($ticketDetails['atribuido_user_id']);

        // Notificar criador
        if ($creatorUser) {
            $subjectCreator = 'Ticket #' . $ticketDetails['id'] . ' Aceite e em Resolução';
            $messageCreator = view('emails/ticket_accepted_creator', ['ticket' => $ticketDetails, 'user' => $creatorUser, 'assignedUser' => $assignedUser]);
            $this->sendEmail($creatorUser['email'], $subjectCreator, $messageCreator);
        }

        // Notificar atribuído (se diferente do criador)
        if ($assignedUser && $creatorUser['id'] != $assignedUser['id']) {
            $subjectAssigned = 'Você Aceitou o Ticket #' . $ticketDetails['id'];
            $messageAssigned = view('emails/ticket_accepted_assigned', ['ticket' => $ticketDetails, 'user' => $assignedUser]);
            $this->sendEmail($assignedUser['email'], $subjectAssigned, $messageAssigned);
        }
    }

    /**
     * Atualizar prioridade do ticket
     */
    public function updatePrioridade()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        $ticketId = (int) $this->request->getPost('ticket_id');
        $prioridade = $this->request->getPost('prioridade');

        // Validação
        if (!in_array($prioridade, ['baixa', 'media', 'alta', 'critica'])) {
            return $this->fail('Prioridade inválida.');
        }

        // Verificar se o ticket existe
        $ticket = $this->ticketsModel->find($ticketId);
        if (!$ticket) {
            return $this->failNotFound('Ticket não encontrado.');
        }

        // Bloquear mudança de prioridade em tickets reparados
        if ($ticket['estado'] == 'reparado') {
            return $this->fail('Não é possível alterar a prioridade de um ticket já reparado.');
        }

        // Atualizar prioridade
        if ($this->ticketsModel->update($ticketId, ['prioridade' => $prioridade])) {
            // Log de atividade
            log_activity(
                (int) session()->get('user_id'),
                'Tickets',
                'Atualizar Prioridade',
                'Prioridade do ticket #' . $ticketId . ' alterada para ' . $prioridade,
                $ticketId,
                ['prioridade_anterior' => $ticket['prioridade']],
                ['prioridade_nova' => $prioridade]
            );

            return $this->respond([
                'success' => true,
                'message' => 'Prioridade atualizada com sucesso.',
                'prioridade' => $prioridade
            ], 200);
        }

        return $this->failServerError('Erro ao atualizar prioridade.');
    }

    /**
     * Resolver ticket e criar registo de reparação
     */
    public function resolverTicket()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        // Apenas técnicos (level >= 5) podem resolver tickets
        if (session()->get('level') < 5) {
            return $this->failUnauthorized('Sem permissão para resolver tickets.');
        }

        $ticketId = (int) $this->request->getPost('ticket_id');
        $descricao = $this->request->getPost('descricao');
        $tempoGasto = $this->request->getPost('tempo_gasto_min') ? (int) $this->request->getPost('tempo_gasto_min') : null;

        // Validação
        $rules = [
            'ticket_id' => 'required|is_natural_no_zero',
            'descricao' => 'required|min_length[10]',
            'tempo_gasto_min' => 'permit_empty|is_natural'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Verificar se o ticket existe
        $ticket = $this->ticketsModel->find($ticketId);
        if (!$ticket) {
            return $this->failNotFound('Ticket não encontrado.');
        }

        // Verificar se já não está resolvido
        if ($ticket['estado'] == 'reparado') {
            return $this->fail('Este ticket já está resolvido.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Criar registo de reparação
            $registoReparacaoModel = new \App\Models\RegistosReparacaoModel();
            $registoId = $registoReparacaoModel->insert([
                'ticket_id' => $ticketId,
                'user_id' => session()->get('user_id'),
                'descricao' => $descricao,
                'tempo_gasto_min' => $tempoGasto ? $tempoGasto : null,
                'criado_em' => date('Y-m-d H:i:s')
            ]);

            if (!$registoId) {
                throw new \Exception('Erro ao criar registo de reparação.');
            }

            // 2. Atualizar estado do ticket para "reparado"
            if (!$this->ticketsModel->update($ticketId, ['estado' => 'reparado'])) {
                throw new \Exception('Erro ao atualizar estado do ticket.');
            }

            // 3. Log de atividade
            log_activity(
                (int) session()->get('user_id'),
                'Tickets',
                'Resolver Ticket',
                'Ticket #' . $ticketId . ' resolvido',
                (int) $ticketId,
                ['estado_anterior' => $ticket['estado']],
                [
                    'estado_novo' => 'reparado',
                    'registo_reparacao_id' => $registoId,
                    'tempo_gasto_min' => $tempoGasto
                ]
            );

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Erro ao processar resolução do ticket.');
            }

            return $this->respond([
                'message' => 'Ticket resolvido com sucesso!',
                'registo_id' => $registoId
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Erro ao resolver ticket: ' . $e->getMessage());
            return $this->failServerError('Erro ao resolver ticket: ' . $e->getMessage());
        }
    }

    /**
     * Reabrir ticket (apenas admins level >= 8)
     */
    public function reabrirTicket()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        // Apenas admins (level >= 8) podem reabrir tickets
        if (session()->get('level') < 8) {
            return $this->failUnauthorized('Sem permissão para reabrir tickets.');
        }

        $ticketId = (int) $this->request->getPost('ticket_id');
        $motivo = $this->request->getPost('motivo');

        // Validação
        if (empty($motivo)) {
            return $this->fail('Por favor, indique o motivo da reabertura.');
        }

        // Verificar se o ticket existe
        $ticket = $this->ticketsModel->find($ticketId);
        if (!$ticket) {
            return $this->failNotFound('Ticket não encontrado.');
        }

        // Verificar se está reparado
        if ($ticket['estado'] != 'reparado') {
            return $this->fail('Apenas tickets reparados podem ser reabertos.');
        }

        // Verificar se tem técnico atribuído
        if (!$ticket['atribuido_user_id']) {
            return $this->fail('Este ticket não tem técnico atribuído. Por favor, atribua um técnico primeiro.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Atualizar estado para em_resolucao
            if (!$this->ticketsModel->update($ticketId, ['estado' => 'em_resolucao'])) {
                throw new \Exception('Erro ao atualizar estado do ticket.');
            }

            // Log de atividade
            log_activity(
                (int) session()->get('user_id'),
                'Tickets',
                'Reabrir Ticket',
                'Ticket #' . $ticketId . ' reaberto. Motivo: ' . $motivo,
                $ticketId,
                ['estado_anterior' => 'reparado'],
                ['estado_novo' => 'em_resolucao', 'motivo' => $motivo]
            );

            // Enviar email ao técnico
            $tecnico = $this->userModel->find($ticket['atribuido_user_id']);
            
            if ($tecnico && $tecnico['email']) {
                try {
                    $adminNome = session()->get('name') ?? 'Administrador';
                    
                    // Carregar detalhes completos do ticket para o email
                    $ticketDetalhes = $this->ticketsModel->getTicketDetails($ticketId);
                    
                    $emailData = [
                        'ticket' => $ticketDetalhes,
                        'tecnico' => $tecnico,
                        'adminNome' => $adminNome,
                        'motivo' => $motivo
                    ];
                    
                    $subject = "Ticket #{$ticketId} Reaberto - Requer Atenção";
                    $message = view('emails/ticket_reopened', $emailData);
                    
                    $this->sendEmail($tecnico['email'], $subject, $message);
                } catch (\Exception $e) {
                    log_message('error', 'Erro ao enviar email de reabertura: ' . $e->getMessage());
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Erro ao reabrir ticket.');
            }

            return $this->respond([
                'success' => true,
                'message' => 'Ticket reaberto com sucesso! Email enviado ao técnico.'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Erro ao reabrir ticket: ' . $e->getMessage());
            return $this->failServerError('Erro ao reabrir ticket: ' . $e->getMessage());
        }
    }

    /**
     * Aceitar ticket atribuído
     */
    public function aceitarTicket()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        $ticketId = (int) $this->request->getPost('ticket_id');
        $userId = (int) session()->get('user_id');

        // Verificar se o ticket existe
        $ticket = $this->ticketsModel->find($ticketId);
        if (!$ticket) {
            return $this->failNotFound('Ticket não encontrado.');
        }

        // Verificar se o ticket está atribuído ao usuário atual
        if ($ticket['atribuido_user_id'] != $userId) {
            return $this->fail('Este ticket não está atribuído a você.');
        }

        // Verificar se já foi aceite
        if ($ticket['ticket_aceite']) {
            return $this->fail('Este ticket já foi aceite.');
        }

        // Verificar se está em estado válido
        if (in_array($ticket['estado'], ['reparado', 'anulado'])) {
            return $this->fail('Não é possível aceitar um ticket ' . $ticket['estado'] . '.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Marcar como aceite
            if (!$this->ticketsModel->update($ticketId, ['ticket_aceite' => true])) {
                throw new \Exception('Erro ao aceitar ticket.');
            }

            // Log de atividade
            log_activity(
                $userId,
                'Tickets',
                'Aceitar Ticket',
                'Ticket #' . $ticketId . ' aceite pelo técnico',
                $ticketId,
                ['ticket_aceite' => false],
                ['ticket_aceite' => true]
            );

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Erro ao aceitar ticket.');
            }

            return $this->respond([
                'success' => true,
                'message' => 'Ticket aceite com sucesso!'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Erro ao aceitar ticket: ' . $e->getMessage());
            return $this->failServerError('Erro ao aceitar ticket: ' . $e->getMessage());
        }
    }

    /**
     * Rejeitar ticket atribuído (remove atribuição)
     */
    public function rejeitarTicket()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        $ticketId = (int) $this->request->getPost('ticket_id');
        $motivo = $this->request->getPost('motivo');
        $userId = (int) session()->get('user_id');

        // Validação
        if (empty($motivo)) {
            return $this->fail('Por favor, indique o motivo da rejeição.');
        }

        // Verificar se o ticket existe
        $ticket = $this->ticketsModel->find($ticketId);
        if (!$ticket) {
            return $this->failNotFound('Ticket não encontrado.');
        }

        // Verificar se o ticket está atribuído ao usuário atual
        if ($ticket['atribuido_user_id'] != $userId) {
            return $this->fail('Este ticket não está atribuído a você.');
        }

        // Verificar se já foi aceite
        if ($ticket['ticket_aceite']) {
            return $this->fail('Não é possível rejeitar um ticket já aceite.');
        }

        // Verificar se está em estado válido
        if (in_array($ticket['estado'], ['reparado', 'anulado'])) {
            return $this->fail('Não é possível rejeitar um ticket ' . $ticket['estado'] . '.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Remover atribuição e voltar estado para 'novo'
            $updateData = [
                'atribuido_user_id' => null,
                'ticket_aceite' => false,
                'estado' => 'novo'
            ];

            if (!$this->ticketsModel->update($ticketId, $updateData)) {
                throw new \Exception('Erro ao rejeitar ticket.');
            }

            // Log de atividade
            log_activity(
                $userId,
                'Tickets',
                'Rejeitar Ticket',
                'Ticket #' . $ticketId . ' rejeitado. Motivo: ' . $motivo,
                $ticketId,
                [
                    'atribuido_user_id' => $ticket['atribuido_user_id'],
                    'estado' => $ticket['estado']
                ],
                [
                    'atribuido_user_id' => null,
                    'estado' => 'novo',
                    'motivo' => $motivo
                ]
            );

            // Notificar admins sobre a rejeição
            $this->notifyAdminsTicketRejection($ticketId, $motivo);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Erro ao rejeitar ticket.');
            }

            return $this->respond([
                'success' => true,
                'message' => 'Ticket rejeitado com sucesso. O ticket voltou a ficar sem atribuição.'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Erro ao rejeitar ticket: ' . $e->getMessage());
            return $this->failServerError('Erro ao rejeitar ticket: ' . $e->getMessage());
        }
    }

    /**
     * Notificar admins sobre rejeição de ticket
     */
    private function notifyAdminsTicketRejection($ticketId, $motivo)
    {
        try {
            // Buscar admins (level >= 8)
            $admins = $this->userModel->where('level >=', 8)->where('status', 1)->findAll();
            
            if (empty($admins)) {
                return;
            }

            // Carregar detalhes do ticket
            $ticket = $this->ticketsModel->getTicketDetails($ticketId);
            $tecnico = $this->userModel->find(session()->get('user_id'));

            $subject = "Ticket #{$ticketId} Rejeitado por Técnico";
            
            foreach ($admins as $admin) {
                if ($admin['email']) {
                    $emailData = [
                        'ticket' => $ticket,
                        'admin' => $admin,
                        'tecnico' => $tecnico,
                        'motivo' => $motivo
                    ];
                    
                    $message = view('emails/ticket_rejected', $emailData);
                    $this->sendEmail($admin['email'], $subject, $message);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao notificar admins sobre rejeição: ' . $e->getMessage());
        }
    }

    /**
     * Obter estatísticas básicas de tickets
     */
    public function getStatistics()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        try {
            $stats = [
                'total' => $this->ticketsModel->countAll(),
                'novo' => $this->ticketsModel->where('estado', 'novo')->countAllResults(false),
                'em_resolucao' => $this->ticketsModel->where('estado', 'em_resolucao')->countAllResults(false),
                'aguarda_peca' => $this->ticketsModel->where('estado', 'aguarda_peca')->countAllResults(false),
                'reparado' => $this->ticketsModel->where('estado', 'reparado')->countAllResults(false),
                'anulado' => $this->ticketsModel->where('estado', 'anulado')->countAllResults(false),
            ];

            return $this->respond([
                'status' => 200,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao carregar estatísticas: ' . $e->getMessage());
            return $this->failServerError('Erro ao carregar estatísticas.');
        }
    }

    /**
     * Obter estatísticas avançadas de tickets
     */
    public function getAdvancedStatistics()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        try {
            $stats = [
                'por_estado' => $this->ticketsModel
                    ->select('estado, COUNT(*) as total')
                    ->groupBy('estado')
                    ->findAll(),
                
                'por_prioridade' => $this->ticketsModel
                    ->select('prioridade, COUNT(*) as total')
                    ->groupBy('prioridade')
                    ->findAll(),
                
                'por_tipo_avaria' => $this->ticketsModel
                    ->select('tipos_avaria.descricao, COUNT(*) as total')
                    ->join('tipos_avaria', 'tipos_avaria.id = tickets.tipo_avaria_id')
                    ->groupBy('tickets.tipo_avaria_id')
                    ->findAll(),
                
                'por_usuario' => $this->ticketsModel
                    ->select('user.name, COUNT(*) as total')
                    ->join('user', 'user.id = tickets.user_id')
                    ->groupBy('tickets.user_id')
                    ->orderBy('total', 'DESC')
                    ->limit(10)
                    ->findAll(),
            ];

            return $this->respond([
                'status' => 200,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao carregar estatísticas avançadas: ' . $e->getMessage());
            return $this->failServerError('Erro ao carregar estatísticas avançadas.');
        }
    }

    /**
     * Exportar tickets para Excel
     */
    public function exportToExcel()
    {
        // Apenas admins podem exportar
        if (session()->get('level') < 8) {
            return redirect()->to('/dashboard')->with('error', 'Acesso não autorizado.');
        }

        try {
            $tickets = $this->ticketsModel->getAllTicketsOrdered();

            // Preparar dados para CSV (compatível com Excel)
            $filename = 'tickets_' . date('Y-m-d_His') . '.csv';
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Cabeçalhos
            fputcsv($output, [
                'ID',
                'Equipamento',
                'Sala',
                'Tipo de Avaria',
                'Descrição',
                'Estado',
                'Prioridade',
                'Criado em',
                'Criado por',
                'Atribuído a',
                'Aceite'
            ], ';');
            
            // Dados
            foreach ($tickets as $ticket) {
                fputcsv($output, [
                    $ticket['id'],
                    $ticket['equipamento_marca'] . ' ' . $ticket['equipamento_modelo'],
                    $ticket['codigo_sala'],
                    $ticket['tipo_avaria_descricao'],
                    $ticket['descricao'],
                    $ticket['estado'],
                    $ticket['prioridade'],
                    $ticket['created_at'],
                    $ticket['user_nome'],
                    $ticket['atribuido_user_nome'] ?? 'Não Atribuído',
                    $ticket['ticket_aceite'] ? 'Sim' : 'Não'
                ], ';');
            }
            
            fclose($output);
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Erro ao exportar tickets: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao exportar tickets.');
        }
    }
}

