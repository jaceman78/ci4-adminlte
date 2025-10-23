<?php

namespace App\Models;

use CodeIgniter\Model;

class PermutasModel extends Model
{
    protected $table            = 'permutas';
    protected $primaryKey       = 'id_permuta';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_professor_original',
        'id_turma',
        'id_disciplina',
        'id_sala_original',
        'data_original',
        'id_bloco_original',
        'motivo',
        'id_professor_substituto',
        'data_nova',
        'id_bloco_novo',
        'id_sala_nova',
        'estado',
        'data_criacao',
        'data_aprovacao',
        'observacoes_aprovador'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'id_professor_original' => 'permit_empty|integer',
        'id_turma'              => 'permit_empty|integer',
        'id_disciplina'         => 'permit_empty|integer',
        'id_sala_original'      => 'permit_empty|integer',
        'data_original'         => 'required|valid_date',
        'id_bloco_original'     => 'permit_empty|integer',
        'motivo'                => 'required|min_length[3]|max_length[255]',
        'id_professor_substituto' => 'permit_empty|integer',
        'data_nova'             => 'permit_empty|valid_date',
        'id_bloco_novo'         => 'permit_empty|integer',
        'id_sala_nova'          => 'permit_empty|integer',
        'estado'                => 'required|in_list[Pendente,Aprovada,Rejeitada,Cancelada,Concluida]',
        'data_criacao'          => 'required|valid_date'
    ];
    protected $validationMessages   = [
        'data_original' => [
            'required'   => 'A data original é obrigatória',
            'valid_date' => 'Data original inválida'
        ],
        'motivo' => [
            'required'   => 'O motivo é obrigatório',
            'min_length' => 'O motivo deve ter pelo menos 3 caracteres',
            'max_length' => 'O motivo não pode exceder 255 caracteres'
        ],
        'estado' => [
            'required' => 'O estado é obrigatório',
            'in_list'  => 'Estado inválido'
        ],
        'data_criacao' => [
            'required'   => 'A data de criação é obrigatória',
            'valid_date' => 'Data de criação inválida'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setDataCriacao'];

    /**
     * Definir data de criação automaticamente
     */
    protected function setDataCriacao(array $data)
    {
        if (!isset($data['data']['data_criacao'])) {
            $data['data']['data_criacao'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Obter permuta completa com todas as relações
     * 
     * @param int|null $idPermuta
     * @return array
     */
    public function getPermutaCompleta($idPermuta = null)
    {
        $builder = $this->select('
                permutas.*,
                prof_orig.name as nome_professor_original,
                prof_subst.name as nome_professor_substituto,
                turma.nome as nome_turma,
                turma.ano as ano_turma,
                disciplina.nome as nome_disciplina,
                sala_orig.codigo_sala as codigo_sala_original,
                sala_nova.codigo_sala as codigo_sala_nova,
                bloco_orig.hora_inicio as hora_inicio_original,
                bloco_orig.hora_fim as hora_fim_original,
                bloco_orig.designacao as designacao_bloco_original,
                bloco_novo.hora_inicio as hora_inicio_nova,
                bloco_novo.hora_fim as hora_fim_nova,
                bloco_novo.designacao as designacao_bloco_novo
            ')
            ->join('users as prof_orig', 'prof_orig.id = permutas.id_professor_original', 'left')
            ->join('users as prof_subst', 'prof_subst.id = permutas.id_professor_substituto', 'left')
            ->join('turma', 'turma.id_turma = permutas.id_turma', 'left')
            ->join('disciplina', 'disciplina.id_disciplina = permutas.id_disciplina', 'left')
            ->join('salas as sala_orig', 'sala_orig.id = permutas.id_sala_original', 'left')
            ->join('salas as sala_nova', 'sala_nova.id = permutas.id_sala_nova', 'left')
            ->join('blocos_horarios as bloco_orig', 'bloco_orig.id_bloco = permutas.id_bloco_original', 'left')
            ->join('blocos_horarios as bloco_novo', 'bloco_novo.id_bloco = permutas.id_bloco_novo', 'left');
        
        if ($idPermuta) {
            return $builder->where('permutas.id_permuta', $idPermuta)->first();
        }
        
        return $builder->orderBy('permutas.data_criacao', 'DESC')->findAll();
    }

    /**
     * Obter permutas por professor
     * 
     * @param int $idProfessor
     * @param string|null $estado
     * @return array
     */
    public function getPermutasPorProfessor($idProfessor, $estado = null)
    {
        $builder = $this->where('id_professor_original', $idProfessor)
                        ->orWhere('id_professor_substituto', $idProfessor);
        
        if ($estado) {
            $builder->where('estado', $estado);
        }
        
        return $this->getPermutaCompleta()->where($builder)->findAll();
    }

    /**
     * Obter permutas por estado
     * 
     * @param string $estado
     * @return array
     */
    public function getPermutasPorEstado($estado)
    {
        return $this->where('estado', $estado)
                    ->orderBy('data_original', 'ASC')
                    ->findAll();
    }

    /**
     * Obter permutas pendentes
     * 
     * @return array
     */
    public function getPermutasPendentes()
    {
        return $this->getPermutaCompleta()
                    ->where('permutas.estado', 'Pendente')
                    ->orderBy('permutas.data_criacao', 'ASC')
                    ->findAll();
    }

    /**
     * Obter permutas por período
     * 
     * @param string $dataInicio
     * @param string $dataFim
     * @param string|null $estado
     * @return array
     */
    public function getPermutasPorPeriodo($dataInicio, $dataFim, $estado = null)
    {
        $builder = $this->where('data_original >=', $dataInicio)
                        ->where('data_original <=', $dataFim);
        
        if ($estado) {
            $builder->where('estado', $estado);
        }
        
        return $builder->orderBy('data_original', 'ASC')->findAll();
    }

    /**
     * Aprovar permuta
     * 
     * @param int $idPermuta
     * @param string|null $observacoes
     * @return bool
     */
    public function aprovarPermuta($idPermuta, $observacoes = null)
    {
        $data = [
            'estado'                 => 'Aprovada',
            'data_aprovacao'         => date('Y-m-d H:i:s'),
            'observacoes_aprovador'  => $observacoes
        ];
        
        return $this->update($idPermuta, $data);
    }

    /**
     * Rejeitar permuta
     * 
     * @param int $idPermuta
     * @param string|null $observacoes
     * @return bool
     */
    public function rejeitarPermuta($idPermuta, $observacoes = null)
    {
        $data = [
            'estado'                 => 'Rejeitada',
            'data_aprovacao'         => date('Y-m-d H:i:s'),
            'observacoes_aprovador'  => $observacoes
        ];
        
        return $this->update($idPermuta, $data);
    }

    /**
     * Cancelar permuta
     * 
     * @param int $idPermuta
     * @return bool
     */
    public function cancelarPermuta($idPermuta)
    {
        return $this->update($idPermuta, ['estado' => 'Cancelada']);
    }

    /**
     * Marcar permuta como concluída
     * 
     * @param int $idPermuta
     * @return bool
     */
    public function concluirPermuta($idPermuta)
    {
        return $this->update($idPermuta, ['estado' => 'Concluida']);
    }

    /**
     * Obter estatísticas de permutas
     * 
     * @param string|null $dataInicio
     * @param string|null $dataFim
     * @return array
     */
    public function getEstatisticas($dataInicio = null, $dataFim = null)
    {
        $builder = $this->builder();
        
        if ($dataInicio && $dataFim) {
            $builder->where('data_original >=', $dataInicio)
                    ->where('data_original <=', $dataFim);
        }
        
        $total = $builder->countAllResults(false);
        
        $porEstado = $this->select('estado, COUNT(*) as total')
                          ->groupBy('estado')
                          ->findAll();
        
        $porProfessor = $this->select('
                            users.name as professor, 
                            COUNT(*) as total_permutas
                        ')
                        ->join('users', 'users.id = permutas.id_professor_original', 'left')
                        ->groupBy('permutas.id_professor_original')
                        ->orderBy('total_permutas', 'DESC')
                        ->limit(10)
                        ->findAll();
        
        $porMotivo = $this->select('motivo, COUNT(*) as total')
                          ->groupBy('motivo')
                          ->orderBy('total', 'DESC')
                          ->limit(10)
                          ->findAll();
        
        return [
            'total'          => $total,
            'por_estado'     => $porEstado,
            'por_professor'  => $porProfessor,
            'por_motivo'     => $porMotivo
        ];
    }

    /**
     * Verificar conflito de permuta
     * (Verificar se já existe permuta para a mesma aula)
     * 
     * @param int $idProfessor
     * @param string $dataOriginal
     * @param int $idBloco
     * @param int|null $excluirId
     * @return bool
     */
    public function verificarConflitoPermuta($idProfessor, $dataOriginal, $idBloco, $excluirId = null)
    {
        $builder = $this->where('id_professor_original', $idProfessor)
                        ->where('data_original', $dataOriginal)
                        ->where('id_bloco_original', $idBloco)
                        ->whereIn('estado', ['Pendente', 'Aprovada']);
        
        if ($excluirId) {
            $builder->where('id_permuta !=', $excluirId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Obter mapa de estados
     * 
     * @return array
     */
    public function getEstados()
    {
        return [
            'Pendente'   => 'Pendente',
            'Aprovada'   => 'Aprovada',
            'Rejeitada'  => 'Rejeitada',
            'Cancelada'  => 'Cancelada',
            'Concluida'  => 'Concluída'
        ];
    }

    /**
     * Obter badge HTML para estado
     * 
     * @param string $estado
     * @return string
     */
    public function getEstadoBadge($estado)
    {
        $badges = [
            'Pendente'   => '<span class="badge bg-warning">Pendente</span>',
            'Aprovada'   => '<span class="badge bg-success">Aprovada</span>',
            'Rejeitada'  => '<span class="badge bg-danger">Rejeitada</span>',
            'Cancelada'  => '<span class="badge bg-secondary">Cancelada</span>',
            'Concluida'  => '<span class="badge bg-primary">Concluída</span>'
        ];
        
        return $badges[$estado] ?? '<span class="badge bg-secondary">' . $estado . '</span>';
    }

    /**
     * Contar permutas por estado
     * 
     * @return array
     */
    public function contarPorEstado()
    {
        $result = $this->select('estado, COUNT(*) as total')
                       ->groupBy('estado')
                       ->findAll();
        
        $contagem = [
            'Pendente'   => 0,
            'Aprovada'   => 0,
            'Rejeitada'  => 0,
            'Cancelada'  => 0,
            'Concluida'  => 0
        ];
        
        foreach ($result as $row) {
            $contagem[$row['estado']] = $row['total'];
        }
        
        return $contagem;
    }
}
