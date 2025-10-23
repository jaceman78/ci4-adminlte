<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DisciplinaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // 10º Ano - Tipologia Regular (id_tipologia = 1)
            ['nome' => 'Português', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Educação Física', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'LE I - Inglês', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Física e Química A', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Biologia e Geologia', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Geometria Descritiva A', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Filosofia', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Matemática A', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Economia A', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Geografia A', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Matemática Aplicada às Ciências Sociais', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'História A', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'História da Cultura e das Artes', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Desenho A', 'horas' => 0, 'tipologia_id' => 1],
            
            // 11º Ano - Tipologia Regular (id_tipologia = 1)
            // (Reutiliza alguns nomes mas são registros diferentes)
            ['nome' => 'Português', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Educação Física', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Biologia e Geologia', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Matemática A', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Física e Química A', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Geometria Descritiva A', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Economia A', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Filosofia', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Geografia A', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'LE I - Inglês', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'LE II - Francês', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'História A', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Matemática Aplicada às Ciências Sociais', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Desenho A', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'História da Cultura e das Artes', 'horas' => 0, 'tipologia_id' => 1],
            
            // 12º Ano - Tipologia Regular (id_tipologia = 1)
            ['nome' => 'Biologia', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Educação Física', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Matemática A', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Psicologia B', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Português', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Aplicações Informáticas B', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Física', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'LE I - Inglês', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Economia C', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Geografia C', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'História A', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Oficina de Artes', 'horas' => 0, 'tipologia_id' => 1],
            ['nome' => 'Desenho A', 'horas' => 0, 'tipologia_id' => 1],
            
            // Cursos Profissionais - Tipologia Profissional (id_tipologia = 2)
            ['nome' => 'Português', 'horas' => null, 'tipologia_id' => 2],
            ['nome' => 'Matemática', 'horas' => null, 'tipologia_id' => 2],
            ['nome' => 'Física e Química', 'horas' => null, 'tipologia_id' => 2],
            ['nome' => 'Area de Integração', 'horas' => null, 'tipologia_id' => 2],
            ['nome' => 'LE I - Inglês - Nível de continuação', 'horas' => null, 'tipologia_id' => 2],
            ['nome' => 'Tecnologias da Informação e Comunicação', 'horas' => null, 'tipologia_id' => 2],
            ['nome' => 'Eletrónica Fundamental', 'horas' => null, 'tipologia_id' => 2],
            ['nome' => 'SDAC', 'horas' => null, 'tipologia_id' => 2],
            ['nome' => 'COM_DADOS', 'horas' => null, 'tipologia_id' => 2],
            ['nome' => 'Educação Física', 'horas' => null, 'tipologia_id' => 2],
            ['nome' => 'IMEI', 'horas' => null, 'tipologia_id' => 2],
        ];

        // Inserir os dados
        foreach ($data as $disciplina) {
            $this->db->table('disciplina')->insert($disciplina);
        }
        
        echo "✓ DisciplinaSeeder: " . count($data) . " disciplinas inseridas com sucesso\n";
    }
}
