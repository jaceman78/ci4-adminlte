<?php
namespace App\Models;

use CodeIgniter\Model;

class RegistoAvariasKitModel extends Model
{
    protected $table = 'registo_avarias_kit';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'numero_aluno',
        'nome',
        'turma',
        'nif',
        'email_aluno',
        'email_ee',
        'estado',
        'avaria',
        'obs',
        'created_at',
        'finished_at',
        'id_ano_letivo'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'numero_aluno' => 'required|exact_length[5]|numeric',
        'nome' => 'required|min_length[3]',
        'turma' => 'required',
        'nif' => 'required|exact_length[9]|numeric',
        'email_aluno' => 'required|valid_email',
        'email_ee' => 'required|valid_email',
        'avaria' => 'required|min_length[10]',
    ];

    protected $validationMessages = [
        'numero_aluno' => [
            'required' => 'Número de aluno obrigatório.',
            'exact_length' => 'Número de aluno deve ter exatamente 5 dígitos.',
            'numeric' => 'Número de aluno deve ser numérico.'
        ],
        'nome' => [
            'required' => 'Nome obrigatório.',
            'min_length' => 'Nome deve ter pelo menos 3 caracteres.'
        ],
        'turma' => [
            'required' => 'Turma obrigatória.'
        ],
        'nif' => [
            'required' => 'NIF obrigatório.',
            'exact_length' => 'NIF deve ter 9 dígitos.',
            'numeric' => 'NIF deve ser numérico.'
        ],
        'email_aluno' => [
            'required' => 'Email do aluno obrigatório.',
            'valid_email' => 'Email do aluno inválido.'
        ],
        'email_ee' => [
            'required' => 'Email do encarregado de educação obrigatório.',
            'valid_email' => 'Email do encarregado de educação inválido.'
        ],
        'avaria' => [
            'required' => 'Descrição da avaria obrigatória.',
            'min_length' => 'Descrição da avaria deve ter pelo menos 10 caracteres.'
        ],
    ];

    /**
     * Obter estatísticas por estado
     */
    public function getStatsByEstado(): array
    {
        $stats = [
            'total' => $this->countAll(),
            'novo' => 0,
            'lido' => 0,
            'a_analisar' => 0,
            'por_levantar' => 0,
            'rejeitado' => 0,
            'anulado' => 0,
            'terminado' => 0,
        ];

        $rows = $this->builder()
            ->select('estado, COUNT(*) AS total')
            ->groupBy('estado')
            ->get()
            ->getResultArray();

        foreach ($rows as $r) {
            $estado = $r['estado'] ?? '';
            // Normalizar estado para corresponder às chaves do array
            $estado_key = str_replace(' ', '_', $estado);
            if (isset($stats[$estado_key])) {
                $stats[$estado_key] = (int) $r['total'];
            }
        }

        return $stats;
    }

    /**
     * Obter dados para DataTable com paginação e pesquisa
     */
    public function getDataTable($start, $length, $searchValue, $orderColumnIndex, $orderDir, $estadoFilter = null)
    {
        $builder = $this->builder();

        // Aplicar filtro de estado se fornecido
        if ($estadoFilter) {
            $builder->where('estado', $estadoFilter);
        }

        // Aplicar pesquisa
        if ($searchValue) {
            $builder->groupStart()
                ->like('numero_aluno', $searchValue)
                ->orLike('nome', $searchValue)
                ->orLike('turma', $searchValue)
                ->orLike('nif', $searchValue)
                ->orLike('email_aluno', $searchValue)
                ->orLike('avaria', $searchValue)
                ->groupEnd();
        }

        // Total de registros filtrados
        $recordsFiltered = $builder->countAllResults(false);

        // Ordenação
        $columns = ['id', 'numero_aluno', 'nome', 'turma', 'nif', 'estado', 'created_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';
        $builder->orderBy($orderColumn, $orderDir);

        // Paginação
        $data = $builder->limit($length, $start)->get()->getResultArray();

        // Total de registros (sem filtro)
        $recordsTotal = $this->countAll();

        return [
            'data' => $data,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered
        ];
    }
}
