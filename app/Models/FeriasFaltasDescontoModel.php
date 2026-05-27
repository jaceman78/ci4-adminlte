<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model para gestão de faltas que descontam em férias
 * Tabela: ferias_faltas_desconto
 * 
 * Legislação:
 * - Artigo 102.º do ECD: Faltas injustificadas
 * - Artigo 134.º n.3 do ECD: Faltas por doença
 */
class FeriasFaltasDescontoModel extends Model
{
    protected $table            = 'ferias_faltas_desconto';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_nif',
        'anoletivo_id_falta',
        'anoletivo_id_desconto',
        'data_falta',
        'tipo_falta',
        'dias_desconto',
        'motivo',
        'observacoes',
        'registado_por'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';

    // Validation
    protected $validationRules = [
        'user_nif'                => 'required|integer',
        'anoletivo_id_falta'      => 'required|integer',
        'anoletivo_id_desconto'   => 'required|integer',
        'data_falta'              => 'required|valid_date',
        'tipo_falta'              => 'required|in_list[artigo_102,art_135_n4_ltfp,artigo_89_ecd,injustificada]',
        'dias_desconto'           => 'required|decimal|greater_than[0]'
    ];

    protected $validationMessages = [
        'user_nif' => [
            'required' => 'O NIF do professor é obrigatório'
        ],
        'tipo_falta' => [
            'in_list' => 'Tipo de falta inválido'
        ],
        'dias_desconto' => [
            'greater_than' => 'Dias de desconto deve ser maior que 0'
        ]
    ];

    /**
     * Obter faltas de um professor por ano letivo de desconto
     * 
     * @param string $userNif NIF do professor
     * @param int $anoLetivoIdDesconto Ano letivo onde será descontado
     * @return array
     */
    public function getFaltasPorProfessorEAno($userNif, $anoLetivoIdDesconto)
    {
        return $this->select('ferias_faltas_desconto.*, 
                             al_falta.anoletivo as ano_falta,
                             al_desconto.anoletivo as ano_desconto,
                             u.name as registado_por_nome')
                    ->join('ano_letivo as al_falta', 'al_falta.id_anoletivo = ferias_faltas_desconto.anoletivo_id_falta')
                    ->join('ano_letivo as al_desconto', 'al_desconto.id_anoletivo = ferias_faltas_desconto.anoletivo_id_desconto')
                    ->join('user as u', 'u.id = ferias_faltas_desconto.registado_por', 'left')
                    ->where('ferias_faltas_desconto.user_nif', $userNif)
                    ->where('ferias_faltas_desconto.anoletivo_id_desconto', $anoLetivoIdDesconto)
                    ->orderBy('ferias_faltas_desconto.data_falta', 'DESC')
                    ->findAll();
    }

    /**
     * Calcular total de dias a descontar de um professor num ano letivo
     * 
     * @param string $userNif NIF do professor
     * @param int $anoLetivoIdDesconto Ano letivo onde será descontado
     * @return float Total de dias a descontar
     */
    public function calcularTotalDescontoPorAno($userNif, $anoLetivoIdDesconto)
    {
        $result = $this->selectSum('dias_desconto')
                       ->where('user_nif', $userNif)
                       ->where('anoletivo_id_desconto', $anoLetivoIdDesconto)
                       ->get()
                       ->getRowArray();

        // Soma raw das faltas (pode ter decimais, ex: 0.5 + 0.8 = 1.3)
        $total = (float) ($result['dias_desconto'] ?? 0);

        // Só desconta dias inteiros: 1.3 -> 1 dia; 0.9 -> 0 dias
        return (int) floor($total);
    }

    /**
     * Obter faltas já registadas para o mesmo utilizador na mesma data
     *
     * @param string $userNif NIF do professor
     * @param string $dataFalta Data no formato YYYY-MM-DD
     * @param int|null $excludeId ID a excluir (para edições)
     * @return array
     */
    public function getFaltasMesmoDia(string $userNif, string $dataFalta, ?int $excludeId = null): array
    {
        $builder = $this->where('user_nif', $userNif)
                        ->where('data_falta', $dataFalta);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->findAll();
    }

    /**
     * Registar nova falta que desconta em férias
     * 
     * @param array $data Dados da falta
     * @return int|false ID do registo ou false em caso de erro
     */
    public function registarFalta($data)
    {
        if ($this->insert($data)) {
            return $this->getInsertID();
        }
        
        return false;
    }

    /**
     * Obter descrição legível do tipo de falta
     * 
     * @param string $tipoFalta Código do tipo de falta
     * @return string
     */
    public static function getDescricaoTipoFalta($tipoFalta)
    {
        $tipos = [
            'artigo_102'        => 'Artigo 102.º do ECD (Justificada por participação)',
            'art_135_n4_ltfp'  => 'Artigo 135º, nº4 de 35/2014 LTFP',
            'artigo_89_ecd'    => 'Artigo 89.º do ECD — Acumulação de férias',
            'injustificada'    => 'Falta Injustificada'
        ];
        
        return $tipos[$tipoFalta] ?? 'Desconhecido';
    }

    /**
     * Verificar se um tipo de falta é justificada
     * 
     * @param string $tipoFalta Código do tipo de falta
     * @return bool
     */
    public static function isFaltaJustificada($tipoFalta)
    {
        return in_array($tipoFalta, ['artigo_102', 'art_135_n4_ltfp', 'artigo_89_ecd']);
    }

    /**
     * Obter categoria da falta (justificada ou injustificada)
     * 
     * @param string $tipoFalta Código do tipo de falta
     * @return string 'justificada' ou 'injustificada'
     */
    public static function getCategoriaFalta($tipoFalta)
    {
        return self::isFaltaJustificada($tipoFalta) ? 'justificada' : 'injustificada';
    }

    /**
     * Obter referência legal detalhada
     * 
     * @param string $tipoFalta Código do tipo de falta
     * @return string
     */
    public static function getReferenciaLegal($tipoFalta)
    {
        $referencias = [
            'artigo_102'        => 'Artigo 102.º - Estatuto da Carreira Docente',
            'art_135_n4_ltfp'  => 'Artigo 135º, nº4 de 35/2014 LTFP',
            'artigo_89_ecd'    => 'Artigo 89.º do ECD — Acumulação de férias',
            'injustificada'    => 'Falta sem justificação aceite'
        ];
        
        return $referencias[$tipoFalta] ?? '';
    }

    /**
     * Listar todas as faltas de um professor (todos os anos)
     * 
     * @param string $userNif NIF do professor
     * @return array
     */
    public function getFaltasPorProfessor($userNif)
    {
        return $this->select('ferias_faltas_desconto.*, 
                             al_falta.anoletivo as ano_falta,
                             al_desconto.anoletivo as ano_desconto,
                             u.name as registado_por_nome')
                    ->join('ano_letivo as al_falta', 'al_falta.id_anoletivo = ferias_faltas_desconto.anoletivo_id_falta')
                    ->join('ano_letivo as al_desconto', 'al_desconto.id_anoletivo = ferias_faltas_desconto.anoletivo_id_desconto')
                    ->join('user as u', 'u.id = ferias_faltas_desconto.registado_por', 'left')
                    ->where('ferias_faltas_desconto.user_nif', $userNif)
                    ->orderBy('ferias_faltas_desconto.data_falta', 'DESC')
                    ->findAll();
    }

    /**
     * Obter resumo de faltas agrupadas por professor e ano de desconto
     * 
     * @param int $anoLetivoIdDesconto Ano letivo de desconto
     * @return array
     */
    public function getResumoFaltasPorAno($anoLetivoIdDesconto)
    {
        return $this->select('ferias_faltas_desconto.user_nif,
                             user.name as nome_professor,
                             COUNT(ferias_faltas_desconto.id) as total_faltas,
                             SUM(ferias_faltas_desconto.dias_desconto) as total_dias_desconto')
                    ->join('user', 'user.NIF = ferias_faltas_desconto.user_nif')
                    ->where('ferias_faltas_desconto.anoletivo_id_desconto', $anoLetivoIdDesconto)
                    ->groupBy('ferias_faltas_desconto.user_nif')
                    ->orderBy('user.name', 'ASC')
                    ->findAll();
    }

    /**
     * Dados para heatmap: total de faltas por data (dia exacto)
     */
    public function getHeatmapPorData($anoLetivoId)
    {
        return $this->db->table('ferias_faltas_desconto')
            ->select('data_falta, COUNT(id) as total_ocorrencias, SUM(dias_desconto) as total_dias')
            ->where('anoletivo_id_falta', $anoLetivoId)
            ->groupBy('data_falta')
            ->orderBy('data_falta', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Detalhe das faltas num dia específico (para popover AJAX)
     */
    public function getFaltasPorDataDetalhe(string $data, int $anoLetivoId): array
    {
        return $this->db->table('ferias_faltas_desconto as f')
            ->select("COALESCE(u.NIF, f.user_nif) as user_nif, COALESCE(u.name, CONCAT('NIF: ', f.user_nif)) as nome, f.tipo_falta, f.dias_desconto, f.motivo")
            ->join('user as u', 'u.NIF = f.user_nif', 'left')
            ->where('f.data_falta', $data)
            ->where('f.anoletivo_id_falta', $anoLetivoId)
            ->orderBy('u.name', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Resumo de absentismo por professor num ano letivo
     */
    public function getAbsentismoPorProfessor($anoLetivoId)
    {
        return $this->db->table('ferias_faltas_desconto as f')
            ->select('f.user_nif, u.name as nome_professor, u.escola_servico,
                      e.nome as escola_nome,
                      COUNT(f.id) as total_faltas,
                      SUM(f.dias_desconto) as total_dias_desconto,
                      MAX(f.data_falta) as ultima_falta,
                      SUM(CASE WHEN f.tipo_falta = "injustificada" THEN 1 ELSE 0 END) as faltas_injustificadas,
                      SUM(CASE WHEN f.tipo_falta != "injustificada" THEN 1 ELSE 0 END) as faltas_justificadas')
            ->join('user as u', 'u.NIF = f.user_nif')
            ->join('escolas as e', 'e.id = u.escola_servico', 'left')
            ->where('f.anoletivo_id_falta', $anoLetivoId)
            ->groupBy('f.user_nif, u.name, u.escola_servico, e.nome')
            ->orderBy('total_dias_desconto', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Totais de absentismo agrupados por mês num ano letivo
     */
    public function getAbsentismoPorMes($anoLetivoId)
    {
        return $this->db->table('ferias_faltas_desconto')
            ->select('MONTH(data_falta) as mes, COUNT(id) as total_faltas,
                      SUM(dias_desconto) as total_dias,
                      COUNT(DISTINCT user_nif) as professores_afetados')
            ->where('anoletivo_id_falta', $anoLetivoId)
            ->groupBy('MONTH(data_falta)')
            ->orderBy('MONTH(data_falta)', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Obter todas as faltas registadas (para listagem geral)
     * 
     * @return array
     */
    public function getAllFaltas()
    {
        return $this->select('ferias_faltas_desconto.*, 
                             user.name as nome_professor,
                             user.email as email_professor,
                             al_falta.anoletivo as ano_falta,
                             al_desconto.anoletivo as ano_desconto,
                             registador.name as nome_registador')
                    ->join('user', 'user.NIF = ferias_faltas_desconto.user_nif', 'left')
                    ->join('ano_letivo as al_falta', 'al_falta.id_anoletivo = ferias_faltas_desconto.anoletivo_id_falta', 'left')
                    ->join('ano_letivo as al_desconto', 'al_desconto.id_anoletivo = ferias_faltas_desconto.anoletivo_id_desconto', 'left')
                    ->join('user as registador', 'registador.id = ferias_faltas_desconto.registado_por', 'left')
                    ->orderBy('ferias_faltas_desconto.criado_em', 'DESC')
                    ->findAll();
    }
}
