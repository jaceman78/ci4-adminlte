<?php
namespace App\Controllers;

use App\Models\ReparacoesExternasModel;
use App\Models\EmpresaChaveAcessoModel;
use CodeIgniter\HTTP\ResponseInterface;

class ReparacoesExternasController extends BaseController
{
    protected $reparacoesModel;
    protected $empresaChaveModel;

    public function __construct()
    {
        $this->reparacoesModel = new ReparacoesExternasModel();
        $this->empresaChaveModel = new EmpresaChaveAcessoModel();
        helper(['logs', 'form']);
    }

    /**
     * Verificar se o utilizador tem nível 7 ou superior (Técnico Sénior)
     */
    private function checkAccess()
    {
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel < 7) {
            return redirect()->to('/')->with('error', 'Acesso negado. Apenas Técnicos Sénior podem aceder a esta área.');
        }
        return null;
    }

    /**
     * Página principal com listagem
     */
    public function index()
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        // Log de acesso à página
        $userData = session()->get('LoggedUserData');
        $userId = $userData['ID'] ?? $userData['id'] ?? null;
        if ($userId) {
            log_activity(
                'reparacoes_externas',
                'view_index',
                null,
                "Acedeu à listagem de reparações externas"
            );
        }

        // Obter estatísticas
        $stats = $this->reparacoesModel->getEstatisticas();
        
        // Obter lista de empresas ativas
        $empresas = $this->empresaChaveModel->where('ativo', 1)->orderBy('empresa_nome', 'ASC')->findAll();

        return view('reparacoes_externas/index', [
            'stats' => $stats,
            'empresas' => $empresas
        ]);
    }

    /**
     * Dados para DataTable (AJAX)
     */
    public function getData()
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON(['error' => 'Acesso negado'])->setStatusCode(403);
        }

        $reparacoes = $this->reparacoesModel->getReparacoesComDetalhes();

        return $this->response->setJSON([
            'data' => $reparacoes
        ]);
    }

    /**
     * Obter estatísticas (AJAX)
     */
    public function getStats()
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON(['error' => 'Acesso negado'])->setStatusCode(403);
        }

        $stats = $this->reparacoesModel->getEstatisticas();
        $porTipologia = $this->reparacoesModel->getEstatisticasPorTipologia();
        $porAvaria = $this->reparacoesModel->getEstatisticasPorAvaria();

        return $this->response->setJSON([
            'stats' => $stats,
            'por_tipologia' => $porTipologia,
            'por_avaria' => $porAvaria
        ]);
    }

    /**
     * Criar nova reparação
     */
    public function create()
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        if ($this->request->getMethod() === 'POST') {
            $userData = session()->get('LoggedUserData');
            $userId = $userData['ID'] ?? $userData['id'] ?? null;

            $data = [
                'n_serie_equipamento' => $this->request->getPost('n_serie_equipamento'),
                'tipologia' => $this->request->getPost('tipologia'),
                'possivel_avaria' => $this->request->getPost('possivel_avaria'),
                'descricao_avaria' => $this->request->getPost('descricao_avaria'),
                'data_envio' => $this->request->getPost('data_envio'),
                'empresa_reparacao' => $this->request->getPost('empresa_reparacao'),
                'n_guia' => $this->request->getPost('n_guia'),
                'trabalho_efetuado' => $this->request->getPost('trabalho_efetuado'),
                'custo' => $this->request->getPost('custo') ?: null,
                'data_recepcao' => $this->request->getPost('data_recepcao') ?: null,
                'observacoes' => $this->request->getPost('observacoes'),
                'estado' => $this->request->getPost('estado') ?: 'enviado',
                'id_tecnico' => $userId
            ];

            if ($this->reparacoesModel->insert($data)) {
                // Log da ação
                if ($userId) {
                    log_activity(
                        'reparacoes_externas',
                        'create',
                        $this->reparacoesModel->getInsertID(),
                        "Criou nova reparação externa para equipamento: {$data['n_serie_equipamento']}"
                    );
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Reparação registada com sucesso!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao registar reparação.',
                    'errors' => $this->reparacoesModel->errors()
                ])->setStatusCode(400);
            }
        }
    }

    /**
     * Atualizar reparação existente
     */
    public function update($id)
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'n_serie_equipamento' => $this->request->getPost('n_serie_equipamento'),
                'tipologia' => $this->request->getPost('tipologia'),
                'possivel_avaria' => $this->request->getPost('possivel_avaria'),
                'descricao_avaria' => $this->request->getPost('descricao_avaria'),
                'data_envio' => $this->request->getPost('data_envio'),
                'empresa_reparacao' => $this->request->getPost('empresa_reparacao'),
                'n_guia' => $this->request->getPost('n_guia'),
                'trabalho_efetuado' => $this->request->getPost('trabalho_efetuado'),
                'custo' => $this->request->getPost('custo') ?: null,
                'data_recepcao' => $this->request->getPost('data_recepcao') ?: null,
                'observacoes' => $this->request->getPost('observacoes'),
                'estado' => $this->request->getPost('estado')
            ];

            if ($this->reparacoesModel->update($id, $data)) {
                // Log da ação
                $userData = session()->get('LoggedUserData');
                $userId = $userData['ID'] ?? $userData['id'] ?? null;
                if ($userId) {
                    log_activity(
                        'reparacoes_externas',
                        'update',
                        $id,
                        "Atualizou reparação externa ID: {$id}"
                    );
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Reparação atualizada com sucesso!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao atualizar reparação.',
                    'errors' => $this->reparacoesModel->errors()
                ])->setStatusCode(400);
            }
        }
    }

    /**
     * Obter detalhes de uma reparação (AJAX)
     */
    public function getDetails($id)
    {
        if ($redirect = $this->checkAccess()) {
            return $this->response->setJSON(['error' => 'Acesso negado'])->setStatusCode(403);
        }

        $reparacao = $this->reparacoesModel->find($id);

        if ($reparacao) {
            return $this->response->setJSON([
                'success' => true,
                'data' => $reparacao
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Reparação não encontrada.'
            ])->setStatusCode(404);
        }
    }

    /**
     * Eliminar reparação (soft delete)
     */
    public function delete($id)
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        if ($this->reparacoesModel->delete($id)) {
            // Log da ação
            $userData = session()->get('LoggedUserData');
            $userId = $userData['ID'] ?? $userData['id'] ?? null;
            if ($userId) {
                log_activity(
                    'reparacoes_externas',
                    'delete',
                    $id,
                    "Eliminou reparação externa ID: {$id}"
                );
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Reparação eliminada com sucesso!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao eliminar reparação.'
            ])->setStatusCode(400);
        }
    }

    /**
     * Exportar para CSV
     */
    public function export()
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        $estado = $this->request->getGet('estado');
        $tipologia = $this->request->getGet('tipologia');

        $builder = $this->reparacoesModel->builder();
        
        if ($estado) {
            $builder->where('estado', $estado);
        }
        
        if ($tipologia) {
            $builder->where('tipologia', $tipologia);
        }

        $builder->where('deleted_at', null);
        $reparacoes = $builder->orderBy('data_envio', 'DESC')->get()->getResultArray();

        $filename = 'reparacoes_externas_' . date('Ymd_His') . '.csv';

        $output = fopen('php://temp', 'r+');
        // BOM UTF-8 para Excel reconhecer acentos
        fwrite($output, "\xEF\xBB\xBF");

        // Cabeçalho CSV
        fputcsv($output, [
            'ID',
            'Nº Série',
            'Tipologia',
            'Tipo Avaria',
            'Descrição Avaria',
            'Data Envio',
            'Empresa Reparação',
            'Nº Guia',
            'Trabalho Efetuado',
            'Custo (€)',
            'Data Receção',
            'Observações',
            'Estado',
            'Criado Em'
        ]);

        foreach ($reparacoes as $r) {
            fputcsv($output, [
                $r['id_reparacao'] ?? '',
                $r['n_serie_equipamento'] ?? '',
                $r['tipologia'] ?? '',
                $r['possivel_avaria'] ?? '',
                $r['descricao_avaria'] ?? '',
                $r['data_envio'] ?? '',
                $r['empresa_reparacao'] ?? '',
                $r['n_guia'] ?? '',
                $r['trabalho_efetuado'] ?? '',
                $r['custo'] ?? '',
                $r['data_recepcao'] ?? '',
                $r['observacoes'] ?? '',
                $r['estado'] ?? '',
                $r['created_at'] ?? ''
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        // Log da ação
        $userData = session()->get('LoggedUserData');
        $userId = $userData['ID'] ?? $userData['id'] ?? null;
        if ($userId) {
            log_activity(
                'reparacoes_externas',
                'export',
                null,
                "Exportou dados de reparações externas para CSV"
            );
        }

        return $this->response
            ->setStatusCode(200)
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }

    /**
     * Importar de CSV
     */
    public function import()
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        if ($this->request->getMethod() === 'POST') {
            $file = $this->request->getFile('csv_file');

            if (!$file || !$file->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ficheiro inválido ou não foi enviado.'
                ])->setStatusCode(400);
            }

            // Verificar se é CSV
            $ext = $file->getClientExtension();
            if ($ext !== 'csv') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Apenas ficheiros CSV são permitidos.'
                ])->setStatusCode(400);
            }

            // Ler CSV
            $handle = fopen($file->getTempName(), 'r');
            
            // Ignorar BOM se existir
            fseek($handle, 0);
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                fseek($handle, 0);
            }

            // Ler cabeçalho
            $header = fgetcsv($handle, 1000, ',');
            
            if (!$header) {
                fclose($handle);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ficheiro CSV vazio ou formato inválido.'
                ])->setStatusCode(400);
            }

            $userData = session()->get('LoggedUserData');
            $userId = $userData['ID'] ?? $userData['id'] ?? null;

            $dados = [];
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                // Mapear campos do CSV para o modelo
                // Espera-se: Nº Série, Tipologia, Tipo Avaria, Descrição Avaria, Data Envio, Empresa, Nº Guia, Trabalho, Custo, Data Receção, Observações, Estado
                if (count($row) >= 5) { // Mínimo de campos obrigatórios
                    $dados[] = [
                        'n_serie_equipamento' => $row[0] ?? '',
                        'tipologia' => $row[1] ?? 'Tipo I',
                        'possivel_avaria' => $row[2] ?? 'Outro',
                        'descricao_avaria' => $row[3] ?? '',
                        'data_envio' => $row[4] ?? date('Y-m-d'),
                        'empresa_reparacao' => $row[5] ?? '',
                        'n_guia' => $row[6] ?? '',
                        'trabalho_efetuado' => $row[7] ?? '',
                        'custo' => !empty($row[8]) ? $row[8] : null,
                        'data_recepcao' => !empty($row[9]) ? $row[9] : null,
                        'observacoes' => $row[10] ?? '',
                        'estado' => $row[11] ?? 'enviado',
                        'id_tecnico' => $userId
                    ];
                }
            }
            fclose($handle);

            // Importar dados
            $resultado = $this->reparacoesModel->importarCSV($dados);

            // Log da ação
            if ($userId) {
                log_activity(
                    'reparacoes_externas',
                    'import',
                    null,
                    "Importou {$resultado['sucesso']}/{$resultado['total']} reparações de CSV"
                );
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Importação concluída: {$resultado['sucesso']} registos importados de {$resultado['total']} total.",
                'resultado' => $resultado
            ]);
        }
    }

    /**
     * Download do template CSV
     */
    public function downloadTemplate()
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        $output = fopen('php://temp', 'r+');
        // BOM UTF-8
        fwrite($output, "\xEF\xBB\xBF");

        // Cabeçalho
        fputcsv($output, [
            'Nº Série',
            'Tipologia',
            'Tipo Avaria',
            'Descrição Avaria',
            'Data Envio',
            'Empresa Reparação',
            'Nº Guia',
            'Trabalho Efetuado',
            'Custo (€)',
            'Data Receção',
            'Observações',
            'Estado'
        ]);

        // Exemplo
        fputcsv($output, [
            'ABC123456',
            'Tipo I',
            'Bateria',
            'Bateria não carrega',
            date('Y-m-d'),
            'TechRepair Lda',
            'GR2024001',
            'Substituição de bateria',
            '45.50',
            '',
            'Equipamento em garantia',
            'enviado'
        ]);

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $this->response
            ->setStatusCode(200)
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="template_reparacoes_externas.csv"')
            ->setBody($csv);
    }
}
