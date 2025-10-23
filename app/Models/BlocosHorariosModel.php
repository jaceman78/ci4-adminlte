<?php

namespace App\Models;

use CodeIgniter\Model;

class BlocosHorariosModel extends Model
{
    protected $table            = 'blocos_horarios';
    protected $primaryKey       = 'id_bloco';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['hora_inicio', 'hora_fim', 'designacao', 'dia_semana'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'hora_inicio' => 'required|valid_time',
        'hora_fim'    => 'required|valid_time',
        'designacao'  => 'permit_empty|max_length[50]',
        'dia_semana'  => 'required|in_list[Segunda_Feira,Terca_Feira,Quarta_Feira,Quinta_Feira,Sexta_Feira,Sabado]'
    ];
    protected $validationMessages   = [
        'hora_inicio' => [
            'required'   => 'A hora de início é obrigatória',
            'valid_time' => 'A hora de início deve estar no formato válido (HH:MM:SS)'
        ],
        'hora_fim' => [
            'required'   => 'A hora de fim é obrigatória',
            'valid_time' => 'A hora de fim deve estar no formato válido (HH:MM:SS)'
        ],
        'designacao' => [
            'max_length' => 'A designação não pode exceder 50 caracteres'
        ],
        'dia_semana' => [
            'required' => 'O dia da semana é obrigatório',
            'in_list'  => 'Dia da semana inválido'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['validarHorarios'];
    protected $beforeUpdate   = ['validarHorarios'];

    /**
     * Validar se hora_fim é posterior a hora_inicio
     * 
     * @param array $data
     * @return array
     */
    protected function validarHorarios(array $data)
    {
        if (isset($data['data']['hora_inicio']) && isset($data['data']['hora_fim'])) {
            $inicio = strtotime($data['data']['hora_inicio']);
            $fim = strtotime($data['data']['hora_fim']);
            
            if ($fim <= $inicio) {
                throw new \RuntimeException('A hora de fim deve ser posterior à hora de início');
            }
        }
        
        return $data;
    }

    /**
     * Obter blocos por dia da semana
     * 
     * @param string $diaSemana
     * @return array
     */
    public function getBlocosPorDia($diaSemana)
    {
        return $this->where('dia_semana', $diaSemana)
                    ->orderBy('hora_inicio', 'ASC')
                    ->findAll();
    }

    /**
     * Obter todos os blocos agrupados por dia
     * 
     * @return array
     */
    public function getBlocosAgrupadosPorDia()
    {
        $blocos = $this->orderBy('dia_semana', 'ASC')
                       ->orderBy('hora_inicio', 'ASC')
                       ->findAll();
        
        $agrupado = [];
        foreach ($blocos as $bloco) {
            $dia = $bloco['dia_semana'];
            if (!isset($agrupado[$dia])) {
                $agrupado[$dia] = [];
            }
            $agrupado[$dia][] = $bloco;
        }
        
        return $agrupado;
    }

    /**
     * Obter blocos em intervalo de tempo
     * 
     * @param string $horaInicio
     * @param string $horaFim
     * @param string|null $diaSemana
     * @return array
     */
    public function getBlocosEmIntervalo($horaInicio, $horaFim, $diaSemana = null)
    {
        $builder = $this->where('hora_inicio >=', $horaInicio)
                        ->where('hora_fim <=', $horaFim);
        
        if ($diaSemana) {
            $builder->where('dia_semana', $diaSemana);
        }
        
        return $builder->orderBy('dia_semana', 'ASC')
                       ->orderBy('hora_inicio', 'ASC')
                       ->findAll();
    }

    /**
     * Obter lista para dropdown por dia
     * 
     * @param string $diaSemana
     * @return array [id_bloco => "HH:MM - HH:MM (Designação)"]
     */
    public function getListaDropdown($diaSemana)
    {
        $blocos = $this->getBlocosPorDia($diaSemana);
        
        $lista = [];
        foreach ($blocos as $bloco) {
            $horaInicio = substr($bloco['hora_inicio'], 0, 5);
            $horaFim = substr($bloco['hora_fim'], 0, 5);
            $designacao = $bloco['designacao'] ? ' (' . $bloco['designacao'] . ')' : '';
            
            $lista[$bloco['id_bloco']] = $horaInicio . ' - ' . $horaFim . $designacao;
        }
        
        return $lista;
    }

    /**
     * Obter lista completa para dropdown (todos os blocos)
     * 
     * @return array [id_bloco => "Dia - HH:MM - HH:MM (Designação)"]
     */
    public function getListaDropdownCompleta()
    {
        $blocos = $this->orderBy('dia_semana', 'ASC')
                       ->orderBy('hora_inicio', 'ASC')
                       ->findAll();
        
        $diasSemana = $this->getDiasSemana();
        
        $lista = [];
        foreach ($blocos as $bloco) {
            $horaInicio = substr($bloco['hora_inicio'], 0, 5);
            $horaFim = substr($bloco['hora_fim'], 0, 5);
            $designacao = $bloco['designacao'] ? ' (' . $bloco['designacao'] . ')' : '';
            $dia = str_replace('_', '-', $bloco['dia_semana']);
            
            $lista[$bloco['id_bloco']] = $dia . ' | ' . $horaInicio . ' - ' . $horaFim . $designacao;
        }
        
        return $lista;
    }

    /**
     * Verificar se há conflito de horários
     * 
     * @param string $diaSemana
     * @param string $horaInicio
     * @param string $horaFim
     * @param int|null $excluirId
     * @return bool
     */
    public function verificarConflitoHorario($diaSemana, $horaInicio, $horaFim, $excluirId = null)
    {
        $builder = $this->where('dia_semana', $diaSemana)
                        ->groupStart()
                            ->where('hora_inicio <', $horaFim)
                            ->where('hora_fim >', $horaInicio)
                        ->groupEnd();
        
        if ($excluirId) {
            $builder->where('id_bloco !=', $excluirId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Obter dias da semana disponíveis
     * 
     * @return array
     */
    public function getDiasSemana()
    {
        return [
            'Segunda_Feira' => 'Segunda-Feira',
            'Terca_Feira'   => 'Terça-Feira',
            'Quarta_Feira'  => 'Quarta-Feira',
            'Quinta_Feira'  => 'Quinta-Feira',
            'Sexta_Feira'   => 'Sexta-Feira',
            'Sabado'        => 'Sábado'
        ];
    }

    /**
     * Contar blocos por dia da semana
     * 
     * @return array
     */
    public function contarBlocosPorDia()
    {
        return $this->select('dia_semana, COUNT(*) as total')
                    ->groupBy('dia_semana')
                    ->orderBy('dia_semana', 'ASC')
                    ->findAll();
    }

    /**
     * Obter bloco por hora específica e dia
     * 
     * @param string $diaSemana
     * @param string $hora
     * @return array|null
     */
    public function getBlocoPorHora($diaSemana, $hora)
    {
        return $this->where('dia_semana', $diaSemana)
                    ->where('hora_inicio <=', $hora)
                    ->where('hora_fim >', $hora)
                    ->first();
    }

    /**
     * Obter estatísticas gerais
     * 
     * @return array
     */
    public function getEstatisticas()
    {
        $total = $this->countAllResults(false);
        $porDia = $this->contarBlocosPorDia();
        
        return [
            'total'                => $total,
            'total_dias'           => count($porDia),
            'blocos_por_dia'       => $porDia,
            'primeiro_bloco'       => $this->orderBy('hora_inicio', 'ASC')->first(),
            'ultimo_bloco'         => $this->orderBy('hora_fim', 'DESC')->first()
        ];
    }
}
