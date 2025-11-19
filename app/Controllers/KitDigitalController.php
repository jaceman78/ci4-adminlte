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
            return redirect()->back()->with('formErrors', ['Detetado padrão de bot.'])->withInput();
        }

        // Captcha
        $captchaAnswer = $request->getPost('captcha_answer');
        $expected = session()->get('captcha_sum');
        log_message('debug', 'KitDigitalController::submit - captcha answer: ' . var_export($captchaAnswer, true) . ' expected: ' . var_export($expected, true));
        if ((int)$captchaAnswer !== (int)$expected) {
            log_message('info', 'KitDigitalController::submit - captcha mismatch');
            return redirect()->back()->with('formErrors', ['Resposta ao desafio inválida.'])->withInput();
        }

        // Aceitação das condições (checkbox)
        $aceito = $request->getPost('aceito');
        log_message('debug', 'KitDigitalController::submit - aceito: ' . var_export($aceito, true));
        if (!$aceito) {
            log_message('info', 'KitDigitalController::submit - checkbox aceito not set');
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
            return redirect()->back()->with('formErrors', ['NIF inválido.'])->withInput();
        }

        if (!$this->kitModel->insert($data)) {
            $errs = $this->kitModel->errors();
            log_message('error', 'KitDigitalController::submit - insert failed: ' . var_export($errs, true));
            return redirect()->back()->with('formErrors', $errs)->withInput();
        }

        log_message('info', 'KitDigitalController::submit - insert successful for NIF: ' . $data['nif']);
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
}
