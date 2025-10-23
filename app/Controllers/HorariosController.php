<?php

namespace App\Controllers;

use App\Models\HorarioAulasModel;
use App\Models\TurmaModel;
use App\Models\DisciplinaModel;
use App\Models\SalasModel;
use App\Models\UserModel;

class HorariosController extends BaseController
{
    protected $horarioModel;
    protected $turmaModel;
    protected $disciplinaModel;
    protected $salaModel;
    protected $userModel;

    public function __construct()
    {
        $this->horarioModel = new HorarioAulasModel();
        $this->turmaModel = new TurmaModel();
        $this->disciplinaModel = new DisciplinaModel();
        $this->salaModel = new SalasModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Verificar nível de acesso
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel < 6) {
            return redirect()->to('/')->with('error', 'Acesso negado');
        }

        $data = [
            'title' => 'Gestão de Horários',
            'page_title' => 'Gestão de Horários',
            'page_subtitle' => 'Listagem e gestão de horários de aulas',
            'turmas' => $this->turmaModel->getListaDropdownPorCodigo(),
            'disciplinas' => $this->disciplinaModel->getListaAgrupadaPorTipologia(),
            'salas' => $this->salaModel->orderBy('codigo_sala', 'ASC')->findAll(),
            'professores' => $this->userModel
                ->select('id, name, NIF, level')
                ->where('level', 5)
                ->where('NIF IS NOT NULL', null, false)
                ->where('NIF !=', '')
                ->orderBy('name', 'ASC')
                ->findAll(),
            'diasSemana' => $this->horarioModel->getDiasSemana()
        ];

        return view('gestao_letiva/horarios_index', $data);
    }

    public function getDataTable()
    {
        $horarios = $this->horarioModel->getHorarioCompleto();

        $diasSemana = $this->horarioModel->getDiasSemana();
        $dados = [];

        foreach ($horarios as $horario) {
            $horaInicio = $this->formatarHoraParaInterface($horario['hora_inicio'] ?? null);
            $horaFim = $this->formatarHoraParaInterface($horario['hora_fim'] ?? null);
            $intervalo = $horario['intervalo'] ?? null;
            if (!$intervalo && $horaInicio && $horaFim) {
                $intervalo = $horaInicio . ' - ' . $horaFim;
            }

            $diaSemanaValor = $horario['dia_semana'] !== null ? (int) $horario['dia_semana'] : null;

            $dados[] = [
                'id_aula'          => $horario['id_aula'],
                'codigo_turma'     => $horario['codigo_turma'],
                'turma_nome'       => $horario['nome_turma'] ?? '',
                'turma_label'      => $this->formatarTurmaLabel($horario['codigo_turma'] ?? '', $horario['nome_turma'] ?? '', $horario['ano_turma'] ?? null),
                'disciplina_nome'  => $horario['nome_disciplina'] ?? '',
                'professor_nome'   => $horario['nome_professor'] ?? '',
                'user_nif'         => $horario['user_nif'],
                'sala_id'          => $horario['sala_id'] ?? '',
                'sala_label'       => $this->formatarSalaLabel($horario['sala_id'] ?? '', $horario['sala_descricao'] ?? ''),
                'dia_semana'       => $diaSemanaValor ?? '',
                'dia_semana_label' => ($diaSemanaValor !== null && isset($diasSemana[$diaSemanaValor])) ? $diasSemana[$diaSemanaValor] : '',
                'hora_inicio'      => $horaInicio,
                'hora_fim'         => $horaFim,
                'intervalo'        => $intervalo,
                'tempo'            => $horario['tempo'],
                'turno'            => $horario['turno'] ?? '',
                'turno_label'      => $horario['turno'] ?? 'Sem turno'
            ];
        }

        return $this->response->setJSON(['data' => $dados]);
    }

    public function create()
    {
        $data = $this->extrairDadosFormulario();

        if ($mensagemErro = $this->validarIntervaloHoras($data['hora_inicio'], $data['hora_fim'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $mensagemErro
            ]);
        }

        // Verificar conflitos
        $conflitos = $this->verificarConflitos($data);
        if (!empty($conflitos)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Conflitos detectados',
                'conflitos' => $conflitos
            ]);
        }
        
        $horarioId = $this->horarioModel->insert($data);
        
        if ($horarioId) {
            // LOG: Registar criação
            try {
                $userId = session()->get('LoggedUserData')['id'] ?? null;
                $turma = $data['turma_codigo'] ?? '';
                $disciplina = $data['disciplina_id'] ?? '';
                $dia = $data['dia_semana'] ?? '';
                $inicio = $data['hora_inicio'] ?? '';
                $fim = $data['hora_fim'] ?? '';
                
                log_activity(
                    $userId,
                    'horarios',
                    'create',
                    "Criou horário: Turma {$turma}, Disciplina {$disciplina}, {$dia} ({$inicio}-{$fim})",
                    $horarioId,
                    null,
                    $data
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de criação de horário: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Horário criado com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao criar horário',
            'errors' => $this->horarioModel->errors()
        ]);
    }

    public function update($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        
        // Buscar dados anteriores
        $dadosAnteriores = $this->horarioModel->find($id);
        
        $data = $this->extrairDadosFormulario();

        if ($mensagemErro = $this->validarIntervaloHoras($data['hora_inicio'], $data['hora_fim'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $mensagemErro
            ]);
        }

        // Verificar conflitos (excluindo o próprio horário)
        $conflitos = $this->verificarConflitos($data, $id);
        if (!empty($conflitos)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Conflitos detectados',
                'conflitos' => $conflitos
            ]);
        }
        
        if ($this->horarioModel->update($id, $data)) {
            // LOG: Registar atualização
            try {
                $turma = $data['turma_codigo'] ?? $dadosAnteriores['turma_codigo'] ?? '';
                $disciplina = $data['disciplina_id'] ?? $dadosAnteriores['disciplina_id'] ?? '';
                $dia = $data['dia_semana'] ?? $dadosAnteriores['dia_semana'] ?? '';
                
                log_activity(
                    $userId,
                    'horarios',
                    'update',
                    "Atualizou horário: Turma {$turma}, Disciplina {$disciplina}, {$dia}",
                    $id,
                    $dadosAnteriores,
                    $data
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de atualização de horário: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Horário atualizado com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar horário',
            'errors' => $this->horarioModel->errors()
        ]);
    }

    public function delete($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        
        // Buscar dados antes de eliminar
        $horario = $this->horarioModel->find($id);
        
        if (!$horario) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Horário não encontrado'
            ]);
        }
        
        if ($this->horarioModel->delete($id)) {
            // LOG: Registar eliminação
            try {
                $turma = $horario['turma_codigo'] ?? '';
                $disciplina = $horario['disciplina_id'] ?? '';
                $dia = $horario['dia_semana'] ?? '';
                $inicio = $horario['hora_inicio'] ?? '';
                $fim = $horario['hora_fim'] ?? '';
                
                log_activity(
                    $userId,
                    'horarios',
                    'delete',
                    "Eliminou horário: Turma {$turma}, Disciplina {$disciplina}, {$dia} ({$inicio}-{$fim})",
                    $id,
                    $horario,
                    null
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de eliminação de horário: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Horário excluído com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao excluir horário'
        ]);
    }

    public function get($id)
    {
        $horario = $this->horarioModel->find($id);
        
        if ($horario) {
            $horario['hora_inicio'] = $this->formatarHoraParaInterface($horario['hora_inicio'] ?? null);
            $horario['hora_fim'] = $this->formatarHoraParaInterface($horario['hora_fim'] ?? null);
            return $this->response->setJSON($horario);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Horário não encontrado'
        ]);
    }

    private function verificarConflitos($data, $excluirId = null)
    {
        $conflitos = [];
        
        if (!empty($data['user_nif']) && $data['dia_semana'] !== null && $this->horarioModel->verificarConflitoProfessor(
            $data['user_nif'],
            (int) $data['dia_semana'],
            $data['hora_inicio'],
            $data['hora_fim'],
            $excluirId
        )) {
            $conflitos[] = 'Professor já possui aula neste horário';
        }

        if (!empty($data['codigo_turma']) && $data['dia_semana'] !== null && $this->horarioModel->verificarConflitoTurma(
            $data['codigo_turma'],
            (int) $data['dia_semana'],
            $data['hora_inicio'],
            $data['hora_fim'],
            $excluirId
        )) {
            $conflitos[] = 'Turma já possui aula neste horário';
        }

        if (!empty($data['sala_id']) && $data['dia_semana'] !== null && $this->horarioModel->verificarConflitoSala(
            $data['sala_id'],
            (int) $data['dia_semana'],
            $data['hora_inicio'],
            $data['hora_fim'],
            $excluirId
        )) {
            $conflitos[] = 'Sala já está ocupada neste horário';
        }

        return $conflitos;
    }

    private function extrairDadosFormulario(): array
    {
        $dados = $this->request->getPost([
            'codigo_turma',
            'disciplina_id',
            'user_nif',
            'sala_id',
            'turno',
            'dia_semana',
            'tempo',
            'hora_inicio',
            'hora_fim'
        ]);

        $dados['codigo_turma'] = trim($dados['codigo_turma'] ?? '');
        $dados['disciplina_id'] = trim($dados['disciplina_id'] ?? '');
        $dados['user_nif'] = trim($dados['user_nif'] ?? '');
        $dados['sala_id'] = trim($dados['sala_id'] ?? '');
    $dados['turno'] = isset($dados['turno']) ? trim($dados['turno']) : null;
        $dados['dia_semana'] = isset($dados['dia_semana']) ? (int) $dados['dia_semana'] : null;
        $dados['tempo'] = isset($dados['tempo']) && $dados['tempo'] !== '' ? (int) $dados['tempo'] : null;

        $horaInicio = $this->formatarHoraParaPersistencia($dados['hora_inicio'] ?? null);
        $horaFim = $this->formatarHoraParaPersistencia($dados['hora_fim'] ?? null);
        $dados['hora_inicio'] = $horaInicio;
        $dados['hora_fim'] = $horaFim;

        if ($horaInicio && $horaFim) {
            $dados['intervalo'] = $this->construirIntervalo($horaInicio, $horaFim);
        }

        if ($dados['turno'] === '') {
            $dados['turno'] = null;
        }

        if ($dados['sala_id'] === '') {
            $dados['sala_id'] = null;
        }

        return $dados;
    }

    private function construirIntervalo(?string $horaInicio, ?string $horaFim): ?string
    {
        if (!$horaInicio || !$horaFim) {
            return null;
        }

        return substr($horaInicio, 0, 5) . ' - ' . substr($horaFim, 0, 5);
    }

    private function formatarHoraParaPersistencia(?string $hora): ?string
    {
        if (!$hora) {
            return null;
        }

        $hora = trim($hora);
        if ($hora === '') {
            return null;
        }

        if (strlen($hora) === 5) {
            return $hora . ':00';
        }

        return $hora;
    }

    private function formatarHoraParaInterface(?string $hora): ?string
    {
        if (!$hora) {
            return null;
        }

        return substr($hora, 0, 5);
    }

    private function formatarTurmaLabel(string $codigo, string $nome, $ano): string
    {
        if ($codigo === '' && $nome === '') {
            return '';
        }

        $anoLabel = is_numeric($ano) ? ($ano == 0 ? 'Pré' : $ano . 'º') : '';
        $partes = array_filter([
            $codigo,
            $anoLabel !== '' ? $anoLabel . ' - ' . $nome : $nome
        ], static function ($valor) {
            return $valor !== null && $valor !== '';
        });

        return implode(' | ', $partes);
    }

    private function formatarSalaLabel(string $codigo, ?string $descricao): string
    {
        if ($codigo === '') {
            return '';
        }

        $label = $codigo;
        if ($descricao) {
            $label .= ' - ' . trim($descricao);
        }

        return $label;
    }

    private function validarIntervaloHoras(?string $horaInicio, ?string $horaFim): ?string
    {
        if (!$horaInicio || !$horaFim) {
            return null;
        }

        if ($horaFim <= $horaInicio) {
            return 'A hora de fim deve ser posterior à hora de início.';
        }

        return null;
    }

    /**
     * Importar horários a partir de ficheiro CSV
     */
    public function importarCSV()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Verificar nível de acesso
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel < 6) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        $file = $this->request->getFile('csv_file');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nenhum ficheiro foi enviado ou o ficheiro é inválido.'
            ]);
        }

        // Verificar extensão
        $extension = $file->getExtension();
        if (!in_array($extension, ['csv', 'txt'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Formato de ficheiro inválido. Use .csv ou .txt'
            ]);
        }

        // Ler o ficheiro
        $filepath = $file->getTempName();
        
        // Detectar encoding e converter para UTF-8
        $content = file_get_contents($filepath);
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            // Salvar conteúdo convertido temporariamente
            $tempFile = tempnam(sys_get_temp_dir(), 'horarios_');
            file_put_contents($tempFile, $content);
            $filepath = $tempFile;
        }
        
        $handle = fopen($filepath, 'r');

        if (!$handle) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao abrir o ficheiro.'
            ]);
        }

        $totalLinhas = 0;
        $sucessos = 0;
        $erros = 0;
        $detalhesErros = [];
        $primeiraLinha = true;

        // Ler linha por linha
        while (($linha = fgets($handle)) !== false) {
            // Pular primeira linha (cabeçalho)
            if ($primeiraLinha) {
                $primeiraLinha = false;
                continue;
            }

            $totalLinhas++;

            // Separar por TAB ou ponto e vírgula
            $campos = explode("\t", trim($linha));
            if (count($campos) < 10) {
                // Tentar separar por ponto e vírgula
                $campos = explode(";", trim($linha));
            }

            // Verificar se tem todos os campos necessários (10 campos)
            if (count($campos) < 10) {
                $erros++;
                $detalhesErros[] = [
                    'linha' => $totalLinhas + 1,
                    'erro' => 'Número de campos insuficiente. Esperado: 10, Encontrado: ' . count($campos)
                ];
                continue;
            }

            // Extrair campos
            $codigoTurma = trim($campos[0]);
            $disciplinaId = trim($campos[1]);
            $userNif = trim($campos[2]);
            $salaId = trim($campos[3]);
            $turno = trim($campos[4]);
            $diaSemana = trim($campos[5]);
            $tempo = trim($campos[6]);
            $intervalo = trim($campos[7]);
            $horaInicio = trim($campos[8]);
            $horaFim = trim($campos[9]);

            // Validações básicas
            $errosLinha = [];

            if (empty($codigoTurma)) {
                $errosLinha[] = 'codigo_turma é obrigatório';
            }

            if (empty($disciplinaId)) {
                $errosLinha[] = 'disciplina_id é obrigatório';
            }

            if (empty($userNif)) {
                $errosLinha[] = 'user_nif é obrigatório';
            }

            if (empty($diaSemana) || !is_numeric($diaSemana) || $diaSemana < 2 || $diaSemana > 7) {
                $errosLinha[] = 'dia deve ser um número entre 2 e 7';
            }

            if (empty($horaInicio) || !preg_match('/^\d{2}:\d{2}$/', $horaInicio)) {
                $errosLinha[] = 'hora_inicio inválida (formato: HH:MM)';
            }

            if (empty($horaFim) || !preg_match('/^\d{2}:\d{2}$/', $horaFim)) {
                $errosLinha[] = 'hora_fim inválida (formato: HH:MM)';
            }

            if (!empty($turno) && !in_array($turno, ['T1', 'T2'])) {
                $errosLinha[] = 'Turno deve ser T1, T2 ou vazio';
            }

            if (count($errosLinha) > 0) {
                $erros++;
                $detalhesErros[] = [
                    'linha' => $totalLinhas + 1,
                    'erro' => implode(', ', $errosLinha)
                ];
                continue;
            }

            // Preparar dados para inserção
            $dados = [
                'codigo_turma' => $codigoTurma,
                'disciplina_id' => $disciplinaId,
                'user_nif' => $userNif,
                'sala_id' => !empty($salaId) ? $salaId : null,
                'turno' => !empty($turno) ? $turno : null,
                'dia_semana' => (int)$diaSemana,
                'tempo' => !empty($tempo) && is_numeric($tempo) ? (int)$tempo : null,
                'intervalo' => !empty($intervalo) ? $intervalo : $horaInicio . ' - ' . $horaFim,
                'hora_inicio' => $horaInicio . ':00',
                'hora_fim' => $horaFim . ':00'
            ];

            // Tentar inserir
            try {
                if ($this->horarioModel->insert($dados)) {
                    $sucessos++;
                } else {
                    $erros++;
                    $validationErrors = $this->horarioModel->errors();
                    $detalhesErros[] = [
                        'linha' => $totalLinhas + 1,
                        'erro' => 'Erro de validação: ' . implode(', ', $validationErrors)
                    ];
                }
            } catch (\Exception $e) {
                $erros++;
                $detalhesErros[] = [
                    'linha' => $totalLinhas + 1,
                    'erro' => 'Erro ao inserir: ' . $e->getMessage()
                ];
            }
        }

        fclose($handle);

        // LOG: Resumo da importação
        try {
            log_activity(
                session()->get('LoggedUserData')['id'] ?? null,
                'horarios',
                'import',
                "Importação CSV de horários concluída (Sucesso: {$sucessos}, Erros: {$erros})",
                null,
                null,
                null,
                [
                    'arquivo' => $file->getName(),
                    'total_processados' => $totalLinhas,
                    'sucesso' => $sucessos,
                    'erros' => $erros
                ]
            );
        } catch (\Exception $e) {
            log_message('error', 'Erro ao registar log de resumo de importação de horários: ' . $e->getMessage());
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Importação concluída',
            'total' => $totalLinhas,
            'sucesso' => $sucessos,
            'erros' => $erros,
            'detalhes_erros' => $detalhesErros
        ]);
    }
}
