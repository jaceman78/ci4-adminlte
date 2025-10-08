<?php 

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'oauth_id',
        'name', 
        'email',
        'NIF',
        'profile_img',
        'grupo_id',
        'level',
        'status',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[user.email,id,{id}]',
        'name' => 'permit_empty|max_length[100]',
        'NIF' => 'permit_empty|integer|max_length[11]',
        'profile_img' => 'permit_empty|max_length[500]',
        'grupo_id' => 'permit_empty|integer',
        'level' => 'permit_empty|integer',
        'status' => 'permit_empty|integer'
    ];

    protected $validationMessages = [
        'email' => [
            'required' => 'O email é obrigatório.',
            'valid_email' => 'Por favor, insira um email válido.',
            'is_unique' => 'Este email já está registado.',
            'regex_match' => 'O email deve pertencer ao domínio @aejoadebarros.pt.' 
        ],
        'name' => [
            'max_length' => 'O nome não pode ter mais de 100 caracteres.'
        ],
        'NIF' => [
            'integer' => 'O NIF deve ser um número.',
            'max_length' => 'O NIF não pode ter mais de 9 dígitos.'
        ]
    ];

    protected $skipValidation = true;
    protected $cleanValidationRules = false;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Obter todos os utilizadores com paginação
     */
    public function getAllUsers($limit = 10, $offset = 0)
    {
        return $this->orderBy('created_at', 'DESC')
                    ->findAll($limit, $offset);
    }

    /**
     * Obter utilizadores ativos
     */
    public function getActiveUsers()
    {
        return $this->where('status', 1)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Obter utilizadores inativos
     */
    public function getInactiveUsers()
    {
        return $this->where('status', 0)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Pesquisar utilizadores por nome ou email
     */
    public function searchUsers($search)
    {
        return $this->groupStart()
                    ->like('name', $search)
                    ->orLike('email', $search)
                    ->groupEnd()
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Obter utilizador por email
     */
    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Obter utilizador por OAuth ID
     */
    public function getUserByOAuthId($oauthId)
    {
        return $this->where('oauth_id', $oauthId)->first();
    }

    /**
     * Obter utilizadores por grupo
     */
    public function getUsersByGroup($grupoId)
    {
        return $this->where('grupo_id', $grupoId)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Obter utilizadores por nível
     */
    public function getUsersByLevel($level)
    {
        return $this->where('level', $level)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Contar utilizadores por status
     */
    public function countUsersByStatus($status = null)
    {
        if ($status !== null) {
            return $this->where('status', $status)->countAllResults();
        }
        return $this->countAllResults();
    }

    /**
     * Obter estatísticas dos utilizadores
     */
    public function getUserStats()
    {
        $stats = [];
        $stats['total'] = $this->countAllResults();
        $stats['active'] = $this->where('status', 1)->countAllResults();
        $stats['inactive'] = $this->where('status', 0)->countAllResults();
        
        // Reset builder para próximas queries
        $this->builder()->resetQuery();
        
        return $stats;
    }

    /**
     * Atualizar status do utilizador
     */
    public function updateUserStatus($userId, $status)
    {
        return $this->update($userId, ['status' => $status]);
    }

    /**
     * Atualizar imagem de perfil
     */
    public function updateProfileImage($userId, $imagePath)
    {
        return $this->update($userId, ['profile_img' => $imagePath]);
    }

    /**
     * Verificar se email existe (excluindo um ID específico)
     */
    public function emailExists($email, $excludeId = null)
    {
        $builder = $this->where('email', $email);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Obter utilizadores criados numa data específica
     */
    public function getUsersCreatedOn($date)
    {
        return $this->where('DATE(created_at)', $date)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Obter utilizadores criados entre datas
     */
    public function getUsersCreatedBetween($startDate, $endDate)
    {
        return $this->where('created_at >=', $startDate)
                    ->where('created_at <=', $endDate)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Obter dados para DataTable com filtros
     */
    public function getDataTableData($start = 0, $length = 10, $search = '', $orderColumn = 'id', $orderDir = 'asc')
    {
        $builder = $this->builder();
        
        // Aplicar pesquisa se fornecida
        if (!empty($search)) {
            $builder->groupStart()
                   ->like('name', $search)
                   ->orLike('email', $search)
                   ->orLike('NIF', $search)
                   ->groupEnd();
        }
        
        // Total de registos filtrados
        $recordsFiltered = $builder->countAllResults(false);
        
        // Aplicar ordenação
        $builder->orderBy($orderColumn, $orderDir);
        
        // Aplicar paginação
        $builder->limit($length, $start);
        
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
     * Soft delete personalizado (atualizar status para 0)
     */
    public function softDelete($id)
    {
        return $this->update($id, ['status' => 0]);
    }

    /**
     * Restaurar utilizador (atualizar status para 1)
     */
    public function restore($id)
    {
        return $this->update($id, ['status' => 1]);
    }

    /**
     * Obter utilizadores com informações de grupo (se tiver tabela de grupos)
     */
    public function getUsersWithGroup()
    {
        return $this->select('user.*, grupos.nome as grupo_nome')
                    ->join('grupos', 'grupos.id = user.grupo_id', 'left')
                    ->orderBy('user.name', 'ASC')
                    ->findAll();
    }

    /**
     * Validar dados antes de inserir/atualizar
     */
    public function validateUserData($data, $id = null)
{
    $validation = \Config\Services::validation();
    
    $rules = $this->validationRules;
    
    // Ajustar a regra de validação do email dinamicamente
    $emailRule = 'required|valid_email';
    if ($id) {
        $emailRule .= '|is_unique[user.email,id,' . $id . ']';
    } else {
        $emailRule .= '|is_unique[user.email]';
    }
    $rules['email'] = $emailRule;

    // dd($rules["email"]); // Remova esta linha de depuração

    $validation->setRules($rules);
    
    // Verificação manual adicional para is_unique (apenas para depuração se o problema persistir)
    // if ($id && isset($data['email'])) {
    //     $existingUser = $this->where('email', $data['email'])->first();
    //     if ($existingUser && $existingUser['id'] != $id) {
    //         $validation->setError('email', 'Este email já está registado para outro utilizador.');
    //     }
    // }

    if (!$validation->run($data)) {
        return [
            'success' => false,
            'errors' => $validation->getErrors()
        ];
    }
    
    return ['success' => true];
}

}
