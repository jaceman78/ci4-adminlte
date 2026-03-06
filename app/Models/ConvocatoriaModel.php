<?php

namespace App\Models;

use CodeIgniter\Model;

class ConvocatoriaModel extends Model
{
    protected $table = 'convocatoria';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'sessao_exame_id',
        'user_id',
        'sessao_exame_sala_id',
        'funcao',
        'estado_confirmacao',
        'presenca',
        'data_confirmacao',
        'observacoes',
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
        'sessao_exame_id' => 'required|integer|is_not_unique[sessao_exame.id]',
        'user_id' => 'required|integer|is_not_unique[user.id]',
        'sessao_exame_sala_id' => 'permit_empty|integer',
        'funcao' => 'required|in_list[Vigilante,Suplente,Coadjuvante,Júri,Verificar Calculadoras,Apoio TIC]',
        'estado_confirmacao' => 'permit_empty|in_list[Pendente,Confirmado,Rejeitado]',
    ];

    protected $validationMessages = [
        'sessao_exame_id' => [
            'required' => 'A sessão de exame é obrigatória',
            'is_not_unique' => 'Sessão de exame não encontrada',
        ],
        'user_id' => [
            'required' => 'O professor é obrigatório',
            'is_not_unique' => 'Professor não encontrado',
        ],
        'funcao' => [
            'required' => 'A função é obrigatória',
            'in_list' => 'Função inválida',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Busca convocatórias com todas as informações relacionadas
     */
    public function getWithDetails($id = null)
    {
        $this->select('
            convocatoria.*,
            user.name as professor_nome,
            user.email as professor_email,
            user.telefone as professor_telefone,
            user.NIF as professor_nif,
            salas.codigo_sala,
            salas.descricao as sala_descricao,
            sessao_exame.data_exame,
            sessao_exame.hora_exame,
            sessao_exame.duracao_minutos,
            sessao_exame.tolerancia_minutos,
            sessao_exame.fase,
            exame.codigo_prova,
            exame.nome_prova,
            exame.tipo_prova,
            exame.ano_escolaridade
        ')
        ->join('user', 'user.id = convocatoria.user_id', 'left')
        ->join('sessao_exame_sala', 'sessao_exame_sala.id = convocatoria.sessao_exame_sala_id', 'left')
        ->join('salas', 'salas.id = sessao_exame_sala.sala_id', 'left')
        ->join('sessao_exame', 'sessao_exame.id = convocatoria.sessao_exame_id', 'left')
        ->join('exame', 'exame.id = sessao_exame.exame_id', 'left');
        
        if ($id !== null) {
            return $this->find($id);
        }
        
        return $this->orderBy('sessao_exame.data_exame', 'ASC')
                    ->orderBy('sessao_exame.hora_exame', 'ASC')
                    ->findAll();
    }

    /**
     * Busca convocatórias por sessão
     */
    public function getBySessao($sessaoId)
    {
        return $this->select('
            convocatoria.*,
            user.name as professor_nome,
            user.email as professor_email,
            user.telefone as professor_telefone,
            user.NIF as professor_nif,
            salas.codigo_sala,
            salas.descricao as sala_descricao,
            sessao_exame.data_exame,
            sessao_exame.hora_exame,
            sessao_exame.duracao_minutos,
            sessao_exame.tolerancia_minutos,
            sessao_exame.fase,
            exame.codigo_prova,
            exame.nome_prova,
            exame.tipo_prova,
            exame.ano_escolaridade
        ')
        ->join('user', 'user.id = convocatoria.user_id', 'left')
        ->join('sessao_exame_sala', 'sessao_exame_sala.id = convocatoria.sessao_exame_sala_id', 'left')
        ->join('salas', 'salas.id = sessao_exame_sala.sala_id', 'left')
        ->join('sessao_exame', 'sessao_exame.id = convocatoria.sessao_exame_id', 'left')
        ->join('exame', 'exame.id = sessao_exame.exame_id', 'left')
        ->where('convocatoria.sessao_exame_id', $sessaoId)
        ->orderBy('convocatoria.funcao', 'ASC')
        ->orderBy('user.name', 'ASC')
        ->findAll();
    }

    /**
     * Busca convocatórias de um professor
     */
    public function getByProfessor($userId, $apenasAtivas = true)
    {
        $this->select('
            convocatoria.*,
            sessao_exame.data_exame,
            sessao_exame.hora_exame,
            sessao_exame.duracao_minutos,
            sessao_exame.fase,
            exame.codigo_prova,
            exame.nome_prova,
            exame.tipo_prova,
            salas.codigo_sala,
            salas.descricao as sala_descricao,
            permutas_vigilancia.id as permuta_id,
            permutas_vigilancia.estado as permuta_estado
        ')
        ->join('sessao_exame', 'sessao_exame.id = convocatoria.sessao_exame_id', 'left')
        ->join('exame', 'exame.id = sessao_exame.exame_id', 'left')
        ->join('sessao_exame_sala', 'sessao_exame_sala.id = convocatoria.sessao_exame_sala_id', 'left')
        ->join('salas', 'salas.id = sessao_exame_sala.sala_id', 'left')
        ->join('permutas_vigilancia', 'permutas_vigilancia.convocatoria_id = convocatoria.id AND permutas_vigilancia.estado NOT IN ("CANCELADO", "RECUSADO")', 'left')
        ->where('convocatoria.user_id', $userId);
        
        if ($apenasAtivas) {
            $this->where('sessao_exame.data_exame >=', date('Y-m-d'))
                 ->where('sessao_exame.ativo', 1);
        }
        
        return $this->orderBy('sessao_exame.data_exame', 'ASC')
                    ->orderBy('sessao_exame.hora_exame', 'ASC')
                    ->findAll();
    }

    /**
     * Busca convocatórias pendentes de confirmação
     */
    public function getPendentes($userId = null)
    {
        $this->select('
            convocatoria.*,
            user.name as professor_nome,
            user.email as professor_email,
            sessao_exame.data_exame,
            sessao_exame.hora_exame,
            sessao_exame.fase,
            exame.codigo_prova,
            exame.nome_prova,
            salas.codigo_sala
        ')
        ->join('user', 'user.id = convocatoria.user_id', 'left')
        ->join('sessao_exame', 'sessao_exame.id = convocatoria.sessao_exame_id', 'left')
        ->join('exame', 'exame.id = sessao_exame.exame_id', 'left')
        ->join('sessao_exame_sala', 'sessao_exame_sala.id = convocatoria.sessao_exame_sala_id', 'left')
        ->join('salas', 'salas.id = sessao_exame_sala.sala_id', 'left')
        ->where('convocatoria.estado_confirmacao', 'Pendente')
        ->where('sessao_exame.ativo', 1);
        
        if ($userId) {
            $this->where('convocatoria.user_id', $userId);
        }
        
        return $this->orderBy('sessao_exame.data_exame', 'ASC')
                    ->findAll();
    }

    /**
     * Confirma uma convocatória
     */
    public function confirmar($id, $observacoes = null)
    {
        return $this->update($id, [
            'estado_confirmacao' => 'Confirmado',
            'data_confirmacao' => date('Y-m-d H:i:s'),
            'observacoes' => $observacoes
        ]);
    }

    /**
     * Rejeita uma convocatória
     */
    public function rejeitar($id, $observacoes = null)
    {
        return $this->update($id, [
            'estado_confirmacao' => 'Rejeitado',
            'data_confirmacao' => date('Y-m-d H:i:s'),
            'observacoes' => $observacoes
        ]);
    }

    /**
     * Verifica conflitos de horário para um professor
     */
    public function hasConflitoHorario($userId, $sessaoId, $convocatoriaIdExcluir = null)
    {
        $sessaoModel = new SessaoExameModel();
        $sessao = $sessaoModel->find($sessaoId);
        
        if (!$sessao) {
            return false;
        }
        
        // Buscar outras convocatórias do professor no mesmo dia
        $builder = $this->select('convocatoria.*, sessao_exame.hora_exame, sessao_exame.duracao_minutos, sessao_exame.tolerancia_minutos')
                        ->join('sessao_exame', 'sessao_exame.id = convocatoria.sessao_exame_id', 'inner')
                        ->where('convocatoria.user_id', $userId)
                        ->where('sessao_exame.data_exame', $sessao['data_exame'])
                        ->where('sessao_exame.ativo', 1);
        
        if ($convocatoriaIdExcluir) {
            $builder->where('convocatoria.id !=', $convocatoriaIdExcluir);
        }
        
        $outras = $builder->findAll();
        
        // Calcular horário de término da nova sessão
        $horaInicio = strtotime($sessao['hora_exame']);
        $horaFim = $horaInicio + ($sessao['duracao_minutos'] + $sessao['tolerancia_minutos']) * 60;
        
        // Verificar sobreposição
        foreach ($outras as $outra) {
            $outraInicio = strtotime($outra['hora_exame']);
            $outraFim = $outraInicio + ($outra['duracao_minutos'] + $outra['tolerancia_minutos']) * 60;
            
            // Se há sobreposição
            if (($horaInicio >= $outraInicio && $horaInicio < $outraFim) ||
                ($horaFim > $outraInicio && $horaFim <= $outraFim) ||
                ($horaInicio <= $outraInicio && $horaFim >= $outraFim)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Conta convocatórias por função numa sessão
     */
    public function countByFuncao($sessaoId)
    {
        return $this->select('funcao, COUNT(*) as total')
                    ->where('sessao_exame_id', $sessaoId)
                    ->groupBy('funcao')
                    ->findAll();
    }

    /**
     * Busca convocatórias por data
     */
    public function getByData($data, $userId = null)
    {
        $this->select('
            convocatoria.*,
            user.name as professor_nome,
            sessao_exame.data_exame,
            sessao_exame.hora_exame,
            sessao_exame.duracao_minutos,
            sessao_exame.fase,
            exame.codigo_prova,
            exame.nome_prova,
            salas.codigo_sala
        ')
        ->join('user', 'user.id = convocatoria.user_id', 'left')
        ->join('sessao_exame', 'sessao_exame.id = convocatoria.sessao_exame_id', 'left')
        ->join('exame', 'exame.id = sessao_exame.exame_id', 'left')
        ->join('sessao_exame_sala', 'sessao_exame_sala.id = convocatoria.sessao_exame_sala_id', 'left')
        ->join('salas', 'salas.id = sessao_exame_sala.sala_id', 'left')
        ->where('sessao_exame.data_exame', $data)
        ->where('sessao_exame.ativo', 1);
        
        if ($userId) {
            $this->where('convocatoria.user_id', $userId);
        }
        
        return $this->orderBy('sessao_exame.hora_exame', 'ASC')
                    ->findAll();
    }

    /**
     * Estatísticas de confirmações
     */
    public function getEstatisticas($sessaoId = null)
    {
        $builder = $this->select('
            estado_confirmacao,
            COUNT(*) as total
        ');
        
        if ($sessaoId) {
            $builder->where('sessao_exame_id', $sessaoId);
        }
        
        return $builder->groupBy('estado_confirmacao')->findAll();
    }

    /**
     * Buscar convocatórias agrupadas por sessão para marcação de presenças
     */
    public function getSessionsComConvocatorias($filtros = [])
    {
        $builder = $this->db->table('sessao_exame se')
            ->select('se.*, e.codigo_prova, e.nome_prova, e.tipo_prova')
            ->join('exame e', 'e.id = se.exame_id')
            ->join('convocatoria c', 'c.sessao_exame_id = se.id')
            ->where('se.ativo', 1)
            ->groupBy('se.id')
            ->orderBy('se.data_exame', 'DESC')
            ->orderBy('se.hora_exame', 'DESC');

        // Filtros opcionais
        if (!empty($filtros['data_inicio'])) {
            $builder->where('se.data_exame >=', $filtros['data_inicio']);
        }
        if (!empty($filtros['data_fim'])) {
            $builder->where('se.data_exame <=', $filtros['data_fim']);
        }
        if (!empty($filtros['tipo_prova'])) {
            $builder->where('e.tipo_prova', $filtros['tipo_prova']);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Buscar convocatórias de uma sessão específica com dados dos professores
     */
    public function getConvocatoriasBySessaoComProfessores($sessaoId)
    {
        return $this->select('
                convocatoria.id,
                convocatoria.sessao_exame_id,
                convocatoria.sessao_exame_sala_id,
                convocatoria.user_id,
                convocatoria.funcao,
                convocatoria.presenca,
                user.name as professor_nome,
                user.telefone as professor_telefone,
                user.email as professor_email,
                salas.codigo_sala as codigo_sala
            ')
            ->join('user', 'user.id = convocatoria.user_id', 'left')
            ->join('sessao_exame_sala ses', 'ses.id = convocatoria.sessao_exame_sala_id', 'left')
            ->join('salas', 'salas.id = ses.sala_id', 'left')
            ->where('convocatoria.sessao_exame_id', $sessaoId)
            ->orderBy('convocatoria.funcao', 'ASC')
            ->orderBy('user.name', 'ASC')
            ->findAll();
    }

    /**
     * Atualizar presença de uma convocatória
     */
    public function atualizarPresenca($convocatoriaId, $presenca)
    {
        return $this->update($convocatoriaId, ['presenca' => $presenca]);
    }

    /**
     * Buscar faltas de uma sessão
     */
    public function getFaltasBySessao($sessaoId)
    {
        return $this->select('
                convocatoria.*,
                user.name as professor_nome,
                user.telefone as professor_telefone,
                user.email as professor_email,
                salas.codigo_sala as codigo_sala
            ')
            ->join('user', 'user.id = convocatoria.user_id', 'left')
            ->join('sessao_exame_sala ses', 'ses.id = convocatoria.sessao_exame_sala_id', 'left')
            ->join('salas', 'salas.id = ses.sala_id', 'left')
            ->where('convocatoria.sessao_exame_id', $sessaoId)
            ->whereIn('convocatoria.presenca', ['Falta', 'Falta Justificada'])
            ->orderBy('convocatoria.funcao', 'ASC')
            ->orderBy('user.name', 'ASC')
            ->findAll();
    }

    /**
     * Estatísticas de presenças de uma sessão
     */
    public function getEstatisticasPresencas($sessaoId)
    {
        $total = $this->where('sessao_exame_id', $sessaoId)->countAllResults();
        
        $presentes = $this->where('sessao_exame_id', $sessaoId)
            ->where('presenca', 'Presente')
            ->countAllResults();
        
        $faltas = $this->where('sessao_exame_id', $sessaoId)
            ->where('presenca', 'Falta')
            ->countAllResults();
        
        $faltasJustificadas = $this->where('sessao_exame_id', $sessaoId)
            ->where('presenca', 'Falta Justificada')
            ->countAllResults();
        
        $pendentes = $this->where('sessao_exame_id', $sessaoId)
            ->where('presenca', 'Pendente')
            ->countAllResults();

        return [
            'total' => $total,
            'presentes' => $presentes,
            'faltas' => $faltas,
            'faltas_justificadas' => $faltasJustificadas,
            'pendentes' => $pendentes
        ];
    }
}

