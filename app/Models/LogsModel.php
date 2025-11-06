<?php

namespace App\Models;

use CodeIgniter\Model;

class LogsModel extends Model
{
    protected $table            = 'system_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'user_nif',
        'user_name',
        'module',
        'action',
        'record_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'severity',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
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
     */
    public function logActivity(array $data)
    {
        $request = service('request');
        
        // Dados padrão do log
        $logData = [
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s'),
        ];
        
        // Mesclar com dados fornecidos
        $logData = array_merge($logData, $data);
        
        // Converter arrays para JSON
        if (isset($logData['old_values']) && is_array($logData['old_values'])) {
            $logData['old_values'] = json_encode($logData['old_values']);
        }
        if (isset($logData['new_values']) && is_array($logData['new_values'])) {
            $logData['new_values'] = json_encode($logData['new_values']);
        }
        
        return $this->insert($logData);
    }

    /**
     * Buscar logs por módulo
     */
    public function getLogsByModule(string $module, int $limit = 100)
    {
        return $this->where('module', $module)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Buscar logs por usuário
     */
    public function getLogsByUser($userIdentifier, int $limit = 100)
    {
        return $this->groupStart()
                    ->where('user_id', $userIdentifier)
                    ->orWhere('user_nif', $userIdentifier)
                    ->groupEnd()
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Buscar logs por ação
     */
    public function getLogsByAction(string $action, int $limit = 100)
    {
        return $this->where('action', $action)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Buscar logs por registro
     */
    public function getLogsByRecord(string $module, $recordId, int $limit = 100)
    {
        return $this->where('module', $module)
                    ->where('record_id', $recordId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Buscar logs por severidade
     */
    public function getLogsBySeverity(string $severity, int $limit = 100)
    {
        return $this->where('severity', $severity)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Buscar logs por período
     */
    public function getLogsByDateRange(string $startDate, string $endDate, int $limit = 1000)
    {
        return $this->where('created_at >=', $startDate)
                    ->where('created_at <=', $endDate)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Limpar logs antigos
     */
    public function cleanOldLogs(int $daysToKeep = 90)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysToKeep} days"));
        return $this->where('created_at <', $cutoffDate)->delete();
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
        $stats['by_module'] = $this->select('module, COUNT(*) as total')
                                   ->groupBy('module')
                                   ->findAll();
        
        // Logs por ação
        $stats['by_action'] = $this->select('action, COUNT(*) as total')
                                   ->groupBy('action')
                                   ->findAll();
        
        // Logs por severidade
        $stats['by_severity'] = $this->select('severity, COUNT(*) as total')
                                     ->groupBy('severity')
                                     ->findAll();
        
        return $stats;
    }
}
