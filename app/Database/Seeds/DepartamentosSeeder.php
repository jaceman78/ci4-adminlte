<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepartamentosSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Educação Pré-Escolar e 1º Ciclo
            ['cod_departamento' => 100, 'nomedepartamento' => 'Educação Pré-Escolar', 'status' => 1],
            ['cod_departamento' => 110, 'nomedepartamento' => 'Ensino Básico - 1º Ciclo', 'status' => 1],
            ['cod_departamento' => 120, 'nomedepartamento' => 'Inglês', 'status' => 1],
            
            // 2º Ciclo
            ['cod_departamento' => 200, 'nomedepartamento' => 'Português e Estudos Sociais/História', 'status' => 1],
            ['cod_departamento' => 210, 'nomedepartamento' => 'Português e Francês', 'status' => 1],
            ['cod_departamento' => 220, 'nomedepartamento' => 'Português e Inglês', 'status' => 1],
            ['cod_departamento' => 230, 'nomedepartamento' => 'Matemática e Ciências da Natureza', 'status' => 1],
            ['cod_departamento' => 240, 'nomedepartamento' => 'Educação Visual e Tecnológica', 'status' => 1],
            ['cod_departamento' => 250, 'nomedepartamento' => 'Educação Musical', 'status' => 1],
            ['cod_departamento' => 260, 'nomedepartamento' => 'Educação Física', 'status' => 1],
            ['cod_departamento' => 290, 'nomedepartamento' => 'Educação Moral e Religiosa', 'status' => 1],
            
            // Línguas - 3º Ciclo e Secundário
            ['cod_departamento' => 300, 'nomedepartamento' => 'Português', 'status' => 1],
            ['cod_departamento' => 310, 'nomedepartamento' => 'Latim e Grego', 'status' => 1],
            ['cod_departamento' => 320, 'nomedepartamento' => 'Francês', 'status' => 1],
            ['cod_departamento' => 330, 'nomedepartamento' => 'Inglês', 'status' => 1],
            ['cod_departamento' => 340, 'nomedepartamento' => 'Alemão', 'status' => 1],
            ['cod_departamento' => 350, 'nomedepartamento' => 'Espanhol', 'status' => 1],
            ['cod_departamento' => 360, 'nomedepartamento' => 'Língua Gestual Portuguesa', 'status' => 1],
            
            // Ciências Sociais e Humanas
            ['cod_departamento' => 400, 'nomedepartamento' => 'História', 'status' => 1],
            ['cod_departamento' => 410, 'nomedepartamento' => 'Filosofia', 'status' => 1],
            ['cod_departamento' => 420, 'nomedepartamento' => 'Geografia', 'status' => 1],
            ['cod_departamento' => 430, 'nomedepartamento' => 'Economia e Contabilidade', 'status' => 1],
            
            // Matemática e Ciências Experimentais
            ['cod_departamento' => 500, 'nomedepartamento' => 'Matemática', 'status' => 1],
            ['cod_departamento' => 510, 'nomedepartamento' => 'Física e Química', 'status' => 1],
            ['cod_departamento' => 520, 'nomedepartamento' => 'Biologia e Geologia', 'status' => 1],
            ['cod_departamento' => 530, 'nomedepartamento' => 'Educação Tecnológica', 'status' => 1],
            ['cod_departamento' => 540, 'nomedepartamento' => 'Electrotecnia', 'status' => 1],
            ['cod_departamento' => 550, 'nomedepartamento' => 'Informática', 'status' => 1],
            ['cod_departamento' => 560, 'nomedepartamento' => 'Ciências Agro-pecuárias', 'status' => 1],
            
            // Expressões
            ['cod_departamento' => 600, 'nomedepartamento' => 'Artes Visuais', 'status' => 1],
            ['cod_departamento' => 610, 'nomedepartamento' => 'Música', 'status' => 1],
            ['cod_departamento' => 620, 'nomedepartamento' => 'Educação Física', 'status' => 1],
            
            // Educação Especial
            [
                'cod_departamento' => 910, 
                'nomedepartamento' => 'Educação Especial' . "\r\n" . '- apoio a crianças e jovens com graves problemas cognitivos, com graves problemas motores, com graves perturbações da personalidade ou da conduta, com multideficiência e para o apoio em intervenção precoce na infância.', 
                'status' => 1
            ],
            [
                'cod_departamento' => 920, 
                'nomedepartamento' => 'Educação Especial' . "\r\n" . '- apoio a crianças e jovens com surdez moderada, severa ou profunda, com graves problemas de comunicação, linguagem ou fala.', 
                'status' => 1
            ],
            [
                'cod_departamento' => 930, 
                'nomedepartamento' => 'Educação Especial' . "\r\n" . '- apoio educativo a crianças e jovens com cegueira ou baixa visão.', 
                'status' => 1
            ],
        ];

        // Inserir os dados
        $this->db->table('departamentos')->insertBatch($data);
        
        echo "✓ DepartamentosSeeder: " . count($data) . " departamentos inseridos com sucesso\n";
    }
}
