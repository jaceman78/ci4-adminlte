<?php

namespace App\Models;

use CodeIgniter\Model;

class SessaoExameModel extends Model
{
    protected $table = 'sessao_exame';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'exame_id',
        'fase',
        'data_exame',
        'hora_exame',
        'duracao_minutos',
        'tolerancia_minutos',
        'num_alunos',
        'observacoes',
        'ativo',
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
        'exame_id' => 'required|integer|is_not_unique[exame.id]',
        'fase' => 'required|max_length[50]',
        'data_exame' => 'required|valid_date',
        'hora_exame' => 'required',
        'duracao_minutos' => 'required|integer|greater_than[0]',
        'tolerancia_minutos' => 'permit_empty|integer|greater_than_equal_to[0]',
        'num_alunos' => 'permit_empty|integer|greater_than[0]',
    ];

    protected $validationMessages = [
        'exame_id' => [
            'required' => 'O exame é obrigatório',
            'is_not_unique' => 'Exame não encontrado',
        ],
        'fase' => [
            'required' => 'A fase é obrigatória',
        ],
        'data_exame' => [
            'required' => 'A data do exame é obrigatória',
            'valid_date' => 'Data inválida',
        ],
        'hora_exame' => [
            'required' => 'A hora do exame é obrigatória',
        ],
        'duracao_minutos' => [
            'required' => 'A duração é obrigatória',
            'greater_than' => 'A duração deve ser maior que zero',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Busca sessões com informações do exame
     */
    public function getWithExame($id = null)
    {
        $this->select('sessao_exame.id, sessao_exame.exame_id, sessao_exame.data_exame, 
                       sessao_exame.hora_exame, sessao_exame.duracao_minutos, sessao_exame.tolerancia_minutos,
                       sessao_exame.fase, sessao_exame.num_alunos, sessao_exame.observacoes, sessao_exame.ativo,
                       exame.codigo_prova, exame.nome_prova, exame.tipo_prova, exame.ano_escolaridade')
             ->join('exame', 'exame.id = sessao_exame.exame_id', 'left');
        
        if ($id !== null) {
            return $this->find($id);
        }
        
        return $this->where('sessao_exame.ativo', 1)
                    ->orderBy('sessao_exame.data_exame', 'ASC')
                    ->orderBy('sessao_exame.hora_exame', 'ASC')
                    ->findAll();
    }

    /**
     * Alias para getWithExame - busca uma sessão com informações do exame
     */
    public function getSessaoComExame($id)
    {
        return $this->getWithExame($id);
    }

    /**
     * Busca sessões por data
     */
    public function getByData($data)
    {
        return $this->select('sessao_exame.*, exame.codigo_prova, exame.nome_prova, exame.tipo_prova')
                    ->join('exame', 'exame.id = sessao_exame.exame_id', 'left')
                    ->where('sessao_exame.data_exame', $data)
                    ->where('sessao_exame.ativo', 1)
                    ->orderBy('sessao_exame.hora_exame', 'ASC')
                    ->findAll();
    }

    /**
     * Busca sessões por período
     */
    public function getByPeriodo($dataInicio, $dataFim)
    {
        return $this->select('sessao_exame.*, exame.codigo_prova, exame.nome_prova, exame.tipo_prova')
                    ->join('exame', 'exame.id = sessao_exame.exame_id', 'left')
                    ->where('sessao_exame.data_exame >=', $dataInicio)
                    ->where('sessao_exame.data_exame <=', $dataFim)
                    ->where('sessao_exame.ativo', 1)
                    ->orderBy('sessao_exame.data_exame', 'ASC')
                    ->orderBy('sessao_exame.hora_exame', 'ASC')
                    ->findAll();
    }

    /**
     * Busca sessões futuras
     */
    public function getSessoesFuturas($limite = null)
    {
        $this->select('sessao_exame.*, exame.codigo_prova, exame.nome_prova, exame.tipo_prova')
             ->join('exame', 'exame.id = sessao_exame.exame_id', 'left')
             ->where('sessao_exame.data_exame >=', date('Y-m-d'))
             ->where('sessao_exame.ativo', 1)
             ->orderBy('sessao_exame.data_exame', 'ASC')
             ->orderBy('sessao_exame.hora_exame', 'ASC');
        
        if ($limite) {
            $this->limit($limite);
        }
        
        return $this->findAll();
    }

    /**
     * Busca sessões por exame
     */
    public function getByExame($exameId)
    {
        return $this->where('exame_id', $exameId)
                    ->where('ativo', 1)
                    ->orderBy('data_exame', 'ASC')
                    ->orderBy('hora_exame', 'ASC')
                    ->findAll();
    }

    /**
     * Conta vigilantes necessários por sessão
     */
    public function getVigilantesNecessarios($sessaoId)
    {
        $sessao = $this->find($sessaoId);
        if (!$sessao || !$sessao['num_alunos']) {
            return ['vigilantes' => 2, 'suplentes' => 1]; // valores padrão
        }
        
        // Regra: 1 vigilante por cada 20 alunos (mínimo 2)
        $vigilantes = max(2, ceil($sessao['num_alunos'] / 20));
        $suplentes = max(1, ceil($vigilantes / 3));
        
        return [
            'vigilantes' => $vigilantes,
            'suplentes' => $suplentes,
            'total' => $vigilantes + $suplentes
        ];
    }

    /**
     * Verifica conflitos de horário
     */
    public function hasConflito($data, $horaInicio, $exameIdExcluir = null)
    {
        $builder = $this->where('data_exame', $data)
                        ->where('hora_exame', $horaInicio)
                        ->where('ativo', 1);
        
        if ($exameIdExcluir) {
            $builder->where('id !=', $exameIdExcluir);
        }
        
        return $builder->countAllResults() > 0;
    }
}
