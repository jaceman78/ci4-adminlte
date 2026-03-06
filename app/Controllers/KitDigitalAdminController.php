<?php
namespace App\Controllers;

use App\Models\RequesicaoKitModel;
use CodeIgniter\Email\Email;

class KitDigitalAdminController extends BaseController
{
    protected $kitModel;

    public function __construct()
    {
        $this->kitModel = new RequesicaoKitModel();
        helper('logs'); // Carregar helper de logs
    }

    /**
     * Verificar se o utilizador tem nível 5 ou superior
     */
    private function checkAccess()
    {
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel < 5) {
            return redirect()->to('/')->with('error', 'Acesso negado. Nível insuficiente.');
        }
        return null;
    }

    /**
     * Listagem de todos os pedidos (DataTable via AJAX)
     */
    public function index()
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        // Log de acesso à página
        $userData = session()->get('LoggedUserData');
        $userId = $userData['ID'] ?? $userData['id'] ?? null;
        if ($userId) {
            log_activity(
                'kit_digital',
                'view_index',
                null,
                "Acedeu à listagem de pedidos de Kit Digital"
            );
        }

        // Stats para dashboard (usar agrupamento para evitar acumulação de condições)
        $stats = [
            'total' => $this->kitModel->countAll(),
            'pendente' => 0,
            'dados_invalidos' => 0,
            'por_levantar' => 0,
            'rejeitado' => 0,
            'anulado' => 0,
            'terminado' => 0,
        ];
        $rows = $this->kitModel->builder()
            ->select("estado, COUNT(*) AS total")
            ->groupBy('estado')
            ->get()
            ->getResultArray();
        foreach ($rows as $r) {
            $estado = $r['estado'] ?? '';
            // Normalizar "por levantar" para "por_levantar" para corresponder à chave do array
            if ($estado === 'por levantar') {
                $estado = 'por_levantar';
            }
            if (isset($stats[$estado])) {
                $stats[$estado] = (int) $r['total'];
            }
        }

        return view('kit_digital_admin/index', ['stats' => $stats]);
    }

    public function export()
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        $estado = $this->request->getGet('estado');
        if ($estado === 'por_levantar') { // normalizar caso venha com underscore
            $estado = 'por levantar';
        }
        $model = new RequesicaoKitModel();
        if ($estado) {
            $model->where('estado', $estado);
        }
        $rows = $model->orderBy('created_at', 'DESC')->findAll();

        $filename = 'kit_digital_' . ($estado ?: 'todos') . '_' . date('Ymd_His') . '.csv';

    $output = fopen('php://temp', 'r+');
    // Definir BOM UTF-8 para Excel reconhecer acentos
    fwrite($output, "\xEF\xBB\xBF");
        // Cabeçalho CSV
        fputcsv($output, ['ID','Nº Aluno','Nome','Turma','NIF','ASE','Email Aluno','Email EE','Estado','Criado Em','Finalizado Em']);
        foreach ($rows as $r) {
            fputcsv($output, [
                $r['id'] ?? '',
                $r['numero_aluno'] ?? '',
                $r['nome'] ?? '',
                $r['turma'] ?? '',
                $r['nif'] ?? '',
                $r['ase'] ?? '',
                $r['email_aluno'] ?? '',
                $r['email_ee'] ?? '',
                $r['estado'] ?? '',
                $r['created_at'] ?? '',
                $r['finished_at'] ?? ''
            ]);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $this->response
            ->setStatusCode(200)
            ->setHeader('Content-Type','text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition','attachment; filename="'.$filename.'"')
            ->setBody($csv);
    }

    /**
     * JSON com contagens por estado para atualizar badges dos filtros
     */
    public function getStats()
    {
        if ($redirect = $this->checkAccess()) {
            // Para requests AJAX, devolver 403 JSON
            return $this->response->setJSON(['error' => 'Acesso negado'])->setStatusCode(403);
        }

        $stats = [
            'total' => $this->kitModel->countAll(),
            'pendente' => 0,
            'dados_invalidos' => 0,
            'por_levantar' => 0,
            'rejeitado' => 0,
            'anulado' => 0,
            'terminado' => 0,
        ];

        $rows = $this->kitModel->builder()
            ->select('estado, COUNT(*) AS total')
            ->groupBy('estado')
            ->get()
            ->getResultArray();
        foreach ($rows as $r) {
            $estado = $r['estado'] ?? '';
            // Normalizar "por levantar" para "por_levantar" para corresponder à chave do array
            if ($estado === 'por levantar') {
                $estado = 'por_levantar';
            }
            if (isset($stats[$estado])) {
                $stats[$estado] = (int) $r['total'];
            }
        }

        return $this->response->setJSON($stats);
    }

    /**
     * DataTable source (AJAX)
     */
    public function getData()
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON(['error' => 'Acesso negado'])->setStatusCode(403);
        }

        $request = $this->request;
        
        $draw = $request->getPost('draw');
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $searchValue = $request->getPost('search')['value'] ?? '';
        $filterEstado = $request->getPost('filter_estado');
        
        // Processar ordenação
        $orderColumn = $request->getPost('order')[0]['column'] ?? 7; // default: created_at
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'desc';
        
        // Mapeamento de colunas para ordenação
        $columns = [
            0 => 'id',
            1 => 'numero_aluno',
            2 => 'nome',
            3 => 'turma',
            4 => 'nif',
            5 => 'ase',
            6 => 'estado',
            7 => 'created_at',
            8 => null // ações - não ordenável
        ];
        
        $orderByColumn = $columns[$orderColumn] ?? 'created_at';
        
        // Normalizar 'por_levantar' para valor da BD
        if ($filterEstado === 'por_levantar') {
            $filterEstado = 'por levantar';
        }
        
        // Total de registos (sem filtros)
        $totalRecords = $this->kitModel->countAll();
        
        // Builder para query filtrada
        $builder = $this->kitModel->builder();
        
        // Filtro de pesquisa
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('nome', $searchValue)
                ->orLike('numero_aluno', $searchValue)
                ->orLike('nif', $searchValue)
                ->orLike('turma', $searchValue)
                ->groupEnd();
        }

        // Filtro por estado
        if (!empty($filterEstado) && in_array($filterEstado, ['pendente','dados_invalidos','por levantar','rejeitado','anulado','terminado'])) {
            $builder->where('estado', $filterEstado);
        }
        
        // Contar registos filtrados (ANTES de aplicar limit/offset)
        $filteredRecords = $builder->countAllResults(false);
        
        // Obter dados com paginação e ordenação
        $data = $builder->orderBy($orderByColumn, $orderDir)
            ->limit($length, $start)
            ->get()
            ->getResultArray();
        
        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    /**
     * Ver detalhes (retorna JSON para modal)
     */
    public function view($id)
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON(['error' => 'Acesso negado'])->setStatusCode(403);
        }

        $record = $this->kitModel->find($id);
        if (!$record) {
            return $this->response->setJSON(['error' => 'Registo não encontrado'])->setStatusCode(404);
        }
        return $this->response->setJSON($record);
    }

    /**
     * Aprovar pedido → estado 'por levantar' + email
     */
    public function approve($id)
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        $record = $this->kitModel->find($id);
        if (!$record) {
            return redirect()->back()->with('error', 'Registo não encontrado.');
        }

        // Atualizar estado
        $this->kitModel->update($id, ['estado' => 'por levantar']);

        // Enviar email
        $this->sendApprovalEmail($record);

        return redirect()->to('kit-digital-admin')->with('success', 'Kit aprovado e email enviado.');
    }

    /**
     * Rejeitar pedido → estado 'rejeitado' + email com motivo
     */
    public function reject($id)
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        $motivo = $this->request->getPost('motivo');
        if (empty($motivo)) {
            return redirect()->back()->with('error', 'É necessário indicar o motivo da rejeição.');
        }

        $record = $this->kitModel->find($id);
        if (!$record) {
            return redirect()->back()->with('error', 'Registo não encontrado.');
        }

        // Atualizar estado e obs
        $this->kitModel->update($id, [
            'estado' => 'rejeitado',
            'obs' => $motivo
        ]);

        // Enviar email
        $this->sendRejectionEmail($record, $motivo);

        return redirect()->to('kit-digital-admin')->with('success', 'Pedido rejeitado e email enviado.');
    }

    /**
     * Anular pedido → estado 'anulado'
     */
    public function cancel($id)
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        $record = $this->kitModel->find($id);
        if (!$record) {
            return redirect()->back()->with('error', 'Registo não encontrado.');
        }

        $this->kitModel->update($id, ['estado' => 'anulado']);

        return redirect()->to('kit-digital-admin')->with('success', 'Pedido anulado.');
    }

    /**
     * Terminar pedido → estado 'terminado' (apenas se atualmente 'por levantar')
     */
    public function finish($id)
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        $record = $this->kitModel->find($id);
        if (!$record) {
            return redirect()->back()->with('error', 'Registo não encontrado.');
        }

        if ($record['estado'] !== 'por levantar') {
            return redirect()->back()->with('error', 'Só é possível terminar pedidos que estejam em "por levantar".');
        }

        $this->kitModel->update($id, [
            'estado' => 'terminado',
            'finished_at' => date('Y-m-d H:i:s')
        ]);

        // Enviar email de conclusão (opcional, CC aluno)
        $this->sendFinishedEmail($record);

        return redirect()->to('kit-digital-admin')->with('success', 'Processo terminado com sucesso. Email enviado.');
    }

    /**
     * Reenviar aviso de levantamento (apenas para estado 'por levantar')
     */
    public function resendPickupReminder($id)
    {
        // Verificar acesso
        $accessCheck = $this->checkAccess();
        if ($accessCheck !== null) return $accessCheck;

        $record = $this->kitModel->find($id);
        if (!$record) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pedido não encontrado'
            ]);
        }

        // Verificar se está no estado correto
        if ($record['estado'] !== 'por levantar') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Apenas pedidos no estado "Por Levantar" podem receber este aviso'
            ]);
        }

        // Enviar email de aviso
        try {
            $this->sendPickupReminderEmail($record);
            
            // Log da ação
            $userData = session()->get('LoggedUserData');
            $userId = $userData['ID'] ?? $userData['id'] ?? null;
            
            if ($userId) {
                log_activity(
                    'kit_digital',
                    'resend_reminder',
                    $id,
                    "Reenviou aviso de levantamento para o pedido #{$id} - {$record['nome']}",
                    null,
                    ['email_ee' => $record['email_ee'], 'email_aluno' => $record['email_aluno']]
                );
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Aviso de levantamento reenviado com sucesso'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao reenviar aviso de levantamento ID ' . $id . ': ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao enviar email: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Email de aviso de prazo de levantamento
     */
    private function sendPickupReminderEmail($record)
    {
        $email = \Config\Services::email();
        
        // Caminho absoluto para o logo (FCPATH já é public/)
        $logoPath = FCPATH . 'adminlte/img/logo.png';
        
        $message = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <p>Exmo Encarregado de Educação,</p>
            <p>Recordamos que o Kit Digital destinado ao seu educando <strong>{$record['nome']}</strong> encontra-se disponível para levantamento.</p>
            <p style='color: #d9534f;'><strong>⚠️ AVISO IMPORTANTE:</strong> O prazo para levantamento está a terminar. Se não proceder ao levantamento em tempo útil, perderá o direito ao Kit Digital.</p>
            <p>Deverá dirigir-se às instalações da Escola Secundária João de Barros para proceder ao levantamento.</p>
            <p><strong>Horário de atendimento da Escola Digital durante o período letivo:</strong><br>
            Segundas e Terças-feiras entre as 14:00 e as 17:00<br>
            Local: Sala D012</p>
            <p>Agradecemos a vossa colaboração.</p>
            <p>Com os melhores cumprimentos,</p>
            <p><strong>António Neto<br>Escola Digital<br>Agrupamento de Escolas João de Barros</strong></p>
            <p><img src='cid:logo_escola' alt='Logo Escola' style='max-width:200px; height:auto;'></p>
        </body>
        </html>
        ";

        $email->setFrom('noreply@aejoaodebarros.pt', 'AE João de Barros - Kit Digital');
        $email->setTo($record['email_ee']);
        if (!empty($record['email_aluno'])) {
            $email->setCC($record['email_aluno']);
        }
        $email->setSubject('AVISO: Prazo de Levantamento do Kit Digital');
        $email->setMessage($message);
        
        // Anexar logo como imagem inline
        if (file_exists($logoPath)) {
            $cid = $email->setAttachmentCID($logoPath);
            $email->attach($logoPath, 'inline', null, '', 'logo_escola');
        }

        if (!$email->send()) {
            log_message('error', 'Erro ao enviar email de aviso de levantamento: ' . $email->printDebugger(['headers']));
            throw new \Exception('Falha ao enviar email');
        }
    }

    /**
     * Email de término (entrega efetuada)
     */
    private function sendFinishedEmail($record)
    {
        $email = \Config\Services::email();

        // Caminho absoluto para o logo (FCPATH já é public/)
        $logoPath = FCPATH . 'adminlte/img/logo.png';
        
        $message = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <p>Exmo Encarregado de Educação,</p>
            <p>Confirmamos que o Kit Digital referente a <strong>{$record['nome']}</strong> foi entregue e o processo foi concluído.</p>
            <p>Recordamos que o equipamento se encontra em regime de comodato e deve ser mantido em bom estado de conservação.</p>
            <p>Com os melhores cumprimentos,</p>
            <p><strong>António Neto<br>Escola Digital<br>Agrupamento de Escolas João de Barros</strong></p>
            <p><img src='cid:logo_escola' alt='Logo Escola' style='max-width:200px; height:auto;'></p>
        </body>
        </html>
        ";

        $email->setFrom('noreply@aejoaodebarros.pt', 'AE João de Barros - Kit Digital');
        $email->setTo($record['email_ee']);
        if (!empty($record['email_aluno'])) {
            $email->setCC($record['email_aluno']);
        }
        $email->setSubject('Kit Digital - Entrega Efetuada');
        $email->setMessage($message);
        
        // Anexar logo como imagem inline
        if (file_exists($logoPath)) {
            $cid = $email->setAttachmentCID($logoPath);
            $email->attach($logoPath, 'inline', null, '', 'logo_escola');
        }

        if (!$email->send()) {
            log_message('error', 'Erro ao enviar email de término: ' . $email->printDebugger(['headers']));
        }
    }

    /**
     * Email de aprovação
     */
    private function sendApprovalEmail($record)
    {
        $email = \Config\Services::email();
        
        // Caminho absoluto para o logo (FCPATH já é public/)
        $logoPath = FCPATH . 'adminlte/img/logo.png';
        
        $message = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <p>Exmo Encarregado de Educação,</p>
            <p>O kit digital destinado ao seu educando <strong>{$record['nome']}</strong> já se encontra disponível. Deverá dirigir-se às instalações da Escola Secundária João de Barros para levantamento do Kit digital.</p>
            <p>O horário de atendimento da Escola Digital durante o período letivo é às segundas e terças feiras entre as 14:00 e as 17:00 na sala D012.</p>
            <p>Com os melhores cumprimentos,</p>
            <p><strong>António Neto<br>Escola Digital<br>Agrupamento de Escolas João de Barros</strong></p>
            <p><img src='cid:logo_escola' alt='Logo Escola' style='max-width:200px; height:auto;'></p>
        </body>
        </html>
        ";

        $email->setFrom('noreply@aejoaodebarros.pt', 'AE João de Barros - Kit Digital');
        $email->setTo($record['email_ee']);
        $email->setCC($record['email_aluno']);
        $email->setSubject('Kit Digital Disponível para Levantamento');
        $email->setMessage($message);
        
        // Anexar logo como imagem inline
        if (file_exists($logoPath)) {
            $cid = $email->setAttachmentCID($logoPath);
            $email->attach($logoPath, 'inline', null, '', 'logo_escola');
        }

        if (!$email->send()) {
            log_message('error', 'Erro ao enviar email de aprovação: ' . $email->printDebugger(['headers']));
        }
    }

    /**
     * Email de rejeição
     */
    private function sendRejectionEmail($record, $motivo)
    {
        $email = \Config\Services::email();
        
        // Caminho absoluto para o logo (FCPATH já é public/)
        $logoPath = FCPATH . 'adminlte/img/logo.png';

        $message = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <p>Exmo Encarregado de Educação,</p>
            <p>Foi rejeitada a atribuição do Kit Digital referente ao aluno <strong>{$record['nome']}</strong> pelo seguinte motivo:</p>
            <p><strong>{$motivo}</strong></p>
            <p>Para mais esclarecimentos, contacte a Escola Digital.</p>
            <p>Com os melhores cumprimentos,</p>
            <p><strong>António Neto<br>Escola Digital<br>Agrupamento de Escolas João de Barros</strong></p>
            <p><img src='cid:logo_escola' alt='Logo Escola' style='max-width:200px; height:auto;'></p>
        </body>
        </html>
        ";

        $email->setFrom('noreply@aejoaodebarros.pt', 'AE João de Barros - Kit Digital');
        $email->setTo($record['email_ee']);
        $email->setCC($record['email_aluno']);
        $email->setSubject('Kit Digital - Pedido Rejeitado');
        $email->setMessage($message);
        
        // Anexar logo como imagem inline
        if (file_exists($logoPath)) {
            $cid = $email->setAttachmentCID($logoPath);
            $email->attach($logoPath, 'inline', null, '', 'logo_escola');
        }

        if (!$email->send()) {
            log_message('error', 'Erro ao enviar email de rejeição: ' . $email->printDebugger(['headers']));
        }
    }

    /**
     * Estatísticas
     */
    public function estatisticas()
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        // Log de acesso à página de estatísticas
        $userData = session()->get('LoggedUserData');
        $userId = $userData['ID'] ?? $userData['id'] ?? null;
        if ($userId) {
            log_activity(
                'kit_digital',
                'view_estatisticas',
                null,
                "Acedeu à página de estatísticas de Kit Digital"
            );
        }

        // Por ano (extrair de turma código, ex: 10D -> ano 10)
        $db = \Config\Database::connect();
        $porAno = $db->query("
            SELECT SUBSTRING(turma, 1, 2) as ano, COUNT(*) as total
            FROM requisicao_kit
            GROUP BY ano
            ORDER BY ano
        ")->getResultArray();

        // Por estado
        $porEstado = $db->query("
            SELECT estado, COUNT(*) as total
            FROM requisicao_kit
            GROUP BY estado
        ")->getResultArray();

        // Por ASE (escalões)
        $porASE = $db->query("
            SELECT ase, COUNT(*) as total
            FROM requisicao_kit
            GROUP BY ase
            ORDER BY FIELD(ase, 'A', 'B', 'C', 'Nao')
        ")->getResultArray();

        // Por Turma
        $porTurma = $db->query("
            SELECT turma, COUNT(*) as total
            FROM requisicao_kit
            GROUP BY turma
            ORDER BY turma
        ")->getResultArray();

        return view('kit_digital_admin/estatisticas', [
            'porAno' => $porAno,
            'porEstado' => $porEstado,
            'porASE' => $porASE,
            'porTurma' => $porTurma
        ]);
    }

    /**
     * Obter lista de turmas para select
     */
    public function getTurmas()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('turma');
        $turmas = $builder->select('codigo')->orderBy('codigo', 'ASC')->get()->getResultArray();
        
        return $this->response->setJSON([
            'success' => true,
            'turmas' => array_column($turmas, 'codigo')
        ]);
    }

    /**
     * Atualizar dados do pedido (apenas nível 8+)
     */
    public function update($id = null)
    {
        // Log de entrada
        log_message('debug', 'KitDigitalAdminController::update - ID: ' . $id);
        log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));
        
        // Verificar acesso
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel < 8) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado. Apenas utilizadores de nível 8 ou superior podem editar.'
            ]);
        }

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID inválido'
            ]);
        }

        // Verificar se o pedido existe
        $record = $this->kitModel->find($id);
        if (!$record) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pedido não encontrado'
            ]);
        }

        // Obter dados do formulário
        $data = [
            'numero_aluno' => $this->request->getPost('numero_aluno'),
            'nome' => $this->request->getPost('nome'),
            'turma' => $this->request->getPost('turma'),
            'nif' => $this->request->getPost('nif'),
            'ase' => $this->request->getPost('ase'),
            'email_aluno' => $this->request->getPost('email_aluno'),
            'email_ee' => $this->request->getPost('email_ee'),
            'estado' => $this->request->getPost('estado'),
            'obs' => $this->request->getPost('obs')
        ];
        
        log_message('debug', 'Data to update: ' . json_encode($data));

        // Validação básica
        if (empty($data['numero_aluno']) || empty($data['nome']) || empty($data['turma']) || 
            empty($data['nif']) || empty($data['ase']) || empty($data['email_aluno']) || empty($data['email_ee']) || empty($data['estado'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Todos os campos obrigatórios devem ser preenchidos'
            ]);
        }

        // Validar NIF (9 dígitos)
        if (!preg_match('/^\d{9}$/', $data['nif'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'NIF inválido (deve ter 9 dígitos)'
            ]);
        }
        
        // Verificar se o NIF já existe (excluindo o registo atual)
        $existingNif = $this->kitModel->where('nif', $data['nif'])
                                       ->where('id !=', $id)
                                       ->first();
        if ($existingNif) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'NIF já existe em outro registo'
            ]);
        }

        // Validar emails
        if (!filter_var($data['email_aluno'], FILTER_VALIDATE_EMAIL) || 
            !filter_var($data['email_ee'], FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email inválido'
            ]);
        }

        // Validar ASE
        if (!in_array($data['ase'], ['Escalão A', 'Escalão B', 'Escalão C', 'Sem Escalão'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Valor ASE inválido'
            ]);
        }

        // Validar estado
        $estadosValidos = ['pendente', 'dados_invalidos', 'por levantar', 'terminado', 'rejeitado', 'anulado'];
        if (!in_array($data['estado'], $estadosValidos)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Estado inválido'
            ]);
        }

        // Atualizar registro
        try {
            // Guardar dados anteriores para o log
            $dadosAnteriores = $record;
            
            log_message('debug', 'Antes do update - Record ID: ' . $id);
            
            // Desabilitar validação do Model para evitar conflito com is_unique
            $this->kitModel->skipValidation(true);
            $updateResult = $this->kitModel->update($id, $data);
            $this->kitModel->skipValidation(false);
            
            log_message('debug', 'Resultado do update: ' . ($updateResult ? 'sucesso' : 'falhou'));
            
            // Log detalhado da alteração
            $userData = session()->get('LoggedUserData');
            $userId = $userData['ID'] ?? $userData['id'] ?? null;
            
            // Construir descrição detalhada das alterações
            $alteracoes = [];
            foreach ($data as $campo => $valorNovo) {
                $valorAntigo = $dadosAnteriores[$campo] ?? '';
                if ($valorAntigo != $valorNovo) {
                    $alteracoes[] = "{$campo}: '{$valorAntigo}' → '{$valorNovo}'";
                }
            }
            $descricaoAlteracoes = implode(', ', $alteracoes);
            
            log_message('debug', 'Alterações realizadas: ' . $descricaoAlteracoes);
            
            if ($userId) {
                log_activity(
                    'kit_digital',
                    'update',
                    $id,
                    "Editou detalhes do pedido #{$id} - Alterações: {$descricaoAlteracoes}",
                    $dadosAnteriores,
                    $data
                );
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Dados atualizados com sucesso'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar Kit Digital ID ' . $id . ': ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao atualizar dados: ' . $e->getMessage()
            ]);
        }
    }
}
