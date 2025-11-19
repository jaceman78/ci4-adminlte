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

        // Stats para dashboard (usar agrupamento para evitar acumulação de condições)
        $stats = [
            'total' => $this->kitModel->countAll(),
            'pendente' => 0,
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
        if (!empty($filterEstado) && in_array($filterEstado, ['pendente','por levantar','rejeitado','anulado','terminado'])) {
            $builder->where('estado', $filterEstado);
        }
        
        // Contar registos filtrados (ANTES de aplicar limit/offset)
        $filteredRecords = $builder->countAllResults(false);
        
        // Obter dados com paginação
        $data = $builder->orderBy('created_at', 'DESC')
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
     * Email de término (entrega efetuada)
     */
    private function sendFinishedEmail($record)
    {
        $email = \Config\Services::email();

        $logoPath = FCPATH . 'adminlte/img/logo.png';
        $message = "
        <p>Exmo Encarregado de Educação</p>
        <p>Confirmamos que o Kit Digital referente a <strong>{$record['nome']}</strong> foi entregue e o processo foi concluído.</p>
        <p>Recordamos que o equipamento se encontra em regime de comodato e deve ser mantido em bom estado de conservação.</p>
        <p>Com os melhores cumprimentos</p>
        <p><strong>António Neto</strong></p>
        <p><img src='cid:logo_escola' alt='Logo Escola' style='max-width:200px;'></p>
        ";

        $email->setFrom('noreply@aejoaodebarros.pt', 'AE João de Barros - Kit Digital');
        $email->setTo($record['email_ee']);
        if (!empty($record['email_aluno'])) {
            $email->setCC($record['email_aluno']);
        }
        $email->setSubject('Kit Digital - Entrega Efetuada');
        $email->setMessage($message);
        if (file_exists($logoPath)) {
            $email->attach($logoPath, 'inline', 'logo.png', 'image/png', 'logo_escola');
        }

        if (!$email->send()) {
            log_message('error', 'Erro ao enviar email de término: ' . $email->printDebugger());
        }
    }

    /**
     * Email de aprovação
     */
    private function sendApprovalEmail($record)
    {
        $email = \Config\Services::email();
        
        // Caminho absoluto para o logo
        $logoPath = FCPATH . 'adminlte/img/logo.png';
        
        $message = "
        <p>Exmo Encarregado de Educação</p>
        <p>O kit digital destinado ao seu educando <strong>{$record['nome']}</strong> já se encontra disponível. Deverá dirigir-se às instalações da Escola Secundária João de Barros para levantamento do Kit digital.</p>
        <p>O horário de atendimento da Escola Digital durante o período letivo é às segundas e terças feiras entre as 14:00 e as 17:00 na sala D012.</p>
        <p>Com os melhores cumprimentos</p>
        <p><strong>António Neto</strong></p>
        <p><img src='cid:logo_escola' alt='Logo Escola' style='max-width:200px;'></p>
        ";

        $email->setFrom('noreply@aejoaodebarros.pt', 'AE João de Barros - Kit Digital');
        $email->setTo($record['email_ee']);
        $email->setCC($record['email_aluno']);
        $email->setSubject('Kit Digital Disponível para Levantamento');
        $email->setMessage($message);
        
        // Anexar logo como imagem inline
        if (file_exists($logoPath)) {
            $email->attach($logoPath, 'inline', 'logo.png', 'image/png', 'logo_escola');
        }

        if (!$email->send()) {
            log_message('error', 'Erro ao enviar email de aprovação: ' . $email->printDebugger());
        }
    }

    /**
     * Email de rejeição
     */
    private function sendRejectionEmail($record, $motivo)
    {
        $email = \Config\Services::email();

        $message = "
        <p>Exmo Encarregado de Educação</p>
        <p>Foi rejeitada a atribuição do Kit Digital pelo seguinte motivo:</p>
        <p><strong>{$motivo}</strong></p>
        <p>Com os melhores cumprimentos</p>
        <p><strong>António Neto</strong></p>
        ";

        $email->setFrom('noreply@aejoaodebarros.pt', 'AE João de Barros - Kit Digital');
        $email->setTo($record['email_ee']);
        $email->setCC($record['email_aluno']);
        $email->setSubject('Kit Digital - Pedido Rejeitado');
        $email->setMessage($message);

        if (!$email->send()) {
            log_message('error', 'Erro ao enviar email de rejeição: ' . $email->printDebugger());
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

        return view('kit_digital_admin/estatisticas', [
            'porAno' => $porAno,
            'porEstado' => $porEstado
        ]);
    }
}
