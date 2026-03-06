<?php
namespace App\Models;

use CodeIgniter\Model;

class ReparacoesExternasModel extends Model
{
    protected $table = 'reparacoes_externas';
    protected $primaryKey = 'id_reparacao';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'n_serie_equipamento',
        'tipologia',
        'possivel_avaria',
        'descricao_avaria',
        'data_envio',
        'empresa_reparacao',
        'n_guia',
        'trabalho_efetuado',
        'custo',
        'data_recepcao',
        'observacoes',
        'estado',
        'id_tecnico',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'n_serie_equipamento' => 'required|min_length[3]|max_length[255]',
        'tipologia' => 'required|in_list[Tipo I,Tipo II,Tipo III]',
        'possivel_avaria' => 'required|in_list[Teclado,Monitor,Bateria,Disco,Sistema Operativo,CUCo,Gráfica,Outro]',
        'data_envio' => 'required|valid_date',
        'empresa_reparacao' => 'required|min_length[2]|max_length[255]',
        'estado' => 'required|in_list[enviado,em_reparacao,reparado,irreparavel,cancelado]',
        'custo' => 'permit_empty|decimal',
        'data_recepcao' => 'permit_empty|valid_date'
    ];

    protected $validationMessages = [
        'n_serie_equipamento' => [
            'required' => 'Número de série é obrigatório.',
            'min_length' => 'Número de série deve ter pelo menos 3 caracteres.',
            'max_length' => 'Número de série não pode exceder 255 caracteres.'
        ],
        'tipologia' => [
            'required' => 'Tipologia é obrigatória.',
            'in_list' => 'Tipologia inválida.'
        ],
        'possivel_avaria' => [
            'required' => 'Tipo de avaria é obrigatório.',
            'in_list' => 'Tipo de avaria inválido.'
        ],
        'data_envio' => [
            'required' => 'Data de envio é obrigatória.',
            'valid_date' => 'Data de envio inválida.'
        ],
        'empresa_reparacao' => [
            'required' => 'Empresa de reparação é obrigatória.',
            'min_length' => 'Empresa de reparação deve ter pelo menos 2 caracteres.',
            'max_length' => 'Empresa de reparação não pode exceder 255 caracteres.'
        ],
        'estado' => [
            'required' => 'Estado é obrigatório.',
            'in_list' => 'Estado inválido.'
        ],
        'custo' => [
            'decimal' => 'Custo deve ser um valor decimal válido.'
        ],
        'data_recepcao' => [
            'valid_date' => 'Data de receção inválida.'
        ]
    ];

    /**
     * Obter todas as reparações com informações resumidas
     */
    public function getReparacoesComDetalhes($incluirApagadas = false)
    {
        $builder = $this->builder();
        
        $builder->select('
            reparacoes_externas.*,
            user.name as tecnico_nome,
            CASE 
                WHEN reparacoes_externas.data_recepcao IS NOT NULL THEN 
                    ABS(DATEDIFF(reparacoes_externas.data_recepcao, reparacoes_externas.data_envio))
                ELSE 
                    ABS(DATEDIFF(CURRENT_DATE, reparacoes_externas.data_envio))
            END as dias_reparacao
        ');
        
        $builder->join('user', 'user.id = reparacoes_externas.id_tecnico', 'left');
        
        if (!$incluirApagadas) {
            $builder->where('reparacoes_externas.deleted_at', null);
        }
        
        $builder->orderBy('reparacoes_externas.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obter estatísticas das reparações
     */
    public function getEstatisticas()
    {
        $stats = [
            'total' => $this->countAllResults(false),
            'enviado' => 0,
            'em_reparacao' => 0,
            'reparado' => 0,
            'irreparavel' => 0,
            'cancelado' => 0,
            'custo_total' => 0,
            'tempo_medio' => 0
        ];

        // Contagem por estado
        $estados = $this->builder()
            ->select('estado, COUNT(*) as total')
            ->where('deleted_at', null)
            ->groupBy('estado')
            ->get()
            ->getResultArray();

        foreach ($estados as $estado) {
            $key = $estado['estado'];
            if (isset($stats[$key])) {
                $stats[$key] = (int)$estado['total'];
            }
        }

        // Custo total
        $custoTotal = $this->builder()
            ->selectSum('custo', 'total')
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();
        $stats['custo_total'] = $custoTotal['total'] ?? 0;

        // Tempo médio de reparação (em dias)
        $tempoMedio = $this->builder()
            ->select('AVG(DATEDIFF(data_recepcao, data_envio)) as media')
            ->where('data_recepcao IS NOT NULL')
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();
        $stats['tempo_medio'] = round($tempoMedio['media'] ?? 0, 1);

        return $stats;
    }

    /**
     * Obter reparações por tipologia
     */
    public function getEstatisticasPorTipologia()
    {
        return $this->builder()
            ->select('tipologia, COUNT(*) as total')
            ->where('deleted_at', null)
            ->groupBy('tipologia')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Obter reparações por tipo de avaria
     */
    public function getEstatisticasPorAvaria()
    {
        return $this->builder()
            ->select('possivel_avaria, COUNT(*) as total')
            ->where('deleted_at', null)
            ->groupBy('possivel_avaria')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Importar dados de CSV
     * @param array $dados Array de dados para importar
     * @return array Resultado da importação com sucessos e erros
     */
    public function importarCSV($dados)
    {
        $resultado = [
            'sucesso' => 0,
            'erros' => [],
            'total' => count($dados)
        ];

        foreach ($dados as $index => $linha) {
            try {
                // Validar e inserir
                if ($this->insert($linha)) {
                    $resultado['sucesso']++;
                } else {
                    $resultado['erros'][] = [
                        'linha' => $index + 2, // +2 porque linha 1 é cabeçalho e index começa em 0
                        'erro' => implode(', ', $this->errors())
                    ];
                }
            } catch (\Exception $e) {
                $resultado['erros'][] = [
                    'linha' => $index + 2,
                    'erro' => $e->getMessage()
                ];
            }
        }

        return $resultado;
    }
}
