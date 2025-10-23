<?php

namespace App\Models;

use CodeIgniter\Model;

class HorarioAulasModel extends Model
{
    protected $table            = 'horario_aulas';
    protected $primaryKey       = 'id_aula';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'codigo_turma',
        'disciplina_id',
        'user_nif',
        'sala_id',
        'turno',
        'dia_semana',
        'tempo',
        'intervalo',
        'hora_inicio',
        'hora_fim'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'codigo_turma'  => 'required|max_length[50]',
        'disciplina_id' => 'required|max_length[50]',
        'user_nif'      => 'required|max_length[20]',
        'sala_id'       => 'permit_empty|max_length[50]',
        'turno'         => 'permit_empty|in_list[T1,T2]',
        'dia_semana'    => 'required|integer|greater_than_equal_to[2]|less_than_equal_to[7]',
        'tempo'         => 'permit_empty|integer|greater_than_equal_to[0]',
        'intervalo'     => 'permit_empty|max_length[20]',
        'hora_inicio'   => 'required|regex_match[/^\d{2}:\d{2}(:\d{2})?$/]',
        'hora_fim'      => 'required|regex_match[/^\d{2}:\d{2}(:\d{2})?$/]'
    ];
    protected $validationMessages   = [
        'codigo_turma' => [
            'required'   => 'A turma é obrigatória',
            'max_length' => 'O código da turma não pode exceder 50 caracteres'
        ],
        'disciplina_id' => [
            'required'   => 'A disciplina é obrigatória',
            'max_length' => 'O identificador da disciplina não pode exceder 50 caracteres'
        ],
        'user_nif' => [
            'required'   => 'O professor é obrigatório',
            'max_length' => 'O NIF do professor não pode exceder 20 caracteres'
        ],
        'sala_id' => [
            'max_length' => 'O código da sala não pode exceder 50 caracteres'
        ],
        'turno' => [
            'in_list' => 'O turno deve ser T1 ou T2'
        ],
        'dia_semana' => [
            'required' => 'O dia da semana é obrigatório',
            'integer'  => 'O dia da semana deve ser numérico'
        ],
        'tempo' => [
            'integer' => 'O tempo deve ser numérico'
        ],
        'hora_inicio' => [
            'required'     => 'A hora de início é obrigatória',
            'regex_match' => 'A hora de início deve estar no formato HH:MM ou HH:MM:SS'
        ],
        'hora_fim' => [
            'required'     => 'A hora de fim é obrigatória',
            'regex_match' => 'A hora de fim deve estar no formato HH:MM ou HH:MM:SS'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    /**
     * Obter horário completo com todas as relações
     * 
     * @param int|null $idAula
     * @return array
     */
    public function getHorarioCompleto($idAula = null)
    {
        $builder = $this->select('
                horario_aulas.*,
                user.name as nome_professor,
                disciplina.abreviatura as nome_disciplina,
                disciplina.descritivo as disciplina_descritivo,
                turma.nome as nome_turma,
                turma.ano as ano_turma,
                salas.codigo_sala as codigo_sala,
                salas.descricao as sala_descricao
            ')
            ->join('user', 'user.NIF = horario_aulas.user_nif', 'left')
            ->join('disciplina', 'disciplina.id_disciplina = horario_aulas.disciplina_id', 'left')
            ->join('turma', 'turma.codigo = horario_aulas.codigo_turma', 'left')
            ->join('salas', 'salas.codigo_sala = horario_aulas.sala_id', 'left');
        
        if ($idAula) {
            return $builder->where('horario_aulas.id_aula', $idAula)->first();
        }

        return $builder->orderBy('horario_aulas.dia_semana', 'ASC')
                       ->orderBy('horario_aulas.hora_inicio', 'ASC')
                       ->findAll();
    }

    /**
     * Obter horário de um professor
     *
     * @param string   $idProfessor NIF do professor
     * @param int|null $diaSemana
     * @return array
     */
    public function getHorarioProfessor($idProfessor, $diaSemana = null)
    {
        $builder = $this->select('
                horario_aulas.*,
                disciplina.abreviatura as nome_disciplina,
                turma.nome as nome_turma,
                turma.ano as ano_turma,
                salas.codigo_sala as codigo_sala,
                salas.descricao as sala_descricao
            ')
            ->join('disciplina', 'disciplina.id_disciplina = horario_aulas.disciplina_id', 'left')
            ->join('turma', 'turma.codigo = horario_aulas.codigo_turma', 'left')
            ->join('salas', 'salas.codigo_sala = horario_aulas.sala_id', 'left')
            ->where('horario_aulas.user_nif', $idProfessor);
        
        if ($diaSemana) {
            $builder->where('horario_aulas.dia_semana', $diaSemana);
        }
        
        return $builder->orderBy('horario_aulas.dia_semana', 'ASC')
                       ->orderBy('horario_aulas.hora_inicio', 'ASC')
                       ->findAll();
    }

    /**
     * Obter horário de uma turma
     *
     * @param string   $idTurma   Código da turma
     * @param int|null $diaSemana
     * @return array
     */
    public function getHorarioTurma($idTurma, $diaSemana = null)
    {
        $builder = $this->select('
                horario_aulas.*,
                user.name as nome_professor,
                disciplina.abreviatura as nome_disciplina,
                salas.codigo_sala as codigo_sala,
                salas.descricao as sala_descricao
            ')
            ->join('user', 'user.NIF = horario_aulas.user_nif', 'left')
            ->join('disciplina', 'disciplina.id_disciplina = horario_aulas.disciplina_id', 'left')
            ->join('salas', 'salas.codigo_sala = horario_aulas.sala_id', 'left')
            ->where('horario_aulas.codigo_turma', $idTurma);
        
        if ($diaSemana) {
            $builder->where('horario_aulas.dia_semana', $diaSemana);
        }
        
        return $builder->orderBy('horario_aulas.dia_semana', 'ASC')
                       ->orderBy('horario_aulas.hora_inicio', 'ASC')
                       ->findAll();
    }

    /**
     * Obter horário de uma sala
     *
     * @param string   $idSala    Código da sala
     * @param int|null $diaSemana
     * @return array
     */
    public function getHorarioSala($idSala, $diaSemana = null)
    {
        $builder = $this->select('
                horario_aulas.*,
                user.name as nome_professor,
                disciplina.abreviatura as nome_disciplina,
                turma.nome as nome_turma,
                turma.ano as ano_turma
            ')
            ->join('user', 'user.NIF = horario_aulas.user_nif', 'left')
            ->join('disciplina', 'disciplina.id_disciplina = horario_aulas.disciplina_id', 'left')
            ->join('turma', 'turma.codigo = horario_aulas.codigo_turma', 'left')
            ->where('horario_aulas.sala_id', $idSala);
        
        if ($diaSemana) {
            $builder->where('horario_aulas.dia_semana', $diaSemana);
        }
        
        return $builder->orderBy('horario_aulas.dia_semana', 'ASC')
                       ->orderBy('horario_aulas.hora_inicio', 'ASC')
                       ->findAll();
    }

    /**
     * Verificar conflito de professor (já tem aula no mesmo horário)
     *
     * @param string   $userNif
     * @param int      $diaSemana
     * @param string   $horaInicio
     * @param string   $horaFim
     * @param int|null $excluirId
     * @return bool
     */
    public function verificarConflitoProfessor($userNif, $diaSemana, $horaInicio, $horaFim, $excluirId = null)
    {
        $builder = $this->builder();
        $builder->where('user_nif', $userNif)
                ->where('dia_semana', $diaSemana)
                ->groupStart()
                    ->where('hora_inicio <', $horaFim)
                    ->where('hora_fim >', $horaInicio)
                ->groupEnd();

        if ($excluirId) {
            $builder->where('id_aula !=', $excluirId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Verificar conflito de turma (já tem aula no mesmo horário)
     *
     * @param string   $codigoTurma
     * @param int      $diaSemana
     * @param string   $horaInicio
     * @param string   $horaFim
     * @param int|null $excluirId
     * @return bool
     */
    public function verificarConflitoTurma($codigoTurma, $diaSemana, $horaInicio, $horaFim, $excluirId = null)
    {
        $builder = $this->builder();
        $builder->where('codigo_turma', $codigoTurma)
                ->where('dia_semana', $diaSemana)
                ->groupStart()
                    ->where('hora_inicio <', $horaFim)
                    ->where('hora_fim >', $horaInicio)
                ->groupEnd();

        if ($excluirId) {
            $builder->where('id_aula !=', $excluirId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Verificar conflito de sala (já está ocupada no mesmo horário)
     *
     * @param string   $salaId
     * @param int      $diaSemana
     * @param string   $horaInicio
     * @param string   $horaFim
     * @param int|null $excluirId
     * @return bool
     */
    public function verificarConflitoSala($salaId, $diaSemana, $horaInicio, $horaFim, $excluirId = null)
    {
        $builder = $this->builder();
        $builder->where('sala_id', $salaId)
                ->where('dia_semana', $diaSemana)
                ->groupStart()
                    ->where('hora_inicio <', $horaFim)
                    ->where('hora_fim >', $horaInicio)
                ->groupEnd();

        if ($excluirId) {
            $builder->where('id_aula !=', $excluirId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Obter carga horária semanal do professor
     * 
    * @param string $userNif
     * @return int
     */
    public function getCargaHorariaProfessor($userNif)
    {
        return $this->where('user_nif', $userNif)->countAllResults();
    }

    /**
     * Obter estatísticas gerais
     * 
     * @return array
     */
    public function getEstatisticas()
    {
        return [
            'total_aulas'              => $this->countAllResults(false),
            'professores_ativos'       => $this->select('COUNT(DISTINCT user_nif) as total')
                                               ->get()->getRowArray()['total'],
            'turmas_com_aulas'         => $this->select('COUNT(DISTINCT codigo_turma) as total')
                                               ->get()->getRowArray()['total'],
            'salas_utilizadas'         => $this->select('COUNT(DISTINCT sala_id) as total')
                                               ->get()->getRowArray()['total']
        ];
    }

    /**
     * Obter mapa de nomes de dias da semana
     * 
     * @return array
     */
    public function getDiasSemana()
    {
        return [
            2 => 'Segunda-Feira',
            3 => 'Terça-Feira',
            4 => 'Quarta-Feira',
            5 => 'Quinta-Feira',
            6 => 'Sexta-Feira',
            7 => 'Sábado'
        ];
    }

    /**
     * Obter nome do dia da semana
     * 
     * @param int $dia
     * @return string
     */
    public function getNomeDiaSemana($dia)
    {
        $dias = $this->getDiasSemana();
        return $dias[$dia] ?? 'Dia Inválido';
    }
}
