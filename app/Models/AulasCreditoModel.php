<?php

namespace App\Models;

use CodeIgniter\Model;

class AulasCreditoModel extends Model
{
    protected $table            = 'aulas_credito';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'professor_nif',
        'codigo_turma',
        'disciplina_id',
        'turno',
        'data_visita',
        'origem',
        'ano_letivo_id',
        'estado',
        'usado_em_permuta_id',
        'data_uso',
        'observacoes',
        'criado_por_user_id',
        'cancelado_por_user_id',
        'motivo_cancelamento'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'professor_nif'      => 'required|max_length[20]',
        'codigo_turma'       => 'required|max_length[20]',
        'disciplina_id'      => 'required|max_length[100]',
        'data_visita'        => 'required|valid_date',
        'origem'             => 'required|max_length[255]',
        'ano_letivo_id'      => 'required|integer',
        'estado'             => 'in_list[disponivel,usado,expirado,cancelado]',
        'criado_por_user_id' => 'required|integer',
    ];

    protected $validationMessages   = [
        'professor_nif' => [
            'required' => 'O NIF do professor é obrigatório',
        ],
        'codigo_turma' => [
            'required' => 'O código da turma é obrigatório',
        ],
        'disciplina_id' => [
            'required' => 'A disciplina é obrigatória',
        ],
        'origem' => [
            'required' => 'A origem/descrição é obrigatória',
        ],
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
     * Obter créditos disponíveis de um professor
     */
    public function getCreditosDisponiveis($professorNif, $anoLetivoId = null)
    {
        $builder = $this->select('aulas_credito.*, 
                                 disciplina.descritivo as disciplina_nome,
                                 disciplina.abreviatura as disciplina_abrev,
                                 turma.nome as turma_nome,
                                 ano_letivo.anoletivo')
                        ->join('disciplina', 'disciplina.descritivo = aulas_credito.disciplina_id', 'left')
                        ->join('turma', 'turma.codigo = aulas_credito.codigo_turma', 'left')
                        ->join('ano_letivo', 'ano_letivo.id_anoletivo = aulas_credito.ano_letivo_id', 'left')
                        ->where('aulas_credito.professor_nif', $professorNif)
                        ->where('aulas_credito.estado', 'disponivel');

        if ($anoLetivoId) {
            $builder->where('aulas_credito.ano_letivo_id', $anoLetivoId);
        }

        return $builder->orderBy('aulas_credito.data_visita', 'DESC')
                       ->findAll();
    }

    /**
     * Obter créditos usados de um professor
     */
    public function getCreditosUsados($professorNif, $anoLetivoId = null)
    {
        $builder = $this->select('aulas_credito.*, 
                                 disciplina.descritivo as disciplina_nome,
                                 disciplina.abreviatura as disciplina_abrev,
                                 turma.nome as turma_nome,
                                 permutas.id as permuta_id,
                                 permutas.data_aula_original,
                                 permutas.estado as permuta_estado')
                        ->join('disciplina', 'disciplina.descritivo = aulas_credito.disciplina_id', 'left')
                        ->join('turma', 'turma.codigo = aulas_credito.codigo_turma', 'left')
                        ->join('permutas', 'permutas.id = aulas_credito.usado_em_permuta_id', 'left')
                        ->where('aulas_credito.professor_nif', $professorNif)
                        ->where('aulas_credito.estado', 'usado');

        if ($anoLetivoId) {
            $builder->where('aulas_credito.ano_letivo_id', $anoLetivoId);
        }

        return $builder->orderBy('aulas_credito.data_uso', 'DESC')
                       ->findAll();
    }

    /**
     * Contar créditos disponíveis de um professor
     */
    public function contarCreditosDisponiveis($professorNif, $anoLetivoId = null)
    {
        $builder = $this->where('professor_nif', $professorNif)
                        ->where('estado', 'disponivel');

        if ($anoLetivoId) {
            $builder->where('ano_letivo_id', $anoLetivoId);
        }

        return $builder->countAllResults();
    }

    /**
     * Buscar crédito específico para usar em permuta
     * Busca por turma, disciplina e turno compatíveis
     */
    public function buscarCreditoParaPermuta($professorNif, $codigoTurma, $disciplinaId, $turno = null, $anoLetivoId = null)
    {
        $builder = $this->where('professor_nif', $professorNif)
                        ->where('codigo_turma', $codigoTurma)
                        ->where('disciplina_id', $disciplinaId)
                        ->where('estado', 'disponivel');

        // Se tem turno, buscar exato ou NULL (genérico)
        if ($turno) {
            $builder->groupStart()
                    ->where('turno', $turno)
                    ->orWhere('turno', null)
                    ->groupEnd();
        } else {
            $builder->where('turno', null);
        }

        if ($anoLetivoId) {
            $builder->where('ano_letivo_id', $anoLetivoId);
        }

        return $builder->orderBy('data_visita', 'ASC') // Usar créditos mais antigos primeiro
                       ->first();
    }

    /**
     * Marcar crédito como usado
     */
    public function marcarComoUsado($creditoId, $permutaId)
    {
        return $this->update($creditoId, [
            'estado'              => 'usado',
            'usado_em_permuta_id' => $permutaId,
            'data_uso'            => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Cancelar crédito
     */
    public function cancelarCredito($creditoId, $userId, $motivo)
    {
        return $this->update($creditoId, [
            'estado'                 => 'cancelado',
            'cancelado_por_user_id'  => $userId,
            'motivo_cancelamento'    => $motivo,
        ]);
    }

    /**
     * Expirar créditos de anos letivos anteriores
     */
    public function expirarCreditosAntigos($anoLetivoAtualId)
    {
        return $this->where('ano_letivo_id !=', $anoLetivoAtualId)
                    ->where('estado', 'disponivel')
                    ->set(['estado' => 'expirado'])
                    ->update();
    }

    /**
     * Criar múltiplos créditos de uma vez (visita de estudo com várias aulas)
     */
    public function criarMultiplosCreditos($dados, $quantidade)
    {
        $registos = [];
        for ($i = 0; $i < $quantidade; $i++) {
            $registos[] = $dados;
        }

        return $this->insertBatch($registos);
    }

    /**
     * Relatório de créditos por professor
     */
    public function relatorioCreditosPorProfessor($anoLetivoId = null)
    {
        $builder = $this->select('aulas_credito.professor_nif,
                                 user.name as professor_nome,
                                 COUNT(CASE WHEN aulas_credito.estado = "disponivel" THEN 1 END) as total_disponiveis,
                                 COUNT(CASE WHEN aulas_credito.estado = "usado" THEN 1 END) as total_usados,
                                 COUNT(CASE WHEN aulas_credito.estado = "expirado" THEN 1 END) as total_expirados,
                                 COUNT(CASE WHEN aulas_credito.estado = "cancelado" THEN 1 END) as total_cancelados')
                        ->join('user', 'user.NIF = aulas_credito.professor_nif', 'left')
                        ->groupBy('aulas_credito.professor_nif, user.name');

        if ($anoLetivoId) {
            $builder->where('aulas_credito.ano_letivo_id', $anoLetivoId);
        }

        return $builder->orderBy('user.name', 'ASC')
                       ->findAll();
    }

    /**
     * Validar se professor leciona a turma/disciplina
     */
    public function validarProfessorLecionaTurmaDisciplina($professorNif, $codigoTurma, $disciplinaId, $turno = null)
    {
        $horarioModel = new \App\Models\HorarioAulasModel();
        
        $builder = $horarioModel->where('user_nif', $professorNif)
                                ->where('codigo_turma', $codigoTurma)
                                ->where('disciplina_id', $disciplinaId);

        if ($turno) {
            $builder->where('turno', $turno);
        }

        return $builder->countAllResults() > 0;
    }
}
