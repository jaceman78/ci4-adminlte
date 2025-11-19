<?php

namespace App\Models;

use CodeIgniter\Model;

class LogsModel extends Model
{
    protected $table            = 'logs_atividade';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
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
    protected $useTimestamps = false; // Using criado_em instead
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'criado_em';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Registrar uma atividade no sistema
     * Adapta campos do novo formato para o esquema existente logs_atividade
     */
    public function logActivity(array $data)
    {
        $request = service('request');
        
        // Mapear campos do novo formato para o esquema logs_atividade
        $logData = [
            'user_id'         => $data['user_id'] ?? null,
            'modulo'          => $data['module'] ?? ($data['modulo'] ?? null),
            'acao'            => $data['action'] ?? ($data['acao'] ?? null),
            'registro_id'     => $data['record_id'] ?? ($data['registro_id'] ?? null),
            'descricao'       => $data['description'] ?? ($data['descricao'] ?? ''),
            'ip_address'      => $request->getIPAddress(),
            'user_agent'      => $request->getUserAgent()->getAgentString(),
            'criado_em'       => date('Y-m-d H:i:s'),
        ];
        
        // Mapear old_values para dados_anteriores
        if (isset($data['old_values'])) {
            $logData['dados_anteriores'] = is_array($data['old_values']) 
                ? json_encode($data['old_values'], JSON_UNESCAPED_UNICODE) 
                : $data['old_values'];
        } elseif (isset($data['dados_anteriores'])) {
            $logData['dados_anteriores'] = is_array($data['dados_anteriores']) 
                ? json_encode($data['dados_anteriores'], JSON_UNESCAPED_UNICODE) 
                : $data['dados_anteriores'];
        }
        
        // Mapear new_values para dados_novos
        if (isset($data['new_values'])) {
            $logData['dados_novos'] = is_array($data['new_values']) 
                ? json_encode($data['new_values'], JSON_UNESCAPED_UNICODE) 
                : $data['new_values'];
        } elseif (isset($data['dados_novos'])) {
            $logData['dados_novos'] = is_array($data['dados_novos']) 
                ? json_encode($data['dados_novos'], JSON_UNESCAPED_UNICODE) 
                : $data['dados_novos'];
        }
        
        // Armazenar severity e outros campos extras em detalhes
        $detalhes = [];
        if (isset($data['severity'])) {
            $detalhes['severity'] = $data['severity'];
        }
        if (isset($data['user_nif'])) {
            $detalhes['user_nif'] = $data['user_nif'];
        }
        if (isset($data['user_name'])) {
            $detalhes['user_name'] = $data['user_name'];
        }
        if (!empty($detalhes)) {
            $logData['detalhes'] = json_encode($detalhes, JSON_UNESCAPED_UNICODE);
        }
        
        return $this->insert($logData);
    }

    /**
     * Buscar logs por módulo
     */
    public function getLogsByModule(string $module, int $limit = 100)
    {
        return $this->where('modulo', $module)
                    ->orderBy('criado_em', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Buscar logs por usuário
     */
    public function getLogsByUser($userIdentifier, int $limit = 100)
    {
        return $this->where('user_id', $userIdentifier)
                    ->orderBy('criado_em', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Buscar logs por ação
     */
    public function getLogsByAction(string $action, int $limit = 100)
    {
        return $this->where('acao', $action)
                    ->orderBy('criado_em', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Buscar logs por registro
     */
    public function getLogsByRecord(string $module, $recordId, int $limit = 100)
    {
        return $this->where('modulo', $module)
                    ->where('registro_id', $recordId)
                    ->orderBy('criado_em', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Buscar logs por severidade (armazenada em detalhes)
     */
    public function getLogsBySeverity(string $severity, int $limit = 100)
    {
        return $this->like('detalhes', '"severity":"' . $severity . '"')
                    ->orderBy('criado_em', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Buscar logs por período
     */
    public function getLogsByDateRange(string $startDate, string $endDate, int $limit = 1000)
    {
        return $this->where('criado_em >=', $startDate)
                    ->where('criado_em <=', $endDate)
                    ->orderBy('criado_em', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Limpar logs antigos
     */
    public function cleanOldLogs(int $daysToKeep = 90)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysToKeep} days"));
        return $this->where('criado_em <', $cutoffDate)->delete();
    }

    /**
     * Estatísticas de logs
     */
    public function getLogStats()
    {
        $stats = [];
        
        // Total de logs
        $stats['total'] = $this->countAllResults(false);
        
        // Logs por módulo
        $stats['by_module'] = $this->select('modulo as module, COUNT(*) as total')
                                   ->groupBy('modulo')
                                   ->findAll();
        
        // Logs por ação
        $stats['by_action'] = $this->select('acao as action, COUNT(*) as total')
                                   ->groupBy('acao')
                                   ->findAll();
        
        return $stats;
    }
}
