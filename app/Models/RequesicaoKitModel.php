<?php
namespace App\Models;

use CodeIgniter\Model;

class RequesicaoKitModel extends Model
{
    protected $table = 'requisicao_kit';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'numero_aluno','nome','turma','nif','ase','email_aluno','email_ee','estado','obs','created_at'
    ];

    protected $useTimestamps = false; // using only created_at default

    protected $validationRules = [
        'numero_aluno' => 'required|exact_length[5]|numeric',
        'nome' => 'required|min_length[3]',
    'turma' => 'required',
        'nif' => 'required|exact_length[9]|numeric|is_unique[requisicao_kit.nif]',
        'ase' => 'required|in_list[Escalão A,Escalão B,Escalão C,Sem Escalão]',
        'email_aluno' => 'required|valid_email',
        'email_ee' => 'required|valid_email',
    ];

    protected $validationMessages = [
        'numero_aluno' => [
            'required' => 'Número de aluno obrigatório.',
            'exact_length' => 'Número de aluno deve ter exatamente 5 dígitos.',
            'numeric' => 'Número de aluno deve ser numérico.'
        ],
        'turma' => [
            'required' => 'Turma obrigatória.'
        ],
        'nif' => [
            'exact_length' => 'NIF deve ter 9 dígitos.',
            'numeric' => 'NIF deve ser numérico.',
            'is_unique' => 'Este NIF já possui uma requisição.'
        ],
        'ase' => [
            'in_list' => 'Escalão inválido.'
        ],
        'email_aluno' => [
            'valid_email' => 'Email do aluno inválido.'
        ],
        'email_ee' => [
            'valid_email' => 'Email do encarregado de educação inválido.'
        ],
    ];
}
