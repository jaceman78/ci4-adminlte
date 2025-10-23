<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartamentosModel extends Model
{
    protected $table            = 'departamentos';
    protected $primaryKey       = 'cod_departamento';
    protected $useAutoIncrement = false; // Código do departamento é definido manualmente
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['cod_departamento', 'nomedepartamento', 'status'];

    // Dates
    protected $useTimestamps = false;

    // Validation
    protected $validationRules      = [
        'cod_departamento' => 'required|integer|is_unique[departamentos.cod_departamento,cod_departamento,{cod_departamento}]',
        'nomedepartamento' => 'required|min_length[3]|max_length[255]',
        'status'           => 'required|in_list[0,1]'
    ];
    protected $validationMessages   = [
        'cod_departamento' => [
            'required'  => 'O código do departamento é obrigatório',
            'integer'   => 'O código deve ser um número inteiro',
            'is_unique' => 'Este código de departamento já existe'
        ],
        'nomedepartamento' => [
            'required'   => 'O nome do departamento é obrigatório',
            'min_length' => 'O nome deve ter pelo menos 3 caracteres',
            'max_length' => 'O nome não pode exceder 255 caracteres'
        ],
        'status' => [
            'required' => 'O status é obrigatório',
            'in_list'  => 'O status deve ser 0 (Inativo) ou 1 (Ativo)'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    /**
     * Obter apenas departamentos ativos
     * 
     * @return array
     */
    public function getDepartamentosAtivos()
    {
        return $this->where('status', 1)
                    ->orderBy('nomedepartamento', 'ASC')
                    ->findAll();
    }

    /**
     * Obter departamentos por código (parcial ou completo)
     * 
     * @param int $codigo
     * @return array
     */
    public function getDepartamentosPorCodigo($codigo)
    {
        return $this->like('cod_departamento', $codigo)
                    ->where('status', 1)
                    ->orderBy('cod_departamento', 'ASC')
                    ->findAll();
    }

    /**
     * Obter departamentos por ciclo de ensino
     * 
     * @param string $ciclo (pré-escolar, 1º ciclo, 2º ciclo, 3º ciclo, secundário, especial)
     * @return array
     */
    public function getDepartamentosPorCiclo($ciclo)
    {
        $ranges = [
            'pré-escolar' => [100, 109],
            '1º ciclo'    => [110, 199],
            '2º ciclo'    => [200, 299],
            '3º ciclo'    => [300, 599],
            'secundário'  => [300, 699],
            'especial'    => [900, 999]
        ];

        if (!isset($ranges[$ciclo])) {
            return [];
        }

        [$min, $max] = $ranges[$ciclo];

        return $this->where('cod_departamento >=', $min)
                    ->where('cod_departamento <=', $max)
                    ->where('status', 1)
                    ->orderBy('cod_departamento', 'ASC')
                    ->findAll();
    }

    /**
     * Obter lista para dropdown (código - nome)
     * 
     * @param bool $apenasAtivos
     * @return array
     */
    public function getListaDropdown($apenasAtivos = true)
    {
        $builder = $this->select('cod_departamento, nomedepartamento');
        
        if ($apenasAtivos) {
            $builder->where('status', 1);
        }
        
        $departamentos = $builder->orderBy('nomedepartamento', 'ASC')->findAll();
        
        $lista = [];
        foreach ($departamentos as $dep) {
            $lista[$dep['cod_departamento']] = $dep['cod_departamento'] . ' - ' . $dep['nomedepartamento'];
        }
        
        return $lista;
    }

    /**
     * Ativar/Desativar departamento
     * 
     * @param int $cod_departamento
     * @param int $novoStatus (0 ou 1)
     * @return bool
     */
    public function alterarStatus($cod_departamento, $novoStatus)
    {
        return $this->update($cod_departamento, ['status' => $novoStatus]);
    }

    /**
     * Contar departamentos por status
     * 
     * @return array ['ativos' => X, 'inativos' => Y]
     */
    public function contarPorStatus()
    {
        return [
            'ativos'   => $this->where('status', 1)->countAllResults(),
            'inativos' => $this->where('status', 0)->countAllResults()
        ];
    }
}
