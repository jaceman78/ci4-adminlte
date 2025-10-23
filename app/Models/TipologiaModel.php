<?php

namespace App\Models;

use CodeIgniter\Model;

class TipologiaModel extends Model
{
    protected $table            = 'tipologia';
    protected $primaryKey       = 'id_tipologia';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nome_tipologia', 'status'];

    // Dates
    protected $useTimestamps = false;

    // Validation
    protected $validationRules      = [
        'nome_tipologia' => 'required|min_length[2]|max_length[255]',
        'status'         => 'required|in_list[0,1]'
    ];
    protected $validationMessages   = [
        'nome_tipologia' => [
            'required'   => 'O nome da tipologia é obrigatório',
            'min_length' => 'O nome deve ter pelo menos 2 caracteres',
            'max_length' => 'O nome não pode exceder 255 caracteres'
        ],
        'status' => [
            'required' => 'O status é obrigatório',
            'in_list'  => 'O status deve ser 0 (Inativo) ou 1 (Ativo)'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    /**
     * Obter apenas tipologias ativas
     * 
     * @return array
     */
    public function getTipologiasAtivas()
    {
        return $this->where('status', 1)
                    ->orderBy('nome_tipologia', 'ASC')
                    ->findAll();
    }

    /**
     * Obter lista para dropdown (id => nome)
     * 
     * @param bool $apenasAtivas
     * @return array
     */
    public function getListaDropdown($apenasAtivas = true)
    {
        $builder = $this->select('id_tipologia, nome_tipologia');
        
        if ($apenasAtivas) {
            $builder->where('status', 1);
        }
        
        $tipologias = $builder->orderBy('nome_tipologia', 'ASC')->findAll();
        
        $lista = [];
        foreach ($tipologias as $tip) {
            $lista[$tip['id_tipologia']] = $tip['nome_tipologia'];
        }
        
        return $lista;
    }

    /**
     * Ativar/Desativar tipologia
     * 
     * @param int $id
     * @param int $novoStatus (0 ou 1)
     * @return bool
     */
    public function alterarStatus($id, $novoStatus)
    {
        return $this->update($id, ['status' => $novoStatus]);
    }

    /**
     * Verificar se uma tipologia já existe (por nome)
     * 
     * @param string $nome
     * @param int|null $excluirId
     * @return bool
     */
    public function tipologiaExiste($nome, $excluirId = null)
    {
        $builder = $this->where('nome_tipologia', $nome);
        
        if ($excluirId) {
            $builder->where('id_tipologia !=', $excluirId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Contar tipologias por status
     * 
     * @return array ['ativos' => X, 'inativos' => Y]
     */
    public function contarPorStatus()
    {
        return [
            'ativos'   => $this->where('status', 1)->countAllResults(),
            'inativos' => $this->where('status', 0)->countAllResults()
        ];
    }

    /**
     * Obter tipologia com contagem de cursos/turmas associados
     * (Preparado para futuras relações)
     * 
     * @param int $id
     * @return array|null
     */
    public function getTipologiaComDetalhes($id)
    {
        $tipologia = $this->find($id);
        
        if (!$tipologia) {
            return null;
        }
        
        // Aqui pode adicionar joins com outras tabelas quando necessário
        // Ex: contar cursos desta tipologia
        
        return $tipologia;
    }
}
