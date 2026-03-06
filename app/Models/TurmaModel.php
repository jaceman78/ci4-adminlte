<?php

namespace App\Models;

use CodeIgniter\Model;

class TurmaModel extends Model
{
    protected $table            = 'turma';
    protected $primaryKey       = 'id_turma';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'codigo',
        'abreviatura',
        'descritivo',
        'ano',
        'nome',
        'num_alunos',
        'secretario_nif',
        'escola_id',
        'dir_turma_nif',
        'anoletivo_id',
        'tipologia_id'
    ];

    // Dates
    protected $useTimestamps = false;

    // Validation
    protected $validationRules      = [
        'codigo'         => 'required|max_length[50]',
        'abreviatura'    => 'required|min_length[1]|max_length[100]',
        'descritivo'     => 'permit_empty|max_length[255]',
        'ano'            => 'permit_empty|integer|in_list[0,1,2,3,4,5,6,7,8,9,10,11,12]',
        'nome'           => 'required|min_length[1]|max_length[63]',
        'num_alunos'     => 'permit_empty|integer|greater_than_equal_to[0]',
        'secretario_nif' => 'permit_empty|max_length[20]',
        'escola_id'      => 'permit_empty|integer',
        'dir_turma_nif'  => 'permit_empty|max_length[20]',
        'anoletivo_id'   => 'permit_empty|integer',
        'tipologia_id'   => 'permit_empty|integer'
    ];
    protected $validationMessages   = [
        'codigo' => [
            'required'   => 'O código da turma é obrigatório',
            'max_length' => 'O código não pode exceder 50 caracteres'
        ],
        'abreviatura' => [
            'required'   => 'A abreviatura da turma é obrigatória',
            'min_length' => 'A abreviatura deve ter pelo menos 1 caractere',
            'max_length' => 'A abreviatura não pode exceder 100 caracteres'
        ],
        'descritivo' => [
            'max_length' => 'O descritivo não pode exceder 255 caracteres'
        ],
        'ano' => [
            'integer'  => 'O ano de escolaridade deve ser um número',
            'in_list'  => 'O ano deve estar entre 0 (Pré-escolar) e 12'
        ],
        'nome' => [
            'required'   => 'O nome da turma é obrigatório',
            'min_length' => 'O nome deve ter pelo menos 1 caractere',
            'max_length' => 'O nome não pode exceder 63 caracteres'
        ],
        'num_alunos' => [
            'integer'               => 'O número de alunos deve ser um número',
            'greater_than_equal_to' => 'O número de alunos não pode ser negativo'
        ],
        'secretario_nif' => [
            'max_length' => 'O NIF do secretário não pode exceder 20 caracteres'
        ],
        'escola_id' => [
            'integer' => 'ID da escola inválido'
        ],
        'dir_turma_nif' => [
            'max_length' => 'O NIF do diretor de turma não pode exceder 20 caracteres'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    /**
     * Obter turmas com informações relacionadas
     * 
     * @param int|null $anoLetivoId
     * @return array
     */
    public function getTurmasComDetalhes($anoLetivoId = null)
    {
        $builder = $this->select('turma.*, 
                                  ano_letivo.anoletivo, 
                                  tipologia.nome_tipologia,
                                  escolas.nome as nome_escola,
                                  dt.name as nome_dt,
                                  sec.name as nome_secretario')
                        ->join('ano_letivo', 'ano_letivo.id_anoletivo = turma.anoletivo_id', 'left')
                        ->join('tipologia', 'tipologia.id_tipologia = turma.tipologia_id', 'left')
                        ->join('escolas', 'escolas.id = turma.escola_id', 'left')
                        ->join('user as dt', 'dt.NIF = turma.dir_turma_nif AND turma.dir_turma_nif IS NOT NULL AND turma.dir_turma_nif != "" AND turma.dir_turma_nif != "0"', 'left')
                        ->join('user as sec', 'sec.NIF = turma.secretario_nif AND turma.secretario_nif IS NOT NULL AND turma.secretario_nif != "" AND turma.secretario_nif != "0"', 'left');
        
        if ($anoLetivoId) {
            $builder->where('turma.anoletivo_id', $anoLetivoId);
        }
        
        return $builder->orderBy('turma.ano', 'ASC')
                       ->orderBy('turma.nome', 'ASC')
                       ->findAll();
    }

    /**
     * Obter turmas por ano letivo
     * 
     * @param int $anoLetivoId
     * @return array
     */
    public function getTurmasPorAnoLetivo($anoLetivoId)
    {
        return $this->where('anoletivo_id', $anoLetivoId)
                    ->orderBy('ano', 'ASC')
                    ->orderBy('nome', 'ASC')
                    ->findAll();
    }

    /**
     * Obter turmas por ano de escolaridade
     * 
     * @param int $ano
     * @param int|null $anoLetivoId
     * @return array
     */
    public function getTurmasPorAno($ano, $anoLetivoId = null)
    {
        $builder = $this->where('ano', $ano);
        
        if ($anoLetivoId) {
            $builder->where('anoletivo_id', $anoLetivoId);
        }
        
        return $builder->orderBy('nome', 'ASC')->findAll();
    }

    /**
     * Obter turmas por tipologia
     * 
     * @param int $tipologiaId
     * @param int|null $anoLetivoId
     * @return array
     */
    public function getTurmasPorTipologia($tipologiaId, $anoLetivoId = null)
    {
        $builder = $this->where('tipologia_id', $tipologiaId);
        
        if ($anoLetivoId) {
            $builder->where('anoletivo_id', $anoLetivoId);
        }
        
        return $builder->orderBy('ano', 'ASC')
                       ->orderBy('nome', 'ASC')
                       ->findAll();
    }

    /**
     * Obter lista para dropdown (id => nome completo)
     * 
     * @param int|null $anoLetivoId
     * @param int|null $tipologiaId
     * @return array
     */
    public function getListaDropdown($anoLetivoId = null, $tipologiaId = null)
    {
        $builder = $this->select('id_turma, ano, nome');
        
        if ($anoLetivoId) {
            $builder->where('anoletivo_id', $anoLetivoId);
        }
        
        if ($tipologiaId) {
            $builder->where('tipologia_id', $tipologiaId);
        }
        
        $turmas = $builder->orderBy('ano', 'ASC')
                         ->orderBy('nome', 'ASC')
                         ->findAll();
        
        $lista = [];
        foreach ($turmas as $turma) {
            $anoLabel = $turma['ano'] == 0 ? 'Pré' : $turma['ano'] . 'º';
            $lista[$turma['id_turma']] = $anoLabel . ' - ' . $turma['nome'];
        }
        
        return $lista;
    }

    /**
     * Obter lista para dropdown usando o código da turma como chave
     *
     * @param int|null $anoLetivoId
     * @param int|null $tipologiaId
     * @return array
     */
    public function getListaDropdownPorCodigo($anoLetivoId = null, $tipologiaId = null)
    {
    $builder = $this->select('codigo, ano, nome');

    $builder->where('codigo IS NOT NULL', null, false)
        ->where('codigo !=', '');

        if ($anoLetivoId) {
            $builder->where('anoletivo_id', $anoLetivoId);
        }

        if ($tipologiaId) {
            $builder->where('tipologia_id', $tipologiaId);
        }

        $turmas = $builder->orderBy('ano', 'ASC')
                          ->orderBy('nome', 'ASC')
                          ->findAll();

        $lista = [];
        foreach ($turmas as $turma) {
            $codigo = trim((string) $turma['codigo']);
            $anoLabel = $turma['ano'] == 0 ? 'Pré' : $turma['ano'] . 'º';
            $lista[$codigo] = $codigo . ' | ' . $anoLabel . ' - ' . $turma['nome'];
        }

        return $lista;
    }

    /**
     * Atribuir diretor de turma
     * 
     * @param int $turmaId
     * @param int $diretorId
     * @return bool
     */
    public function atribuirDiretorTurma($turmaId, $diretorNif)
    {
        return $this->update($turmaId, ['dir_turma_nif' => $diretorNif]);
    }

    /**
     * Remover diretor de turma
     * 
     * @param int $turmaId
     * @return bool
     */
    public function removerDiretorTurma($turmaId)
    {
        return $this->update($turmaId, ['dir_turma_nif' => null]);
    }

    /**
     * Contar turmas por ano letivo
     * 
     * @param int $anoLetivoId
     * @return int
     */
    public function contarPorAnoLetivo($anoLetivoId)
    {
        return $this->where('anoletivo_id', $anoLetivoId)->countAllResults();
    }

    /**
     * Contar turmas por tipologia
     * 
     * @param int $tipologiaId
     * @param int|null $anoLetivoId
     * @return int
     */
    public function contarPorTipologia($tipologiaId, $anoLetivoId = null)
    {
        $builder = $this->where('tipologia_id', $tipologiaId);
        
        if ($anoLetivoId) {
            $builder->where('anoletivo_id', $anoLetivoId);
        }
        
        return $builder->countAllResults();
    }

    /**
     * Verificar se turma já existe
     * 
     * @param int $ano
     * @param string $nome
     * @param int $anoLetivoId
     * @param int|null $excluirId
     * @return bool
     */
    public function turmaExiste($ano, $nome, $anoLetivoId, $excluirId = null)
    {
        $builder = $this->where('ano', $ano)
                        ->where('nome', $nome)
                        ->where('anoletivo_id', $anoLetivoId);
        
        if ($excluirId) {
            $builder->where('id_turma !=', $excluirId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Obter estatísticas gerais
     * 
     * @param int|null $anoLetivoId
     * @return array
     */
    public function getEstatisticas($anoLetivoId = null)
    {
        $builder = $this->select('
            COUNT(*) as total_turmas,
            COUNT(DISTINCT ano) as total_anos,
            COUNT(CASE WHEN dir_turma_nif IS NOT NULL THEN 1 END) as turmas_com_diretor,
            COUNT(CASE WHEN dir_turma_nif IS NULL THEN 1 END) as turmas_sem_diretor,
            SUM(num_alunos) as total_alunos,
            AVG(num_alunos) as media_alunos_turma
        ');
        
        if ($anoLetivoId) {
            $builder->where('anoletivo_id', $anoLetivoId);
        }
        
        return $builder->get()->getRowArray();
    }
}
