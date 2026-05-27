<?php

namespace App\Models;

use CodeIgniter\Model;

class ManutencaoModel extends Model
{
    protected $table = 'sistema_manutencao';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'ativo',
        'mensagem',
        'data_inicio',
        'data_fim_prevista',
        'criado_por',
        'criado_em',
        'atualizado_em'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'criado_em';
    protected $updatedField = 'atualizado_em';

    // Validation
    protected $validationRules = [
        'ativo' => 'required|in_list[0,1]',
        'mensagem' => 'permit_empty|max_length[1000]'
    ];

    protected $validationMessages = [
        'ativo' => [
            'required' => 'O estado de manutenção é obrigatório.',
            'in_list' => 'O estado de manutenção deve ser 0 ou 1.'
        ],
        'mensagem' => [
            'max_length' => 'A mensagem não pode ter mais de 1000 caracteres.'
        ]
    ];

    /**
     * Verifica se o site está em modo de manutenção
     * 
     * @return bool
     */
    public function estaEmManutencao(): bool
    {
        $config = $this->find(1);
        return $config && $config['ativo'] == 1;
    }

    /**
     * Obtém a configuração atual de manutenção
     * 
     * @return array|null
     */
    public function getConfiguracao(): ?array
    {
        return $this->find(1);
    }

    /**
     * Ativa o modo de manutenção
     * 
     * @param string $mensagem
     * @param string|null $dataFimPrevista
     * @param int $userNif
     * @return bool
     */
    public function ativarManutencao(string $mensagem, ?string $dataFimPrevista, int $userNif): bool
    {
        $data = [
            'ativo' => 1,
            'mensagem' => $mensagem,
            'data_inicio' => date('Y-m-d H:i:s'),
            'data_fim_prevista' => $dataFimPrevista,
            'criado_por' => $userNif
        ];

        return $this->update(1, $data);
    }

    /**
     * Desativa o modo de manutenção
     * 
     * @return bool
     */
    public function desativarManutencao(): bool
    {
        $data = [
            'ativo' => 0,
            'data_inicio' => null,
            'data_fim_prevista' => null
        ];

        return $this->update(1, $data);
    }
}
