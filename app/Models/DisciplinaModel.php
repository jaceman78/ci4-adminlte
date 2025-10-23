<?php

namespace App\Models;

use CodeIgniter\Model;

class DisciplinaModel extends Model
{
    protected $table            = 'disciplina';
    protected $primaryKey       = 'id_disciplina';
    protected $useAutoIncrement = false; // Agora aceita IDs alfanuméricos
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_disciplina', 'abreviatura', 'descritivo', 'tipologia_id'];

    // Dates
    protected $useTimestamps = false;

    // Validation
    protected $validationRules      = [
        'id_disciplina' => 'required|max_length[50]',
        'abreviatura'   => 'required|min_length[2]|max_length[255]',
        'descritivo'    => 'permit_empty|string',
        'tipologia_id'  => 'required|integer'
    ];
    protected $validationMessages   = [
        'id_disciplina' => [
            'required'   => 'O ID da disciplina é obrigatório',
            'max_length' => 'O ID não pode exceder 50 caracteres'
        ],
        'abreviatura' => [
            'required'   => 'A abreviatura da disciplina é obrigatória',
            'min_length' => 'A abreviatura deve ter pelo menos 2 caracteres',
            'max_length' => 'A abreviatura não pode exceder 255 caracteres'
        ],
        'descritivo' => [
            'string' => 'O descritivo deve ser um texto válido'
        ],
        'tipologia_id' => [
            'required' => 'A tipologia é obrigatória',
            'integer'  => 'ID da tipologia inválido'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    /**
     * Obter disciplinas com informações da tipologia
     * 
     * @param int|null $tipologiaId
     * @return array
     */
    public function getDisciplinasComDetalhes($tipologiaId = null)
    {
        $builder = $this->select('disciplina.*, tipologia.nome_tipologia')
                        ->join('tipologia', 'tipologia.id_tipologia = disciplina.tipologia_id', 'left');
        
        if ($tipologiaId) {
            $builder->where('disciplina.tipologia_id', $tipologiaId);
        }
        
        return $builder->orderBy('disciplina.abreviatura', 'ASC')->findAll();
    }

    /**
     * Obter disciplinas por tipologia
     * 
     * @param int $tipologiaId
     * @return array
     */
    public function getDisciplinasPorTipologia($tipologiaId)
    {
        return $this->where('tipologia_id', $tipologiaId)
                    ->orderBy('abreviatura', 'ASC')
                    ->findAll();
    }

    /**
     * Obter lista para dropdown (id => abreviatura)
     * 
     * @param int|null $tipologiaId
     * @return array
     */
    public function getListaDropdown($tipologiaId = null)
    {
        $builder = $this->select('id_disciplina, abreviatura');
        
        if ($tipologiaId) {
            $builder->where('tipologia_id', $tipologiaId);
        }
        
        $disciplinas = $builder->orderBy('abreviatura', 'ASC')->findAll();
        
        $lista = [];
        foreach ($disciplinas as $disc) {
            $lista[$disc['id_disciplina']] = $disc['abreviatura'];
        }
        
        return $lista;
    }

    /**
     * Obter lista agrupada por tipologia
     * 
     * @return array
     */
    public function getListaAgrupadaPorTipologia()
    {
        $disciplinas = $this->select('disciplina.*, tipologia.nome_tipologia')
                            ->join('tipologia', 'tipologia.id_tipologia = disciplina.tipologia_id', 'left')
                            ->where('tipologia.status', 1)
                            ->orderBy('tipologia.nome_tipologia', 'ASC')
                            ->orderBy('disciplina.abreviatura', 'ASC')
                            ->findAll();
        
        $agrupado = [];
        foreach ($disciplinas as $disc) {
            $tipologia = $disc['nome_tipologia'] ?? 'Sem Tipologia';
            if (!isset($agrupado[$tipologia])) {
                $agrupado[$tipologia] = [];
            }
            $agrupado[$tipologia][] = $disc;
        }
        
        return $agrupado;
    }

    /**
     * Buscar disciplinas por abreviatura (parcial)
     * 
     * @param string $abreviatura
     * @param int|null $tipologiaId
     * @return array
     */
    public function buscarPorAbreviatura($abreviatura, $tipologiaId = null)
    {
        $builder = $this->like('abreviatura', $abreviatura);
        
        if ($tipologiaId) {
            $builder->where('tipologia_id', $tipologiaId);
        }
        
        return $builder->orderBy('abreviatura', 'ASC')->findAll();
    }

    /**
     * Verificar se disciplina já existe
     * 
     * @param string $abreviatura
     * @param int $tipologiaId
     * @param int|null $excluirId
     * @return bool
     */
    public function disciplinaExiste($abreviatura, $tipologiaId, $excluirId = null)
    {
        $builder = $this->where('abreviatura', $abreviatura)
                        ->where('tipologia_id', $tipologiaId);
        
        if ($excluirId) {
            $builder->where('id_disciplina !=', $excluirId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Atualizar carga horária
     * 
     * @param int $id
     * @param int $horas
     * @return bool
     */
    public function atualizarCargaHoraria($id, $horas)
    {
        return $this->update($id, ['horas' => $horas]);
    }

    /**
     * Contar disciplinas por tipologia
     * 
     * @param int $tipologiaId
     * @return int
     */
    public function contarPorTipologia($tipologiaId)
    {
        return $this->where('tipologia_id', $tipologiaId)->countAllResults();
    }

    /**
     * Obter estatísticas gerais
     * 
     * @return array
     */
    public function getEstatisticas()
    {
        return [
            'total'                => $this->countAllResults(false),
            'com_carga_horaria'    => $this->where('horas >', 0)->countAllResults(),
            'sem_carga_horaria'    => $this->where('horas', 0)->orWhere('horas', null)->countAllResults(),
            'por_tipologia'        => $this->select('tipologia.nome_tipologia, COUNT(*) as total')
                                           ->join('tipologia', 'tipologia.id_tipologia = disciplina.tipologia_id')
                                           ->groupBy('disciplina.tipologia_id')
                                           ->findAll()
        ];
    }

    /**
     * Obter disciplinas com carga horária definida
     * 
     * @param int|null $tipologiaId
     * @return array
     */
    public function getDisciplinasComCargaHoraria($tipologiaId = null)
    {
        $builder = $this->where('horas >', 0);
        
        if ($tipologiaId) {
            $builder->where('tipologia_id', $tipologiaId);
        }
        
        return $builder->orderBy('nome', 'ASC')->findAll();
    }
}
