<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model para gestão de feriados
 * Tabela: ferias_feriados
 */
class FeriasFeriadosModel extends Model
{
    protected $table            = 'ferias_feriados';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'data',
        'descricao',
        'tipo',
        'ativo'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';

    // Validation
    protected $validationRules = [
        'data'      => 'required|valid_date',
        'descricao' => 'required|max_length[255]',
        'tipo'      => 'required|in_list[fixo,movel,municipal]',
        'ativo'     => 'required|in_list[0,1]'
    ];

    /**
     * Obter feriados ativos num intervalo de datas
     * 
     * @param string $dataInicio Data de início
     * @param string $dataFim Data de fim
     * @return array
     */
    public function getFeriadosPorIntervalo($dataInicio, $dataFim)
    {
        return $this->where('data >=', $dataInicio)
                    ->where('data <=', $dataFim)
                    ->where('ativo', 1)
                    ->orderBy('data', 'ASC')
                    ->findAll();
    }

    /**
     * Verificar se uma data é feriado
     * 
     * @param string $data Data a verificar
     * @return bool
     */
    public function isFeriado($data)
    {
        return $this->where('data', $data)
                    ->where('ativo', 1)
                    ->countAllResults() > 0;
    }

    /**
     * Obter lista de feriados de um ano específico
     * 
     * @param int $ano Ano
     * @return array
     */
    public function getFeriadosAno($ano)
    {
        $dataInicio = "{$ano}-01-01";
        $dataFim = "{$ano}-12-31";

        return $this->getFeriadosPorIntervalo($dataInicio, $dataFim);
    }

    /**
     * Adicionar feriados de um ano
     * (útil para popular a tabela com feriados de Portugal)
     * 
     * @param int $ano Ano
     * @return int Número de feriados adicionados
     */
    public function adicionarFeriadosPortugal($ano)
    {
        // Calcular Páscoa (algoritmo de Meeus/Jones/Butcher)
        $a = $ano % 19;
        $b = intval($ano / 100);
        $c = $ano % 100;
        $d = intval($b / 4);
        $e = $b % 4;
        $f = intval(($b + 8) / 25);
        $g = intval(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intval($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intval(($a + 11 * $h + 22 * $l) / 451);
        $mes = intval(($h + $l - 7 * $m + 114) / 31);
        $dia = (($h + $l - 7 * $m + 114) % 31) + 1;

        $pascoa = date('Y-m-d', strtotime("{$ano}-{$mes}-{$dia}"));
        $sextaSanta = date('Y-m-d', strtotime("{$pascoa} -2 days"));
        $corpoDeus = date('Y-m-d', strtotime("{$pascoa} +60 days"));

        // Feriados de Portugal
        $feriados = [
            ["{$ano}-01-01", 'Ano Novo', 'fixo'],
            [$sextaSanta, 'Sexta-feira Santa', 'movel'],
            [$pascoa, 'Páscoa', 'movel'],
            ["{$ano}-04-25", 'Dia da Liberdade', 'fixo'],
            ["{$ano}-05-01", 'Dia do Trabalhador', 'fixo'],
            [$corpoDeus, 'Corpo de Deus', 'movel'],
            ["{$ano}-06-10", 'Dia de Portugal', 'fixo'],
            ["{$ano}-08-15", 'Assunção de Nossa Senhora', 'fixo'],
            ["{$ano}-10-05", 'Implantação da República', 'fixo'],
            ["{$ano}-11-01", 'Todos os Santos', 'fixo'],
            ["{$ano}-12-01", 'Restauração da Independência', 'fixo'],
            ["{$ano}-12-08", 'Imaculada Conceição', 'fixo'],
            ["{$ano}-12-25", 'Natal', 'fixo']
        ];

        $adicionados = 0;
        foreach ($feriados as $feriado) {
            // Verificar se já existe
            $existe = $this->where('data', $feriado[0])->first();
            if (!$existe) {
                $this->insert([
                    'data'      => $feriado[0],
                    'descricao' => $feriado[1],
                    'tipo'      => $feriado[2],
                    'ativo'     => 1
                ]);
                $adicionados++;
            }
        }

        return $adicionados;
    }
}
