<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'logs_atividade';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'modulo',
        'acao',
        'registro_id',
        'descricao',
        'dados_anteriores',
        'dados_novos',
        'ip_address',
        'user_agent',
        'detalhes',
        'criado_em'
    ];

    // Dates
    protected $useTimestamps = false; // Usamos criado_em personalizado
    protected $dateFormat = 'datetime';

    // Validation
    protected $validationRules = [
        'modulo' => 'required|max_length[50]',
        'acao' => 'required|max_length[100]',
        'descricao' => 'required|max_length[500]',
        'criado_em' => 'required'
    ];

    protected $validationMessages = [
        'modulo' => [
            'required' => 'O módulo é obrigatório.',
            'max_length' => 'O módulo não pode ter mais de 50 caracteres.'
        ],
        'acao' => [
            'required' => 'A ação é obrigatória.',
            'max_length' => 'A ação não pode ter mais de 100 caracteres.'
        ],
        'descricao' => [
            'required' => 'A descrição é obrigatória.',
            'max_length' => 'A descrição não pode ter mais de 500 caracteres.'
        ]
    ];

    protected $skipValidation = false;

    /**
     * Registar uma atividade no log
     */
    public function logActivity(array $data): bool
    {
        // Garantir que criado_em está definido
        if (!isset($data['criado_em'])) {
            $data['criado_em'] = date('Y-m-d H:i:s');
        }

        // Converter arrays/objetos para JSON
        if (isset($data['dados_anteriores']) && is_array($data['dados_anteriores'])) {
            $data['dados_anteriores'] = json_encode($data['dados_anteriores'], JSON_UNESCAPED_UNICODE);
        }

        if (isset($data['dados_novos']) && is_array($data['dados_novos'])) {
            $data['dados_novos'] = json_encode($data['dados_novos'], JSON_UNESCAPED_UNICODE);
        }

        if (isset($data['detalhes']) && is_array($data['detalhes'])) {
            $data['detalhes'] = json_encode($data['detalhes'], JSON_UNESCAPED_UNICODE);
        }

        return $this->insert($data) !== false;
    }

    /**
     * Obter logs com informações do utilizador
     */
    public function getLogsWithUser($limit = 50, $offset = 0)
    {
        return $this->select('logs_atividade.*, user.name as user_name, user.email as user_email')
                    ->join('user', 'user.id = logs_atividade.user_id', 'left')
                    ->orderBy('logs_atividade.criado_em', 'DESC')
                    ->findAll($limit, $offset);
    }

    /**
     * Obter logs por utilizador
     */
    public function getLogsByUser($userId, $limit = 50, $offset = 0)
    {
        return $this->select('logs_atividade.*, user.name as user_name, user.email as user_email')
                    ->join('user', 'user.id = logs_atividade.user_id', 'left')
                    ->where('logs_atividade.user_id', $userId)
                    ->orderBy('logs_atividade.criado_em', 'DESC')
                    ->findAll($limit, $offset);
    }

    /**
     * Obter logs por módulo
     */
    public function getLogsByModule($modulo, $limit = 50, $offset = 0)
    {
        return $this->select('logs_atividade.*, user.name as user_name, user.email as user_email')
                    ->join('user', 'user.id = logs_atividade.user_id', 'left')
                    ->where('logs_atividade.modulo', $modulo)
                    ->orderBy('logs_atividade.criado_em', 'DESC')
                    ->findAll($limit, $offset);
    }

    /**
     * Obter logs por ação
     */
    public function getLogsByAction($acao, $limit = 50, $offset = 0)
    {
        return $this->select('logs_atividade.*, user.name as user_name, user.email as user_email')
                    ->join('user', 'user.id = logs_atividade.user_id', 'left')
                    ->where('logs_atividade.acao', $acao)
                    ->orderBy('logs_atividade.criado_em', 'DESC')
                    ->findAll($limit, $offset);
    }

    /**
     * Obter logs por período
     */
    public function getLogsByDateRange($startDate, $endDate, $limit = 50, $offset = 0)
    {
        return $this->select('logs_atividade.*, user.name as user_name, user.email as user_email')
                    ->join('user', 'user.id = logs_atividade.user_id', 'left')
                    ->where('logs_atividade.criado_em >=', $startDate)
                    ->where('logs_atividade.criado_em <=', $endDate)
                    ->orderBy('logs_atividade.criado_em', 'DESC')
                    ->findAll($limit, $offset);
    }

    /**
     * Pesquisar logs
     */
    public function searchLogs($search, $limit = 50, $offset = 0)
    {
        return $this->select('logs_atividade.*, user.name as user_name, user.email as user_email')
                    ->join('user', 'user.id = logs_atividade.user_id', 'left')
                    ->groupStart()
                        ->like('logs_atividade.descricao', $search)
                        ->orLike('logs_atividade.modulo', $search)
                        ->orLike('logs_atividade.acao', $search)
                        ->orLike('user.name', $search)
                        ->orLike('user.email', $search)
                    ->groupEnd()
                    ->orderBy('logs_atividade.criado_em', 'DESC')
                    ->findAll($limit, $offset);
    }

    /**
     * Obter dados para DataTable
     */
    public function getDataTableData($start = 0, $length = 10, $search = '', $orderColumn = 'criado_em', $orderDir = 'desc', $filters = [])
    {
        $builder = $this->select('logs_atividade.*, user.name as user_name, user.email as user_email')
                       ->join('user', 'user.id = logs_atividade.user_id', 'left');

        // Aplicar filtros
        if (!empty($filters['user_id'])) {
            $builder->where('logs_atividade.user_id', $filters['user_id']);
        }

        if (!empty($filters['modulo'])) {
            $builder->where('logs_atividade.modulo', $filters['modulo']);
        }

        if (!empty($filters['acao'])) {
            $builder->where('logs_atividade.acao', $filters['acao']);
        }

        if (!empty($filters['data_inicio'])) {
            $builder->where('logs_atividade.criado_em >=', $filters['data_inicio']);
        }

        if (!empty($filters['data_fim'])) {
            $builder->where('logs_atividade.criado_em <=', $filters['data_fim']);
        }

        // Aplicar pesquisa se fornecida
        if (!empty($search)) {
            $builder->groupStart()
                   ->like('logs_atividade.descricao', $search)
                   ->orLike('logs_atividade.modulo', $search)
                   ->orLike('logs_atividade.acao', $search)
                   ->orLike('user.name', $search)
                   ->orLike('user.email', $search)
                   ->groupEnd();
        }

        // Total de registos filtrados
        $recordsFiltered = $builder->countAllResults(false);

        // Aplicar ordenação
        $validColumns = [
            'id' => 'logs_atividade.id',
            'user_name' => 'user.name',
            'modulo' => 'logs_atividade.modulo',
            'acao' => 'logs_atividade.acao',
            'descricao' => 'logs_atividade.descricao',
            'criado_em' => 'logs_atividade.criado_em'
        ];

        $dbOrderColumn = $validColumns[$orderColumn] ?? 'logs_atividade.criado_em';
        $builder->orderBy($dbOrderColumn, $orderDir);

        // Aplicar paginação
        $builder->limit((int)$length, (int)$start);

        // Obter dados
        $data = $builder->get()->getResultArray();

        // Total de registos sem filtro
        $recordsTotal = $this->countAllResults();

        return [
            'data' => $data,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered
        ];
    }

    /**
     * Obter estatísticas dos logs
     */
    public function getLogStats()
    {
        $stats = [];

        // Total de logs
        $stats['total'] = $this->countAllResults();

        // Logs por módulo
        $stats['por_modulo'] = $this->select('modulo, COUNT(*) as total')
                                   ->groupBy('modulo')
                                   ->orderBy('total', 'DESC')
                                   ->findAll();

        // Logs por ação
        $stats['por_acao'] = $this->select('acao, COUNT(*) as total')
                                 ->groupBy('acao')
                                 ->orderBy('total', 'DESC')
                                 ->findAll();

        // Logs por utilizador (top 10)
        $stats['por_utilizador'] = $this->select('logs_atividade.user_id, user.name as user_name, COUNT(*) as total')
                                       ->join('user', 'user.id = logs_atividade.user_id', 'left')
                                       ->where('logs_atividade.user_id IS NOT NULL')
                                       ->groupBy('logs_atividade.user_id')
                                       ->orderBy('total', 'DESC')
                                       ->limit(10)
                                       ->findAll();

        // Logs por dia (últimos 30 dias)
        $stats['por_dia'] = $this->select('DATE(criado_em) as data, COUNT(*) as total')
                                ->where('criado_em >=', date('Y-m-d', strtotime('-30 days')))
                                ->groupBy('DATE(criado_em)')
                                ->orderBy('data', 'DESC')
                                ->findAll();

        // Logs de hoje
        $stats['hoje'] = $this->where('DATE(criado_em)', date('Y-m-d'))->countAllResults();

        // Logs desta semana
        $stats['esta_semana'] = $this->where('criado_em >=', date('Y-m-d', strtotime('-7 days')))->countAllResults();

        // Logs deste mês
        $stats['este_mes'] = $this->where('criado_em >=', date('Y-m-01'))->countAllResults();

        return $stats;
    }

    /**
     * Obter logs recentes
     */
    public function getRecentLogs($limit = 10)
    {
        return $this->select('logs_atividade.*, user.name as user_name, user.email as user_email')
                    ->join('user', 'user.id = logs_atividade.user_id', 'left')
                    ->orderBy('logs_atividade.criado_em', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Limpar logs antigos
     */
    public function cleanOldLogs($days = 90)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $this->where('criado_em <', $cutoffDate)->delete();
    }

    /**
     * Obter logs de um registo específico
     */
    public function getLogsByRecord($modulo, $registroId, $limit = 50)
    {
        return $this->select('logs_atividade.*, user.name as user_name, user.email as user_email')
                    ->join('user', 'user.id = logs_atividade.user_id', 'left')
                    ->where('logs_atividade.modulo', $modulo)
                    ->where('logs_atividade.registro_id', $registroId)
                    ->orderBy('logs_atividade.criado_em', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Obter lista de módulos únicos
     */
    public function getUniqueModules()
    {
        return $this->select('modulo')
                    ->distinct()
                    ->orderBy('modulo', 'ASC')
                    ->findColumn('modulo');
    }

    /**
     * Obter lista de ações únicas
     */
    public function getUniqueActions()
    {
        return $this->select('acao')
                    ->distinct()
                    ->orderBy('acao', 'ASC')
                    ->findColumn('acao');
    }

    /**
     * Obter lista de utilizadores que têm logs
     */
    public function getUsersWithLogs()
    {
        return $this->select('logs_atividade.user_id, user.name as user_name, user.email as user_email')
                    ->join('user', 'user.id = logs_atividade.user_id', 'left')
                    ->where('logs_atividade.user_id IS NOT NULL')
                    ->distinct()
                    ->orderBy('user.name', 'ASC')
                    ->findAll();
    }

    /**
     * Exportar logs para CSV
     */
    public function exportToCSV($filters = [])
    {
        $builder = $this->select('logs_atividade.*, user.name as user_name, user.email as user_email')
                       ->join('user', 'user.id = logs_atividade.user_id', 'left');

        // Aplicar filtros se fornecidos
        if (!empty($filters['user_id'])) {
            $builder->where('logs_atividade.user_id', $filters['user_id']);
        }

        if (!empty($filters['modulo'])) {
            $builder->where('logs_atividade.modulo', $filters['modulo']);
        }

        if (!empty($filters['acao'])) {
            $builder->where('logs_atividade.acao', $filters['acao']);
        }

        if (!empty($filters['data_inicio'])) {
            $builder->where('logs_atividade.criado_em >=', $filters['data_inicio']);
        }

        if (!empty($filters['data_fim'])) {
            $builder->where('logs_atividade.criado_em <=', $filters['data_fim']);
        }

        return $builder->orderBy('logs_atividade.criado_em', 'DESC')->findAll();
    }
}