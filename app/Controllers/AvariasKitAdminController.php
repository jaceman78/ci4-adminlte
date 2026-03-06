<?php
namespace App\Controllers;

use App\Models\RegistoAvariasKitModel;
use CodeIgniter\Email\Email;

class AvariasKitAdminController extends BaseController
{
    protected $avariasModel;

    public function __construct()
    {
        $this->avariasModel = new RegistoAvariasKitModel();
        helper('logs');
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
     * Listagem de avarias reportadas
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
                'avarias_kit',
                'view_index',
                null,
                "Acedeu à listagem de avarias reportadas"
            );
        }

        // Stats para dashboard
        $stats = $this->avariasModel->getStatsByEstado();

        return view('avarias_kit_admin/index', ['stats' => $stats]);
    }

    /**
     * Exportar para CSV
     */
    public function export()
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        $estado = $this->request->getGet('estado');
        if ($estado === 'por_levantar') {
            $estado = 'por levantar';
        }
        if ($estado === 'a_analisar') {
            $estado = 'a analisar';
        }
        
        $model = new RegistoAvariasKitModel();
        if ($estado) {
            $model->where('estado', $estado);
        }
        $rows = $model->orderBy('created_at', 'DESC')->findAll();

        $filename = 'avarias_kit_' . ($estado ?: 'todos') . '_' . date('Ymd_His') . '.csv';

        $output = fopen('php://temp', 'r+');
        fwrite($output, "\xEF\xBB\xBF");
        
        fputcsv($output, ['ID','Nº Aluno','Nome','Turma','NIF','Email Aluno','Email EE','Avaria','Estado','Observações','Criado Em','Finalizado Em']);
        
        foreach ($rows as $r) {
            fputcsv($output, [
                $r['id'] ?? '',
                $r['numero_aluno'] ?? '',
                $r['nome'] ?? '',
                $r['turma'] ?? '',
                $r['nif'] ?? '',
                $r['email_aluno'] ?? '',
                $r['email_ee'] ?? '',
                $r['avaria'] ?? '',
                $r['estado'] ?? '',
                $r['obs'] ?? '',
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
     * JSON com contagens por estado
     */
    public function getStats()
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON(['error' => 'Acesso negado'])->setStatusCode(403);
        }

        $stats = $this->avariasModel->getStatsByEstado();

        return $this->response->setJSON($stats);
    }

    /**
     * DataTable via AJAX
     */
    public function getDataTable()
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON(['error' => 'Acesso negado'])->setStatusCode(403);
        }

        $request = $this->request->getPost();
        
        $draw = (int)($request['draw'] ?? 1);
        $start = (int)($request['start'] ?? 0);
        $length = (int)($request['length'] ?? 10);
        $searchValue = $request['search']['value'] ?? '';
        
        $orderColumnIndex = (int)($request['order'][0]['column'] ?? 0);
        $orderDir = $request['order'][0]['dir'] ?? 'desc';
        
        $filterEstado = $request['estado'] ?? null;
        
        // Normalizar estados
        if ($filterEstado === 'por_levantar') {
            $filterEstado = 'por levantar';
        }
        if ($filterEstado === 'a_analisar') {
            $filterEstado = 'a analisar';
        }
        
        $result = $this->avariasModel->getDataTable($start, $length, $searchValue, $orderColumnIndex, $orderDir, $filterEstado);
        
        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => $result['data']
        ]);
    }

    /**
     * Ver detalhes de uma avaria
     */
    public function view($id)
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON(['error' => 'Acesso negado'])->setStatusCode(403);
        }

        $record = $this->avariasModel->find($id);
        if (!$record) {
            return $this->response->setJSON(['error' => 'Registo não encontrado'])->setStatusCode(404);
        }
        
        return $this->response->setJSON($record);
    }

    /**
     * Atualizar estado da avaria
     */
    public function updateStatus($id)
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON(['error' => 'Acesso negado'])->setStatusCode(403);
        }

        $novoEstado = $this->request->getPost('estado');
        $obs = $this->request->getPost('obs');

        $estadosValidos = ['novo', 'lido', 'a analisar', 'por levantar', 'rejeitado', 'anulado', 'terminado'];
        if (!in_array($novoEstado, $estadosValidos)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Estado inválido'
            ])->setStatusCode(400);
        }

        $record = $this->avariasModel->find($id);
        if (!$record) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Registo não encontrado'
            ])->setStatusCode(404);
        }

        $dataUpdate = ['estado' => $novoEstado];
        
        if ($obs) {
            $dataUpdate['obs'] = $obs;
        }
        
        if ($novoEstado === 'terminado') {
            $dataUpdate['finished_at'] = date('Y-m-d H:i:s');
        }

        $this->avariasModel->update($id, $dataUpdate);

        // Log da ação
        $userData = session()->get('LoggedUserData');
        $userId = $userData['ID'] ?? $userData['id'] ?? null;
        if ($userId) {
            log_activity(
                'avarias_kit',
                'update_status',
                $id,
                "Atualizou estado da avaria #{$id} para '{$novoEstado}'",
                ['estado' => $record['estado']],
                ['estado' => $novoEstado]
            );
        }

        // Enviar email conforme o novo estado
        $this->sendStatusChangeEmail($record, $novoEstado, $obs);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Estado atualizado com sucesso'
        ]);
    }

    /**
     * Eliminar uma avaria
     */
    public function delete($id)
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON(['error' => 'Acesso negado'])->setStatusCode(403);
        }

        $record = $this->avariasModel->find($id);
        if (!$record) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Registo não encontrado'
            ])->setStatusCode(404);
        }

        $this->avariasModel->delete($id);

        // Log da ação
        $userData = session()->get('LoggedUserData');
        $userId = $userData['ID'] ?? $userData['id'] ?? null;
        if ($userId) {
            log_activity(
                'avarias_kit',
                'delete',
                $id,
                "Eliminou avaria #{$id} - {$record['nome']}",
                $record
            );
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Avaria eliminada com sucesso'
        ]);
    }

    /**
     * Enviar email de mudança de estado
     */
    private function sendStatusChangeEmail(array $record, string $novoEstado, ?string $obs): void
    {
        try {
            $email = \Config\Services::email();
            
            // Caminho absoluto para o logo
            $logoPath = FCPATH . 'adminlte/img/logo.png';
            
            $estadoTexto = match($novoEstado) {
                'novo' => 'Novo',
                'lido' => 'Lido',
                'a analisar' => 'A Analisar',
                'por levantar' => 'Por Levantar - Kit pronto para levantamento',
                'rejeitado' => 'Rejeitado',
                'anulado' => 'Anulado',
                'terminado' => 'Terminado',
                default => ucfirst($novoEstado)
            };

            $corEstado = match($novoEstado) {
                'novo' => '#17a2b8',
                'lido' => '#ffc107',
                'a analisar' => '#0d6efd',
                'por levantar' => '#0dcaf0',
                'rejeitado' => '#dc3545',
                'anulado' => '#6c757d',
                'terminado' => '#198754',
                default => '#6c757d'
            };

            $message = "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <p>Exmo Encarregado de Educação,</p>
                <p>O estado do reporte de avaria do Kit Digital do aluno <strong>{$record['nome']}</strong> (Nº {$record['numero_aluno']}) foi atualizado.</p>
                
                <div style='background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='margin-top: 0; color: #495057;'>Detalhes do Reporte:</h3>
                    <ul style='list-style: none; padding: 0;'>
                        <li style='padding: 5px 0;'><strong>Número de Aluno:</strong> {$record['numero_aluno']}</li>
                        <li style='padding: 5px 0;'><strong>Turma:</strong> {$record['turma']}</li>
                        <li style='padding: 5px 0;'><strong>Descrição da Avaria:</strong> " . substr($record['avaria'], 0, 150) . (strlen($record['avaria']) > 150 ? '...' : '') . "</li>
                    </ul>
                    <div style='margin-top: 15px; padding: 15px; background-color: white; border-left: 4px solid {$corEstado}; border-radius: 4px;'>
                        <strong style='color: {$corEstado}; font-size: 16px;'>Novo Estado: {$estadoTexto}</strong>
                    </div>
                </div>
            ";
            
            if ($obs) {
                $message .= "
                <div style='background-color: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;'>
                    <h4 style='margin-top: 0; color: #856404;'><i class='bi bi-info-circle'></i> Observações:</h4>
                    <p style='margin-bottom: 0; color: #856404;'>{$obs}</p>
                </div>
                ";
            }
            
            if ($novoEstado === 'lido') {
                $message .= "
                <div style='background-color: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;'>
                    <h4 style='margin-top: 0; color: #856404;'><i class='bi bi-calendar-check'></i> Próximos Passos:</h4>
                    <p style='color: #856404; margin-bottom: 0;'>
                        Para avaliação da avaria, por favor dirija-se às instalações da <strong>Escola Secundária João de Barros - Sala D012</strong> durante o seguinte horário:<br>
                        <strong>Segunda e terças, das 10h00 às 12h00 e das 14h00 às 17h00</strong>
                    </p>
                </div>
                ";
            }
            
            if ($novoEstado === 'por levantar') {
                $message .= "
                <div style='background-color: #d1ecf1; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #0dcaf0;'>
                    <h4 style='margin-top: 0; color: #0c5460;'><i class='bi bi-building'></i> Próximos Passos:</h4>
                    <p style='color: #0c5460; margin-bottom: 0;'>
                        O seu equipamento está pronto para levantamento ou reparado e disponível para recolha.<br>
                        Por favor, dirija-se à <strong>Sala D012 da Escola Secundária João de Barros</strong> durante o seguinte horário:<br>
                        <strong>Segunda e terças, das 10h00 às 12h00 e das 14h00 às 17h00</strong>
                    </p>
                </div>
                ";
            }
            
            if ($novoEstado === 'terminado') {
                $message .= "
                <div style='background-color: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745;'>
                    <h4 style='margin-top: 0; color: #155724;'><i class='bi bi-check-circle'></i> Processo Concluído</h4>
                    <p style='color: #155724; margin-bottom: 0;'>
                        O processo de reporte de avaria foi concluído com sucesso.<br>
                        Obrigado pela sua colaboração.
                    </p>
                </div>
                ";
            }

            if ($novoEstado === 'rejeitado') {
                $message .= "
                <div style='background-color: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #dc3545;'>
                    <h4 style='margin-top: 0; color: #721c24;'><i class='bi bi-x-circle'></i> Pedido Rejeitado</h4>
                    <p style='color: #721c24; margin-bottom: 0;'>
                        Lamentamos informar que o seu reporte foi rejeitado.<br>
                        Para mais esclarecimentos, por favor contacte os serviços administrativos.
                    </p>
                </div>
                ";
            }
            
            $message .= "
                <p style='margin-top: 30px;'>Para qualquer esclarecimento adicional, não hesite em contactar-nos.</p>
                <p>Com os melhores cumprimentos,</p>
                <p><strong>Escola Digital<br>Agrupamento de Escolas João de Barros</strong></p>
                <p><img src='cid:logo_escola' alt='Logo Escola' style='max-width:200px; height:auto;'></p>
            </body>
            </html>
            ";

            $email->setFrom('noreply@aejoaodebarros.pt', 'AE João de Barros - Kit Digital');
            $email->setTo($record['email_ee']);
            $email->setCC($record['email_aluno']);
            $email->setSubject('Kit Digital - Atualização de Estado: ' . $estadoTexto);
            $email->setMessage($message);
            
            // Anexar logo como imagem inline
            if (file_exists($logoPath)) {
                $cid = $email->setAttachmentCID($logoPath);
                $email->attach($logoPath, 'inline', null, '', 'logo_escola');
            }

            if (!$email->send()) {
                log_message('error', 'AvariasKitAdminController - Erro ao enviar email: ' . $email->printDebugger(['headers']));
            } else {
                log_message('info', 'AvariasKitAdminController - Email de atualização enviado para: ' . $record['email_ee']);
            }
        } catch (\Exception $e) {
            log_message('error', 'AvariasKitAdminController - Exceção ao enviar email: ' . $e->getMessage());
        }
    }
}
