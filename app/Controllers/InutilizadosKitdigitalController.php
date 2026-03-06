<?php
namespace App\Controllers;

use App\Models\InutilizadosKitdigitalModel;
use CodeIgniter\HTTP\ResponseInterface;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class InutilizadosKitdigitalController extends BaseController
{
    protected $inutilizadosModel;

    public function __construct()
    {
        $this->inutilizadosModel = new InutilizadosKitdigitalModel();
        helper(['logs', 'form']);
    }

    /**
     * Verificar se o utilizador tem nível 7 ou superior (Técnico Sénior)
     */
    private function checkAccess()
    {
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel < 7) {
            return redirect()->to('/')->with('error', 'Acesso negado. Apenas Técnicos Sénior podem aceder a esta área.');
        }
        return null;
    }

    /**
     * Página principal com listagem
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
                'inutilizados_kitdigital',
                'view_index',
                null,
                "Acedeu à listagem de equipamentos inutilizados"
            );
        }

        // Obter estatísticas
        $stats = $this->inutilizadosModel->getEstatisticas();

        return view('inutilizados_kitdigital/index', ['stats' => $stats]);
    }

    /**
     * Dados para DataTable (AJAX)
     */
    public function getData()
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON(['error' => 'Acesso negado'])->setStatusCode(403);
        }

        $equipamentos = $this->inutilizadosModel->getEquipamentosComDetalhes();

        return $this->response->setJSON([
            'data' => $equipamentos
        ]);
    }

    /**
     * Obter estatísticas (AJAX)
     */
    public function getStats()
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON(['error' => 'Acesso negado'])->setStatusCode(403);
        }

        $stats = $this->inutilizadosModel->getEstatisticas();
        $porMarca = $this->inutilizadosModel->getEstatisticasPorMarca();

        return $this->response->setJSON([
            'stats' => $stats,
            'por_marca' => $porMarca
        ]);
    }

    /**
     * Criar novo equipamento inutilizado
     */
    public function create()
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $data = $this->request->getPost();
        $userData = session()->get('LoggedUserData');
        $userId = $userData['ID'] ?? $userData['id'] ?? null;

        // Adicionar ID do técnico
        $data['id_tecnico'] = $userId;

        // Converter checkboxes para valores binários
        $componentes = ['ram', 'disco', 'teclado', 'ecra', 'bateria', 'caixa'];
        foreach ($componentes as $comp) {
            $data[$comp] = isset($data[$comp]) && $data[$comp] == '1' ? 1 : 0;
        }

        if ($this->inutilizadosModel->save($data)) {
            $id = $this->inutilizadosModel->getInsertID();

            // Log da ação
            if ($userId) {
                log_activity(
                    'inutilizados_kitdigital',
                    'create',
                    $id,
                    "Registou equipamento inutilizado: {$data['n_serie']}"
                );
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Equipamento registado com sucesso!',
                'id' => $id
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao registar equipamento.',
            'errors' => $this->inutilizadosModel->errors()
        ])->setStatusCode(400);
    }

    /**
     * Atualizar equipamento inutilizado
     */
    public function update($id)
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $data = $this->request->getPost();

        // Converter checkboxes para valores binários
        $componentes = ['ram', 'disco', 'teclado', 'ecra', 'bateria', 'caixa'];
        foreach ($componentes as $comp) {
            $data[$comp] = isset($data[$comp]) && $data[$comp] == '1' ? 1 : 0;
        }

        if ($this->inutilizadosModel->update($id, $data)) {
            // Verificar se ficou esgotado
            $this->inutilizadosModel->atualizarEstadoSeEsgotado($id);

            // Log da ação
            $userData = session()->get('LoggedUserData');
            $userId = $userData['ID'] ?? $userData['id'] ?? null;
            if ($userId) {
                log_activity(
                    'inutilizados_kitdigital',
                    'update',
                    $id,
                    "Atualizou equipamento inutilizado: {$data['n_serie']}"
                );
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Equipamento atualizado com sucesso!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar equipamento.',
            'errors' => $this->inutilizadosModel->errors()
        ])->setStatusCode(400);
    }

    /**
     * Obter detalhes de um equipamento (AJAX)
     */
    public function getDetails($id)
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON(['error' => 'Acesso negado'])->setStatusCode(403);
        }

        $equipamento = $this->inutilizadosModel->find($id);

        if (!$equipamento) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Equipamento não encontrado.'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $equipamento
        ]);
    }

    /**
     * Eliminar equipamento (soft delete)
     */
    public function delete($id)
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $equipamento = $this->inutilizadosModel->find($id);
        
        if (!$equipamento) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Equipamento não encontrado.'
            ])->setStatusCode(404);
        }

        if ($this->inutilizadosModel->delete($id)) {
            // Log da ação
            $userData = session()->get('LoggedUserData');
            $userId = $userData['ID'] ?? $userData['id'] ?? null;
            if ($userId) {
                log_activity(
                    'inutilizados_kitdigital',
                    'delete',
                    $id,
                    "Eliminou equipamento inutilizado: {$equipamento['n_serie']}"
                );
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Equipamento eliminado com sucesso!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao eliminar equipamento.'
        ])->setStatusCode(400);
    }

    /**
     * Gerar QR Code dinamicamente (sem guardar ficheiro)
     */
    private function gerarQRCodeDataUri($equipamento)
    {
        try {
            // Informações para o QR Code
            $id = $equipamento['id'];
            $info = "ID: {$id}\n";
            $info .= "N/S: {$equipamento['n_serie']}\n";
            $info .= "Marca: {$equipamento['marca']}\n";
            if ($equipamento['modelo']) {
                $info .= "Modelo: {$equipamento['modelo']}\n";
            }
            $info .= "\nComponentes Disponíveis:\n";
            $info .= "RAM: " . ($equipamento['ram'] ? 'Sim' : 'Não') . "\n";
            $info .= "Disco: " . ($equipamento['disco'] ? 'Sim' : 'Não') . "\n";
            $info .= "Teclado: " . ($equipamento['teclado'] ? 'Sim' : 'Não') . "\n";
            $info .= "Ecrã: " . ($equipamento['ecra'] ? 'Sim' : 'Não') . "\n";
            $info .= "Bateria: " . ($equipamento['bateria'] ? 'Sim' : 'Não') . "\n";
            $info .= "Caixa: " . ($equipamento['caixa'] ? 'Sim' : 'Não') . "\n";
            $info .= "\nURL: " . base_url("inutilizados-kitdigital/view/{$id}");

            // Gerar QR Code em memória (compatível com versões antigas do Endroid)
            $qrCode = new QrCode($info);
            $qrCode->setSize(300);
            $qrCode->setMargin(10);

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Devolver como Data URI (base64)
            return $result->getDataUri();

        } catch (\Exception $e) {
            log_message('error', 'Erro ao gerar QR Code: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Gerar e devolver QR Code como imagem PNG (on-the-fly)
     */
    public function getQRCode($id)
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setStatusCode(403);
        }

        $equipamento = $this->inutilizadosModel->find($id);

        if (!$equipamento) {
            return $this->response->setStatusCode(404);
        }

        try {
            // Informações para o QR Code
            $info = "ID: {$id}\n";
            $info .= "N/S: {$equipamento['n_serie']}\n";
            $info .= "Marca: {$equipamento['marca']}\n";
            if ($equipamento['modelo']) {
                $info .= "Modelo: {$equipamento['modelo']}\n";
            }
            $info .= "\nComponentes Disponíveis:\n";
            $info .= "RAM: " . ($equipamento['ram'] ? 'Sim' : 'Não') . "\n";
            $info .= "Disco: " . ($equipamento['disco'] ? 'Sim' : 'Não') . "\n";
            $info .= "Teclado: " . ($equipamento['teclado'] ? 'Sim' : 'Não') . "\n";
            $info .= "Ecrã: " . ($equipamento['ecra'] ? 'Sim' : 'Não') . "\n";
            $info .= "Bateria: " . ($equipamento['bateria'] ? 'Sim' : 'Não') . "\n";
            $info .= "Caixa: " . ($equipamento['caixa'] ? 'Sim' : 'Não') . "\n";
            $info .= "\nURL: " . base_url("inutilizados-kitdigital/view/{$id}");

            // Gerar QR Code simples (compatível com versões antigas do Endroid)
            $qrCode = new QrCode($info);
            $qrCode->setSize(300);
            $qrCode->setMargin(10);

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Devolver como imagem PNG
            return $this->response
                ->setHeader('Content-Type', 'image/png')
                ->setBody($result->getString());

        } catch (\Exception $e) {
            log_message('error', 'Erro ao gerar QR Code: ' . $e->getMessage());
            return $this->response->setStatusCode(500);
        }
    }

    /**
     * Buscar equipamentos por componente disponível (AJAX)
     */
    public function buscarPorComponente()
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON(['error' => 'Acesso negado'])->setStatusCode(403);
        }

        $componente = $this->request->getGet('componente');
        
        if (!$componente) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Componente não especificado.'
            ])->setStatusCode(400);
        }

        $equipamentos = $this->inutilizadosModel->buscarPorComponente($componente);

        return $this->response->setJSON([
            'success' => true,
            'data' => $equipamentos
        ]);
    }

    /**
     * Ver detalhes de um equipamento (página completa)
     */
    public function view($id)
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        $equipamento = $this->inutilizadosModel->find($id);

        if (!$equipamento) {
            return redirect()->to('inutilizados-kitdigital')->with('error', 'Equipamento não encontrado.');
        }

        return view('inutilizados_kitdigital/view', [
            'equipamento' => $equipamento
        ]);
    }
}
