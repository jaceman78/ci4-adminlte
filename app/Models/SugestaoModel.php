<?php

namespace App\Models;

use CodeIgniter\Model;

class SugestaoModel extends Model
{
    protected $table            = 'sugestoes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_nif',
        'categoria',
        'titulo',
        'descricao',
        'prioridade',
        'estado',
        'resposta',
        'respondido_por',
        'respondido_em'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules = [
        'categoria'  => 'required|max_length[50]',
        'titulo'     => 'required|max_length[200]',
        'descricao'  => 'required',
        'prioridade' => 'permit_empty|in_list[baixa,media,alta]',
        'estado'     => 'permit_empty|in_list[pendente,em_analise,implementada,rejeitada]'
    ];

    protected $validationMessages = [
        'categoria' => [
            'required' => 'A categoria é obrigatória'
        ],
        'titulo' => [
            'required' => 'O título é obrigatório'
        ],
        'descricao' => [
            'required' => 'A descrição é obrigatória'
        ]
    ];

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
     * Obter todas as sugestões com informações do usuário
     * 
     * @return array
     */
    public function getSugestoesComUsuario()
    {
        return $this->select('sugestoes.*, 
                             user.name as user_nome, 
                             user.email as user_email,
                             respondedor.name as respondedor_nome')
                    ->join('user', 'user.NIF = sugestoes.user_nif', 'left')
                    ->join('user as respondedor', 'respondedor.id = sugestoes.respondido_por', 'left')
                    ->orderBy('sugestoes.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Obter sugestões de um usuário específico
     * 
     * @param int $userNif
     * @return array
     */
    public function getSugestoesPorUsuario($userNif)
    {
        return $this->select('sugestoes.*, 
                             user.name as user_nome, 
                             user.email as user_email,
                             respondedor.name as respondedor_nome')
                    ->join('user', 'user.NIF = sugestoes.user_nif', 'left')
                    ->join('user as respondedor', 'respondedor.id = sugestoes.respondido_por', 'left')
                    ->where('sugestoes.user_nif', $userNif)
                    ->orderBy('sugestoes.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Contar sugestões por estado
     * 
     * @return array
     */
    public function contarPorEstado()
    {
        return $this->select('estado, COUNT(*) as total')
                    ->groupBy('estado')
                    ->findAll();
    }

    /**
     * Responder a uma sugestão
     * 
     * @param int $id
     * @param int $userId
     * @param string $resposta
     * @param string $novoEstado
     * @return bool
     */
    public function responderSugestao($id, $userId, $resposta, $novoEstado = 'em_analise')
    {
        return $this->update($id, [
            'resposta' => $resposta,
            'estado' => $novoEstado,
            'respondido_por' => $userId,
            'respondido_em' => date('Y-m-d H:i:s')
        ]);
    }
}
