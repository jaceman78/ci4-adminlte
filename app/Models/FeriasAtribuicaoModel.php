<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model para gestão de atribuição de férias
 * Tabela: ferias_atribuicao
 */
class FeriasAtribuicaoModel extends Model
{
    protected $table            = 'ferias_atribuicao';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_nif',
        'anoletivo_id',
        'dias_base',
        'dias_ajuste',
        'dias_gozados_anterior',
        'dias_atribuidos_anterior',
        'dias_extra',
        'observacoes',
        'atribuido_por',
        'permite_marcar_fora_periodo',
        'obriga_totalidade_dias',
        'alinea'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';

    // Validation
    protected $validationRules = [
        'user_nif'              => 'required|integer',
        'anoletivo_id'          => 'required|integer',
        'dias_base'             => 'required|integer|greater_than_equal_to[0]',
        'dias_ajuste'           => 'permit_empty|integer',
        'dias_gozados_anterior' => 'permit_empty|integer|greater_than_equal_to[0]',
        'dias_atribuidos_anterior' => 'permit_empty|integer|greater_than_equal_to[0]',
        'dias_extra'            => 'permit_empty|integer', // Permite negativos para ajustes
        'observacoes'           => 'permit_empty|max_length[5000]',
        'atribuido_por'         => 'permit_empty|integer'
    ];

    protected $validationMessages = [
        'user_nif' => [
            'required' => 'O NIF do professor é obrigatório'
        ],
        'anoletivo_id' => [
            'required' => 'O ano letivo é obrigatório'
        ],
        'dias_base' => [
            'required' => 'Os dias base são obrigatórios',
            'greater_than_equal_to' => 'Os dias base não podem ser negativos'
        ]
    ];

    /**
     * Obter atribuição de férias de um professor num ano letivo específico
     * 
     * @param string $userNif NIF do professor
     * @param int $anoLetivoId ID do ano letivo
     * @return array|null
     */
    public function getAtribuicaoProfessor($userNif, $anoLetivoId)
    {
        return $this->select('ferias_atribuicao.*, 
                             user.name as nome_professor, 
                             user.email as email_professor,
                             ano_letivo.anoletivo as ano,
                             atribuidor.name as nome_atribuidor')
                    ->join('user', 'user.NIF = ferias_atribuicao.user_nif', 'left')
                    ->join('ano_letivo', 'ano_letivo.id_anoletivo = ferias_atribuicao.anoletivo_id', 'left')
                    ->join('user as atribuidor', 'atribuidor.id = ferias_atribuicao.atribuido_por', 'left')
                    ->where('ferias_atribuicao.user_nif', $userNif)
                    ->where('ferias_atribuicao.anoletivo_id', $anoLetivoId)
                    ->first();
    }

    /**
     * Obter todas as atribuições de um ano letivo
     * 
     * @param int $anoLetivoId ID do ano letivo
     * @return array
     */
    public function getAtribuicoesPorAno($anoLetivoId)
    {
        return $this->select('ferias_atribuicao.*, 
                             user.name as nome_professor, 
                             user.email as email_professor,
                             ano_letivo.anoletivo as ano')
                    ->join('user', 'user.NIF = ferias_atribuicao.user_nif', 'left')
                    ->join('ano_letivo', 'ano_letivo.id_anoletivo = ferias_atribuicao.anoletivo_id', 'left')
                    ->where('ferias_atribuicao.anoletivo_id', $anoLetivoId)
                    ->orderBy('user.name', 'ASC')
                    ->findAll();
    }

    /**
     * Atribuir férias a um professor
     * 
     * @param string $userNif NIF do professor
     * @param int $anoLetivoId ID do ano letivo
     * @param int $diasBase Dias base de férias
     * @param int $diasAjuste Ajuste de dias (pode ser negativo)
     * @param int $diasExtra Dias extra
     * @param string $observacoes Observações
     * @param int $atribuidoPor ID do utilizador que atribuiu
     * @return bool|int
     */
    public function atribuirFerias($userNif, $anoLetivoId, $diasBase = 22, $diasAjuste = 0, $diasExtra = 0, $observacoes = null, $atribuidoPor = null, $diasGozadosAnterior = 0, $permiteMarcarForaPeriodo = 0, $obrigaTotalidadeDias = 1, $diasAtribuidosAnterior = 0, $alinea = null)
    {
        // Verificar se já existe atribuição
        $existing = $this->where('user_nif', $userNif)
                        ->where('anoletivo_id', $anoLetivoId)
                        ->first();

        $data = [
            'user_nif'                      => $userNif,
            'anoletivo_id'                  => $anoLetivoId,
            'dias_base'                     => $diasBase,
            'dias_ajuste'                   => $diasAjuste,
            'dias_gozados_anterior'         => $diasGozadosAnterior,
            'dias_atribuidos_anterior'      => $diasAtribuidosAnterior,
            'dias_extra'                    => $diasExtra,
            'observacoes'                   => $observacoes,
            'atribuido_por'                 => $atribuidoPor,
            'permite_marcar_fora_periodo'   => $permiteMarcarForaPeriodo,
            'obriga_totalidade_dias'        => $obrigaTotalidadeDias,
            'alinea'                        => ($alinea !== '' ? $alinea : null)
        ];

        if ($existing) {
            // Atualizar
            return $this->update($existing['id'], $data);
        } else {
            // Criar novo
            return $this->insert($data);
        }
    }

    /**
     * Calcular dias gastos de um professor num ano letivo
     * 
     * @param string $userNif NIF do professor
     * @param int $anoLetivoId ID do ano letivo
     * @return int
     */
    public function calcularDiasGastos($userNif, $anoLetivoId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('ferias_pedido');
        
        $result = $builder->select('COALESCE(SUM(total_dias), 0) as total')
                          ->where('user_nif', $userNif)
                          ->where('anoletivo_id', $anoLetivoId)
                          ->whereIn('estado', ['aprovado', 'aguarda_assinatura', 'concluido'])
                          ->get()
                          ->getRowArray();
        
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Calcular saldo disponível de um professor
     * 
     * @param string $userNif NIF do professor
     * @param int $anoLetivoId ID do ano letivo
     * @return array ['dias_total' => int, 'dias_gastos' => int, 'dias_disponiveis' => int, 'dias_desconto_faltas' => float]
     */
    public function calcularSaldo($userNif, $anoLetivoId)
    {
        $atribuicao = $this->getAtribuicaoProfessor($userNif, $anoLetivoId);
        
        if (!$atribuicao) {
            return [
                'dias_total' => 0,
                'dias_gastos' => 0,
                'dias_desconto_faltas' => 0,
                'dias_disponiveis' => 0
            ];
        }

        $diasTotal = $atribuicao['dias_total'] ?? 0;
        $diasGastos = $this->calcularDiasGastos($userNif, $anoLetivoId);
        
        // Calcular dias a descontar por faltas
        $faltasModel = new \App\Models\FeriasFaltasDescontoModel();
        $diasDescontoFaltas = $faltasModel->calcularTotalDescontoPorAno($userNif, $anoLetivoId);
        
        // Dias disponíveis = dias totais - dias já gozados - dias a descontar por faltas
        $diasDisponiveis = $diasTotal - $diasGastos - $diasDescontoFaltas;

        return [
            'dias_total' => $diasTotal,
            'dias_gastos' => $diasGastos,
            'dias_desconto_faltas' => $diasDescontoFaltas,
            'dias_disponiveis' => $diasDisponiveis,
            'dias_base' => $atribuicao['dias_base'] ?? 0,
            'dias_ajuste' => $atribuicao['dias_ajuste'] ?? 0,
            'dias_extra' => $atribuicao['dias_extra'] ?? 0
        ];
    }

    /**
     * Obter professores sem atribuição de férias para um ano letivo
     * 
     * @param int $anoLetivoId ID do ano letivo
     * @return array
     */
    public function getProfessoresSemAtribuicao($anoLetivoId)
    {
        $db = \Config\Database::connect();
        
        return $db->table('user')
                  ->select('user.NIF, user.name, user.email')
                  ->where('user.level', 1) // Apenas professores (nível 1)
                  ->where('user.NIF IS NOT NULL')
                  ->where('user.NIF !=', '')
                  ->where('user.status', 1) // Apenas ativos
                  ->whereNotIn('user.NIF', function($builder) use ($anoLetivoId) {
                      $builder->select('user_nif')
                              ->from('ferias_atribuicao')
                              ->where('anoletivo_id', $anoLetivoId);
                  })
                  ->orderBy('user.name', 'ASC')
                  ->get()
                  ->getResultArray();
    }

    /**
     * Copiar atribuições do ano anterior para um novo ano
     * (útil para inicialização de um novo ano letivo)
     * 
     * @param int $anoLetivoAnteriorId ID do ano letivo anterior
     * @param int $anoLetivoNovoId ID do novo ano letivo
     * @param int $atribuidoPor ID do utilizador que está a fazer a cópia
     * @return int Número de registos copiados
     */
    public function copiarAtribuicoesAnoAnterior($anoLetivoAnteriorId, $anoLetivoNovoId, $atribuidoPor = null)
    {
        $atribuicoesAnteriores = $this->where('anoletivo_id', $anoLetivoAnteriorId)->findAll();
        $copiados = 0;

        foreach ($atribuicoesAnteriores as $atribuicao) {
            // Calcular saldo do ano anterior
            $saldo = $this->calcularSaldo($atribuicao['user_nif'], $anoLetivoAnteriorId);
            $diasRestantes = $saldo['dias_disponiveis'];
            
            // Limitar dias transitados (ex: máximo 5 dias)
            $diasAjuste = min(max($diasRestantes, -5), 5); // Entre -5 e +5

            $this->insert([
                'user_nif'      => $atribuicao['user_nif'],
                'anoletivo_id'  => $anoLetivoNovoId,
                'dias_base'     => 22, // Reiniciar com 22 dias base
                'dias_ajuste'   => $diasAjuste,
                'dias_extra'    => 0,
                'observacoes'   => "Ajuste automático do ano anterior: {$diasRestantes} dias",
                'atribuido_por' => $atribuidoPor
            ], false); // false = não validar (caso já exista)

            $copiados++;
        }

        return $copiados;
    }
}
