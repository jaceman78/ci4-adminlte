<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ConvocatoriaModel;
use App\Models\SessaoExameModel;
use App\Models\ExameModel;

/**
 * Controller exemplo para gestão de convocatórias
 * Área do Professor - Para confirmar convocatórias
 */
class MinhasConvocatoriasController extends BaseController
{
    protected $convocatoriaModel;
    protected $sessaoExameModel;
    protected $exameModel;

    public function __construct()
    {
        $this->convocatoriaModel = new ConvocatoriaModel();
        $this->sessaoExameModel = new SessaoExameModel();
        $this->exameModel = new ExameModel();
    }

    /**
     * Lista as convocatórias do professor logado
     */
    public function index()
    {
        // Verificar se está autenticado
        if (!session()->has('user_id')) {
            return redirect()->to('/login')->with('error', 'Por favor, faça login.');
        }

        $userId = session()->get('user_id');

        // Buscar convocatórias do professor
        $convocatorias = $this->convocatoriaModel->getByProfessor($userId, true);

        // Separar por estado
        $pendentes = [];
        $confirmadas = [];
        $rejeitadas = [];

        foreach ($convocatorias as $conv) {
            if ($conv['estado_confirmacao'] === 'Pendente') {
                $pendentes[] = $conv;
            } elseif ($conv['estado_confirmacao'] === 'Confirmado') {
                $confirmadas[] = $conv;
            } else {
                $rejeitadas[] = $conv;
            }
        }

        $data = [
            'title' => 'Minhas Convocatórias',
            'pendentes' => $pendentes,
            'confirmadas' => $confirmadas,
            'rejeitadas' => $rejeitadas,
            'total_pendentes' => count($pendentes)
        ];

        return view('convocatorias/minhas_convocatorias', $data);
    }

    /**
     * Mostra detalhes de uma convocatória
     */
    public function detalhes($id)
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $convocatoria = $this->convocatoriaModel->getWithDetails($id);

        // Verificar se a convocatória pertence ao professor
        if (!$convocatoria || $convocatoria['user_id'] != $userId) {
            return redirect()->to('/minhas-convocatorias')
                ->with('error', 'Convocatória não encontrada.');
        }

        $data = [
            'title' => 'Detalhes da Convocatória',
            'convocatoria' => $convocatoria
        ];

        return view('convocatorias/detalhes_convocatoria', $data);
    }

    /**
     * Confirma uma convocatória
     */
    public function confirmar()
    {
        if (!session()->has('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Não autenticado'
            ]);
        }

        $userId = session()->get('user_id');
        $convocatoriaId = $this->request->getPost('convocatoria_id');
        $observacoes = $this->request->getPost('observacoes');

        // Verificar se a convocatória pertence ao professor
        $convocatoria = $this->convocatoriaModel->find($convocatoriaId);

        if (!$convocatoria || $convocatoria['user_id'] != $userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Convocatória não encontrada'
            ]);
        }

        // Verificar se já foi confirmada
        if ($convocatoria['estado_confirmacao'] !== 'Pendente') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Esta convocatória já foi processada'
            ]);
        }

        // Confirmar
        $resultado = $this->convocatoriaModel->confirmar($convocatoriaId, $observacoes);

        if ($resultado) {
            // Registar log (opcional)
            log_message('info', "Convocatória #{$convocatoriaId} confirmada pelo user #{$userId}");

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Convocatória confirmada com sucesso!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao confirmar convocatória'
        ]);
    }

    /**
     * Rejeita uma convocatória
     */
    public function rejeitar()
    {
        if (!session()->has('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Não autenticado'
            ]);
        }

        $userId = session()->get('user_id');
        $convocatoriaId = $this->request->getPost('convocatoria_id');
        $observacoes = $this->request->getPost('observacoes');

        // Validar observações (obrigatório para rejeição)
        if (empty($observacoes)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Por favor, indique o motivo da rejeição'
            ]);
        }

        // Verificar se a convocatória pertence ao professor
        $convocatoria = $this->convocatoriaModel->find($convocatoriaId);

        if (!$convocatoria || $convocatoria['user_id'] != $userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Convocatória não encontrada'
            ]);
        }

        // Verificar se já foi processada
        if ($convocatoria['estado_confirmacao'] !== 'Pendente') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Esta convocatória já foi processada'
            ]);
        }

        // Rejeitar
        $resultado = $this->convocatoriaModel->rejeitar($convocatoriaId, $observacoes);

        if ($resultado) {
            // Registar log
            log_message('info', "Convocatória #{$convocatoriaId} rejeitada pelo user #{$userId}: {$observacoes}");

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Convocatória rejeitada. O coordenador será notificado.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao rejeitar convocatória'
        ]);
    }

    /**
     * Mostra calendário de convocatórias
     */
    public function calendario()
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        // Buscar convocatórias dos próximos 60 dias
        $dataInicio = date('Y-m-d');
        $dataFim = date('Y-m-d', strtotime('+60 days'));

        $convocatorias = $this->convocatoriaModel->getByProfessor($userId, true);

        // Formatar para FullCalendar
        $eventos = [];
        foreach ($convocatorias as $conv) {
            $color = 'gray';
            if ($conv['estado_confirmacao'] === 'Confirmado') {
                $color = 'green';
            } elseif ($conv['estado_confirmacao'] === 'Pendente') {
                $color = 'orange';
            } elseif ($conv['estado_confirmacao'] === 'Rejeitado') {
                $color = 'red';
            }

            $eventos[] = [
                'id' => $conv['id'],
                'title' => $conv['nome_prova'] . ' - ' . $conv['funcao'],
                'start' => $conv['data_exame'] . 'T' . $conv['hora_exame'],
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'sala' => $conv['codigo_sala'] ?? 'Suplente',
                    'estado' => $conv['estado_confirmacao'],
                    'fase' => $conv['fase']
                ]
            ];
        }

        $data = [
            'title' => 'Calendário de Convocatórias',
            'eventos' => json_encode($eventos)
        ];

        return view('convocatorias/calendario', $data);
    }

    /**
     * API - Retorna convocatórias em formato JSON
     * Para uso com DataTables ou Ajax
     */
    public function getConvocatoriasJson()
    {
        if (!session()->has('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Não autenticado'
            ]);
        }

        $userId = session()->get('user_id');
        $convocatorias = $this->convocatoriaModel->getByProfessor($userId, true);

        return $this->response->setJSON([
            'success' => true,
            'data' => $convocatorias
        ]);
    }

    /**
     * Verifica se há convocatórias pendentes
     * Para usar em notificações/badges
     */
    public function countPendentes()
    {
        if (!session()->has('user_id')) {
            return $this->response->setJSON(['count' => 0]);
        }

        $userId = session()->get('user_id');
        $pendentes = $this->convocatoriaModel->getPendentes($userId);

        return $this->response->setJSON([
            'count' => count($pendentes)
        ]);
    }
}
