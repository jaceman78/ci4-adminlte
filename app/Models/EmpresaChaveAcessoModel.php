<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpresaChaveAcessoModel extends Model
{
    protected $table            = 'empresas_chaves_acesso';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'empresa_nome',
        'chave_acesso',
        'plafond_com_iva',
        'ativo',
        'ultimo_acesso',
        'total_acessos',
        'ip_ultimo_acesso',
        'observacoes'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'empresa_nome' => 'required|min_length[3]|max_length[255]',
        'chave_acesso' => 'required|min_length[20]|max_length[64]',
    ];

    protected $validationMessages = [
        'empresa_nome' => [
            'required' => 'O nome da empresa é obrigatório',
            'min_length' => 'O nome deve ter pelo menos 3 caracteres',
        ],
        'chave_acesso' => [
            'required' => 'A chave de acesso é obrigatória',
            'min_length' => 'A chave deve ter pelo menos 20 caracteres',
        ],
    ];

    /**
     * Gerar chave de acesso única
     */
    public function gerarChaveAcesso(): string
    {
        do {
            $chave = bin2hex(random_bytes(32)); // 64 caracteres hexadecimais
        } while ($this->where('chave_acesso', $chave)->first());
        
        return $chave;
    }

    /**
     * Verificar chave de acesso
     */
    public function verificarChave(string $chave): ?array
    {
        return $this->where('chave_acesso', $chave)
                    ->where('ativo', 1)
                    ->first();
    }

    /**
     * Registrar acesso
     */
    public function registrarAcesso(int $id, string $ip): bool
    {
        return $this->update($id, [
            'ultimo_acesso' => date('Y-m-d H:i:s'),
            'total_acessos' => $this->db->table($this->table)
                                        ->selectSum('total_acessos')
                                        ->where('id', $id)
                                        ->get()
                                        ->getRow()
                                        ->total_acessos + 1,
            'ip_ultimo_acesso' => $ip,
        ]);
    }

    /**
     * Obter empresas ativas
     */
    public function getEmpresasAtivas()
    {
        return $this->where('ativo', 1)
                    ->orderBy('empresa_nome', 'ASC')
                    ->findAll();
    }

    /**
     * Obter estatísticas
     */
    public function getEstatisticas(): array
    {
        $total = $this->countAllResults(false);
        $ativas = $this->where('ativo', 1)->countAllResults();
        $inativas = $total - $ativas;

        return [
            'total' => $total,
            'ativas' => $ativas,
            'inativas' => $inativas,
        ];
    }

    /**
     * Verificar se empresa existe nas reparações
     */
    public function empresaExisteReparacoes(string $empresaNome): bool
    {
        $db = \Config\Database::connect();
        $result = $db->table('reparacoes_externas')
                    ->where('empresa_reparacao', $empresaNome)
                    ->countAllResults();
        
        return $result > 0;
    }

    /**
     * Obter lista de empresas únicas das reparações
     */
    public function getEmpresasReparacoes(): array
    {
        $db = \Config\Database::connect();
        return $db->table('reparacoes_externas')
                  ->select('empresa_reparacao')
                  ->where('empresa_reparacao IS NOT NULL')
                  ->where('empresa_reparacao !=', '')
                  ->groupBy('empresa_reparacao')
                  ->orderBy('empresa_reparacao', 'ASC')
                  ->get()
                  ->getResultArray();
    }
}
