<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RequisicaoKitSeeder extends Seeder
{
    public function run()
    {
        // Limpar a tabela antes de inserir
        $builder = $this->db->table('requisicao_kit');
        $builder->truncate();
        
        $data = [
            // Pendentes (5)
            [
                'numero_aluno' => '10001',
                'nome' => 'Ana Silva',
                'turma' => '10A',
                'nif' => '123456789',
                'ase' => 'Escalão A',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'pendente',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
            ],
            [
                'numero_aluno' => '10002',
                'nome' => 'Bruno Costa',
                'turma' => '10B',
                'nif' => '234567890',
                'ase' => 'Escalão B',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'pendente',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
            ],
            [
                'numero_aluno' => '11003',
                'nome' => 'Carla Mendes',
                'turma' => '11C',
                'nif' => '345678901',
                'ase' => 'Escalão A',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'pendente',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
            ],
            [
                'numero_aluno' => '12004',
                'nome' => 'David Santos',
                'turma' => '12D',
                'nif' => '456789012',
                'ase' => 'Sem Escalão',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'pendente',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'numero_aluno' => '10005',
                'nome' => 'Eva Rodrigues',
                'turma' => '10E',
                'nif' => '567890123',
                'ase' => 'Escalão C',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'pendente',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            
            // Por Levantar (5)
            [
                'numero_aluno' => '11006',
                'nome' => 'Fernando Alves',
                'turma' => '11A',
                'nif' => '678901234',
                'ase' => 'Escalão A',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'por levantar',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-10 days'))
            ],
            [
                'numero_aluno' => '12007',
                'nome' => 'Gabriela Pereira',
                'turma' => '12B',
                'nif' => '789012345',
                'ase' => 'Escalão B',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'por levantar',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-9 days'))
            ],
            [
                'numero_aluno' => '10008',
                'nome' => 'Hugo Fernandes',
                'turma' => '10C',
                'nif' => '890123456',
                'ase' => 'Escalão A',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'por levantar',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-8 days'))
            ],
            [
                'numero_aluno' => '11009',
                'nome' => 'Inês Marques',
                'turma' => '11D',
                'nif' => '901234567',
                'ase' => 'Escalão C',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'por levantar',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-7 days'))
            ],
            [
                'numero_aluno' => '12010',
                'nome' => 'João Oliveira',
                'turma' => '12E',
                'nif' => '112345678',
                'ase' => 'Sem Escalão',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'por levantar',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-6 days'))
            ],
            
            // Terminados (4)
            [
                'numero_aluno' => '10011',
                'nome' => 'Lara Sousa',
                'turma' => '10F',
                'nif' => '223456789',
                'ase' => 'Escalão A',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'terminado',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-20 days')),
                'finished_at' => date('Y-m-d H:i:s', strtotime('-15 days'))
            ],
            [
                'numero_aluno' => '11012',
                'nome' => 'Miguel Dias',
                'turma' => '11E',
                'nif' => '334567890',
                'ase' => 'Escalão B',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'terminado',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-18 days')),
                'finished_at' => date('Y-m-d H:i:s', strtotime('-12 days'))
            ],
            [
                'numero_aluno' => '12013',
                'nome' => 'Nádia Ribeiro',
                'turma' => '12F',
                'nif' => '445678901',
                'ase' => 'Escalão A',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'terminado',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-16 days')),
                'finished_at' => date('Y-m-d H:i:s', strtotime('-10 days'))
            ],
            [
                'numero_aluno' => '10014',
                'nome' => 'Oscar Gomes',
                'turma' => '10G',
                'nif' => '556789012',
                'ase' => 'Escalão C',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'terminado',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-14 days')),
                'finished_at' => date('Y-m-d H:i:s', strtotime('-8 days'))
            ],
            
            // Rejeitados (3)
            [
                'numero_aluno' => '11015',
                'nome' => 'Paula Martins',
                'turma' => '11F',
                'nif' => '667890123',
                'ase' => 'Sem Escalão',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'rejeitado',
                'obs' => 'Documentação incompleta',
                'created_at' => date('Y-m-d H:i:s', strtotime('-12 days'))
            ],
            [
                'numero_aluno' => '12016',
                'nome' => 'Rui Carvalho',
                'turma' => '12G',
                'nif' => '778901234',
                'ase' => 'Escalão A',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'rejeitado',
                'obs' => 'Não cumpre critérios de ASE',
                'created_at' => date('Y-m-d H:i:s', strtotime('-11 days'))
            ],
            [
                'numero_aluno' => '10017',
                'nome' => 'Sofia Pinto',
                'turma' => '10H',
                'nif' => '889012345',
                'ase' => 'Escalão B',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'rejeitado',
                'obs' => 'Aluno já possui equipamento',
                'created_at' => date('Y-m-d H:i:s', strtotime('-9 days'))
            ],
            
            // Anulados (3)
            [
                'numero_aluno' => '11018',
                'nome' => 'Tiago Lopes',
                'turma' => '11G',
                'nif' => '990123456',
                'ase' => 'Escalão A',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'anulado',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-13 days'))
            ],
            [
                'numero_aluno' => '12019',
                'nome' => 'Vânia Correia',
                'turma' => '12H',
                'nif' => '101234567',
                'ase' => 'Escalão C',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'anulado',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-11 days'))
            ],
            [
                'numero_aluno' => '10020',
                'nome' => 'Xavier Tavares',
                'turma' => '10I',
                'nif' => '212345678',
                'ase' => 'Sem Escalão',
                'email_aluno' => 'correio.tic@gmail.com',
                'email_ee' => 'antonioneto78@gmail.com',
                'estado' => 'anulado',
                'obs' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-10 days'))
            ]
        ];

        // Inserir os dados
        $builder = $this->db->table('requisicao_kit');
        foreach ($data as $record) {
            $builder->insert($record);
        }

        echo "20 registos inseridos com sucesso na tabela requisicao_kit!\n";
    }
}
