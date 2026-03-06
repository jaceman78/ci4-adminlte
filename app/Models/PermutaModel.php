<?php

namespace App\Models;

use CodeIgniter\Model;

class PermutaModel extends Model
{
    protected $table            = 'permutas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'aula_original_id',
        'data_aula_original',
        'data_aula_permutada',
        'professor_autor_nif',
        'professor_substituto_nif',
        'sala_permutada_id',
        'bloco_reposicao_id',
        'grupo_permuta',
        'estado',
        'observacoes',
        'motivo_rejeicao',
        'aprovada_por_user_id',
        'data_aprovacao'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'aula_original_id'          => 'required|integer',
        'data_aula_original'        => 'required|valid_date',
        'data_aula_permutada'       => 'required|valid_date',
        'professor_autor_nif'       => 'permit_empty|integer',
        'professor_substituto_nif'  => 'permit_empty|integer',
        'sala_permutada_id'         => 'permit_empty|max_length[50]',
        'estado'                    => 'required|in_list[pendente,aprovada,rejeitada,cancelada]',
        'observacoes'               => 'permit_empty',
    ];

    protected $validationMessages = [
        'aula_original_id' => [
            'required' => 'A aula original é obrigatória.',
            'integer'  => 'ID da aula inválido.'
        ],
        'data_aula_original' => [
            'required'   => 'A data da aula original é obrigatória.',
            'valid_date' => 'Data inválida.'
        ],
        'data_aula_permutada' => [
            'required'   => 'A data da aula permutada é obrigatória.',
            'valid_date' => 'Data inválida.'
        ],
        'professor_autor_nif' => [
            'integer' => 'NIF do professor autor inválido.'
        ],
        'professor_substituto_nif' => [
            'integer' => 'NIF do professor substituto inválido.'
        ],
        'estado' => [
            'required' => 'O estado é obrigatório.',
            'in_list'  => 'Estado inválido.'
        ]
    ];

    /**
     * Obter permutas de um professor (como autor ou substituto)
     */
    public function getPermutasProfessor($professorNif, $estado = null)
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, 
                         ha.codigo_turma, ha.disciplina_id, ha.dia_semana, ha.hora_inicio, ha.hora_fim, ha.intervalo,
                         d.abreviatura as disciplina_abrev, d.descritivo as disciplina_nome,
                         t.nome as turma_nome, t.ano,
                         u_autor.name as professor_autor_nome, u_autor.email as professor_autor_email,
                         u_subst.name as professor_substituto_nome, u_subst.email as professor_substituto_email,
                         s.codigo_sala, s.descricao as sala_descricao');
        $builder->join('horario_aulas ha', 'ha.id_aula = p.aula_original_id', 'left');
        $builder->join('disciplina d', 'd.descritivo = ha.disciplina_id', 'left');
        $builder->join('turma t', 't.codigo = ha.codigo_turma', 'left');
        $builder->join('user u_autor', 'u_autor.NIF = p.professor_autor_nif', 'left');
        $builder->join('user u_subst', 'u_subst.NIF = p.professor_substituto_nif', 'left');
        $builder->join('salas s', 's.codigo_sala = p.sala_permutada_id', 'left');
        $builder->where('(p.professor_autor_nif = ' . $this->db->escape($professorNif) . 
                       ' OR p.professor_substituto_nif = ' . $this->db->escape($professorNif) . ')');
        
        if ($estado !== null) {
            $builder->where('p.estado', $estado);
        }
        
        $builder->orderBy('p.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obter permutas por grupo
     */
    public function getPermutasPorGrupo($grupoId)
    {
        return $this->where('grupo_permuta', $grupoId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Gerar ID único para grupo de permutas
     */
    public function gerarGrupoPermuta()
    {
        return 'GP_' . date('YmdHis') . '_' . uniqid();
    }

    /**
     * Aprovar permuta
     */
    public function aprovarPermuta($permutaId, $userId)
    {
        $data = [
            'estado' => 'aprovada',
            'aprovada_por_user_id' => $userId,
            'data_aprovacao' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($permutaId, $data);
    }

    /**
     * Rejeitar permuta
     */
    public function rejeitarPermuta($permutaId, $userId, $motivo = null)
    {
        $data = [
            'estado' => 'rejeitada',
            'aprovada_por_user_id' => $userId,
            'data_aprovacao' => date('Y-m-d H:i:s'),
            'motivo_rejeicao' => $motivo
        ];
        
        return $this->update($permutaId, $data);
    }

    /**
     * Cancelar permuta
     */
    public function cancelarPermuta($permutaId)
    {
        return $this->update($permutaId, ['estado' => 'cancelada']);
    }

    /**
     * Obter estatísticas de permutas de um professor
     */
    public function getEstatisticasProfessor($professorNif)
    {
        $stats = [
            'pendentes' => $this->where('professor_autor_nif', $professorNif)
                                ->where('estado', 'pendente')
                                ->countAllResults(),
            'aprovadas' => $this->where('professor_autor_nif', $professorNif)
                                ->where('estado', 'aprovada')
                                ->countAllResults(),
            'rejeitadas' => $this->where('professor_autor_nif', $professorNif)
                                 ->where('estado', 'rejeitada')
                                 ->countAllResults(),
            'como_substituto' => $this->where('professor_substituto_nif', $professorNif)
                                      ->where('professor_autor_nif !=', $professorNif)
                                      ->where('estado', 'aprovada')
                                      ->countAllResults()
        ];
        
        return $stats;
    }

    /**
     * Verificar se existe conflito de horário na data da permuta
     */
    public function verificarConflitoPermuta($professorNif, $dataPermuta, $horaInicio, $horaFim)
    {
        // Verificar se o professor já tem aulas agendadas nessa data e hora
        $builder = $this->db->table('horario_aulas ha');
        $builder->join('permutas p', 'p.aula_original_id = ha.id_aula AND p.data_aula_permutada = ' . $this->db->escape($dataPermuta) . ' AND p.estado = "aprovada"', 'left');
        $builder->where('ha.user_nif', $professorNif);
        $builder->where('(ha.hora_inicio < ' . $this->db->escape($horaFim) . ' AND ha.hora_fim > ' . $this->db->escape($horaInicio) . ')');
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Obter detalhes completos de uma permuta
     */
    public function getDetalhesPermuta($permutaId)
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, 
                         p.data_aula_original, p.data_aula_permutada,
                         ha.codigo_turma, ha.disciplina_id, ha.dia_semana, ha.hora_inicio, ha.hora_fim, ha.intervalo, ha.sala_id as sala_original_id,
                         d.abreviatura as disciplina_abrev, d.descritivo as disciplina_nome,
                         t.nome as turma_nome, t.ano,
                         u_autor.name as professor_autor_nome, u_autor.email as professor_autor_email, u_autor.NIF as professor_autor_nif,
                         u_subst.name as professor_substituto_nome, u_subst.email as professor_substituto_email, u_subst.NIF as professor_substituto_nif,
                         s_orig.codigo_sala as sala_original_codigo, s_orig.descricao as sala_original_descricao,
                         s_perm.codigo_sala as sala_permutada_codigo, s_perm.descricao as sala_permutada_descricao,
                         bh.designacao as bloco_designacao, bh.hora_inicio as bloco_hora_inicio, bh.hora_fim as bloco_hora_fim,
                         u_aprov.name as aprovador_nome');
        $builder->join('horario_aulas ha', 'ha.id_aula = p.aula_original_id', 'left');
        $builder->join('disciplina d', 'd.descritivo = ha.disciplina_id', 'left');
        $builder->join('turma t', 't.codigo = ha.codigo_turma', 'left');
        $builder->join('user u_autor', 'u_autor.NIF = p.professor_autor_nif', 'left');
        $builder->join('user u_subst', 'u_subst.NIF = p.professor_substituto_nif', 'left');
        $builder->join('salas s_orig', 's_orig.codigo_sala = ha.sala_id', 'left');
        $builder->join('salas s_perm', 's_perm.codigo_sala = p.sala_permutada_id', 'left');
        $builder->join('blocos_horarios bh', 'bh.id_bloco = p.bloco_reposicao_id', 'left');
        $builder->join('user u_aprov', 'u_aprov.id = p.aprovada_por_user_id', 'left');
        $builder->where('p.id', $permutaId);
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Buscar todas as permutas do mesmo grupo
     */
    public function getPermutasDoGrupo($grupoPermuta)
    {
        if (empty($grupoPermuta)) {
            return [];
        }
        
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, 
                         ha.codigo_turma, ha.disciplina_id, ha.hora_inicio, ha.hora_fim,
                         d.abreviatura as disciplina_abrev, d.descritivo as disciplina_nome,
                         bh.designacao as bloco_designacao, bh.hora_inicio as bloco_hora_inicio, bh.hora_fim as bloco_hora_fim,
                         s_perm.codigo_sala as sala_permutada_codigo');
        $builder->join('horario_aulas ha', 'ha.id_aula = p.aula_original_id', 'left');
        $builder->join('disciplina d', 'd.descritivo = ha.disciplina_id', 'left');
        $builder->join('blocos_horarios bh', 'bh.id_bloco = p.bloco_reposicao_id', 'left');
        $builder->join('salas s_perm', 's_perm.codigo_sala = p.sala_permutada_id', 'left');
        $builder->where('p.grupo_permuta', $grupoPermuta);
        $builder->orderBy('ha.hora_inicio', 'ASC');
        
        return $builder->get()->getResultArray();
    }
}
