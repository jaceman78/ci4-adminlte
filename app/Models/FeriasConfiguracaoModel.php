<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model para gestão de configurações do sistema de férias
 * Tabela: ferias_configuracao
 */
class FeriasConfiguracaoModel extends Model
{
    protected $table            = 'ferias_configuracao';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'anoletivo_id',
        'data_inicio_permitida',
        'data_fim_permitida',
        'permite_marcacao',
        'mostrar_menu_ferias',
        'mensagem_bloqueio',
        'observacoes',
        'atualizado_por'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';

    // Validation
    protected $validationRules = [
        'anoletivo_id'          => 'required|integer|is_not_unique[ano_letivo.id_anoletivo]',
        'data_inicio_permitida' => 'required|valid_date',
        'data_fim_permitida'    => 'required|valid_date',
        'permite_marcacao'      => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'anoletivo_id' => [
            'required' => 'O ano letivo é obrigatório',
            'is_not_unique' => 'Ano letivo não encontrado'
        ],
        'data_inicio_permitida' => [
            'required' => 'A data de início é obrigatória',
            'valid_date' => 'Data de início inválida'
        ],
        'data_fim_permitida' => [
            'required' => 'A data de fim é obrigatória',
            'valid_date' => 'Data de fim inválida'
        ]
    ];

    /**
     * Obter configuração do ano letivo ativo
     * 
     * @return array|null
     */
    public function getConfiguracaoAnoAtivo()
    {
        return $this->select('ferias_configuracao.*, ano_letivo.anoletivo')
                    ->join('ano_letivo', 'ano_letivo.id_anoletivo = ferias_configuracao.anoletivo_id')
                    ->where('ano_letivo.status', 1)
                    ->first();
    }

    /**
     * Obter configuração de um ano letivo específico
     * 
     * @param int $anoLetivoId
     * @return array|null
     */
    public function getConfiguracaoPorAno($anoLetivoId)
    {
        return $this->where('anoletivo_id', $anoLetivoId)->first();
    }

    /**
     * Criar ou atualizar configuração
     * 
     * @param int $anoLetivoId
     * @param string $dataInicio
     * @param string $dataFim
     * @param bool $permiteMarcacao
     * @param string|null $mensagemBloqueio
     * @param string|null $observacoes
     * @param int|null $atualizadoPor
     * @return bool|int
     */
    public function salvarConfiguracao($anoLetivoId, $dataInicio, $dataFim, $permiteMarcacao = true, $mostrarMenuFerias = true, $mensagemBloqueio = null, $observacoes = null, $atualizadoPor = null)
    {
        $existing = $this->where('anoletivo_id', $anoLetivoId)->first();

        $data = [
            'anoletivo_id'          => $anoLetivoId,
            'data_inicio_permitida' => $dataInicio,
            'data_fim_permitida'    => $dataFim,
            'permite_marcacao'      => $permiteMarcacao ? 1 : 0,
            'mostrar_menu_ferias'   => $mostrarMenuFerias ? 1 : 0,
            'mensagem_bloqueio'     => $mensagemBloqueio,
            'observacoes'           => $observacoes,
            'atualizado_por'        => $atualizadoPor
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
     * Verificar se a marcação de férias está permitida para o ano letivo ativo
     * 
     * @return array ['permitido' => bool, 'mensagem' => string|null]
     */
    public function verificarPermissaoMarcacao()
    {
        $config = $this->getConfiguracaoAnoAtivo();

        if (!$config) {
            // Se não existe configuração, permitir por padrão
            return [
                'permitido' => true,
                'mensagem' => null
            ];
        }

        if (!$config['permite_marcacao']) {
            return [
                'permitido' => false,
                'mensagem' => $config['mensagem_bloqueio'] ?? 'A marcação de férias está temporariamente desativada. Contacte a secretaria.'
            ];
        }

        return [
            'permitido' => true,
            'mensagem' => null
        ];
    }

    /**
     * Validar se uma data está dentro do período permitido
     * 
     * @param string $data Data no formato Y-m-d
     * @param int|null $anoLetivoId Se null, usa ano ativo
     * @return array ['valido' => bool, 'mensagem' => string|null]
     */
    public function validarDataNoPeriodo($data, $anoLetivoId = null)
    {
        if ($anoLetivoId) {
            $config = $this->getConfiguracaoPorAno($anoLetivoId);
        } else {
            $config = $this->getConfiguracaoAnoAtivo();
        }

        if (!$config) {
            // Se não existe configuração, validação padrão
            return ['valido' => true, 'mensagem' => null];
        }

        $dataTimestamp = strtotime($data);
        $inicioTimestamp = strtotime($config['data_inicio_permitida']);
        $fimTimestamp = strtotime($config['data_fim_permitida']);

        if ($dataTimestamp < $inicioTimestamp || $dataTimestamp > $fimTimestamp) {
            return [
                'valido' => false,
                'mensagem' => sprintf(
                    'A data deve estar entre %s e %s',
                    date('d/m/Y', $inicioTimestamp),
                    date('d/m/Y', $fimTimestamp)
                )
            ];
        }

        return ['valido' => true, 'mensagem' => null];
    }
}
