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
    protected $tiposAvariaModel;
    protected $userModel;
    protected $email;

    public function __construct()
    {
        $this->ticketsModel = new TicketsModel();
        $this->equipamentosModel = new EquipamentosModel();
        $this->salasModel = new SalasModel();
        $this->tiposAvariaModel = new TiposAvariaModel();
        $this->userModel = new UserModel();
        $this->email = new Email();
    }

    // --- Vistas --- //

    public function novoTicket()
    {
        // Apenas utilizadores autenticados podem criar tickets
        if (!session()->get("isLoggedIn")) {
            return redirect()->to("/login");
        }

        $data = [
            'equipamentos' => $this->equipamentosModel->findAll(),
            'salas'        => $this->salasModel->findAll(),
            'tiposAvaria'  => $this->tiposAvariaModel->findAll(),
            'title'        => 'Criar Novo Ticket'
        ];
        return view('tickets/novo_ticket', $data);
    }

    public function meusTickets()
    {
        // Apenas utilizadores autenticados podem ver os seus tickets
        if (!session()->get("isLoggedIn")) {
            return redirect()->to("/login");
        }

        $userId = session()->get('id'); // Assumindo que o ID do utilizador está na sessão
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
        // Apenas utilizadores de nível 9 ou superior
        if (!session()->get("isLoggedIn") || session()->get('level') < 9) {
            return redirect()->to("/dashboard")->with('error', 'Acesso não autorizado.');
        }

        $data = [
            'title' => 'Todos os Tickets'
        ];
        return view('tickets/tickets', $data);
    }

    // --- Métodos AJAX (CRUD) --- //

    public function create()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        $rules = [
            'equipamento_id' => 'required|integer',
            'sala_id'        => 'required|integer',
            'tipo_avaria_id' => 'required|integer',
            'descricao'      => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $userId = session()->get('id'); // ID do utilizador logado
        if (!$userId) {
            return $this->failUnauthorized('Utilizador não autenticado.');
        }

        $data = [
            'equipamento_id' => $this->request->getPost('equipamento_id'),
            'sala_id'        => $this->request->get('sala_id'),
            'tipo_avaria_id' => $this->request->get('tipo_avaria_id'),
            'user_id'        => $userId,
            'descricao'      => $this->request->get('descricao'),
            'estado'         => 'novo', // Estado inicial
            'prioridade'     => 'media' // Prioridade inicial
        ];

        if ($this->ticketsModel->insert($data)) {
            $ticketId = $this->ticketsModel->getInsertID();
            $ticketDetails = $this->ticketsModel->getTicketDetails($ticketId);
            $this->sendTicketConfirmationEmail($ticketDetails); // Enviar email de confirmação
            return $this->respondCreated(['message' => 'Ticket criado com sucesso!', 'ticketId' => $ticketId]);
        } else {
            return $this->failServerError('Não foi possível criar o ticket.');
        }
    }

    public function update($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        $ticket = $this->ticketsModel->find($id);
        if (!$ticket) {
            return $this->failNotFound('Ticket não encontrado.');
        }

        // Apenas o criador pode editar se o estado for 'novo'
        $userId = session()->get('id');
        if ($ticket['user_id'] != $userId || $ticket['estado'] != 'novo') {
            return $this->failUnauthorized('Não tem permissão para editar este ticket.');
        }

        $rules = [
            'equipamento_id' => 'required|integer',
            'sala_id'        => 'required|integer',
            'tipo_avaria_id' => 'required|integer',
            'descricao'      => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'equipamento_id' => $this->request->get('equipamento_id'),
            'sala_id'        => $this->request->get('sala_id'),
            'tipo_avaria_id' => $this->request->get('tipo_avaria_id'),
            'descricao'      => $this->request->get('descricao'),
        ];

        if ($this->ticketsModel->update($id, $data)) {
            $ticketDetails = $this->ticketsModel->getTicketDetails($id);
            $this->sendTicketUpdateEmail($ticketDetails); // Enviar email de atualização
            return $this->respondUpdated(['message' => 'Ticket atualizado com sucesso!']);
        } else {
            return $this->failServerError('Não foi possível atualizar o ticket.');
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

        // Apenas o criador pode apagar se o estado for 'novo'
        $userId = session()->get('id');
        if ($ticket['user_id'] != $userId || $ticket['estado'] != 'novo') {
            return $this->failUnauthorized('Não tem permissão para apagar este ticket.');
        }

        if ($this->ticketsModel->delete($id)) {
            $this->sendTicketDeletionEmail($ticket); // Enviar email de notificação de eliminação
            return $this->respondDeleted(['message' => 'Ticket apagado com sucesso!']);
        } else {
            return $this->failServerError('Não foi possível apagar o ticket.');
        }
    }

    // --- Métodos AJAX para DataTables --- //

    public function getMyTicketsDataTable()
    {
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized('Acesso não autorizado.');
        }

        $userId = session()->get('id');
        if (!$userId) {
            return $this->failUnauthorized('Utilizador não autenticado.');
        }

        $tickets = $this->ticketsModel->getMyTickets($userId);

        $data = [];
        foreach ($tickets as $ticket) {
            $row = [];
            $row[] = $ticket['equipamento_marca'] . ' ' . $ticket['equipamento_modelo'];
            $row[] = $ticket['codigo_sala'];
            $row[] = $ticket['tipo_avaria_descricao'];
            $row[] = $ticket['descricao'];
            $row[] = $ticket['estado'];
            $row[] = $ticket['prioridade'];
            $row[] = $ticket['created_at'];
            $row[] = $ticket['updated_at'];
            
            $options = '';
            if ($ticket['estado'] == 'novo') {
                $options .= '<button class="btn btn-sm btn-warning edit-ticket" data-id="' . $ticket['id'] . '">Editar</button> ';
                $options .= '<button class="btn btn-sm btn-danger delete-ticket" data-id="' . $ticket['id'] . '">Apagar</button>';
            }
            $row[] = $options;
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
            $row[] = $ticket['estado'];
            $row[] = $ticket['prioridade'];
            $row[] = $ticket['created_at'];
            $row[] = $ticket['user_nome'];
            $row[] = $ticket['atribuido_user_nome'] ?? 'Não Atribuído';

            $options = '<button class="btn btn-sm btn-info view-ticket" data-id="' . $ticket['id'] . '">Ver</button> ';
            if (session()->get('level') >= 5) { // Apenas técnicos podem atribuir/alterar estado
                $options .= '<button class="btn btn-sm btn-primary assign-ticket" data-id="' . $ticket['id'] . '">Atribuir/Estado</button>';
            }
            $row[] = $options;
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

        $tickets = $this->ticketsModel->getAllTicketsOrdered();

        $data = [];
        foreach ($tickets as $ticket) {
            $row = [];
            $row[] = $ticket['id'];
            $row[] = $ticket['equipamento_marca'] . ' ' . $ticket['equipamento_modelo'];
            $row[] = $ticket['codigo_sala'];
            $row[] = $ticket['tipo_avaria_descricao'];
            $row[] = $ticket['descricao'];
            $row[] = $ticket['estado'];
            $row[] = $ticket['prioridade'];
            $row[] = $ticket['created_at'];
            $row[] = $ticket['user_nome'];
            $row[] = $ticket['atribuido_user_nome'] ?? 'Não Atribuído';
            $row[] = $ticket['ticket_aceite'] ? 'Sim' : 'Não';
            
            $options = '<button class="btn btn-sm btn-info view-ticket" data-id="' . $ticket['id'] . '">Ver</button> ';
            if (session()->get('level') >= 9) { // Apenas admins podem fazer tudo
                $options .= '<button class="btn btn-sm btn-warning edit-ticket" data-id="' . $ticket['id'] . '">Editar</button> ';
                $options .= '<button class="btn btn-sm btn-danger delete-ticket" data-id="' . $ticket['id'] . '">Apagar</button>';
            }
            $row[] = $options;
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

        $rules = [
            'ticket_id'         => 'required|integer',
            'atribuido_user_id' => 'required|integer',
            'estado'            => 'required|in_list[novo,em_resolucao,aguarda_peca,reparado,anulado]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $ticketId = $this->request->get('ticket_id');
        $atribuidoUserId = $this->request->get('atribuido_user_id');
        $estado = $this->request->get('estado');

        $data = [
            'atribuido_user_id' => $atribuidoUserId,
            'estado'            => $estado
        ];

        if ($this->ticketsModel->update($ticketId, $data)) {
            $ticketDetails = $this->ticketsModel->getTicketDetails($ticketId);
            $this->sendTicketAssignmentEmail($ticketDetails); // Enviar email de atribuição
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
        if ($ticket['atribuido_user_id'] != session()->get('id')) {
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
                $this->sendTicketAcceptedEmail($ticketDetails); // Notificar criador e atribuído
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
        // Carregar configurações de e-mail do .env.email_config
        // Em um ambiente real, estas configurações seriam carregadas de forma mais robusta
        // ou diretamente do config/Email.php
        $config = [
            'protocol' => getenv('email.protocol'),
            'SMTPHost' => getenv('email.SMTPHost'),
            'SMTPPort' => getenv('email.SMTPPort'),
            'SMTPUser' => getenv('email.SMTPUser'),
            'SMTPPass' => getenv('email.SMTPPass'),
            'SMTPCrypto' => getenv('email.SMTPCrypto'),
            'mailType' => 'html',
            'charset'  => 'utf-8',
            'CRLF'     => '\r\n',
            'newline'  => '\r\n'
        ];

        $this->email->initialize($config);

        $this->email->setFrom(getenv('email.fromEmail'), getenv('email.fromName'));
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
}

