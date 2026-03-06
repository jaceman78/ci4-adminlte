<?php
namespace App\Controllers;

use App\Models\RegistoAvariasKitModel;
use App\Models\AnoLetivoModel;
use CodeIgniter\HTTP\ResponseInterface;

class AvariasKitController extends BaseController
{
    protected $avariasModel;

    public function __construct()
    {
        $this->avariasModel = new RegistoAvariasKitModel();
    }

    /**
     * Página pública do formulário de reporte de avarias
     */
    public function form()
    {
        // Gerar captcha simples (soma)
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        session()->set('captcha_avaria_sum', $a + $b);

        // Buscar turmas (ordenar por código)
        $turmaModel = new \App\Models\TurmaModel();
        $turmas = $turmaModel->orderBy('codigo', 'asc')->findAll();

        return view('public/reportar_avaria_kit', [
            'captcha_a' => $a,
            'captcha_b' => $b,
            'turmas' => $turmas,
            'errors' => session()->getFlashdata('formErrors'),
            'success' => session()->getFlashdata('success'),
        ]);
    }

    /**
     * Submeter reporte de avaria
     */
    public function submit()
    {
        $request = $this->request;

        log_message('debug', 'AvariasKitController::submit - entry - Method: ' . $request->getMethod());

        // Honeypot (anti bot simples)
        $honeypot = $request->getPost('website');
        log_message('debug', 'AvariasKitController::submit - honeypot: ' . var_export($honeypot, true));
        if ($honeypot) {
            log_message('warning', 'AvariasKitController::submit - bot detected via honeypot');
            if ($request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'errors' => ['Detetado padrão de bot.']])->setStatusCode(400);
            }
            return redirect()->back()->with('formErrors', ['Detetado padrão de bot.'])->withInput();
        }

        // Captcha
        $captchaAnswer = $request->getPost('captcha_answer');
        $expected = session()->get('captcha_avaria_sum');
        log_message('debug', 'AvariasKitController::submit - captcha answer: ' . var_export($captchaAnswer, true) . ' expected: ' . var_export($expected, true));
        if ((int)$captchaAnswer !== (int)$expected) {
            log_message('info', 'AvariasKitController::submit - captcha mismatch');
            if ($request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'errors' => ['Resposta ao desafio inválida.']])->setStatusCode(400);
            }
            return redirect()->back()->with('formErrors', ['Resposta ao desafio inválida.'])->withInput();
        }

        // Obter ano letivo ativo
        $anoLetivoModel = new AnoLetivoModel();
        $anoAtivo = $anoLetivoModel->where('status', 1)->first();
        $idAnoLetivo = $anoAtivo['id'] ?? null;

        $data = [
            'numero_aluno' => trim($request->getPost('numero_aluno')),
            'nome' => trim($request->getPost('nome')),
            'turma' => strtoupper(trim($request->getPost('turma'))),
            'nif' => trim($request->getPost('nif')),
            'email_aluno' => strtolower(trim($request->getPost('email_aluno'))),
            'email_ee' => strtolower(trim($request->getPost('email_ee'))),
            'avaria' => trim($request->getPost('avaria')),
            'estado' => 'novo',
            'created_at' => date('Y-m-d H:i:s'),
            'id_ano_letivo' => $idAnoLetivo,
        ];

        log_message('debug', 'AvariasKitController::submit - prepared data keys: ' . implode(',', array_keys($data)));

        // Extra validação NIF (algoritmo simples português)
        if (!$this->validaNIF($data['nif'])) {
            log_message('info', 'AvariasKitController::submit - invalid NIF: ' . $data['nif']);
            if ($request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'errors' => ['NIF inválido.']])->setStatusCode(400);
            }
            return redirect()->back()->with('formErrors', ['NIF inválido.'])->withInput();
        }

        if (!$this->avariasModel->insert($data)) {
            $errs = $this->avariasModel->errors();
            log_message('error', 'AvariasKitController::submit - insert failed: ' . var_export($errs, true));
            
            if ($request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $errs
                ])->setStatusCode(400);
            }
            
            return redirect()->back()->with('formErrors', $errs)->withInput();
        }

        log_message('info', 'AvariasKitController::submit - insert successful for NIF: ' . $data['nif']);
        
        // Enviar email de confirmação
        $this->sendConfirmationEmail($data);
        
        if ($request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Reporte de avaria enviado com sucesso! Verifique o seu email para confirmação.'
            ]);
        }
        
        return redirect()->back()->with('success', 'Reporte de avaria enviado com sucesso. Estado: pendente.');
    }

    /**
     * Validação básica de NIF português
     */
    private function validaNIF(string $nif): bool
    {
        if (!preg_match('/^[0-9]{9}$/', $nif)) return false;
        $nifsValidos = ['1','2','3','5','6','8','9'];
        if (!in_array($nif[0], $nifsValidos, true)) return false;
        $total = 0;
        for ($i=0;$i<8;$i++) {
            $total += (int)$nif[$i] * (9 - $i);
        }
        $digito = 11 - ($total % 11);
        if ($digito >= 10) $digito = 0;
        return ((int)$nif[8] === $digito);
    }

    /**
     * Enviar email de confirmação de submissão
     */
    private function sendConfirmationEmail(array $data): void
    {
        try {
            $email = \Config\Services::email();
            
            // Caminho absoluto para o logo (FCPATH já é public/)
            $logoPath = FCPATH . 'adminlte/img/logo.png';
            
            $message = "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <p>Exmo Encarregado de Educação,</p>
                <p>O reporte de avaria do Kit Digital do aluno <strong>{$data['nome']}</strong> (Nº {$data['numero_aluno']}) foi recebido com sucesso.</p>
                <p><strong>Dados do Reporte:</strong></p>
                <ul>
                    <li>Nome: {$data['nome']}</li>
                    <li>Número de Aluno: {$data['numero_aluno']}</li>
                    <li>Turma: {$data['turma']}</li>
                    <li>Descrição da Avaria: {$data['avaria']}</li>
                </ul>
                <p>O seu reporte encontra-se em estado <strong>Pendente</strong> e será analisado pela equipa técnica da Escola Digital.</p>
                <p>Receberá um novo email assim que o estado do seu reporte for atualizado ou caso seja necessário algum esclarecimento adicional.</p>
                <p><strong>Importante:</strong> Por favor, aguarde contacto antes de se deslocar à escola.</p>
                <p>Com os melhores cumprimentos,</p>
                <p><strong>Escola Digital<br>Agrupamento de Escolas João de Barros</strong></p>
                <p><img src='cid:logo_escola' alt='Logo Escola' style='max-width:200px; height:auto;'></p>
            </body>
            </html>
            ";

            $email->setFrom('noreply@aejoaodebarros.pt', 'AE João de Barros - Kit Digital');
            $email->setTo($data['email_ee']);
            $email->setCC($data['email_aluno']);
            $email->setSubject('Kit Digital - Confirmação de Reporte de Avaria Recebida');
            $email->setMessage($message);
            
            // Anexar logo como imagem inline
            if (file_exists($logoPath)) {
                $cid = $email->setAttachmentCID($logoPath);
                $email->attach($logoPath, 'inline', null, '', 'logo_escola');
            }

            if (!$email->send()) {
                log_message('error', 'AvariasKitController - Erro ao enviar email de confirmação: ' . $email->printDebugger(['headers']));
            } else {
                log_message('info', 'AvariasKitController - Email de confirmação enviado para: ' . $data['email_ee']);
            }
        } catch (\Exception $e) {
            log_message('error', 'AvariasKitController - Exceção ao enviar email de confirmação: ' . $e->getMessage());
        }
    }
}
