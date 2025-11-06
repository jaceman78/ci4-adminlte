<?php

namespace App\Controllers;

use App\Models\HorarioAulasModel;
use App\Models\BlocosHorariosModel;
use App\Models\UserModel;
use App\Models\PermutaModel;
use App\Models\TurmaModel;
use App\Models\SalasModel;
use App\Models\AulasCreditoModel;
use App\Models\AnoLetivoModel;
use App\Models\LogsModel;

class PermutasController extends BaseController
{
    protected $horarioModel;
    protected $blocosModel;
    protected $userModel;
    protected $permutaModel;
    protected $turmaModel;
    protected $creditoModel;
    protected $anoLetivoModel;
    protected $logsModel;

    public function __construct()
    {
        helper('logs'); // Carregar helper de logs
        
        $this->horarioModel = new HorarioAulasModel();
        $this->blocosModel = new BlocosHorariosModel();
        $this->userModel = new UserModel();
        $this->permutaModel = new PermutaModel();
        $this->turmaModel = new TurmaModel();
        $this->logsModel = new LogsModel();
        $this->creditoModel = new AulasCreditoModel();
        $this->anoLetivoModel = new AnoLetivoModel();
    }

    /**
     * Página principal - Meu Horário e Pedir Permuta
     */
    public function index()
    {
        // Verificar se o usuário está logado
        $userData = session()->get('LoggedUserData');
        if (!$userData) {
            return redirect()->to('/login')->with('error', 'É necessário fazer login');
        }

        $userNif = $userData['NIF'] ?? null;
        $userLevel = $userData['level'] ?? 0;

        // Inicializar variáveis
        $blocos = [];
        $horarioProfessor = [];
        $semNif = false;

        // Se o usuário não tiver NIF, mostrar página vazia com mensagem
        if (!$userNif) {
            $semNif = true;
        } else {
            // Buscar todos os blocos horários (estrutura base do horário)
            // Excluir bloco 0 (reservado para visitas de estudo)
            $blocos = $this->blocosModel
                ->select('id_bloco, hora_inicio, hora_fim, designacao, dia_semana')
                ->where('id_bloco !=', 0)
                ->orderBy('dia_semana', 'ASC')
                ->orderBy('hora_inicio', 'ASC')
                ->findAll();

            // Buscar horário do professor
            $horarioProfessor = $this->horarioModel->getHorarioProfessor($userNif);
        }

        // Organizar blocos por dia da semana
        $diasSemana = [
            'Segunda_Feira' => 2,
            'Terca_Feira' => 3,
            'Quarta_Feira' => 4,
            'Quinta_Feira' => 5,
            'Sexta_Feira' => 6
        ];

        // Agrupar blocos por dia
        $blocosPorDia = [];
        foreach ($blocos as $bloco) {
            $diaNome = $bloco['dia_semana'];
            if (!isset($blocosPorDia[$diaNome])) {
                $blocosPorDia[$diaNome] = [];
            }
            $blocosPorDia[$diaNome][] = $bloco;
        }

        // Mapear horário do professor para estrutura de grade
        $horarioGrid = [];
        foreach ($horarioProfessor as $aula) {
            $diaSemana = (int)$aula['dia_semana'];
            $horaInicio = substr($aula['hora_inicio'], 0, 5); // HH:MM
            $horaFim = substr($aula['hora_fim'], 0, 5); // HH:MM

            // Encontrar o dia correspondente
            $diaNomeKey = array_search($diaSemana, $diasSemana);
            
            if (!isset($horarioGrid[$diaNomeKey])) {
                $horarioGrid[$diaNomeKey] = [];
            }

            $horarioGrid[$diaNomeKey][] = [
                'id_aula' => $aula['id_aula'],
                'hora_inicio' => $horaInicio,
                'hora_fim' => $horaFim,
                'disciplina' => $aula['nome_disciplina'] ?? 'N/A',
                'turma' => ($aula['codigo_turma'] ?? '') . ' - ' . ($aula['nome_turma'] ?? ''),
                'sala' => $aula['codigo_sala'] ?? 'N/A',
                'turno' => $aula['turno'] ?? '',
                'tempo' => $aula['tempo'] ?? ''
            ];
        }

        $data = [
            'title' => 'Meu Horário',
            'page_title' => 'Meu Horário',
            'page_subtitle' => 'Visualize seu horário e solicite permutas',
            'blocosPorDia' => $blocosPorDia ?? [],
            'horarioGrid' => $horarioGrid ?? [],
            'diasSemana' => $diasSemana ?? [],
            'userNif' => $userNif,
            'userName' => $userData['name'] ?? 'Professor',
            'semNif' => $semNif ?? false
        ];

        return view('permutas/meu_horario', $data);
    }

    /**
     * Página de gestão de permutas do professor
     */
    public function minhasPermutas()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData) {
            return redirect()->to('/login')->with('error', 'É necessário fazer login');
        }

        $userNif = $userData['NIF'] ?? null;
        
        if (!$userNif) {
            return redirect()->to('/dashboard')->with('error', 'NIF não encontrado no seu perfil');
        }

        // Buscar permutas do professor
        $permutas = $this->permutaModel->getPermutasProfessor($userNif);
        
        // Buscar estatísticas
        $stats = $this->permutaModel->getEstatisticasProfessor($userNif);

        $data = [
            'title' => 'As Minhas Permutas',
            'page_title' => 'As Minhas Permutas',
            'page_subtitle' => 'Gerir os seus pedidos de permuta',
            'permutas' => $permutas,
            'stats' => $stats,
            'userNif' => $userNif
        ];

        return view('permutas/minhas_permutas', $data);
    }

    /**
     * Formulário para solicitar permuta
     */
    public function pedirPermuta($idAula = null)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData) {
            return redirect()->to('/login')->with('error', 'É necessário fazer login');
        }

        $userNif = $userData['NIF'] ?? null;
        if (!$userNif) {
            return redirect()->to('/permutas')->with('error', 'NIF não encontrado no seu perfil');
        }

        if (!$idAula) {
            return redirect()->to('/permutas')->with('error', 'Aula não especificada');
        }

        // Buscar informações da aula
        $aula = $this->horarioModel
            ->select('horario_aulas.*, 
                     disciplina.abreviatura as disciplina_abrev, 
                     disciplina.descritivo as disciplina_nome,
                     turma.nome as turma_nome, 
                     turma.codigo as turma_codigo,
                     turma.ano')
            ->join('disciplina', 'disciplina.descritivo = horario_aulas.disciplina_id', 'left')
            ->join('turma', 'turma.codigo = horario_aulas.codigo_turma', 'left')
            ->where('horario_aulas.id_aula', $idAula)
            ->where('horario_aulas.user_nif', $userNif)
            ->first();

        if (!$aula) {
            return redirect()->to('/permutas')->with('error', 'Aula não encontrada ou não pertence a você');
        }

        // Buscar professores da mesma turma
        $professoresTurma = [];
        if (!empty($aula['codigo_turma'])) {
            $professoresTurma = $this->horarioModel
                ->select('user.NIF, user.name, user.email')
                ->distinct()
                ->join('user', 'user.NIF = horario_aulas.user_nif', 'inner')
                ->where('horario_aulas.codigo_turma', $aula['codigo_turma'])
                ->orderBy('user.name', 'ASC')
                ->findAll();
        }
        
        // Se não encontrou professores ou não tem turma, buscar todos os professores com NIF
        if (empty($professoresTurma)) {
            $professoresTurma = $this->userModel
                ->select('NIF, name, email')
                ->where('NIF IS NOT NULL')
                ->orderBy('name', 'ASC')
                ->findAll();
        }

        // Buscar outras aulas do professor no mesmo dia E na mesma turma
        $aulasNoDia = $this->horarioModel
            ->select('horario_aulas.*, 
                     disciplina.abreviatura as disciplina_abrev,
                     disciplina.descritivo as disciplina_nome,
                     turma.nome as turma_nome')
            ->join('disciplina', 'disciplina.descritivo = horario_aulas.disciplina_id', 'left')
            ->join('turma', 'turma.codigo = horario_aulas.codigo_turma', 'left')
            ->where('horario_aulas.user_nif', $userNif)
            ->where('horario_aulas.dia_semana', $aula['dia_semana'])
            ->where('horario_aulas.codigo_turma', $aula['codigo_turma'])  // MESMA TURMA
            ->where('horario_aulas.id_aula !=', $idAula)
            ->orderBy('horario_aulas.hora_inicio', 'ASC')
            ->findAll();

        // Não carregar salas aqui - serão carregadas via AJAX após selecionar a data de reposição

        // Buscar ano letivo ativo
        $anoLetivoAtivo = $this->anoLetivoModel->where('status', 1)->first();
        
        // Verificar se há créditos disponíveis para esta aula
        $creditosDisponiveis = [];
        if ($anoLetivoAtivo) {
            $creditosDisponiveis = $this->creditoModel
                ->select('aulas_credito.*, ano_letivo.anoletivo')
                ->join('ano_letivo', 'ano_letivo.id_anoletivo = aulas_credito.ano_letivo_id', 'left')
                ->where('aulas_credito.professor_nif', $userNif)
                ->where('aulas_credito.codigo_turma', $aula['codigo_turma'])
                ->where('aulas_credito.disciplina_id', $aula['disciplina_id'])
                ->where('aulas_credito.estado', 'disponivel')
                ->where('aulas_credito.ano_letivo_id', $anoLetivoAtivo['id_anoletivo'])
                ->groupStart()
                    ->where('aulas_credito.turno', $aula['turno'])
                    ->orWhere('aulas_credito.turno IS NULL')
                ->groupEnd()
                ->orderBy('aulas_credito.turno', 'DESC') // Prioriza turnos específicos
                ->orderBy('aulas_credito.data_visita', 'DESC')
                ->findAll();
        }

        $data = [
            'title' => 'Pedir Permuta',
            'page_title' => 'Solicitar Permuta',
            'page_subtitle' => 'Preencha os dados da permuta',
            'aula' => $aula,
            'professores' => $professoresTurma,
            'aulasNoDia' => $aulasNoDia,
            'userNif' => $userNif,
            'creditosDisponiveis' => $creditosDisponiveis
        ];

        return view('permutas/form_permuta', $data);
    }

    /**
     * Lista completa de permutas (apenas direção/admins - nível 6+)
     */
    public function listaPermutas()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 6) {
            return redirect()->to('/dashboard')->with('error', 'Sem permissão para aceder a esta página');
        }

        // Buscar todas as permutas com informações completas
        $todasPermutas = $this->permutaModel
            ->select('permutas.*, 
                permutas.data_aula_original,
                ha.dia_semana, ha.hora_inicio, ha.hora_fim, ha.codigo_turma, ha.disciplina_id,
                d.id_disciplina, d.abreviatura as disciplina_abrev, d.descritivo as disciplina_nome,
                t.nome as turma_nome, t.ano,
                s_orig.codigo_sala as sala_original_codigo,
                s_perm.codigo_sala as sala_permutada_codigo,
                autor.name as professor_autor_nome, autor.email as professor_autor_email,
                substituto.name as professor_substituto_nome, substituto.email as professor_substituto_email,
                aprovador.name as aprovador_nome')
            ->join('horario_aulas ha', 'ha.id_aula = permutas.aula_original_id', 'left')
            ->join('disciplina d', 'd.descritivo = ha.disciplina_id', 'left')
            ->join('turma t', 't.codigo = ha.codigo_turma', 'left')
            ->join('salas s_orig', 's_orig.codigo_sala = ha.sala_id', 'left')
            ->join('salas s_perm', 's_perm.codigo_sala = permutas.sala_permutada_id', 'left')
            ->join('user autor', 'autor.NIF = permutas.professor_autor_nif', 'left')
            ->join('user substituto', 'substituto.NIF = permutas.professor_substituto_nif', 'left')
            ->join('user aprovador', 'aprovador.id = permutas.aprovada_por_user_id', 'left')
            ->orderBy('permutas.data_aula_original', 'DESC')
            ->orderBy('permutas.created_at', 'DESC')
            ->findAll();

        // Separar permutas por status e data
        $permutasFuturas = [];
        $permutasPassadas = [];
        $dataAtual = date('Y-m-d');

        foreach ($todasPermutas as $permuta) {
            // Considerar data da aula original para classificar
            $dataPermuta = $permuta['data_aula_original'] ?? null;
            
            if ($dataPermuta && $dataPermuta >= $dataAtual) {
                $permutasFuturas[] = $permuta;
            } else {
                $permutasPassadas[] = $permuta;
            }
        }

        $data = [
            'title' => 'Lista de Permutas',
            'page_title' => 'Lista Completa de Permutas',
            'page_subtitle' => 'Gestão e consulta de todas as permutas',
            'permutasFuturas' => $permutasFuturas,
            'permutasPassadas' => $permutasPassadas,
            'isAdmin' => true
        ];

        return view('permutas/lista_permutas', $data);
    }

    /**
     * Listar permutas pendentes para aprovação (apenas direção/admins)
     */
    public function aprovarPermutas()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 6) {
            return redirect()->to('/dashboard')->with('error', 'Sem permissão para aceder a esta página');
        }

        // Buscar todas as permutas pendentes
        $permutasPendentes = $this->permutaModel
            ->select('permutas.*, 
                ha.dia_semana, ha.hora_inicio, ha.hora_fim,
                d.abreviatura as disciplina_abrev, d.descritivo as disciplina_nome,
                t.nome as turma_nome, t.ano,
                s.codigo_sala, s.descricao as sala_descricao,
                autor.name as professor_autor_nome, autor.email as professor_autor_email,
                substituto.name as professor_substituto_nome, substituto.email as professor_substituto_email')
            ->join('horario_aulas ha', 'ha.id_aula = permutas.aula_original_id', 'left')
            ->join('disciplina d', 'd.descritivo = ha.disciplina_id', 'left')
            ->join('turma t', 't.codigo = ha.codigo_turma', 'left')
            ->join('salas s', 's.codigo_sala = permutas.sala_permutada_id', 'left')
            ->join('user autor', 'autor.NIF = permutas.professor_autor_nif', 'left')
            ->join('user substituto', 'substituto.NIF = permutas.professor_substituto_nif', 'left')
            ->where('permutas.estado', 'pendente')
            ->orderBy('permutas.created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Aprovar Permutas',
            'page_title' => 'Permutas Pendentes de Aprovação',
            'page_subtitle' => 'Gerir pedidos de permuta',
            'permutas' => $permutasPendentes
        ];

        return view('permutas/aprovar_permutas', $data);
    }

    /**
     * Processar e salvar permuta
     */
    public function salvarPermuta()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sessão expirada']);
        }

        $userNif = $userData['NIF'] ?? null;
        if (!$userNif) {
            return $this->response->setJSON(['success' => false, 'message' => 'NIF não encontrado']);
        }

        // Obter dados do POST
        $post = $this->request->getPost();
        
        // DEBUG - Log dos dados recebidos
        log_message('debug', 'POST recebido: ' . json_encode($post));
        
        // Verificar se está usando créditos (pode ser array agora)
        $usarCreditoIds = $post['usar_credito_id'] ?? null;
        if ($usarCreditoIds && !is_array($usarCreditoIds)) {
            $usarCreditoIds = [$usarCreditoIds]; // Converter single para array
        }
        
        // DEBUG - Log da verificação de créditos
        log_message('debug', 'Usando créditos: ' . ($usarCreditoIds ? 'SIM' : 'NÃO') . ' | IDs: ' . json_encode($usarCreditoIds));
        
        // Se for crédito, validar e forçar valores ANTES da validação de regras
        if ($usarCreditoIds && !empty($usarCreditoIds)) {
            $creditosValidos = [];
            
            // Calcular quantas aulas serão permutadas
            $totalAulas = 1; // Aula principal
            if (!empty($post['aulas_adicionais']) && is_array($post['aulas_adicionais'])) {
                $totalAulas += count($post['aulas_adicionais']);
            }
            
            // Verificar se há créditos suficientes
            if (count($usarCreditoIds) < $totalAulas) {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => "Precisa selecionar {$totalAulas} crédito(s) para cobrir todas as aulas (1 principal + " . ($totalAulas-1) . " adicional(is))"
                ]);
            }
            
            foreach ($usarCreditoIds as $creditoId) {
                // Validar que o crédito pertence ao professor e está disponível
                $credito = $this->creditoModel->find($creditoId);
                if (!$credito) {
                    return $this->response->setJSON(['success' => false, 'message' => "Crédito {$creditoId} não encontrado"]);
                }
                if ($credito['professor_nif'] != $userNif) {
                    return $this->response->setJSON(['success' => false, 'message' => "Crédito {$creditoId} não pertence a você"]);
                }
                if ($credito['estado'] != 'disponivel') {
                    return $this->response->setJSON(['success' => false, 'message' => "Crédito {$creditoId} já foi usado"]);
                }
                
                $creditosValidos[] = $credito;
            }
            
            // Validar correspondência de turnos entre créditos e aulas
            $aulaOriginal = $this->horarioModel->find($post['aula_original_id']);
            if (!$aulaOriginal) {
                return $this->response->setJSON(['success' => false, 'message' => 'Aula original não encontrada']);
            }
            
            $aulasParaValidar = [$aulaOriginal];
            if (!empty($post['aulas_adicionais']) && is_array($post['aulas_adicionais'])) {
                foreach ($post['aulas_adicionais'] as $aulaId) {
                    $aulaExtra = $this->horarioModel->find($aulaId);
                    if ($aulaExtra) {
                        $aulasParaValidar[] = $aulaExtra;
                    }
                }
            }
            
            // Verificar se os turnos dos créditos correspondem aos turnos das aulas
            $turnosCreditos = array_column($creditosValidos, 'turno');
            $turnosAulas = array_column($aulasParaValidar, 'turno');
            
            foreach ($turnosAulas as $turnoAula) {
                if (!in_array($turnoAula, $turnosCreditos)) {
                    return $this->response->setJSON([
                        'success' => false, 
                        'message' => "Não há crédito disponível para o turno {$turnoAula}. Verifique a correspondência entre turnos dos créditos e das aulas."
                    ]);
                }
            }
            
            // Para créditos, forçar valores específicos ANTES da validação
            $post['sala_permutada_id'] = 'VE';
            $post['bloco_reposicao'] = '0';
            $post['professor_substituto_nif'] = $userNif;
        }
        
        // Validar dados recebidos - ajustar regras se usar créditos
        $rules = [
            'aula_original_id'           => 'required|integer',
            'data_aula_original'         => 'required|valid_date',
            'data_aula_permutada'        => 'required|valid_date',
            'professor_substituto_nif'   => 'required',
            'observacoes'                => 'permit_empty',
            'aulas_adicionais'           => 'permit_empty',
            'usar_credito_id'            => 'permit_empty' // Pode ser array agora
        ];
        
        // Adicionar validação de sala e bloco apenas se NÃO estiver usando créditos
        if (!$usarCreditoIds || empty($usarCreditoIds)) {
            $rules['sala_permutada_id'] = 'required';
            $rules['bloco_reposicao'] = 'required';
        } else {
            $rules['sala_permutada_id'] = 'permit_empty';
            $rules['bloco_reposicao'] = 'permit_empty';
        }

        // Usar dados modificados do $post para validação
        if (!$this->validateData($post, $rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Verificar se a aula pertence ao professor
        $aula = $this->horarioModel->find($post['aula_original_id']);
        if (!$aula || $aula['user_nif'] != $userNif) {
            return $this->response->setJSON(['success' => false, 'message' => 'Aula inválida']);
        }

        // Criar grupo de permutas se houver aulas adicionais
        $grupoPermuta = null;
        $aulasParaPermutar = [$post['aula_original_id']];
        
        if (!empty($post['aulas_adicionais']) && is_array($post['aulas_adicionais'])) {
            $grupoPermuta = $this->permutaModel->gerarGrupoPermuta();
            $aulasParaPermutar = array_merge($aulasParaPermutar, $post['aulas_adicionais']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $primeiraPermutaId = null;
            
            foreach ($aulasParaPermutar as $aulaId) {
                $permutaData = [
                    'aula_original_id'          => $aulaId,
                    'data_aula_original'        => $post['data_aula_original'],
                    'data_aula_permutada'       => $post['data_aula_permutada'],
                    'professor_autor_nif'       => $userNif,
                    'professor_substituto_nif'  => $post['professor_substituto_nif'],
                    'sala_permutada_id'         => $post['sala_permutada_id'] ?? null,
                    'bloco_reposicao_id'        => $post['bloco_reposicao'] ?? null,
                    'grupo_permuta'             => $grupoPermuta,
                    'estado'                    => 'pendente',
                    'observacoes'               => $post['observacoes'] ?? null
                ];

                $permutaId = $this->permutaModel->insert($permutaData);
                
                // Log da criação da permuta
                log_permuta(
                    'create',
                    $permutaId,
                    "Permuta criada - Aula ID: {$aulaId}, Data original: {$post['data_aula_original']}, Data reposição: {$post['data_aula_permutada']}",
                    null,
                    $permutaData
                );
                
                // Guardar o ID da primeira permuta para enviar emails
                if ($primeiraPermutaId === null) {
                    $primeiraPermutaId = $permutaId;
                }
            }

            // Se está usando créditos, marcar apenas os necessários como usados (um por aula)
            if ($usarCreditoIds && !empty($usarCreditoIds) && $primeiraPermutaId) {
                // Calcular quantas aulas foram permutadas
                $totalAulas = count($aulasParaPermutar);
                
                // Obter turnos das aulas permutadas para fazer correspondência correta
                $aulasComTurnos = [];
                foreach ($aulasParaPermutar as $aulaId) {
                    $aula = $this->horarioModel->find($aulaId);
                    if ($aula) {
                        $aulasComTurnos[] = $aula['turno'];
                    }
                }
                
                // Usar apenas os créditos necessários, fazendo correspondência por turno
                $creditosUsados = 0;
                foreach ($aulasComTurnos as $turnoAula) {
                    foreach ($usarCreditoIds as $creditoId) {
                        if ($creditosUsados >= $totalAulas) break 2; // Sair dos dois loops
                        
                        $credito = $this->creditoModel->find($creditoId);
                        if ($credito && $credito['turno'] == $turnoAula && $credito['estado'] == 'disponivel') {
                            $oldCreditoValues = $credito;
                            $newCreditoValues = [
                                'estado' => 'usado',
                                'usado_em_permuta_id' => $primeiraPermutaId,
                                'data_uso' => date('Y-m-d H:i:s')
                            ];
                            
                            $this->creditoModel->update($creditoId, $newCreditoValues);
                            
                            // Log do uso do crédito
                            log_credito(
                                'use',
                                $creditoId,
                                "Crédito usado na permuta #{$primeiraPermutaId} - Turno: {$turnoAula}",
                                ['estado' => $oldCreditoValues['estado']],
                                $newCreditoValues
                            );
                            
                            $creditosUsados++;
                            break; // Usar apenas um crédito por turno/aula
                        }
                    }
                }
                
                // Verificar se conseguimos usar créditos para todas as aulas
                if ($creditosUsados < $totalAulas) {
                    throw new \Exception("Não foi possível usar créditos para todas as aulas. Verifique a correspondência de turnos.");
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Erro ao salvar permuta']);
            }

            // Enviar emails (não bloquear se falhar)
            try {
                $this->enviarEmailsPedidoPermuta($userNif, $post['professor_substituto_nif'], $primeiraPermutaId);
            } catch (\Exception $e) {
                log_message('error', 'Erro ao enviar emails de permuta: ' . $e->getMessage());
                // Continuar mesmo se emails falharem
            }

            // Calcular mensagem baseada nos créditos realmente usados
            $totalAulasPermutadas = count($aulasParaPermutar);
            $mensagem = ($usarCreditoIds && !empty($usarCreditoIds)) ? 
                "Permuta criada com sucesso! Usados {$creditosUsados} crédito(s) para {$totalAulasPermutadas} aula(s) de visita de estudo." : 
                'Permuta solicitada com sucesso! Aguarde aprovação.';

            return $this->response->setJSON([
                'success' => true,
                'message' => $mensagem,
                'redirect' => base_url('permutas/minhas')
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
        }
    }

    /**
     * Aprovar permuta (apenas administradores)
     */
    public function aprovarPermuta($permutaId)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 6) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão']);
        }

        $permuta = $this->permutaModel->find($permutaId);
        if (!$permuta) {
            return $this->response->setJSON(['success' => false, 'message' => 'Permuta não encontrada']);
        }

        $success = $this->permutaModel->aprovarPermuta($permutaId, $userData['id']);

        if ($success) {
            // Log da aprovação
            log_permuta(
                'approve',
                $permutaId,
                "Permuta aprovada por {$userData['Nome']} (ID: {$userData['id']})",
                ['estado' => $permuta['estado']],
                ['estado' => 'aprovada', 'aprovado_por' => $userData['id']]
            );
            
            // Enviar emails de confirmação (não bloquear se falhar)
            try {
                $this->enviarEmailsAprovacao($permutaId);
            } catch (\Exception $e) {
                log_message('error', 'Erro ao enviar emails de aprovação: ' . $e->getMessage());
            }
            
            return $this->response->setJSON(['success' => true, 'message' => 'Permuta aprovada com sucesso']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Erro ao aprovar permuta']);
    }

    /**
     * Rejeitar permuta (apenas administradores)
     */
    public function rejeitarPermuta()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 6) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão']);
        }

        $permutaId = $this->request->getPost('permuta_id');
        $motivo = $this->request->getPost('motivo');

        $permuta = $this->permutaModel->find($permutaId);
        if (!$permuta) {
            return $this->response->setJSON(['success' => false, 'message' => 'Permuta não encontrada']);
        }

        $success = $this->permutaModel->rejeitarPermuta($permutaId, $userData['id'], $motivo);

        if ($success) {
            // Log da rejeição
            log_permuta(
                'reject',
                $permutaId,
                "Permuta rejeitada por {$userData['Nome']} - Motivo: {$motivo}",
                ['estado' => $permuta['estado']],
                ['estado' => 'rejeitada', 'rejeitado_por' => $userData['id'], 'motivo_rejeicao' => $motivo]
            );
            
            // Enviar emails de rejeição (não bloquear se falhar)
            try {
                $this->enviarEmailsRejeicao($permutaId, $motivo);
            } catch (\Exception $e) {
                log_message('error', 'Erro ao enviar emails de rejeição: ' . $e->getMessage());
            }
            
            return $this->response->setJSON(['success' => true, 'message' => 'Permuta rejeitada']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Erro ao rejeitar permuta']);
    }

    /**
     * Aprovar grupo de permutas (apenas administradores)
     */
    public function aprovarGrupo()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 6) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão']);
        }

        $grupoId = $this->request->getPost('grupo_id');
        
        if (empty($grupoId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Grupo não especificado']);
        }

        // Buscar todas as permutas do grupo
        $permutas = $this->permutaModel
            ->where('grupo_permuta', $grupoId)
            ->where('estado', 'pendente')
            ->findAll();

        if (empty($permutas)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nenhuma permuta pendente encontrada neste grupo']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $countAprovadas = 0;
        foreach ($permutas as $permuta) {
            if ($this->permutaModel->aprovarPermuta($permuta['id'], $userData['id'])) {
                $countAprovadas++;
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false || $countAprovadas === 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Erro ao aprovar grupo de permutas']);
        }

        // Enviar UM email com todas as permutas do grupo (não bloquear se falhar)
        try {
            $this->enviarEmailGrupoAprovado($grupoId);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao enviar email de grupo aprovado: ' . $e->getMessage());
        }

        return $this->response->setJSON([
            'success' => true, 
            'message' => "{$countAprovadas} permuta(s) aprovada(s) com sucesso"
        ]);
    }

    /**
     * Rejeitar grupo de permutas (apenas administradores)
     */
    public function rejeitarGrupo()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 6) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão']);
        }

        $grupoId = $this->request->getPost('grupo_id');
        $motivo = $this->request->getPost('motivo');

        if (empty($grupoId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Grupo não especificado']);
        }

        if (empty($motivo)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Motivo não especificado']);
        }

        // Buscar todas as permutas do grupo
        $permutas = $this->permutaModel
            ->where('grupo_permuta', $grupoId)
            ->where('estado', 'pendente')
            ->findAll();

        if (empty($permutas)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nenhuma permuta pendente encontrada neste grupo']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $countRejeitadas = 0;
        foreach ($permutas as $permuta) {
            if ($this->permutaModel->rejeitarPermuta($permuta['id'], $userData['id'], $motivo)) {
                $countRejeitadas++;
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false || $countRejeitadas === 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Erro ao rejeitar grupo de permutas']);
        }

        // Enviar UM email com todas as permutas do grupo rejeitadas (não bloquear se falhar)
        try {
            $this->enviarEmailGrupoRejeitado($grupoId, $motivo);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao enviar email de grupo rejeitado: ' . $e->getMessage());
        }

        return $this->response->setJSON([
            'success' => true, 
            'message' => "{$countRejeitadas} permuta(s) rejeitada(s)"
        ]);
    }

    /**
     * Cancelar permuta (apenas autor)
     */
    public function cancelarPermuta($permutaId)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sessão expirada']);
        }

        $permuta = $this->permutaModel->find($permutaId);
        if (!$permuta) {
            return $this->response->setJSON(['success' => false, 'message' => 'Permuta não encontrada']);
        }

        // Verificar se o usuário é o autor
        if ($permuta['professor_autor_nif'] != $userData['NIF']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão']);
        }

        // Só pode cancelar se estiver pendente
        if ($permuta['estado'] != 'pendente') {
            return $this->response->setJSON(['success' => false, 'message' => 'Só pode cancelar permutas pendentes']);
        }

        $success = $this->permutaModel->cancelarPermuta($permutaId);

        if ($success) {
            return $this->response->setJSON(['success' => true, 'message' => 'Permuta cancelada']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Erro ao cancelar permuta']);
    }

    /**
     * Ver detalhes de uma permuta
     */
    public function verPermuta($permutaId)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData) {
            return redirect()->to('/login');
        }

        $permuta = $this->permutaModel->getDetalhesPermuta($permutaId);
        
        if (!$permuta) {
            return redirect()->to('/permutas/minhas')->with('error', 'Permuta não encontrada');
        }

        // Verificar permissão (autor, substituto ou admin)
        $userNif = $userData['NIF'] ?? null;
        $isAdmin = $userData['level'] >= 6;
        
        if (!$isAdmin && $userNif != $permuta['professor_autor_nif'] && $userNif != $permuta['professor_substituto_nif']) {
            return redirect()->to('/permutas/minhas')->with('error', 'Sem permissão');
        }

        // Buscar outras permutas do mesmo grupo (se existir)
        $permutasGrupo = [];
        if (!empty($permuta['grupo_permuta'])) {
            $permutasGrupo = $this->permutaModel->getPermutasDoGrupo($permuta['grupo_permuta']);
        }

        $data = [
            'title' => 'Detalhes da Permuta',
            'page_title' => 'Detalhes da Permuta #' . $permutaId,
            'page_subtitle' => 'Informações completas',
            'permuta' => $permuta,
            'permutasGrupo' => $permutasGrupo,
            'isAdmin' => $isAdmin,
            'userNif' => $userNif
        ];

        return view('permutas/detalhes_permuta', $data);
    }

    /**
     * Enviar emails após pedido de permuta
     */
    private function enviarEmailsPedidoPermuta($professorAutorNif, $professorSubstitutoNif, $permutaId)
    {
        log_message('info', "=== Iniciando envio de emails - Permuta #{$permutaId} ===");
        log_message('info', "Professor Autor NIF: {$professorAutorNif}, Professor Substituto NIF: {$professorSubstitutoNif}");
        
        $email = \Config\Services::email();
        
        // Buscar dados dos professores
        $autor = $this->userModel->where('NIF', $professorAutorNif)->first();
        $substituto = $this->userModel->where('NIF', $professorSubstitutoNif)->first();
        
        log_message('info', "Professor Autor encontrado: " . ($autor ? 'SIM (' . $autor['name'] . ')' : 'NÃO'));
        log_message('info', "Professor Substituto encontrado: " . ($substituto ? 'SIM (' . $substituto['name'] . ')' : 'NÃO'));
        
        if (!$autor) {
            log_message('error', "Professor autor não encontrado para envio de email: NIF {$professorAutorNif}");
            return; // Não bloquear o processo
        }
        
        if (!$substituto) {
            log_message('error', "Professor substituto não encontrado para envio de email: NIF {$professorSubstitutoNif}");
            return; // Não bloquear o processo
        }
        
        // Buscar detalhes da permuta
        $permuta = $this->permutaModel->getDetalhesPermuta($permutaId);
        
        log_message('info', "Permuta encontrada: " . ($permuta ? 'SIM (ID: ' . $permuta['id'] . ')' : 'NÃO'));
        
        if (!$permuta) {
            log_message('error', "Permuta não encontrada para envio de email: ID {$permutaId}");
            return; // Não bloquear o processo
        }
        
        // Email para aprovação (direção)
        try {
            $email->setTo('escoladigitaljb@aejoaodebarros.pt');
            $email->setSubject('Novo Pedido de Permuta - #' . $permutaId);
            $email->setMailType('html');
            $emailBody = view('emails/permuta_pedido_aprovacao', [
                'permutaId' => $permutaId,
                'autor' => $autor,
                'substituto' => $substituto,
                'permuta' => $permuta
            ]);
            $email->setMessage($emailBody);
            
            if ($email->send()) {
                log_message('info', "Email enviado para direção - Permuta #{$permutaId}");
            } else {
                log_message('error', "Erro ao enviar email para direção - Permuta #{$permutaId}: " . $email->printDebugger(['headers']));
            }
        } catch (\Exception $e) {
            log_message('error', "Exceção ao enviar email para direção: " . $e->getMessage());
        }
        
        // Email de confirmação para autor
        if (!empty($autor['email'])) {
            try {
                $email->clear();
                $email->setTo($autor['email']);
                $email->setSubject('Pedido de Permuta Submetido - #' . $permutaId);
                $email->setMailType('html');
                $emailBody = view('emails/permuta_submetida', [
                    'permutaId' => $permutaId,
                    'permuta' => $permuta
                ]);
                $email->setMessage($emailBody);
                
                if ($email->send()) {
                    log_message('info', "Email enviado para autor ({$autor['email']}) - Permuta #{$permutaId}");
                } else {
                    log_message('error', "Erro ao enviar email para autor ({$autor['email']}) - Permuta #{$permutaId}: " . $email->printDebugger(['headers']));
                }
            } catch (\Exception $e) {
                log_message('error', "Exceção ao enviar email para autor: " . $e->getMessage());
            }
        } else {
            log_message('warning', "Email do professor autor não disponível - NIF: {$professorAutorNif}");
        }
        
        // Email para professor substituto (se diferente do autor)
        if ($professorAutorNif != $professorSubstitutoNif && !empty($substituto['email'])) {
            try {
                $email->clear();
                $email->setTo($substituto['email']);
                $email->setSubject('Permuta Solicitada - #' . $permutaId);
                $email->setMailType('html');
                $emailBody = view('emails/permuta_substituto', [
                    'permutaId' => $permutaId,
                    'autor' => $autor,
                    'substituto' => $substituto,
                    'permuta' => $permuta
                ]);
                $email->setMessage($emailBody);
                
                if ($email->send()) {
                    log_message('info', "Email enviado para substituto ({$substituto['email']}) - Permuta #{$permutaId}");
                } else {
                    log_message('error', "Erro ao enviar email para substituto ({$substituto['email']}) - Permuta #{$permutaId}: " . $email->printDebugger(['headers']));
                }
            } catch (\Exception $e) {
                log_message('error', "Exceção ao enviar email para substituto: " . $e->getMessage());
            }
        } else if ($professorAutorNif == $professorSubstitutoNif) {
            log_message('info', "Email para substituto não enviado (mesmo professor) - Permuta #{$permutaId}");
        } else {
            log_message('warning', "Email do professor substituto não disponível - NIF: {$professorSubstitutoNif}");
        }
    }

    /**
     * Enviar emails após aprovação
     */
    private function enviarEmailsAprovacao($permutaId)
    {
        $permuta = $this->permutaModel->getDetalhesPermuta($permutaId);
        $email = \Config\Services::email();
        
        // Email para autor
        if (!empty($permuta['professor_autor_email'])) {
            $email->setTo($permuta['professor_autor_email']);
            $email->setSubject('Permuta Aprovada - #' . $permutaId);
            $email->setMailType('html');
            $emailBody = view('emails/permuta_aprovada', [
                'permutaId' => $permutaId,
                'permuta' => $permuta
            ]);
            $email->setMessage($emailBody);
            $email->send();
        }
        
        // Email para substituto
        if (!empty($permuta['professor_substituto_email']) && 
            $permuta['professor_autor_email'] != $permuta['professor_substituto_email']) {
            $email->clear();
            $email->setTo($permuta['professor_substituto_email']);
            $email->setSubject('Permuta Aprovada - #' . $permutaId);
            $email->setMailType('html');
            $emailBody = view('emails/permuta_aprovada', [
                'permutaId' => $permutaId,
                'permuta' => $permuta
            ]);
            $email->setMessage($emailBody);
            $email->send();
        }
    }

    /**
     * Enviar emails após rejeição
     */
    private function enviarEmailsRejeicao($permutaId, $motivo = null)
    {
        $permuta = $this->permutaModel->getDetalhesPermuta($permutaId);
        $email = \Config\Services::email();
        
        // Email para autor
        if (!empty($permuta['professor_autor_email'])) {
            $email->setTo($permuta['professor_autor_email']);
            $email->setSubject('Permuta Rejeitada - #' . $permutaId);
            $email->setMailType('html');
            $emailBody = view('emails/permuta_rejeitada', [
                'permutaId' => $permutaId,
                'permuta' => $permuta
            ]);
            $email->setMessage($emailBody);
            $email->send();
        }
        
        // Email para substituto
        if (!empty($permuta['professor_substituto_email']) && 
            $permuta['professor_autor_email'] != $permuta['professor_substituto_email']) {
            $email->clear();
            $email->setTo($permuta['professor_substituto_email']);
            $email->setSubject('Permuta Rejeitada - #' . $permutaId);
            $email->setMailType('html');
            $emailBody = view('emails/permuta_rejeitada', [
                'permutaId' => $permutaId,
                'permuta' => $permuta
            ]);
            $email->setMessage($emailBody);
            $email->send();
        }
    }

    /**
     * Enviar email único com todas as permutas do grupo aprovadas
     */
    private function enviarEmailGrupoAprovado($grupoId)
    {
        // Buscar todas as permutas do grupo
        $permutas = $this->permutaModel
            ->where('grupo_permuta', $grupoId)
            ->where('estado', 'aprovada')
            ->findAll();

        if (empty($permutas)) {
            log_message('warning', "Nenhuma permuta aprovada encontrada para grupo {$grupoId}");
            return;
        }

        // Obter detalhes completos de cada permuta
        $permutasDetalhes = [];
        $professoresEmails = [];
        
        foreach ($permutas as $permuta) {
            $detalhes = $this->permutaModel->getDetalhesPermuta($permuta['id']);
            $permutasDetalhes[] = $detalhes;
            
            // Coletar emails únicos de todos os professores envolvidos
            if (!empty($detalhes['professor_autor_email'])) {
                $professoresEmails[$detalhes['professor_autor_email']] = $detalhes['professor_autor_nome'];
            }
            if (!empty($detalhes['professor_substituto_email']) && 
                $detalhes['professor_autor_email'] != $detalhes['professor_substituto_email']) {
                $professoresEmails[$detalhes['professor_substituto_email']] = $detalhes['professor_substituto_nome'];
            }
        }

        if (empty($professoresEmails)) {
            log_message('warning', "Nenhum email de professor encontrado para grupo {$grupoId}");
            return;
        }

        $email = \Config\Services::email();
        
        // Enviar email para cada professor envolvido
        foreach ($professoresEmails as $emailProfessor => $nomeProfessor) {
            try {
                $email->clear();
                $email->setTo($emailProfessor);
                $email->setSubject('Grupo de Permutas Aprovado - ' . count($permutasDetalhes) . ' permuta(s)');
                $email->setMailType('html');
                
                $emailBody = view('emails/grupo_permutas_aprovado', [
                    'grupoId' => $grupoId,
                    'permutas' => $permutasDetalhes,
                    'totalPermutas' => count($permutasDetalhes),
                    'nomeProfessor' => $nomeProfessor
                ]);
                
                $email->setMessage($emailBody);
                
                if ($email->send()) {
                    log_message('info', "Email de grupo aprovado enviado para {$emailProfessor} - Grupo {$grupoId}");
                } else {
                    log_message('error', "Erro ao enviar email de grupo para {$emailProfessor}: " . $email->printDebugger(['headers']));
                }
            } catch (\Exception $e) {
                log_message('error', "Exceção ao enviar email de grupo para {$emailProfessor}: " . $e->getMessage());
            }
        }
    }

    /**
     * Enviar email único com todas as permutas do grupo rejeitadas
     */
    private function enviarEmailGrupoRejeitado($grupoId, $motivo)
    {
        // Buscar todas as permutas do grupo
        $permutas = $this->permutaModel
            ->where('grupo_permuta', $grupoId)
            ->where('estado', 'rejeitada')
            ->findAll();

        if (empty($permutas)) {
            log_message('warning', "Nenhuma permuta rejeitada encontrada para grupo {$grupoId}");
            return;
        }

        // Obter detalhes completos de cada permuta
        $permutasDetalhes = [];
        $professoresEmails = [];
        
        foreach ($permutas as $permuta) {
            $detalhes = $this->permutaModel->getDetalhesPermuta($permuta['id']);
            $permutasDetalhes[] = $detalhes;
            
            // Coletar emails únicos de todos os professores envolvidos
            if (!empty($detalhes['professor_autor_email'])) {
                $professoresEmails[$detalhes['professor_autor_email']] = $detalhes['professor_autor_nome'];
            }
            if (!empty($detalhes['professor_substituto_email']) && 
                $detalhes['professor_autor_email'] != $detalhes['professor_substituto_email']) {
                $professoresEmails[$detalhes['professor_substituto_email']] = $detalhes['professor_substituto_nome'];
            }
        }

        if (empty($professoresEmails)) {
            log_message('warning', "Nenhum email de professor encontrado para grupo {$grupoId}");
            return;
        }

        $email = \Config\Services::email();
        
        // Enviar email para cada professor envolvido
        foreach ($professoresEmails as $emailProfessor => $nomeProfessor) {
            try {
                $email->clear();
                $email->setTo($emailProfessor);
                $email->setSubject('Grupo de Permutas Rejeitado - ' . count($permutasDetalhes) . ' permuta(s)');
                $email->setMailType('html');
                
                $emailBody = view('emails/grupo_permutas_rejeitado', [
                    'grupoId' => $grupoId,
                    'permutas' => $permutasDetalhes,
                    'totalPermutas' => count($permutasDetalhes),
                    'nomeProfessor' => $nomeProfessor,
                    'motivo' => $motivo
                ]);
                
                $email->setMessage($emailBody);
                
                if ($email->send()) {
                    log_message('info', "Email de grupo rejeitado enviado para {$emailProfessor} - Grupo {$grupoId}");
                } else {
                    log_message('error', "Erro ao enviar email de grupo para {$emailProfessor}: " . $email->printDebugger(['headers']));
                }
            } catch (\Exception $e) {
                log_message('error', "Exceção ao enviar email de grupo para {$emailProfessor}: " . $e->getMessage());
            }
        }
    }

    /**
     * MÉTODO TEMPORÁRIO DE TESTE DE EMAIL
     * Acesse via: /permutas/testeEmail
     */
    public function testeEmail()
    {
        $email = \Config\Services::email();
        
        echo "<h1>Teste de Envio de Email</h1>";
        echo "<p><strong>Configurações:</strong></p>";
        echo "<ul>";
        echo "<li>SMTP Host: " . getenv('email.SMTPHost') . "</li>";
        echo "<li>SMTP Port: " . getenv('email.SMTPPort') . "</li>";
        echo "<li>SMTP User: " . getenv('email.SMTPUser') . "</li>";
        echo "<li>From Email: " . getenv('email.fromEmail') . "</li>";
        echo "<li>From Name: " . getenv('email.fromName') . "</li>";
        echo "</ul>";
        
        echo "<h2>Tentando enviar email para: escoladigitaljb@aejoaodebarros.pt</h2>";
        
        try {
            $email->setTo('escoladigitaljb@aejoaodebarros.pt');
            $email->setSubject('Teste de Email - Sistema de Gestão');
            $email->setMailType('html');
            $email->setMessage('<h1>Teste de Email</h1><p>Este é um email de teste do sistema de gestão escolar.</p><p>Data/Hora: ' . date('d/m/Y H:i:s') . '</p>');
            
            if ($email->send()) {
                echo "<p style='color: green; font-weight: bold;'>✅ Email enviado com sucesso!</p>";
            } else {
                echo "<p style='color: red; font-weight: bold;'>❌ Erro ao enviar email!</p>";
                echo "<pre>" . $email->printDebugger(['headers']) . "</pre>";
            }
        } catch (\Exception $e) {
            echo "<p style='color: red; font-weight: bold;'>❌ Exceção: " . $e->getMessage() . "</p>";
        }
        
        echo "<hr>";
        echo "<h2>Teste com professores</h2>";
        
        // Testar email para Susana Neto
        try {
            $email->clear();
            $email->setTo('susananeto@aejoaodebarros.pt');
            $email->setSubject('Teste de Email - Susana Neto');
            $email->setMessage('<p>Teste para Susana Neto - ' . date('d/m/Y H:i:s') . '</p>');
            
            if ($email->send()) {
                echo "<p style='color: green;'>✅ Email enviado para susananeto@aejoaodebarros.pt</p>";
            } else {
                echo "<p style='color: red;'>❌ Erro ao enviar para susananeto@aejoaodebarros.pt</p>";
            }
        } catch (\Exception $e) {
            echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
        }
        
        // Testar email para António Neto
        try {
            $email->clear();
            $email->setTo('antonioneto@aejoaodebarros.pt');
            $email->setSubject('Teste de Email - António Neto');
            $email->setMessage('<p>Teste para António Neto - ' . date('d/m/Y H:i:s') . '</p>');
            
            if ($email->send()) {
                echo "<p style='color: green;'>✅ Email enviado para antonioneto@aejoaodebarros.pt</p>";
            } else {
                echo "<p style='color: red;'>❌ Erro ao enviar para antonioneto@aejoaodebarros.pt</p>";
            }
        } catch (\Exception $e) {
            echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
        }
    }

    /**
     * Buscar blocos horários disponíveis
     * AJAX endpoint
     * Nota: Retorna todos os blocos pois são iguais para todos os dias
     */
    public function getBlocosHorarios()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sessão expirada']);
        }

        // Buscar todos os blocos horários ordenados por hora de início
        $blocos = $this->blocosModel
            ->orderBy('hora_inicio', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'blocos' => $blocos,
            'total_blocos' => count($blocos)
        ]);
    }

    /**
     * Buscar salas livres para uma data específica
     * AJAX endpoint
     */
    public function getSalasLivres()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sessão expirada']);
        }

        $userNif = $userData['NIF'] ?? null;
        $dataReposicao = $this->request->getPost('data_reposicao');
        $blocoReposicao = $this->request->getPost('bloco_reposicao');
        $aulaOriginalId = $this->request->getPost('aula_original_id');
        $aulasAdicionais = $this->request->getPost('aulas_adicionais') ?? [];
        $professorSubstitutoNif = $this->request->getPost('professor_substituto_nif');
        
        if (!$dataReposicao || !$aulaOriginalId || !$blocoReposicao) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dados incompletos']);
        }

        // Verificar se é permuta com "Eu próprio"
        $isProprio = ($professorSubstitutoNif && $professorSubstitutoNif == $userNif);
        
        // DEBUG: Log para verificar
        log_message('info', "getSalasLivres - isProprio: " . ($isProprio ? 'SIM' : 'NAO') . 
                           ", userNif: {$userNif}, professorSubstitutoNif: {$professorSubstitutoNif}");

        // Buscar informações do bloco horário selecionado
        $blocoInfo = $this->blocosModel->find($blocoReposicao);
        if (!$blocoInfo) {
            return $this->response->setJSON(['success' => false, 'message' => 'Bloco horário não encontrado']);
        }

        // Converter data para dia da semana (2=Segunda, 3=Terça, etc)
        $diaSemana = date('N', strtotime($dataReposicao)) + 1; // PHP usa 1-7, precisamos 2-7
        if ($diaSemana == 8) $diaSemana = 1; // Domingo (não usado mas por precaução)

        // Buscar informações da aula original
        $aulaOriginal = $this->horarioModel->find($aulaOriginalId);
        if (!$aulaOriginal) {
            return $this->response->setJSON(['success' => false, 'message' => 'Aula não encontrada']);
        }

        // Usar o horário do bloco selecionado
        $horariosParaVerificar = [
            [
                'hora_inicio' => $blocoInfo['hora_inicio'],
                'hora_fim' => $blocoInfo['hora_fim']
            ]
        ];

        // Adicionar horários das aulas adicionais (também usando o mesmo bloco)
        if (!empty($aulasAdicionais)) {
            foreach ($aulasAdicionais as $aulaId) {
                $aula = $this->horarioModel->find($aulaId);
                if ($aula) {
                    // Para aulas adicionais, também usar o bloco selecionado
                    $horariosParaVerificar[] = [
                        'hora_inicio' => $blocoInfo['hora_inicio'],
                        'hora_fim' => $blocoInfo['hora_fim']
                    ];
                }
            }
        }

        // Buscar escola da aula original
        $salaOriginal = $this->horarioModel
            ->select('salas.escola_id, salas.codigo_sala')
            ->join('salas', 'salas.codigo_sala = horario_aulas.sala_id', 'left')
            ->where('horario_aulas.id_aula', $aulaOriginalId)
            ->first();

        $escolaId = $salaOriginal['escola_id'] ?? null;
        $salaOriginalCodigo = $salaOriginal['codigo_sala'] ?? $aulaOriginal['sala_id'];

        // Buscar todas as salas da mesma escola
        $salasModel = new SalasModel();
        $salasBuilder = $salasModel->orderBy('codigo_sala', 'ASC');
        
        if ($escolaId) {
            $salasBuilder->where('escola_id', $escolaId);
        }
        
        $todasSalas = $salasBuilder->findAll();

        // Para cada sala, verificar se está livre em TODOS os horários
        $salasLivres = [];
        
        foreach ($todasSalas as $sala) {
            $salaLivreEmTodosHorarios = true;
            
            foreach ($horariosParaVerificar as $horario) {
                // Construir query para verificar se a sala está ocupada
                $query = $this->horarioModel
                    ->where('sala_id', $sala['codigo_sala'])
                    ->where('dia_semana', $diaSemana)
                    ->groupStart()
                        ->where('hora_inicio <', $horario['hora_fim'])
                        ->where('hora_fim >', $horario['hora_inicio'])
                    ->groupEnd();
                
                // Se é "Eu próprio", excluir a aula original da verificação
                // pois essa aula não vai acontecer (está a ser permutada)
                if ($isProprio) {
                    $query->where('id_aula !=', $aulaOriginalId);
                    
                    // Excluir também aulas adicionais se existirem
                    if (!empty($aulasAdicionais)) {
                        $query->whereNotIn('id_aula', $aulasAdicionais);
                    }
                }
                
                $ocupada = $query->countAllResults();
                
                // DEBUG: Log detalhado
                if ($sala['codigo_sala'] == $salaOriginalCodigo) {
                    log_message('info', "Verificando sala original {$salaOriginalCodigo} - " .
                               "isProprio: " . ($isProprio ? 'SIM' : 'NAO') . 
                               ", ocupada: {$ocupada}, aulaOriginalId: {$aulaOriginalId}");
                }

                if ($ocupada > 0) {
                    $salaLivreEmTodosHorarios = false;
                    break;
                }
            }
            
            if ($salaLivreEmTodosHorarios) {
                $salasLivres[] = [
                    'codigo_sala' => $sala['codigo_sala'],
                    'descricao' => $sala['descricao'] ?? '',
                    'capacidade' => $sala['capacidade'] ?? null,
                    'tipo_sala' => $sala['tipo_sala'] ?? null
                ];
            }
        }

        return $this->response->setJSON([
            'success' => true, 
            'salas' => $salasLivres,
            'dia_semana' => $diaSemana,
            'escola_id' => $escolaId,
            'total_horarios' => count($horariosParaVerificar)
        ]);
    }

    // ========================================
    // GESTÃO DE CRÉDITOS DE AULAS
    // ========================================

    /**
     * Página de gestão de créditos
     */
    public function creditos()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData) {
            return redirect()->to('/login')->with('error', 'É necessário fazer login');
        }

        $userNif = $userData['NIF'] ?? null;
        $userLevel = $userData['level'] ?? 0;

        // Buscar ano letivo ativo
        $anoLetivoAtivo = $this->anoLetivoModel->where('status', 1)->first();

        // Buscar créditos do professor (ou todos se for admin)
        if ($userLevel >= 6) {
            // Admin vê todos os créditos
            $creditosDisponiveis = $this->creditoModel
                ->select('aulas_credito.*, 
                         user.name as professor_nome,
                         disciplina.descritivo as disciplina_nome,
                         disciplina.abreviatura as disciplina_abrev,
                         turma.nome as turma_nome')
                ->join('user', 'user.NIF = aulas_credito.professor_nif', 'left')
                ->join('disciplina', 'disciplina.descritivo = aulas_credito.disciplina_id', 'left')
                ->join('turma', 'turma.codigo = aulas_credito.codigo_turma', 'left')
                ->where('aulas_credito.estado', 'disponivel')
                ->where('aulas_credito.ano_letivo_id', $anoLetivoAtivo['id_anoletivo'] ?? 0)
                ->orderBy('aulas_credito.professor_nif, aulas_credito.data_visita', 'DESC')
                ->findAll();

            $creditosUsados = $this->creditoModel
                ->select('aulas_credito.*, 
                         user.name as professor_nome,
                         disciplina.descritivo as disciplina_nome,
                         turma.nome as turma_nome,
                         permutas.data_aula_original')
                ->join('user', 'user.NIF = aulas_credito.professor_nif', 'left')
                ->join('disciplina', 'disciplina.descritivo = aulas_credito.disciplina_id', 'left')
                ->join('turma', 'turma.codigo = aulas_credito.codigo_turma', 'left')
                ->join('permutas', 'permutas.id = aulas_credito.usado_em_permuta_id', 'left')
                ->where('aulas_credito.estado', 'usado')
                ->where('aulas_credito.ano_letivo_id', $anoLetivoAtivo['id_anoletivo'] ?? 0)
                ->orderBy('aulas_credito.data_uso', 'DESC')
                ->findAll();
        } else {
            // Professor vê apenas os seus créditos
            $creditosDisponiveis = $this->creditoModel->getCreditosDisponiveis($userNif, $anoLetivoAtivo['id_anoletivo'] ?? null);
            $creditosUsados = $this->creditoModel->getCreditosUsados($userNif, $anoLetivoAtivo['id_anoletivo'] ?? null);
        }

        // Buscar todos os professores para o formulário (se for admin)
        $professores = [];
        if ($userLevel >= 6) {
            $professores = $this->userModel
                ->select('NIF, name, email')
                ->where('NIF IS NOT NULL')
                ->where('NIF !=', '')
                ->orderBy('name', 'ASC')
                ->findAll();
        }

        $data = [
            'title' => 'Créditos de Aulas',
            'page_title' => 'Gestão de Créditos de Aulas',
            'page_subtitle' => 'Créditos de visitas de estudo',
            'creditosDisponiveis' => $creditosDisponiveis,
            'creditosUsados' => $creditosUsados,
            'totalDisponiveis' => count($creditosDisponiveis),
            'totalUsados' => count($creditosUsados),
            'professores' => $professores,
            'anoLetivoAtivo' => $anoLetivoAtivo,
            'userLevel' => $userLevel,
            'userNif' => $userNif,
        ];

        return view('permutas/creditos', $data);
    }

    /**
     * AJAX: Buscar turmas que o professor leciona
     */
    public function getTurmasProfessor()
    {
        $professorNif = $this->request->getPost('professor_nif');

        if (!$professorNif) {
            return $this->response->setJSON(['success' => false, 'message' => 'NIF do professor não fornecido']);
        }

        // Buscar turmas DISTINTAS que o professor leciona
        $turmas = $this->horarioModel
            ->distinct()
            ->select('turma.codigo, turma.nome, turma.ano')
            ->join('turma', 'turma.codigo = horario_aulas.codigo_turma', 'inner')
            ->where('horario_aulas.user_nif', $professorNif)
            ->orderBy('turma.ano, turma.nome', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'turmas' => $turmas
        ]);
    }

    /**
     * AJAX: Buscar disciplinas que o professor leciona numa turma
     */
    public function getDisciplinasProfessorTurma()
    {
        $professorNif = $this->request->getPost('professor_nif');
        $codigoTurma = $this->request->getPost('codigo_turma');

        if (!$professorNif || !$codigoTurma) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dados incompletos']);
        }

        // Buscar disciplinas DISTINTAS que o professor leciona nessa turma
        $disciplinas = $this->horarioModel
            ->distinct()
            ->select('disciplina.descritivo, disciplina.abreviatura')
            ->join('disciplina', 'disciplina.descritivo = horario_aulas.disciplina_id', 'inner')
            ->where('horario_aulas.user_nif', $professorNif)
            ->where('horario_aulas.codigo_turma', $codigoTurma)
            ->orderBy('disciplina.abreviatura', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'disciplinas' => $disciplinas
        ]);
    }

    /**
     * AJAX: Verificar se a disciplina tem turnos
     */
    public function verificarTurnosDisciplina()
    {
        $professorNif = $this->request->getPost('professor_nif');
        $codigoTurma = $this->request->getPost('codigo_turma');
        $disciplinaId = $this->request->getPost('disciplina_id');

        if (!$professorNif || !$codigoTurma || !$disciplinaId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dados incompletos']);
        }

        // Buscar turnos DISTINTOS (pode retornar NULL se não tiver turnos)
        $turnos = $this->horarioModel
            ->distinct()
            ->select('turno')
            ->where('user_nif', $professorNif)
            ->where('codigo_turma', $codigoTurma)
            ->where('disciplina_id', $disciplinaId)
            ->where('turno IS NOT NULL')
            ->where('turno !=', '')
            ->findAll();

        $temTurnos = count($turnos) > 0;

        return $this->response->setJSON([
            'success' => true,
            'tem_turnos' => $temTurnos,
            'turnos' => $turnos
        ]);
    }

    /**
     * Salvar crédito(s) de aula
     */
    public function salvarCredito()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sessão expirada']);
        }

        $userLevel = $userData['level'] ?? 0;
        if ($userLevel < 6) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissões']);
        }

        $post = $this->request->getPost();

        // Validação
        $validationRules = [
            'professor_nif'  => 'required',
            'codigo_turma'   => 'required',
            'disciplina_id'  => 'required',
            'data_visita'    => 'required|valid_date',
            'origem'         => 'required',
            'num_aulas'      => 'required|integer|greater_than[0]|less_than[21]',
        ];

        if (!$this->validate($validationRules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Buscar ano letivo ativo
        $anoLetivoAtivo = $this->anoLetivoModel->where('status', 1)->first();
        if (!$anoLetivoAtivo) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nenhum ano letivo ativo']);
        }

        // Validar que o professor leciona a turma/disciplina
        $validacao = $this->creditoModel->validarProfessorLecionaTurmaDisciplina(
            $post['professor_nif'],
            $post['codigo_turma'],
            $post['disciplina_id'],
            $post['turno'] ?? null
        );

        if (!$validacao) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'O professor não leciona esta disciplina nesta turma' . 
                           ($post['turno'] ? ' neste turno' : '')
            ]);
        }

        // Preparar dados do crédito
        $turno = $post['turno'] ?? null;
        if ($turno === '' || $turno === 'null') {
            $turno = null;
        }

        $dadosCredito = [
            'professor_nif'      => $post['professor_nif'],
            'codigo_turma'       => $post['codigo_turma'],
            'disciplina_id'      => $post['disciplina_id'],
            'turno'              => $turno,
            'data_visita'        => $post['data_visita'],
            'origem'             => $post['origem'],
            'ano_letivo_id'      => $anoLetivoAtivo['id_anoletivo'],
            'estado'             => 'disponivel',
            'observacoes'        => $post['observacoes'] ?? null,
            'criado_por_user_id' => $userData['id'],
        ];

        // Criar múltiplos créditos
        $numAulas = (int) $post['num_aulas'];
        $resultado = $this->creditoModel->criarMultiplosCreditos($dadosCredito, $numAulas);

        if ($resultado) {
            // Log
            log_message('info', "Créditos criados: {$numAulas} aulas para professor {$post['professor_nif']} por user {$userData['id']}");

            return $this->response->setJSON([
                'success' => true,
                'message' => "{$numAulas} crédito(s) de aula criado(s) com sucesso!"
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao criar créditos',
                'errors' => $this->creditoModel->errors()
            ]);
        }
    }

    /**
     * Cancelar crédito
     */
    public function cancelarCredito()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sessão expirada']);
        }

        $userLevel = $userData['level'] ?? 0;
        if ($userLevel < 6) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissões']);
        }

        $creditoId = $this->request->getPost('credito_id');
        $motivo = $this->request->getPost('motivo');

        if (!$creditoId || !$motivo) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dados incompletos']);
        }

        $resultado = $this->creditoModel->cancelarCredito($creditoId, $userData['id'], $motivo);

        if ($resultado) {
            log_message('info', "Crédito {$creditoId} cancelado por user {$userData['id']}");

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Crédito cancelado com sucesso'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao cancelar crédito'
            ]);
        }
    }
}

