<?php 

namespace App\Controllers;

helper('log');
 helper("LogHelper"); // Carrega o helper de logs 
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class UserController extends ResourceController
{
    protected $userModel;
    protected $validation;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->validation = \Config\Services::validation();
        helper("LogHelper"); // Carrega o helper de logs
    }

    /**
     * Página principal de utilizadores 
     */
    public function index()
    {
        // Verificar nível de acesso
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel < 5) {
            return redirect()->to('/tickets/novo')->with('error', 'Acesso negado. Nível de permissão insuficiente.');
        }
        
        // Log de acesso à página
        $userId = session()->get('user_id');
        if ($userId && $this->userModel->find($userId)) {
        log_activity(
            (int)$userId , // quem acedeu
            'users',
            'view',
            'Acedeu à página de gestão de utilizadores'
        );
    }          // DEBUG: Mostra dados diretamente para confirmar se estão na sessão
    // echo '<pre>';
    // print_r(session()->get('LoggedUserData'));
    // print_r($userId);
    
    // echo '</pre>';
    // exit;
        return view('users/user_index');
    }

    /**
     * Obter dados para DataTable via AJAX
     */
    public function getDataTable()
    {
        if (!$this->request->isAJAX()) {
            // Log de tentativa de acesso não autorizado
            log_activity(
                session()->get('user_id'), // quem tentou aceder
                'users',
                'view_failed',
                'Tentou aceder à lista de utilizadores via DataTable sem ser AJAX'
            );
        }

        $request = $this->request->getPost();
        
        $start = $request['start'] ?? 0;
        $length = $request['length'] ?? 10;
        $search = $request['search']['value'] ?? '';
        
        // Configurar ordenação
        $orderColumn = 'id';
        $orderDir = 'asc';
        
        if (isset($request['order'][0])) {
            $columns = ['id', 'name', 'email', 'telefone', 'NIF', 'level', 'status', 'created_at'];
            $orderColumnIndex = $request['order'][0]['column'];
            $orderColumn = $columns[$orderColumnIndex] ?? 'id';
            $orderDir = $request['order'][0]['dir'] ?? 'asc';
        }

        $result = $this->userModel->getDataTableData($start, $length, $search, $orderColumn, $orderDir);
        
      
        $detalhes = [
            'search' => $search,
            'order_column' => $orderColumn,
            'order_dir' => $orderDir,
            'records_found' => $result['recordsFiltered']
        ];
        
        // Formatar dados para DataTable
        $data = [];
        foreach ($result['data'] as $user) {
            $statusBadge = $user['status'] == 1 
                ? '<span class="badge bg-success">Ativo</span>' 
                : ($user['status'] == 2 
                    ? '<span class="badge bg-warning text-dark">Pendente</span>' 
                    : '<span class="badge bg-danger">Inativo</span>');
            
            $profileImg = '';
            if ($user['profile_img'] && str_starts_with($user['profile_img'], 'http' )) {
                $profileImg = '<img src="' . $user['profile_img'] . '" class="img-circle" width="30" height="30">';
            } else if ($user['profile_img'] && $user['profile_img'] !== 'default.png') {
                $profileImg = '<img src="' . base_url('writable/uploads/profiles/' . $user['profile_img']) . '" class="img-circle" width="30" height="30">';
            } else {
                $profileImg = '<img src="' . base_url('assets/img/default.png') . '" class="img-circle" width="30" height="30">';
            }

            $actions = '
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-primary" onclick="editUser(' . $user['id'] . ')" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-info" onclick="viewUser(' . $user['id'] . ')" title="Ver">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteUser(' . $user['id'] . ')" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>';
            
            $data[] = [
                $user['id'],
                $profileImg,
                $user['name'] ?? 'N/A',
                $user['email'],
                $user['telefone'] ?? 'N/A',
                $user['NIF'] ?? 'N/A',
                $user['level'],
                $statusBadge,
                date('d/m/Y H:i', strtotime($user['created_at'])),
                $actions
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($request['draw'] ?? 1),
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => $data
        ]);
    }

    /**
     * Obter dados de um utilizador específico
     */
    public function getUser($id = null)
    {
        if (!$this->request->isAJAX()) {
            
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        $user = $this->userModel->find($id);
        
        if (!$user) {
            // Log de tentativa de acesso a utilizador inexistente
           
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Utilizador não encontrado']);
        }

        // Log de visualização de utilizador
        
     

        return $this->response->setJSON(['success' => true, 'data' => $user]);
    }

    /**
     * Criar novo utilizador
     */
    public function create()
    {
        if (!$this->request->isAJAX()) {
            
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $data = $this->request->getPost();
        
   
        
        // Validar dados
        $validation = $this->userModel->validateUserData($data);
        
        if (!$validation['success']) {
            // Log de tentativa de criação com dados inválidos
     
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validation['errors']
            ]);
        }

        // Preparar dados para inserção
        $userData = [
            'oauth_id' => $data['oauth_id'] ?? null,
            'name' => $data['name'] ?? null,
            'email' => $data['email'],
            'telefone' => $data['telefone'] ?? null,
            'NIF' => $data['NIF'] ?? null,
            'profile_img' => $data['profile_img'] ?? 'default.png',
            'grupo_id' => $data['grupo_id'] ?? null,
            'level' => $data['level'] ?? 0,
            'status' => $data['status'] ?? 1
        ];

        $userId = $this->userModel->insert($userData);
            

        if ($userId) {
            // Log de criação bem-sucedida
            log_activity(
                session()->get('user_id'), // quem criou
                'users',
                'create',
                'Criou novo utilizador: ' . $userData['name'] . ' (' . $userData['email'] . ')',
                $userId,
                null,
                
            );
       
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Utilizador criado com sucesso!',
                'data' => ['id' => $userId]
            ]);
        } else {
            // Log de erro na criação
            log_activity(
                session()->get('user_id'), // quem tentou criar
                'users',
                'create_failed',
                'Erro ao criar utilizador na base de dados',
                null,
                null,
                $userData
            );
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao criar utilizador',
                'errors' => $this->userModel->errors()
            ]);
        }
    }

    /**
     * Atualizar utilizador existente
     */
    public function update($id = null)
    {
        if (!$this->request->isAJAX()) {
         
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        // Verificar se utilizador existe e obter dados anteriores
        $existingUser = $this->userModel->find($id);
        if (!$existingUser) {
           
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Utilizador não encontrado']);
        }

        $data = $this->request->getPost();
       
        
        // Validar dados
        $validation = $this->userModel->validateUserData($data, $id);
        
        if (!$validation['success']) {
            // Log de tentativa de atualização com dados inválidos
           
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validation['errors']
            ]);
        }

        // Preparar dados para atualização
        $userData = [
            'oauth_id' => $data['oauth_id'] ?? null,
            'name' => $data['name'] ?? null,
            'email' => $data['email'],
            'telefone' => $data['telefone'] ?? null,
            'NIF' => $data['NIF'] ?? null,
            'grupo_id' => $data['grupo_id'] ?? null,
            'level' => $data['level'] ?? 0,
            'status' => $data['status'] ?? 1
        ];

        // Só atualizar profile_img se fornecida
        if (isset($data['profile_img']) && !empty($data['profile_img'])) {
            $userData['profile_img'] = $data['profile_img'];
        }

        $result = $this->userModel->update($id, $userData);
       
        if ($result) {
            // Log de atualização bem-sucedida
          
          log_activity(
                session()->get('user_id'), // quem fez a alteração
                'users',
                'update',
                'Atualizou utilizador: ' . $existingUser['name'] . ' (' . $existingUser['email'] . ')',
                $id,
           
            );
         
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Utilizador atualizado com sucesso!'
            ]);
        } else {
            // Log de erro na atualização
          
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao atualizar utilizador',
                'errors' => $this->userModel->errors()
            ]);
        }
    }

    /**
     * Eliminar utilizador (soft delete)
     */
    public function delete($id = null)
    {
        if (!$this->request->isAJAX()) {
            
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        // Verificar se utilizador existe e obter dados antes da eliminação
        $user = $this->userModel->find($id);
        if (!$user) {
           
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Utilizador não encontrado']);
        }

        // Soft delete (atualizar status para 0)
        $result = $this->userModel->softDelete($id);
        
        if ($result) {
            // Log de eliminação bem-sucedida
          log_activity(
                session()->get('user_id'), // quem eliminou
                'users',
                'delete',
                'Eliminou utilizador: ' . $user['name'] . ' (' . $user['email'] . ')',
                $id,
                null,
                null
            );
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Utilizador eliminado com sucesso!'
            ]);
        } else {
            // Log de erro na eliminação
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao eliminar utilizador'
            ]);
        }
    }

    /**
     * Restaurar utilizador
     */
    public function restore($id = null)
    {
        if (!$this->request->isAJAX()) {
           
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        // Obter dados do utilizador antes da restauração
        $user = $this->userModel->withDeleted()->find($id);
        
        $result = $this->userModel->restore($id);
        
        if ($result) {
            // Log de restauração bem-sucedida
            $userName = $user ? $user['name'] : "ID: {$id}";
            $userEmail = $user ? $user['email'] : 'N/A';
        
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Utilizador restaurado com sucesso!'
            ]);
        } else {
            // Log de erro na restauração
           
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao restaurar utilizador'
            ]);
        }
    }

    /**
     * Atualizar status do utilizador
     */
    public function updateStatus()
    {
        if (!$this->request->isAJAX()) {
            log_activity(
                session()->get('user_id'), // quem tentou aceder
                'users',
                'update_status',
                'Tentou alterar status de utilizador via AJAX sem ser AJAX'
            );
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $data = $this->request->getPost();
        $id = $data['id'] ?? null;
        $status = $data['status'] ?? null;

        if (!$id || $status === null) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Dados incompletos']);
        }

        // Obter dados do utilizador antes da alteração
        $user = $this->userModel->find($id);
        if (!$user) {
            
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Utilizador não encontrado']);
        }

        $result = $this->userModel->updateUserStatus($id, $status);
        
        if ($result) {
            // Log de alteração de status bem-sucedida
            
                            // Status
                if($status == null || $status == 2) // Pendente se nulo
                    {
                       $statusText='Pendente';
                    }
                    else if($status === 1) // Ativo
                    {
                       $statusText = 'Ativo';
                    }
                    else if($status === 0) // Inativo
                    {
                       $statusText = 'Inativo';
                    }
            $dadosAnteriores = ['status' => $user['status']];
            $dadosNovos = ['status' => $status];
           log_activity(
                session()->get('user_id'), // quem fez a alteração
                'users',
                'update_status',
                "Alterou status do utilizador {$user['name']} ({$user['email']}) para {$statusText}",
                $id,
                $dadosAnteriores,
                $dadosNovos
            );
            return $this->response->setJSON([
                'success' => true,
                'message' => "Utilizador {$statusText} com sucesso!"
            ]);
        } else {
            // Log de erro na alteração de status
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao atualizar status do utilizador'
            ]);
        }
    }

    /**
     * Upload de imagem de perfil
     */
    public function uploadProfileImage()
    {
        if (!$this->request->isAJAX()) {
            log_activity(
                session()->get('user_id'),
                'users',
                'upload_failed',
                'Tentou fazer upload de imagem de perfil sem ser AJAX'
            );
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $file = $this->request->getFile('profile_image');
        
        if (!$file || !$file->isValid()) {
            log_activity(
                session()->get('user_id'),
                'users',
                'upload_failed',
                'Nenhum ficheiro válido foi enviado para upload de imagem de perfil'
            );
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nenhum ficheiro válido foi enviado'
            ]);
        }

        // Validar tipo de ficheiro ANTES de tentar mover
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $fileExtension = $file->getClientExtension();
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            log_activity(
                session()->get('user_id'),
                'users',
                'upload_failed',
                'Tipo de ficheiro inválido: ' . $fileExtension
            );
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tipo de ficheiro inválido. Apenas JPG, PNG e GIF são permitidos.'
            ]);
        }

        // Validar tamanho do ficheiro (máximo 2MB)
        $maxSize = 2 * 1024 * 1024; // 2MB em bytes
        if ($file->getSize() > $maxSize) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ficheiro muito grande. Tamanho máximo: 2MB'
            ]);
        }

        if (!$file->hasMoved()) {
            $newName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/profiles/';
            
            // Criar diretório se não existir
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            try {
                if ($file->move($uploadPath, $newName)) {
                    // Log de upload bem-sucedido
                    $detalhes = [
                        'original_name' => $file->getClientName(),
                        'new_name' => $newName,
                        'file_size' => $file->getSize(),
                        'file_extension' => $fileExtension
                    ];
                    
                    log_activity(
                        session()->get('user_id'),
                        'users',
                        'upload',
                        'Fez upload de imagem de perfil: ' . $file->getClientName(),
                        null,
                        null,
                        $detalhes
                    );
                    
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Imagem enviada com sucesso!',
                        'filename' => $newName,
                        'url' => base_url('writable/uploads/profiles/' . $newName)
                    ]);
                }
            } catch (\Exception $e) {
                log_activity(
                    session()->get('user_id'),
                    'users',
                    'upload_failed',
                    'Erro ao mover ficheiro: ' . $e->getMessage()
                );
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao fazer upload da imagem: ' . $e->getMessage()
                ]);
            }
        }

        // Log de erro no upload
        log_activity(
            session()->get('user_id'),
            'users',
            'upload_failed',
            'Erro ao enviar imagem de perfil. Ficheiro já foi movido ou erro no upload.'
        );
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao enviar imagem. Por favor, tente novamente.'
        ]);
    }

    /**
     * Obter estatísticas dos utilizadores
     */
    public function getStats()
    {
        if (!$this->request->isAJAX()) {
            log_activity(
                session()->get('user_id'), // quem tentou aceder
                'users',
                'view_stats_failed',
                'Tentou consultar estatísticas de utilizadores sem ser AJAX'
            );
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $stats = $this->userModel->getUserStats();
        
        // Log de consulta de estatísticas
        log_activity(
            session()->get('user_id'), // quem consultou
            'users',
            'view_stats',
            'Consultou estatísticas de utilizadores'
        );
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Pesquisar utilizadores
     */
    public function search()
    {
        if (!$this->request->isAJAX()) {
            log_activity(
                session()->get('user_id'), // quem tentou aceder
                'users',
                'search_failed',
                'Tentou pesquisar utilizadores sem ser AJAX'
            );
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $search = $this->request->getGet('q');
        
        if (empty($search)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Termo de pesquisa não fornecido'
            ]);
        }

        $users = $this->userModel->searchUsers($search);
        
        // Log de pesquisa
        $detalhes = [
            'search_term' => $search,
            'results_count' => count($users)
        ];
        log_activity(
            session()->get('user_id'), // quem pesquisou
            'users',
            'search',
            'Pesquisou utilizadores com o termo: ' . $search,
            null,
            null,
            $detalhes
        );
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Exportar utilizadores para CSV
     */
    public function exportCSV()
    {
        // Buscar TODOS os utilizadores (sem limite)
        $users = $this->userModel->orderBy('name', 'ASC')->findAll();
        
        // Log de exportação
        log_activity(
            session()->get('user_id'), // quem exportou
            'users',
            'export',
            'Exportou lista de utilizadores para CSV',
            null,
            null,
            ['exported_count' => count($users)]
        );
        
        $filename = 'utilizadores_' . date('Y-m-d_H-i-s') . '.csv';
        
        // Definir headers para download com UTF-8 BOM para Excel
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        
        // Adicionar BOM (Byte Order Mark) para UTF-8 - resolve problema de acentos no Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cabeçalhos
        fputcsv($output, ['ID', 'Nome', 'Email', 'Telefone', 'NIF', 'Grupo ID', 'Nível', 'Status', 'Data Criação'], ';');
        
        // Dados
        foreach ($users as $user) {
            $statusText = $user['status'] == 1 ? 'Ativo' : ($user['status'] == 2 ? 'Pendente' : 'Inativo');
            fputcsv($output, [
                $user['id'],
                $user['name'],
                $user['email'],
                $user['telefone'] ?? '',
                $user['NIF'] ?? '',
                $user['grupo_id'] ?? '',
                $user['level'],
                $statusText,
                date('d/m/Y H:i', strtotime($user['created_at']))
            ], ';');
        }
        
        fclose($output);
        exit;
    }


        public function getTechnicians()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->failUnauthorized("Acesso não autorizado");
            }

            $technicians = $this->userModel
                ->where("level >=", 5)
                ->orderBy('name', 'ASC')
                ->findAll();

            return $this->respond($technicians);
        } catch (\Exception $e) {
            log_message('error', 'Erro getTechnicians: ' . $e->getMessage());
            return $this->fail('Erro ao carregar técnicos: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Importar utilizadores a partir de ficheiro CSV
     */
    public function importar()
    {
        // Verificar nível de acesso
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel < 5) {
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
        $errorLines = [];
        $errorLines[] = "Name;Email;NIF;Telefone;grupo_id;Motivo_Erro"; // Cabeçalho

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

                // Verificar se tem pelo menos 5 colunas
                if (count($fields) < 5) {
                    $errors++;
                    $errorLines[] = $line . ";Número de colunas insuficiente";
                    continue;
                }

                $name = trim($fields[0]);
                $email = trim($fields[1]);
                $nif = trim($fields[2]);
                $telefone = trim($fields[3]);
                $grupo_id = trim($fields[4]);

                // VALIDAÇÃO OBRIGATÓRIA: NIF deve existir
                if (empty($nif)) {
                    $errors++;
                    $errorLines[] = $line . ";NIF obrigatório (campo vazio)";
                    continue;
                }

                // Validar email obrigatório
                if (empty($email)) {
                    $errors++;
                    $errorLines[] = $line . ";Email obrigatório (campo vazio)";
                    continue;
                }

                // Validar formato de email
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors++;
                    $errorLines[] = $line . ";Email inválido";
                    continue;
                }

                // Verificar se email já existe
                $existingUser = $this->userModel->getUserByEmail($email);
                if ($existingUser) {
                    if ($skipDuplicates) {
                        $skipped++;
                        continue;
                    } else {
                        $errors++;
                        $errorLines[] = $line . ";Email já existe";
                        continue;
                    }
                }

                // Preparar dados
                $userData = [
                    'name' => !empty($name) ? $name : null,
                    'email' => $email,
                    'NIF' => $nif,
                    'telefone' => !empty($telefone) ? $telefone : null,
                    'grupo_id' => !empty($grupo_id) && is_numeric($grupo_id) ? (int)$grupo_id : null,
                    'level' => 0, // Nível padrão para novos utilizadores
                    'status' => 2, // Status pendente - será ativado no primeiro login
                    'profile_img' => 'default.png'
                ];

                // Inserir
                if ($this->userModel->insert($userData)) {
                    $imported++;
                    
                    // Log de importação
                    log_activity(
                        session()->get('user_id'),
                        'users',
                        'import',
                        'Importou utilizador via CSV: ' . $email,
                        $this->userModel->getInsertID()
                    );
                } else {
                    $errors++;
                    $validationErrors = $this->userModel->errors();
                    $errorMsg = !empty($validationErrors) ? implode(', ', $validationErrors) : 'Erro ao inserir';
                    $errorLines[] = $line . ";" . $errorMsg;
                }
            }

            // Gerar ficheiro de erros se existirem e se solicitado
            $errorFile = null;
            if ($errors > 0 && $downloadErrors && count($errorLines) > 1) {
                $errorFileName = 'users_import_errors_' . date('YmdHis') . '.csv';
                $errorFilePath = FCPATH . 'upload/' . $errorFileName;
                
                // Criar diretório se não existir
                if (!is_dir(FCPATH . 'upload')) {
                    mkdir(FCPATH . 'upload', 0755, true);
                }
                
                file_put_contents($errorFilePath, implode("\n", $errorLines));
                $errorFile = base_url('upload/' . $errorFileName);
            }

            // Resposta de sucesso
            $message = "Importação concluída!";
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
            log_activity(
                session()->get('user_id'),
                'users',
                'import_failed',
                'Erro ao importar CSV: ' . $e->getMessage()
            );
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao processar ficheiro: ' . $e->getMessage()
            ]);
        }
    }

}

