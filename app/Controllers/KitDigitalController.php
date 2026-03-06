<?php
namespace App\Controllers;

use App\Models\RequesicaoKitModel;
use CodeIgniter\HTTP\ResponseInterface;

class KitDigitalController extends BaseController
{
    protected $kitModel;

    public function __construct()
    {
        $this->kitModel = new RequesicaoKitModel();
    }

    /**
     * Página pública do formulário
     */
    public function form()
    {
        // Gerar captcha simples (soma)
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        session()->set('captcha_sum', $a + $b);

        // Buscar turmas (ordenar por código)
        $turmaModel = new \App\Models\TurmaModel();
        $turmas = $turmaModel->orderBy('codigo', 'asc')->findAll();

        return view('public/kit_digital', [
            'captcha_a' => $a,
            'captcha_b' => $b,
            'turmas' => $turmas,
            'errors' => session()->getFlashdata('formErrors'),
            'success' => session()->getFlashdata('success'),
        ]);
    }

    /**
     * Submeter requisição
     */
    public function submit()
    {
        $request = $this->request;

        // Log de diagnóstico
        log_message('debug', 'KitDigitalController::submit - entry - Method: ' . $request->getMethod());

        // Honeypot (anti bot simples)
        $honeypot = $request->getPost('website');
        log_message('debug', 'KitDigitalController::submit - honeypot: ' . var_export($honeypot, true));
        if ($honeypot) {
            log_message('warning', 'KitDigitalController::submit - bot detected via honeypot');
            if ($request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'errors' => ['Detetado padrão de bot.']])->setStatusCode(400);
            }
            return redirect()->back()->with('formErrors', ['Detetado padrão de bot.'])->withInput();
        }

        // Captcha
        $captchaAnswer = $request->getPost('captcha_answer');
        $expected = session()->get('captcha_sum');
        log_message('debug', 'KitDigitalController::submit - captcha answer: ' . var_export($captchaAnswer, true) . ' expected: ' . var_export($expected, true));
        if ((int)$captchaAnswer !== (int)$expected) {
            log_message('info', 'KitDigitalController::submit - captcha mismatch');
            if ($request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'errors' => ['Resposta ao desafio inválida.']])->setStatusCode(400);
            }
            return redirect()->back()->with('formErrors', ['Resposta ao desafio inválida.'])->withInput();
        }

        // Aceitação das condições (checkbox)
        $aceito = $request->getPost('aceito');
        log_message('debug', 'KitDigitalController::submit - aceito: ' . var_export($aceito, true));
        if (!$aceito) {
            log_message('info', 'KitDigitalController::submit - checkbox aceito not set');
            if ($request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'errors' => ['É necessário aceitar as Condições Gerais e a Política de Proteção de Dados.']])->setStatusCode(400);
            }
            return redirect()->back()->with('formErrors', ['É necessário aceitar as Condições Gerais e a Política de Proteção de Dados.'])->withInput();
        }

        $data = [
            'numero_aluno' => trim($request->getPost('numero_aluno')),
            'nome' => trim($request->getPost('nome')),
            'turma' => strtoupper(trim($request->getPost('turma'))),
            'nif' => trim($request->getPost('nif')),
            'ase' => trim($request->getPost('ase')),
            'email_aluno' => strtolower(trim($request->getPost('email_aluno'))),
            'email_ee' => strtolower(trim($request->getPost('email_ee'))),
            'estado' => 'pendente',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        log_message('debug', 'KitDigitalController::submit - prepared data keys: ' . implode(',', array_keys($data)));

        // Extra validação NIF (algoritmo simples portugués)
        if (!$this->validaNIF($data['nif'])) {
            log_message('info', 'KitDigitalController::submit - invalid NIF: ' . $data['nif']);
            if ($request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'errors' => ['NIF inválido.']])->setStatusCode(400);
            }
            return redirect()->back()->with('formErrors', ['NIF inválido.'])->withInput();
        }

        if (!$this->kitModel->insert($data)) {
            $errs = $this->kitModel->errors();
            log_message('error', 'KitDigitalController::submit - insert failed: ' . var_export($errs, true));
            
            // Se for AJAX, retornar JSON
            if ($request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $errs
                ])->setStatusCode(400);
            }
            
            return redirect()->back()->with('formErrors', $errs)->withInput();
        }

        log_message('info', 'KitDigitalController::submit - insert successful for NIF: ' . $data['nif']);
        
        // Enviar email de confirmação
        $this->sendConfirmationEmail($data);
        
        // Se for AJAX, retornar JSON
        if ($request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Requisição enviada com sucesso! Verifique o seu email para confirmação.'
            ]);
        }
        
        return redirect()->back()->with('success', 'Requisição enviada com sucesso. Estado: pendente.');
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
        return $digito == (int)$nif[8];
    }

    /**
     * Enviar email de confirmação de submissão
     */
    private function sendConfirmationEmail(array $data)
    {
        try {
            $email = \Config\Services::email();
            
            // Caminho absoluto para o logo (FCPATH já é public/)
            $logoPath = FCPATH . 'adminlte/img/logo.png';
            
            $message = "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <p>Exmo Encarregado de Educação,</p>
                <p>A sua requisição de Kit Digital para o aluno <strong>{$data['nome']}</strong> (Nº {$data['numero_aluno']}) foi recebida com sucesso.</p>
                <p><strong>Dados da Requisição:</strong></p>
                <ul>
                    <li>Nome: {$data['nome']}</li>
                    <li>Número de Aluno: {$data['numero_aluno']}</li>
                    <li>Turma: {$data['turma']}</li>
                    <li>Escalão ASE: {$data['ase']}</li>
                </ul>
                <p>O seu pedido encontra-se em estado <strong>Pendente</strong> e será analisado pela equipa da Escola Digital.</p>
                <p>Receberá um novo email assim que o pedido for aprovado ou caso seja necessário algum esclarecimento adicional.</p>
                <p>Com os melhores cumprimentos,</p>
                <p><strong>Escola Digital<br>Agrupamento de Escolas João de Barros</strong></p>
                <p><img src='cid:logo_escola' alt='Logo Escola' style='max-width:200px; height:auto;'></p>
            </body>
            </html>
            ";

            $email->setFrom('noreply@aejoaodebarros.pt', 'AE João de Barros - Kit Digital');
            $email->setTo($data['email_ee']);
            $email->setCC($data['email_aluno']);
            $email->setSubject('Kit Digital - Confirmação de Requisição Recebida');
            $email->setMessage($message);
            
            // Anexar logo como imagem inline
            if (file_exists($logoPath)) {
                $cid = $email->setAttachmentCID($logoPath);
                $email->attach($logoPath, 'inline', null, '', 'logo_escola');
            }

            if (!$email->send()) {
                log_message('error', 'Erro ao enviar email de confirmação: ' . $email->printDebugger(['headers']));
            } else {
                log_message('info', 'Email de confirmação enviado para: ' . $data['email_ee']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Exceção ao enviar email de confirmação: ' . $e->getMessage());
        }
    }
}
