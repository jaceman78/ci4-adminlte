<?php

namespace App\Controllers;

use App\Models\TicketsModel;
use App\Models\UserModel;
use App\Models\EquipamentosModel;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    protected $ticketsModel;
    protected $userModel;
    protected $equipamentosModel;

    public function __construct()
    {
        $this->ticketsModel = new TicketsModel();
        $this->userModel = new UserModel();
        $this->equipamentosModel = new EquipamentosModel();
        helper(['log_helper', 'estado']);
    }

    /**
     * Dashboard principal - redireciona para dashboard específico por nível
     */
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userLevel = session()->get('level') ?? 0;
        $userId = session()->get('id');

        // Log de acesso ao dashboard
        log_activity(
            $userId,
            'dashboard',
            'view_dashboard',
            'Acedeu ao dashboard principal',
            null,
            null,
            null,
            ['user_level' => $userLevel]
        );

        // Redirecionar para dashboard específico baseado no nível
        if ($userLevel >= 9) {
            return $this->superAdminDashboard();
        } elseif ($userLevel >= 8) {
            return $this->adminDashboard();
        } elseif ($userLevel >= 5) {
            return $this->tecnicoDashboard();
        } else {
            return $this->userDashboard();
        }
    }

    /**
     * Dashboard para Utilizadores Básicos (Level 0-4)
     */
    public function userDashboard()
    {
        $userId = session()->get('id');
        $userData = session()->get('LoggedUserData');

        // Obter tickets do utilizador
        $meusTickets = $this->ticketsModel
            ->where('user_id', $userId)
            ->whereNotIn('estado', ['reparado', 'anulado'])
            ->orderBy("FIELD(prioridade, 'critica', 'alta', 'media', 'baixa')", '', false)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Estatísticas básicas
        $stats = [
            'tickets_ativos' => $this->ticketsModel
                ->where('user_id', $userId)
                ->whereIn('estado', ['novo', 'em_resolucao', 'aguarda_peca'])
                ->countAllResults(),
            'tickets_resolvidos' => $this->ticketsModel
                ->where('user_id', $userId)
                ->where('estado', 'reparado')
                ->countAllResults(),
            'tickets_pendentes' => $this->ticketsModel
                ->where('user_id', $userId)
                ->where('estado', 'novo')
                ->countAllResults(),
        ];

        $data = [
            'title' => 'Dashboard',
            'user' => $userData,
            'tickets' => $meusTickets,
            'stats' => $stats
        ];

        return view('dashboard/user_dashboard', $data);
    }

    /**
     * Dashboard para Técnicos (Level 5-7)
     */
    public function tecnicoDashboard()
    {
        $userId = session()->get('id');
        $userData = session()->get('LoggedUserData');
        $userNif = $userData['NIF'] ?? null;

        // Tickets atribuídos ao técnico
        $ticketsAtribuidos = $this->ticketsModel
            ->select('tickets.*, 
                CONCAT(COALESCE(te.nome, ""), " ", e.marca, " ", e.modelo) as equipamento_nome,
                ta.descricao as tipo_avaria_nome')
            ->join('equipamentos e', 'e.id = tickets.equipamento_id', 'left')
            ->join('tipos_equipamento te', 'te.id = e.tipo_id', 'left')
            ->join('tipos_avaria ta', 'ta.id = tickets.tipo_avaria_id', 'left')
            ->where('tickets.atribuido_user_id', $userId)
            ->whereIn('tickets.estado', ['em_resolucao', 'aguarda_peca'])
            ->orderBy("FIELD(tickets.prioridade, 'critica', 'alta', 'media', 'baixa')", '', false)
            ->orderBy('tickets.created_at', 'ASC')
            ->findAll();

        // Tickets urgentes (alta e crítica)
        $ticketsUrgentes = $this->ticketsModel
            ->select('tickets.*, 
                CONCAT(COALESCE(te.nome, ""), " ", e.marca, " ", e.modelo) as equipamento_nome,
                ta.descricao as tipo_avaria_nome')
            ->join('equipamentos e', 'e.id = tickets.equipamento_id', 'left')
            ->join('tipos_equipamento te', 'te.id = e.tipo_id', 'left')
            ->join('tipos_avaria ta', 'ta.id = tickets.tipo_avaria_id', 'left')
            ->where('tickets.atribuido_user_id', $userId)
            ->whereIn('tickets.prioridade', ['alta', 'critica'])
            ->whereIn('tickets.estado', ['novo', 'em_resolucao', 'aguarda_peca'])
            ->orderBy("FIELD(tickets.prioridade, 'critica', 'alta')", '', false)
            ->orderBy('tickets.created_at', 'ASC')
            ->findAll();

        // Tickets aguardam peça
        $ticketsAguardamPeca = $this->ticketsModel
            ->select('tickets.*, 
                CONCAT(COALESCE(te.nome, ""), " ", e.marca, " ", e.modelo) as equipamento_nome')
            ->join('equipamentos e', 'e.id = tickets.equipamento_id', 'left')
            ->join('tipos_equipamento te', 'te.id = e.tipo_id', 'left')
            ->where('tickets.atribuido_user_id', $userId)
            ->where('tickets.estado', 'aguarda_peca')
            ->orderBy('tickets.created_at', 'ASC')
            ->findAll();

        // Estatísticas do técnico
        $stats = $this->getTecnicoStats($userId);

        // Estatísticas de Permutas (se o usuário tiver NIF)
        $permutasStats = null;
        $permutasRecentes = [];
        if ($userNif) {
            $permutaModel = new \App\Models\PermutaModel();
            $permutasStats = $permutaModel->getEstatisticasProfessor($userNif);
            
            // Permutas recentes (últimas 5)
            $permutasRecentes = $permutaModel->getPermutasProfessor($userNif, null);
            $permutasRecentes = array_slice($permutasRecentes, 0, 5);
        }

        // Gráficos - últimos 7 dias
        $chartData = $this->getTecnicoChartData($userId);

        // Tickets por localização
        $ticketsPorLocalizacao = $this->getTicketsByLocation($userId);

        // Tipos de avaria mais comuns (top 5)
        $tiposAvariaComuns = $this->getCommonFaultTypes($userId);

        // Equipamentos mais problemáticos (top 5)
        $equipamentosProblematicos = $this->getProblematicEquipments($userId);

        $data = [
            'title' => 'Dashboard - Técnico',
            'user' => $userData,
            'tickets_atribuidos' => $ticketsAtribuidos,
            'tickets_urgentes' => $ticketsUrgentes,
            'tickets_aguardam_peca' => $ticketsAguardamPeca,
            'stats' => $stats,
            'permutas_stats' => $permutasStats,
            'permutas_recentes' => $permutasRecentes,
            'chart_data' => $chartData,
            'tickets_por_localizacao' => $ticketsPorLocalizacao,
            'tipos_avaria_comuns' => $tiposAvariaComuns,
            'equipamentos_problematicos' => $equipamentosProblematicos
        ];

        return view('dashboard/tecnico_dashboard', $data);
    }

    /**
     * Dashboard para Administradores (Level 8)
     */
    public function adminDashboard()
    {
        $userData = session()->get('LoggedUserData');

        // Estatísticas Gerais do Sistema
        $stats = $this->getAdminStats();

        // Tickets por estado (para gráfico de pizza)
        $ticketsPorEstado = $this->getTicketsByState();

        // Tickets por prioridade
        $ticketsPorPrioridade = $this->getTicketsByPriority();

        // Performance dos técnicos (top 10)
        $performanceTecnicos = $this->getTechniciansPerformance();

        // Tickets críticos não atribuídos
        $ticketsCriticosNaoAtribuidos = $this->ticketsModel
            ->select('tickets.*, 
                CONCAT(COALESCE(te.nome, ""), " ", e.marca, " ", e.modelo) as equipamento_nome,
                ta.descricao as tipo_avaria_nome,
                s.codigo_sala,
                esc.nome as escola_nome,
                u.name as criador_nome')
            ->join('equipamentos e', 'e.id = tickets.equipamento_id', 'left')
            ->join('tipos_equipamento te', 'te.id = e.tipo_id', 'left')
            ->join('tipos_avaria ta', 'ta.id = tickets.tipo_avaria_id', 'left')
            ->join('salas s', 's.id = tickets.sala_id', 'left')
            ->join('escolas esc', 'esc.id = s.escola_id', 'left')
            ->join('user u', 'u.id = tickets.user_id', 'left')
            ->where('tickets.prioridade', 'critica')
            ->where('tickets.atribuido_user_id', null)
            ->whereIn('tickets.estado', ['novo', 'em_resolucao'])
            ->orderBy('tickets.created_at', 'ASC')
            ->findAll();

        // Tickets pendentes há mais de 48h
        $ticketsPendentesAntigos = $this->getOldPendingTickets();

        // Evolução de tickets nos últimos 30 dias
        $evolucaoTickets = $this->getTicketsEvolution();

        // Escolas com mais tickets ativos
        $escolasComMaisTickets = $this->getSchoolsWithMostTickets();

        // Tipos de avaria mais frequentes
        $tiposAvariaFrequentes = $this->getMostFrequentFaults();

        // Equipamentos mais problemáticos
        $equipamentosProblematicos = $this->getAdminProblematicEquipments();

        $data = [
            'title' => 'Dashboard - Administrador',
            'user' => $userData,
            'stats' => $stats,
            'tickets_por_estado' => $ticketsPorEstado,
            'tickets_por_prioridade' => $ticketsPorPrioridade,
            'performance_tecnicos' => $performanceTecnicos,
            'tickets_criticos' => $ticketsCriticosNaoAtribuidos,
            'tickets_antigos' => $ticketsPendentesAntigos,
            'evolucao_tickets' => $evolucaoTickets,
            'escolas_mais_tickets' => $escolasComMaisTickets,
            'tipos_avaria_frequentes' => $tiposAvariaFrequentes,
            'equipamentos_problematicos' => $equipamentosProblematicos
        ];

        return view('dashboard/admin_dashboard', $data);
    }

    /**
     * Dashboard para Super Administradores (Level 9)
     */
    public function superAdminDashboard()
    {
        $userData = session()->get('LoggedUserData');

        // TODO: Implementar dashboard de super admin
        $data = [
            'title' => 'Dashboard - Super Administrador',
            'user' => $userData
        ];

        return view('dashboard/super_admin_dashboard', $data);
    }

    /**
     * Obter estatísticas do técnico
     */
    private function getTecnicoStats($userId)
    {
        $db = \Config\Database::connect();

        // Tickets atribuídos ativos
        $ticketsAtivos = $this->ticketsModel
            ->where('atribuido_user_id', $userId)
            ->whereIn('estado', ['em_resolucao', 'aguarda_peca'])
            ->countAllResults();

        // Tickets urgentes
        $ticketsUrgentes = $this->ticketsModel
            ->where('atribuido_user_id', $userId)
            ->whereIn('prioridade', ['alta', 'critica'])
            ->whereIn('estado', ['novo', 'em_resolucao', 'aguarda_peca'])
            ->countAllResults();

        // Tickets aguardam peça
        $aguardamPeca = $this->ticketsModel
            ->where('atribuido_user_id', $userId)
            ->where('estado', 'aguarda_peca')
            ->countAllResults();

        // Tickets resolvidos este mês
        $resolvidosMes = $this->ticketsModel
            ->where('atribuido_user_id', $userId)
            ->where('estado', 'reparado')
            ->where('MONTH(updated_at)', date('m'))
            ->where('YEAR(updated_at)', date('Y'))
            ->countAllResults();

        // Tempo médio de resolução (em horas) - últimos 30 dias
        // Usar dados reais da tabela registos_reparacao
        $query = $db->query("
            SELECT ROUND(AVG(rr.tempo_gasto_min) / 60, 1) as tempo_medio
            FROM registos_reparacao rr
            INNER JOIN tickets t ON t.id = rr.ticket_id
            WHERE t.atribuido_user_id = ?
            AND t.estado = 'reparado'
            AND rr.criado_em >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ", [$userId]);
        $result = $query->getRow();
        $tempoMedio = $result && $result->tempo_medio ? (float) $result->tempo_medio : 0;

        // Taxa de reabertura (% de tickets reabertos)
        $totalResolvidos = $this->ticketsModel
            ->where('atribuido_user_id', $userId)
            ->where('estado', 'reparado')
            ->countAllResults();

        // Simular taxa de reabertura (pode implementar lógica real com histórico)
        $taxaReabertura = $totalResolvidos > 0 ? rand(0, 10) : 0;

        return [
            'tickets_ativos' => $ticketsAtivos,
            'tickets_urgentes' => $ticketsUrgentes,
            'aguardam_peca' => $aguardamPeca,
            'resolvidos_mes' => $resolvidosMes,
            'tempo_medio_horas' => $tempoMedio,
            'taxa_reabertura' => $taxaReabertura
        ];
    }

    /**
     * Obter dados para gráfico - últimos 7 dias
     */
    private function getTecnicoChartData($userId)
    {
        $db = \Config\Database::connect();

        // Tickets resolvidos nos últimos 7 dias
        $query = $db->query("
            SELECT 
                DATE(updated_at) as data,
                COUNT(*) as total
            FROM tickets
            WHERE atribuido_user_id = ?
            AND estado = 'reparado'
            AND updated_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(updated_at)
            ORDER BY data ASC
        ", [$userId]);

        $results = $query->getResultArray();

        // Preencher todos os dias (últimos 7)
        $labels = [];
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('d/m', strtotime($date));
            
            // Procurar valor para esta data
            $found = false;
            foreach ($results as $row) {
                if ($row['data'] == $date) {
                    $data[] = $row['total'];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $data[] = 0;
            }
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Obter tickets por localização
     */
    private function getTicketsByLocation($userId)
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                e.nome as escola,
                COUNT(t.id) as total
            FROM tickets t
            LEFT JOIN salas s ON t.sala_id = s.id
            LEFT JOIN escolas e ON s.escola_id = e.id
            WHERE t.atribuido_user_id = ?
            AND t.estado IN ('novo', 'em_resolucao', 'aguarda_peca')
            GROUP BY e.id
            ORDER BY total DESC
            LIMIT 5
        ", [$userId]);

        return $query->getResultArray();
    }

    /**
     * Obter tipos de avaria mais comuns
     */
    private function getCommonFaultTypes($userId)
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                ta.descricao as tipo_avaria,
                COUNT(t.id) as total
            FROM tickets t
            LEFT JOIN tipos_avaria ta ON t.tipo_avaria_id = ta.id
            WHERE t.atribuido_user_id = ?
            AND t.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY ta.id
            ORDER BY total DESC
            LIMIT 5
        ", [$userId]);

        return $query->getResultArray();
    }

    /**
     * Obter equipamentos mais problemáticos
     */
    private function getProblematicEquipments($userId)
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                te.nome as tipo_equipamento,
                COUNT(t.id) as total
            FROM tickets t
            LEFT JOIN equipamentos eq ON t.equipamento_id = eq.id
            LEFT JOIN tipos_equipamento te ON eq.tipo_id = te.id
            WHERE t.atribuido_user_id = ?
            AND t.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY te.id
            ORDER BY total DESC
            LIMIT 5
        ", [$userId]);

        return $query->getResultArray();
    }

    /**
     * API: Obter estatísticas (JSON)
     */
    public function getStats()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Não autorizado'])->setStatusCode(401);
        }

        $userLevel = session()->get('level') ?? 0;
        $userId = session()->get('id');

        if ($userLevel >= 5 && $userLevel < 8) {
            // Técnico
            $stats = $this->getTecnicoStats($userId);
            return $this->response->setJSON($stats);
        }

        return $this->response->setJSON(['error' => 'Nível não suportado'])->setStatusCode(400);
    }

    /**
     * API: Obter dados de gráfico (JSON)
     */
    public function getChartData($type = 'resolvidos')
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Não autorizado'])->setStatusCode(401);
        }

        $userLevel = session()->get('level') ?? 0;
        $userId = session()->get('id');

        if ($userLevel >= 5 && $userLevel < 8) {
            // Técnico
            if ($type === 'resolvidos') {
                $chartData = $this->getTecnicoChartData($userId);
                return $this->response->setJSON($chartData);
            }
        }

        return $this->response->setJSON(['error' => 'Tipo não suportado'])->setStatusCode(400);
    }

    // ==================== MÉTODOS PRIVADOS PARA ADMIN ====================

    /**
     * Estatísticas gerais do sistema para Admin
     */
    private function getAdminStats()
    {
        $db = \Config\Database::connect();

        // Usar query direta para evitar conflitos com query builder
        $query = $db->query("
            SELECT 
                COUNT(*) as total_tickets,
                SUM(CASE WHEN estado IN ('novo', 'em_resolucao', 'aguarda_peca') THEN 1 ELSE 0 END) as tickets_ativos,
                SUM(CASE WHEN estado = 'novo' THEN 1 ELSE 0 END) as tickets_novos,
                SUM(CASE WHEN estado = 'em_resolucao' THEN 1 ELSE 0 END) as tickets_em_resolucao,
                SUM(CASE WHEN estado = 'aguarda_peca' THEN 1 ELSE 0 END) as tickets_aguardam_peca,
                SUM(CASE WHEN estado = 'reparado' THEN 1 ELSE 0 END) as tickets_resolvidos,
                SUM(CASE WHEN estado = 'anulado' THEN 1 ELSE 0 END) as tickets_anulados,
                SUM(CASE WHEN prioridade = 'critica' AND estado IN ('novo', 'em_resolucao') THEN 1 ELSE 0 END) as tickets_criticos,
                SUM(CASE WHEN atribuido_user_id IS NULL AND estado = 'novo' THEN 1 ELSE 0 END) as tickets_nao_atribuidos
            FROM tickets
        ");
        
        $stats = $query->getRowArray();
        
        // Contar técnicos ativos (level 5 ou superior)
        $userModel = new UserModel();
        $stats['total_tecnicos'] = $userModel->where('level >=', 5)->countAllResults();
        
        // Contar usuários básicos
        $userModel2 = new UserModel();
        $stats['total_usuarios'] = $userModel2->where('level <', 5)->countAllResults();
        
        $stats['tempo_medio_resolucao'] = $this->getAverageResolutionTime();
        $stats['taxa_resolucao_hoje'] = $this->getTodayResolutionRate();
        
        return $stats;
    }

    /**
     * Tickets por estado
     */
    private function getTicketsByState()
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                et.nome as estado,
                et.cor as cor,
                COUNT(t.id) as total
            FROM tickets t
            LEFT JOIN estados_ticket et ON t.estado = et.codigo
            GROUP BY t.estado
            ORDER BY et.ordem
        ");

        return $query->getResultArray();
    }

    /**
     * Tickets por prioridade
     */
    private function getTicketsByPriority()
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                prioridade,
                COUNT(*) as total
            FROM tickets
            WHERE prioridade IN ('critica', 'alta', 'media', 'baixa')
            AND estado IN ('novo', 'em_resolucao', 'aguarda_peca')
            GROUP BY prioridade
            ORDER BY FIELD(prioridade, 'critica', 'alta', 'media', 'baixa')
        ");

        $data = $query->getResultArray();
        $result = [];
        
        // Garantir que todas as prioridades apareçam, mesmo com 0
        $prioridades = ['critica' => 'Crítica', 'alta' => 'Alta', 'media' => 'Média', 'baixa' => 'Baixa'];
        $dataMap = [];
        foreach ($data as $row) {
            $dataMap[$row['prioridade']] = $row['total'];
        }
        
        foreach ($prioridades as $key => $label) {
            $result[] = [
                'prioridade' => $label,
                'total' => $dataMap[$key] ?? 0
            ];
        }

        return $result;
    }

    /**
     * Performance dos técnicos
     */
    private function getTechniciansPerformance()
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                u.name as tecnico,
                COUNT(t.id) as total_atribuidos,
                SUM(CASE WHEN t.estado = 'reparado' THEN 1 ELSE 0 END) as resolvidos,
                SUM(CASE WHEN t.estado IN ('novo', 'em_resolucao', 'aguarda_peca') THEN 1 ELSE 0 END) as em_progresso,
                ROUND(COALESCE(AVG(rr.tempo_gasto_min) / 60, 0), 1) as tempo_medio_horas
            FROM user u
            LEFT JOIN tickets t ON u.id = t.atribuido_user_id
            LEFT JOIN registos_reparacao rr ON t.id = rr.ticket_id AND t.estado = 'reparado'
            WHERE u.level >= 5 AND u.level <= 7
            GROUP BY u.id
            HAVING total_atribuidos > 0
            ORDER BY resolvidos DESC
            LIMIT 10
        ");

        return $query->getResultArray();
    }

    /**
     * Tickets pendentes há mais de 48h
     */
    private function getOldPendingTickets()
    {
        return $this->ticketsModel
            ->select('tickets.*, 
                CONCAT(COALESCE(te.nome, ""), " ", e.marca, " ", e.modelo) as equipamento_nome,
                ta.descricao as tipo_avaria_nome,
                s.codigo_sala,
                esc.nome as escola_nome,
                u.name as criador_nome,
                TIMESTAMPDIFF(HOUR, tickets.created_at, NOW()) as horas_pendente')
            ->join('equipamentos e', 'e.id = tickets.equipamento_id', 'left')
            ->join('tipos_equipamento te', 'te.id = e.tipo_id', 'left')
            ->join('tipos_avaria ta', 'ta.id = tickets.tipo_avaria_id', 'left')
            ->join('salas s', 's.id = tickets.sala_id', 'left')
            ->join('escolas esc', 'esc.id = s.escola_id', 'left')
            ->join('user u', 'u.id = tickets.user_id', 'left')
            ->whereIn('tickets.estado', ['novo', 'em_resolucao'])
            ->where('tickets.created_at <', date('Y-m-d H:i:s', strtotime('-48 hours')))
            ->orderBy('tickets.created_at', 'ASC')
            ->limit(10)
            ->findAll();
    }

    /**
     * Evolução de tickets nos últimos 30 dias
     */
    private function getTicketsEvolution()
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                DATE(created_at) as data,
                COUNT(*) as criados,
                SUM(CASE WHEN estado = 'reparado' THEN 1 ELSE 0 END) as resolvidos
            FROM tickets
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY data ASC
        ");

        return $query->getResultArray();
    }

    /**
     * Escolas com mais tickets ativos
     */
    private function getSchoolsWithMostTickets()
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                e.nome as escola,
                COUNT(t.id) as total,
                SUM(CASE WHEN t.prioridade = 'critica' THEN 1 ELSE 0 END) as criticos
            FROM tickets t
            LEFT JOIN salas s ON t.sala_id = s.id
            LEFT JOIN escolas e ON s.escola_id = e.id
            WHERE t.estado IN ('novo', 'em_resolucao', 'aguarda_peca')
            GROUP BY e.id
            HAVING total > 0
            ORDER BY total DESC
            LIMIT 10
        ");

        return $query->getResultArray();
    }

    /**
     * Tipos de avaria mais frequentes (sistema todo)
     */
    private function getMostFrequentFaults()
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                ta.descricao as tipo_avaria,
                COUNT(t.id) as total,
                ROUND(COALESCE(AVG(rr.tempo_gasto_min) / 60, 0), 1) as tempo_medio_horas
            FROM tickets t
            LEFT JOIN tipos_avaria ta ON t.tipo_avaria_id = ta.id
            LEFT JOIN registos_reparacao rr ON t.id = rr.ticket_id AND t.estado = 'reparado'
            WHERE t.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
            GROUP BY ta.id
            ORDER BY total DESC
            LIMIT 10
        ");

        return $query->getResultArray();
    }

    /**
     * Equipamentos mais problemáticos (sistema todo)
     */
    private function getAdminProblematicEquipments()
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                te.nome as tipo_equipamento,
                COUNT(t.id) as total_tickets,
                SUM(CASE WHEN t.estado = 'reparado' THEN 1 ELSE 0 END) as resolvidos,
                SUM(CASE WHEN t.estado IN ('novo', 'em_resolucao', 'aguarda_peca') THEN 1 ELSE 0 END) as pendentes
            FROM tickets t
            LEFT JOIN equipamentos eq ON t.equipamento_id = eq.id
            LEFT JOIN tipos_equipamento te ON eq.tipo_id = te.id
            WHERE t.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
              AND eq.estado IS NOT NULL
            GROUP BY te.id
            HAVING total_tickets > 0
            ORDER BY total_tickets DESC
            LIMIT 10
        ");

        return $query->getResultArray();
    }

    /**
     * Tempo médio de resolução (em horas)
     */
    private function getAverageResolutionTime()
    {
        $db = \Config\Database::connect();

        // Buscar tempo médio real dos registos de reparação (em minutos) e converter para horas
        $query = $db->query("
            SELECT 
                ROUND(AVG(rr.tempo_gasto_min) / 60, 1) as media_horas
            FROM registos_reparacao rr
            INNER JOIN tickets t ON t.id = rr.ticket_id
            WHERE t.estado = 'reparado'
            AND rr.criado_em >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");

        $result = $query->getRow();
        
        // Retornar 0 se não houver resultados ou se media for NULL
        if (!$result || $result->media_horas === null) {
            return 0;
        }
        
        return (float) $result->media_horas;
    }

    /**
     * Taxa de resolução hoje
     */
    private function getTodayResolutionRate()
    {
        $db = \Config\Database::connect();
        
        $queryCriados = $db->query("
            SELECT COUNT(*) as total
            FROM tickets
            WHERE DATE(created_at) = ?
        ", [date('Y-m-d')]);
        $criados = $queryCriados->getRow()->total;

        $queryResolvidos = $db->query("
            SELECT COUNT(*) as total
            FROM tickets
            WHERE DATE(updated_at) = ?
            AND estado = 'reparado'
        ", [date('Y-m-d')]);
        $resolvidos = $queryResolvidos->getRow()->total;

        if ($criados == 0) return 0;

        return round(($resolvidos / $criados) * 100, 1);
    }
}
