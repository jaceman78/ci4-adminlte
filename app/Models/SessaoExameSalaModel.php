<?php

namespace App\Models;

use CodeIgniter\Model;

class SessaoExameSalaModel extends Model
{
    protected $table            = 'sessao_exame_sala';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sessao_exame_id',
        'sala_id',
        'num_alunos_sala',
        'vigilantes_necessarios',
        'observacoes'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'sessao_exame_id'   => 'required|integer|is_not_unique[sessao_exame.id]',
        'sala_id'           => 'required|integer|is_not_unique[salas.id]',
        'num_alunos_sala'   => 'required|integer|greater_than_equal_to[0]',
        'vigilantes_necessarios' => 'permit_empty|integer|greater_than_equal_to[0]',
        'observacoes'       => 'permit_empty|max_length[500]',
    ];
    
    protected $validationMessages   = [
        'sessao_exame_id' => [
            'required'         => 'A sessão de exame é obrigatória.',
            'integer'          => 'ID da sessão inválido.',
            'is_not_unique'    => 'Sessão de exame não existe.',
        ],
        'sala_id' => [
            'required'         => 'A sala é obrigatória.',
            'integer'          => 'ID da sala inválido.',
            'is_not_unique'    => 'Sala não existe.',
        ],
        'num_alunos_sala' => [
            'required'         => 'O número de alunos é obrigatório.',
            'integer'          => 'Número de alunos inválido.',
            'greater_than_equal_to' => 'Número de alunos deve ser maior ou igual a 0.',
        ],
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['calcularVigilantes'];
    protected $beforeUpdate   = ['calcularVigilantes'];

    /**
     * Calcula automaticamente o número de vigilantes necessários
     * Regra: 2 vigilantes por sala (sempre), exceto MODa (1 por 20 alunos)
     */
    protected function calcularVigilantes(array $data)
    {
        if (!isset($data['data']['num_alunos_sala'])) {
            return $data;
        }

        $sessaoExameId = $data['data']['sessao_exame_id'] ?? null;
        
        if ($sessaoExameId) {
            // Buscar o tipo de exame e a fase através da sessão
            $db = \Config\Database::connect();
            $builder = $db->table('sessao_exame se')
                ->select('e.tipo_prova, se.fase')
                ->join('exame e', 'e.id = se.exame_id')
                ->where('se.id', $sessaoExameId);
            
            $result = $builder->get()->getRowArray();
            
            if ($result) {
                // Prova Ensaio: SEMPRE 1 vigilante por sala
                if ($result['fase'] === 'Prova Ensaio') {
                    $data['data']['vigilantes_necessarios'] = 1;
                }
                // MODa: 1 vigilante por 20 alunos (mínimo 1)
                elseif ($result['tipo_prova'] === 'MODa') {
                    $numAlunos = (int) $data['data']['num_alunos_sala'];
                    $data['data']['vigilantes_necessarios'] = max(1, (int) ceil($numAlunos / 20));
                } 
                // Regra geral: SEMPRE 2 vigilantes por sala
                else {
                    $data['data']['vigilantes_necessarios'] = 2;
                }
            }
        }

        return $data;
    }

    /**
     * Busca todas as salas alocadas para uma sessão de exame com detalhes
     */
    public function getSalasBySessao($sessaoExameId, $withDetails = true)
    {
        $builder = $this->select('sessao_exame_sala.*')
            ->where('sessao_exame_id', $sessaoExameId);

        if ($withDetails) {
            $builder->select('salas.codigo_sala as sala_nome')
                ->join('salas', 'salas.id = sessao_exame_sala.sala_id');
        }

        return $builder->findAll();
    }

    /**
     * Busca sala específica com contagem de vigilantes alocados
     */
    public function getSalaComVigilantes($salaId)
    {
        $sala = $this->select('sessao_exame_sala.*')
            ->select('salas.codigo_sala as sala_nome')
            ->select('(SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_sala_id = sessao_exame_sala.id AND funcao = "Vigilante" AND deleted_at IS NULL) as vigilantes_alocados')
            ->join('salas', 'salas.id = sessao_exame_sala.sala_id')
            ->find($salaId);

        if ($sala) {
            $sala['vigilantes_em_falta'] = max(0, $sala['vigilantes_necessarios'] - $sala['vigilantes_alocados']);
        }

        return $sala;
    }

    /**
     * Lista todas as salas de uma sessão com estatísticas de alocação
     */
    public function getSalasComEstatisticas($sessaoExameId)
    {
        return $this->select('sessao_exame_sala.*')
            ->select('salas.codigo_sala as sala_nome')
            ->select('(SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_sala_id = sessao_exame_sala.id AND funcao = "Vigilante" AND deleted_at IS NULL) as vigilantes_alocados')
            ->select('CASE 
                WHEN exame.tipo_prova IN (\'Suplentes\', \'Verificacao Calculadoras\', \'Apoio TIC\') THEN 0
                ELSE GREATEST(0, sessao_exame_sala.vigilantes_necessarios - (SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_sala_id = sessao_exame_sala.id AND funcao = "Vigilante" AND deleted_at IS NULL))
            END as vigilantes_em_falta')
            ->join('salas', 'salas.id = sessao_exame_sala.sala_id')
            ->join('sessao_exame', 'sessao_exame.id = sessao_exame_sala.sessao_exame_id')
            ->join('exame', 'exame.id = sessao_exame.exame_id')
            ->where('sessao_exame_sala.sessao_exame_id', $sessaoExameId)
            ->orderBy('salas.codigo_sala', 'ASC')
            ->findAll();
    }

    /**
     * Verifica se uma sala já está alocada a uma sessão
     */
    public function salaJaAlocada($sessaoExameId, $salaId, $excludeId = null)
    {
        $builder = $this->where([
            'sessao_exame_id' => $sessaoExameId,
            'sala_id'         => $salaId,
        ]);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Calcula total de alunos em todas as salas de uma sessão
     */
    public function getTotalAlunosSessao($sessaoExameId)
    {
        $result = $this->selectSum('num_alunos_sala', 'total')
            ->where('sessao_exame_id', $sessaoExameId)
            ->first();

        return $result['total'] ?? 0;
    }

    /**
     * Calcula total de alunos alocados em uma sessão, excluindo opcionalmente um registro
     * Usado para validação ao criar/editar alocações
     */
    public function getTotalAlunosAlocados($sessaoExameId, $excluirId = null)
    {
        $builder = $this->selectSum('num_alunos_sala', 'total')
            ->where('sessao_exame_id', $sessaoExameId);

        if ($excluirId) {
            $builder->where('id !=', $excluirId);
        }

        $result = $builder->first();
        return $result['total'] ?? 0;
    }

    /**
     * Calcula total de vigilantes necessários para uma sessão
     */
    public function getTotalVigilantesNecessarios($sessaoExameId)
    {
        $result = $this->selectSum('vigilantes_necessarios', 'total')
            ->where('sessao_exame_id', $sessaoExameId)
            ->first();

        return $result['total'] ?? 0;
    }

    /**
     * Remove todas as salas de uma sessão (usado ao excluir sessão)
     */
    public function removeSalasDaSessao($sessaoExameId)
    {
        return $this->where('sessao_exame_id', $sessaoExameId)->delete();
    }

    /**
     * Verifica se sala tem capacidade suficiente para os alunos
     * DESATIVADO: A distribuição dos alunos é feita manualmente pelo secretariado
     */
    /*
    public function verificarCapacidade($salaId, $numAlunos)
    {
        $db = \Config\Database::connect();
        $sala = $db->table('salas')->select('capacidade')->where('id', $salaId)->get()->getRowArray();

        if (!$sala) {
            return false;
        }

        return $numAlunos <= $sala['capacidade'];
    }
    */

    /**
     * Busca salas disponíveis (não alocadas) para uma sessão
     */
    public function getSalasDisponiveis($sessaoExameId)
    {
        $db = \Config\Database::connect();
        
        return $db->table('salas')
            ->select('salas.*')
            ->where('salas.id NOT IN (SELECT sala_id FROM sessao_exame_sala WHERE sessao_exame_id = ' . (int)$sessaoExameId . ' AND deleted_at IS NULL)', null, false)
            ->orderBy('salas.codigo_sala', 'ASC')
            ->get()
            ->getResultArray();
    }
}
