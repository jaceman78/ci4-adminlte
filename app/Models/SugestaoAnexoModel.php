<?php

namespace App\Models;

use CodeIgniter\Model;

class SugestaoAnexoModel extends Model
{
    protected $table = 'sugestoes_anexos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'sugestao_id',
        'nome_original',
        'nome_ficheiro',
        'tipo_mime',
        'tamanho',
        'caminho'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';
    protected $deletedField = '';

    // Validation
    protected $validationRules = [
        'sugestao_id' => 'required|numeric',
        'nome_original' => 'required|max_length[255]',
        'nome_ficheiro' => 'required|max_length[255]',
        'caminho' => 'required|max_length[500]'
    ];

    protected $validationMessages = [
        'sugestao_id' => [
            'required' => 'ID da sugestão é obrigatório',
            'numeric' => 'ID da sugestão deve ser numérico'
        ],
        'nome_original' => [
            'required' => 'Nome original do ficheiro é obrigatório'
        ],
        'nome_ficheiro' => [
            'required' => 'Nome do ficheiro é obrigatório'
        ],
        'caminho' => [
            'required' => 'Caminho do ficheiro é obrigatório'
        ]
    ];

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
     * Buscar anexos de uma sugestão
     */
    public function getAnexosSugestao($sugestaoId)
    {
        return $this->where('sugestao_id', $sugestaoId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Deletar anexo e ficheiro físico
     */
    public function deleteAnexoComFicheiro($id)
    {
        $anexo = $this->find($id);
        
        if ($anexo && file_exists($anexo['caminho'])) {
            unlink($anexo['caminho']);
        }
        
        return $this->delete($id);
    }
}
