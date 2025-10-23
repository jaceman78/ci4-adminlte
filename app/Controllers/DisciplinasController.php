<?php

namespace App\Controllers;

use App\Models\DisciplinaModel;
use App\Models\TipologiaModel;

class DisciplinasController extends BaseController
{
    protected $disciplinaModel;
    protected $tipologiaModel;

    public function __construct()
    {
        $this->disciplinaModel = new DisciplinaModel();
        $this->tipologiaModel = new TipologiaModel();
    }

    public function index()
    {
        // Verificar nível de acesso
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel < 6) {
            return redirect()->to('/')->with('error', 'Acesso negado');
        }

        $data = [
            'title' => 'Gestão de Disciplinas',
            'page_title' => 'Gestão de Disciplinas',
            'page_subtitle' => 'Listagem e gestão de disciplinas',
            'tipologias' => $this->tipologiaModel->getTipologiasAtivas()
        ];

        return view('gestao_letiva/disciplinas_index', $data);
    }

    public function getDataTable()
    {
        $disciplinas = $this->disciplinaModel->getDisciplinasComDetalhes();
        
        return $this->response->setJSON(['data' => $disciplinas]);
    }

    public function create()
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        $data = $this->request->getPost();
        
        $disciplinaId = $this->disciplinaModel->insert($data);
        
        if ($disciplinaId) {
            // LOG: Registar criação
            try {
                $codigo = $data['id_disciplina'] ?? '';
                $abreviatura = $data['abreviatura'] ?? '';
                $descritivo = $data['descritivo'] ?? '';
                
                log_activity(
                    $userId,
                    'disciplinas',
                    'create',
                    "Criou disciplina '{$abreviatura}' - {$descritivo} (Código: {$codigo})",
                    $codigo,
                    null,
                    $data
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de criação de disciplina: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Disciplina criada com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao criar disciplina',
            'errors' => $this->disciplinaModel->errors()
        ]);
    }

    public function update($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        
        // Buscar dados anteriores
        $dadosAnteriores = $this->disciplinaModel->find($id);
        
        $data = $this->request->getPost();
        
        if ($this->disciplinaModel->update($id, $data)) {
            // LOG: Registar atualização
            try {
                $abreviatura = $data['abreviatura'] ?? $dadosAnteriores['abreviatura'] ?? '';
                $codigo = $id;
                
                // Identificar campos alterados
                $alteracoes = [];
                foreach ($data as $campo => $novoValor) {
                    if (isset($dadosAnteriores[$campo]) && $dadosAnteriores[$campo] != $novoValor) {
                        $alteracoes[] = "{$campo}";
                    }
                }
                
                $descricao = "Atualizou disciplina '{$abreviatura}' (Código: {$codigo})";
                if (!empty($alteracoes)) {
                    $descricao .= " - Campos alterados: " . implode(', ', $alteracoes);
                }
                
                log_activity(
                    $userId,
                    'disciplinas',
                    'update',
                    $descricao,
                    $codigo,
                    $dadosAnteriores,
                    $data
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de atualização de disciplina: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Disciplina atualizada com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar disciplina',
            'errors' => $this->disciplinaModel->errors()
        ]);
    }

    public function delete($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        
        // Buscar dados antes de eliminar
        $disciplina = $this->disciplinaModel->find($id);
        
        if (!$disciplina) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Disciplina não encontrada'
            ]);
        }
        
        if ($this->disciplinaModel->delete($id)) {
            // LOG: Registar eliminação
            try {
                $abreviatura = $disciplina['abreviatura'] ?? '';
                $descritivo = $disciplina['descritivo'] ?? '';
                
                log_activity(
                    $userId,
                    'disciplinas',
                    'delete',
                    "Eliminou disciplina '{$abreviatura}' - {$descritivo} (Código: {$id})",
                    $id,
                    $disciplina,
                    null
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de eliminação de disciplina: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Disciplina excluída com sucesso'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao excluir disciplina'
        ]);
    }

    public function get($id)
    {
        $disciplina = $this->disciplinaModel->find($id);
        
        if ($disciplina) {
            return $this->response->setJSON($disciplina);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Disciplina não encontrada'
        ]);
    }

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

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ficheiro inválido ou não enviado'
            ]);
        }

        // Verificar extensão
        if ($file->getExtension() !== 'csv') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Apenas ficheiros .csv são permitidos'
            ]);
        }

        $imported = 0;
        $skipped = 0;
        $errors = 0;
        $errorDetails = [];

        try {
            // Ler ficheiro CSV
            $csvData = file_get_contents($file->getTempName());
            
            // Tentar diferentes encodings
            $encodings = ['UTF-8', 'Windows-1252', 'ISO-8859-1'];
            foreach ($encodings as $encoding) {
                if (mb_check_encoding($csvData, $encoding)) {
                    $csvData = mb_convert_encoding($csvData, 'UTF-8', $encoding);
                    break;
                }
            }

            // Processar linhas
            $lines = explode("\n", $csvData);
            $isFirstLine = true;

            foreach ($lines as $lineNum => $line) {
                // Ignorar primeira linha (cabeçalho)
                if ($isFirstLine) {
                    $isFirstLine = false;
                    continue;
                }

                // Ignorar linhas vazias
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }

                // Separar por ponto e vírgula
                $fields = str_getcsv($line, ';');

                // Verificar se tem as 4 colunas esperadas
                if (count($fields) < 4) {
                    $errors++;
                    $errorDetails[] = "Linha " . ($lineNum + 1) . ": Número de colunas insuficiente";
                    continue;
                }

                $codigo = trim($fields[0]);
                $abreviatura = trim($fields[1]);
                $descritivo = trim($fields[2]);
                $tipologia_id = trim($fields[3]);

                // Validações básicas
                if (empty($codigo) || empty($abreviatura)) {
                    $errors++;
                    $errorDetails[] = "Linha " . ($lineNum + 1) . ": Código ou Abreviatura vazio";
                    continue;
                }

                // Verificar se já existe
                $exists = $this->disciplinaModel->find($codigo);
                if ($exists) {
                    if ($skipDuplicates) {
                        $skipped++;
                        continue;
                    } else {
                        $errors++;
                        $errorDetails[] = "Linha " . ($lineNum + 1) . ": ID '$codigo' já existe";
                        continue;
                    }
                }

                // Validar tipologia (se for 0, deixar NULL ou usar valor padrão)
                if ($tipologia_id == '0' || empty($tipologia_id)) {
                    // Buscar primeira tipologia ativa ou usar NULL
                    $tipologias = $this->tipologiaModel->getTipologiasAtivas();
                    $tipologia_id = !empty($tipologias) ? $tipologias[0]['id_tipologia'] : null;
                }

                // Preparar dados
                $data = [
                    'id_disciplina' => $codigo,
                    'abreviatura' => $abreviatura,
                    'descritivo' => !empty($descritivo) ? $descritivo : null,
                    'tipologia_id' => $tipologia_id
                ];

                // Inserir
                if ($this->disciplinaModel->insert($data)) {
                    $imported++;
                    
                    // LOG: Registar importação de linha
                    try {
                        log_activity(
                            session()->get('LoggedUserData')['id'] ?? null,
                            'disciplinas',
                            'import',
                            "Importou disciplina via CSV: '{$abreviatura}' - {$descritivo} (Código: {$codigo})",
                            $codigo,
                            null,
                            $data,
                            ['linha_csv' => trim($line)]
                        );
                    } catch (\Exception $e) {
                        log_message('error', 'Erro ao registar log de importação de disciplina: ' . $e->getMessage());
                    }
                } else {
                    $errors++;
                    $validationErrors = $this->disciplinaModel->errors();
                    $errorDetails[] = "Linha " . ($lineNum + 1) . ": " . implode(', ', $validationErrors);
                }
            }

            // Resposta de sucesso
            $message = "Importação concluída!";
            if (count($errorDetails) > 0 && count($errorDetails) <= 5) {
                $message .= "<br><small>" . implode('<br>', $errorDetails) . "</small>";
            }

            // LOG: Resumo da importação
            try {
                log_activity(
                    session()->get('LoggedUserData')['id'] ?? null,
                    'disciplinas',
                    'import',
                    "Importação CSV de disciplinas concluída (Sucesso: {$imported}, Ignoradas: {$skipped}, Erros: {$errors})",
                    null,
                    null,
                    null,
                    [
                        'arquivo' => $file->getName(),
                        'total_processados' => $imported + $skipped + $errors,
                        'sucesso' => $imported,
                        'ignoradas' => $skipped,
                        'erros' => $errors
                    ]
                );
            } catch (\Exception $e) {
                log_message('error', 'Erro ao registar log de resumo de importação: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao processar ficheiro: ' . $e->getMessage()
            ]);
        }
    }
}
