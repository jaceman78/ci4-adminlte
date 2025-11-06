<?php

namespace App\Controllers;

use App\Models\TurmaModel;
use App\Models\AnoLetivoModel;
use App\Models\TipologiaModel;
use App\Models\UserModel;
use App\Models\EscolasModel;

class TurmasController extends BaseController
{
    protected $turmaModel;
    protected $anoLetivoModel;
    protected $tipologiaModel;
    protected $userModel;
    protected $escolasModel;

    public function __construct()
    {
        $this->turmaModel = new TurmaModel();
        $this->anoLetivoModel = new AnoLetivoModel();
        $this->tipologiaModel = new TipologiaModel();
    $this->userModel = new UserModel();
    $this->escolasModel = new EscolasModel();
    }

    public function index()
    {
        // Verificar nível de acesso
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel < 6) {
            return redirect()->to('/')->with('error', 'Acesso negado');
        }

        $data = [
            'title' => 'Gestão de Turmas',
            'page_title' => 'Gestão de Turmas',
            'page_subtitle' => 'Listagem e gestão de turmas',
            'anos_letivos' => $this->anoLetivoModel->getAnosOrdenados('DESC'),
            'tipologias' => $this->tipologiaModel->getTipologiasAtivas(),
            // Usar NIF como chave de seleção
            'professores' => $this->userModel->select('NIF, name')->orderBy('name', 'ASC')->findAll(),
            'escolas' => $this->escolasModel->select('id, nome')->orderBy('nome', 'ASC')->findAll()
        ];

        return view('gestao_letiva/turmas_index', $data);
    }

    public function getDataTable()
    {
        $request = \Config\Services::request();
        // Apenas turmas do ano letivo ativo
        $anoAtivo = $this->anoLetivoModel->getAnoAtivo();
        if (!$anoAtivo || !isset($anoAtivo['id_anoletivo'])) {
            return $this->response->setJSON(['data' => []]);
        }

        $turmas = $this->turmaModel->getTurmasComDetalhes($anoAtivo['id_anoletivo']);

        return $this->response->setJSON(['data' => $turmas]);
    }

    public function create()
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        $data = $this->request->getPost();

        // Preencher anoletivo_id com o ano letivo ativo se não vier do formulário
        if (empty($data['anoletivo_id'])) {
            $ativo = $this->anoLetivoModel->getAnoAtivo();
            if ($ativo && isset($ativo['id_anoletivo'])) {
                $data['anoletivo_id'] = $ativo['id_anoletivo'];
            }
        }

        // Definir tipologia regular (1) por defeito se não vier do formulário
        if (empty($data['tipologia_id'])) {
            $data['tipologia_id'] = 1;
        }
        
        // Limpar campos de NIF vazios ou "0" para NULL
        if (empty($data['dir_turma_nif']) || $data['dir_turma_nif'] === '0' || $data['dir_turma_nif'] === 0) {
            $data['dir_turma_nif'] = null;
        }
        if (empty($data['secretario_nif']) || $data['secretario_nif'] === '0' || $data['secretario_nif'] === 0) {
            $data['secretario_nif'] = null;
        }
        
        $turmaId = $this->turmaModel->insert($data);
        
        if ($turmaId) {
            // LOG: Registar criação
            try {
                $codigo = $data['codigo'] ?? '';
                $nome = $data['nome'] ?? $data['abreviatura'] ?? '';
                $ano = $data['ano'] ?? '';
                log_activity(
                    $userId,
                    'turmas',
                    'create',
                    "Criou turma '{$nome}' (Código: {$codigo}, Ano: {$ano})",
                    $turmaId,
                    null,
                    $data
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de criação de turma: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Turma criada com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao criar turma',
            'errors' => $this->turmaModel->errors()
        ]);
    }

    public function update($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        
        // Buscar dados anteriores
        $dadosAnteriores = $this->turmaModel->find($id);
        
        $data = $this->request->getPost();
        // Em atualizações, não forçar defaults se o utilizador não mudou; manter valores enviados
        
        // Limpar campos de NIF vazios ou "0" para NULL
        if (empty($data['dir_turma_nif']) || $data['dir_turma_nif'] === '0' || $data['dir_turma_nif'] === 0) {
            $data['dir_turma_nif'] = null;
        }
        if (empty($data['secretario_nif']) || $data['secretario_nif'] === '0' || $data['secretario_nif'] === 0) {
            $data['secretario_nif'] = null;
        }
        
        if ($this->turmaModel->update($id, $data)) {
            // LOG: Registar atualização
            try {
                $nome = $data['nome'] ?? $dadosAnteriores['nome'] ?? '';
                $codigo = $data['codigo'] ?? $dadosAnteriores['codigo'] ?? '';
                
                // Identificar campos alterados
                $alteracoes = [];
                foreach ($data as $campo => $novoValor) {
                    if (isset($dadosAnteriores[$campo]) && $dadosAnteriores[$campo] != $novoValor) {
                        $alteracoes[] = "{$campo}";
                    }
                }
                
                $descricao = "Atualizou turma '{$nome}' (Código: {$codigo})";
                if (!empty($alteracoes)) {
                    $descricao .= " - Campos alterados: " . implode(', ', $alteracoes);
                }
                
                log_activity(
                    $userId,
                    'turmas',
                    'update',
                    $descricao,
                    $id,
                    $dadosAnteriores,
                    $data
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de atualização de turma: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Turma atualizada com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar turma',
            'errors' => $this->turmaModel->errors()
        ]);
    }

    public function delete($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        
        // Buscar dados antes de eliminar
        $turma = $this->turmaModel->find($id);
        
        if (!$turma) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Turma não encontrada'
            ]);
        }
        
        if ($this->turmaModel->delete($id)) {
            // LOG: Registar eliminação
            try {
                $nome = $turma['nome'] ?? '';
                $codigo = $turma['codigo'] ?? '';
                $ano = $turma['ano'] ?? '';
                
                log_activity(
                    $userId,
                    'turmas',
                    'delete',
                    "Eliminou turma '{$nome}' (Código: {$codigo}, Ano: {$ano})",
                    $id,
                    $turma,
                    null
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de eliminação de turma: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Turma excluída com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao excluir turma'
        ]);
    }

    public function get($id)
    {
        $turma = $this->turmaModel->find($id);
        
        if ($turma) {
            return $this->response->setJSON($turma);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Turma não encontrada'
        ]);
    }

    /**
     * Importar turmas via CSV
     * Estrutura esperada:
     * Codigo;Abreviatura;Descritivo;Ano;NumAlunos;Secretario;Escola;DirTurma
     * - Ano Letivo: preenchido automaticamente com o ano ativo
     * - Tipologia: 1 (Regular) por defeito
     * - Outros campos: NULL se não fornecidos
     */
    public function importar()
    {
        // Verificar nível de acesso
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel < 6) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        $file = $this->request->getFile('csv_file');
        $skipDuplicates = $this->request->getPost('skip_duplicates') === 'on';
        $downloadErrors = $this->request->getPost('download_errors') === 'on';

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ficheiro inválido ou não enviado'
            ]);
        }

        if ($file->getExtension() !== 'csv') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Apenas ficheiros .csv são permitidos'
            ]);
        }

        $imported = 0;
        $skipped = 0;
        $errors = 0;
        $errorLines = [];
        $errorLines[] = 'Codigo;Abreviatura;Descritivo;Ano;NumAlunos;Secretario;Escola;DirTurma;Motivo_Erro';

        try {
            $csvData = file_get_contents($file->getTempName());

            // Converter encoding para UTF-8 se necessário
            $encodings = ['UTF-8', 'Windows-1252', 'ISO-8859-1'];
            foreach ($encodings as $encoding) {
                if (mb_check_encoding($csvData, $encoding)) {
                    $csvData = mb_convert_encoding($csvData, 'UTF-8', $encoding);
                    break;
                }
            }

            $lines = explode("\n", $csvData);
            $isFirst = true;

            // Ano letivo ativo
            $anoAtivo = $this->anoLetivoModel->getAnoAtivo();
            $anoletivoId = $anoAtivo['id_anoletivo'] ?? null;

            foreach ($lines as $line) {
                if ($isFirst) { $isFirst = false; continue; }
                $line = trim($line);
                if ($line === '') { continue; }

                $fields = str_getcsv($line, ';');
                if (count($fields) < 8) {
                    $errors++;
                    $errorLines[] = $line . ';Número de colunas insuficiente';
                    continue;
                }

                [$codigo, $abreviatura, $descritivo, $ano, $numAlunos, $secretario, $escola, $dirTurma] = array_map('trim', $fields);

                // Validações básicas
                if ($codigo === '') { $errors++; $errorLines[] = $line . ';Código obrigatório'; continue; }
                if ($abreviatura === '') { $errors++; $errorLines[] = $line . ';Abreviatura obrigatória'; continue; }

                // Ano (0..12)
                if ($ano === '' || !is_numeric($ano) || (int)$ano < 0 || (int)$ano > 12) {
                    $errors++; $errorLines[] = $line . ';Ano inválido (esperado 0..12)'; continue;
                }
                $ano = (int)$ano;

                // Num Alunos
                if ($numAlunos === '' || !is_numeric($numAlunos) || (int)$numAlunos < 0) {
                    $numAlunos = 0; // default
                } else {
                    $numAlunos = (int)$numAlunos;
                }

                // Escola (id)
                $escolaId = null;
                if ($escola !== '') {
                    if (!ctype_digit($escola)) {
                        $errors++; $errorLines[] = $line . ';Escola inválida (usar ID numérico)'; continue;
                    }
                    $e = $this->escolasModel->find((int)$escola);
                    if (!$e) {
                        $errors++; $errorLines[] = $line . ';Escola não encontrada'; continue;
                    }
                    $escolaId = (int)$escola;
                }

                // Verificar NIFs opcionalmente (não bloquear se vazios)
                $dirNif = $dirTurma !== '' ? $dirTurma : null;
                if ($dirNif !== null) {
                    $exists = $this->userModel->where('NIF', $dirNif)->first();
                    if (!$exists) { $errors++; $errorLines[] = $line . ';NIF do Diretor não encontrado'; continue; }
                }

                $secNif = $secretario !== '' ? $secretario : null;
                if ($secNif !== null) {
                    $exists = $this->userModel->where('NIF', $secNif)->first();
                    if (!$exists) { $errors++; $errorLines[] = $line . ';NIF do Secretário não encontrado'; continue; }
                }

                // Duplicados: usar codigo + anoletivo
                if ($skipDuplicates && $anoletivoId) {
                    $dup = $this->turmaModel->where('codigo', $codigo)
                                             ->where('anoletivo_id', $anoletivoId)
                                             ->first();
                    if ($dup) { $skipped++; continue; }
                }

                $data = [
                    'codigo'         => $codigo,
                    'abreviatura'    => $abreviatura,
                    'descritivo'     => $descritivo !== '' ? $descritivo : null,
                    'ano'            => $ano,
                    // Nome: preenche automaticamente a partir da abreviatura
                    'nome'           => $abreviatura,
                    'num_alunos'     => $numAlunos,
                    'secretario_nif' => $secNif,
                    'escola_id'      => $escolaId,
                    'dir_turma_nif'  => $dirNif,
                    'anoletivo_id'   => $anoletivoId,
                    'tipologia_id'   => 1,
                ];

                if ($this->turmaModel->insert($data)) {
                    $imported++;
                    
                    // LOG: Registar importação de linha
                    try {
                        $lastId = $this->turmaModel->getInsertID();
                        log_activity(
                            $userLevel >= 6 ? session()->get('LoggedUserData')['id'] : null,
                            'turmas',
                            'import',
                            "Importou turma via CSV: '{$abreviatura}' (Código: {$codigo})",
                            $lastId,
                            null,
                            $data,
                            ['linha_csv' => trim($line)]
                        );
                    } catch (\Exception $e) {
                        log_message('error', 'Erro ao registar log de importação de turma: ' . $e->getMessage());
                    }
                } else {
                    $errors++;
                    $validationErrors = $this->turmaModel->errors();
                    $errorMsg = !empty($validationErrors) ? implode(', ', $validationErrors) : 'Erro ao inserir';
                    $errorLines[] = $line . ';' . $errorMsg;
                }
            }

            // Gerar ficheiro de erros
            $errorFile = null;
            if ($errors > 0 && $downloadErrors && count($errorLines) > 1) {
                $errorFileName = 'turmas_import_errors_' . date('YmdHis') . '.csv';
                $errorFilePath = FCPATH . 'upload/' . $errorFileName;
                if (!is_dir(FCPATH . 'upload')) { mkdir(FCPATH . 'upload', 0755, true); }
                file_put_contents($errorFilePath, implode("\n", $errorLines));
                $errorFile = base_url('upload/' . $errorFileName);
            }

            // LOG: Resumo da importação
            try {
                log_activity(
                    session()->get('LoggedUserData')['id'] ?? null,
                    'turmas',
                    'import',
                    "Importação CSV de turmas concluída (Sucesso: {$imported}, Ignoradas: {$skipped}, Erros: {$errors})",
                    null,
                    null,
                    null,
                    [
                        'arquivo' => $file->getName(),
                        'total_processados' => $imported + $skipped + $errors,
                        'sucesso' => $imported,
                        'ignoradas' => $skipped,
                        'erros' => $errors,
                        'arquivo_erros' => $errorFile
                    ]
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de resumo de importação: ' . $e->getMessage());
            }

            $message = 'Importação concluída!';
            if ($errors > 0 && !$downloadErrors) {
                $message .= " Utilize a opção 'Gerar ficheiro com linhas rejeitadas' para ver detalhes dos erros.";
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors,
                'error_file' => $errorFile
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao processar ficheiro: ' . $e->getMessage()
            ]);
        }
    }
}
