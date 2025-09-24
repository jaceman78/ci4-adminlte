<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
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
        // Log de acesso à página
        log_user_activity('view_page', 'Acedeu à página de gestão de utilizadores');

        $data = [
            'title' => 'Gestão de Utilizadores',
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => base_url()],
                ['name' => 'Utilizadores', 'url' => '']
            ]
        ];

        return view('users/user_index', $data);
    }

    /**
     * Obter dados para DataTable via AJAX
     */
    public function getDataTable()
    {
        if (!$this->request->isAJAX()) {
            // Log de tentativa de acesso não autorizado
            log_permission_denied('users/getDataTable', 'non_ajax_request');
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $request = $this->request->getPost();
        
        $start = $request['start'] ?? 0;
        $length = $request['length'] ?? 10;
        $search = $request['search']['value'] ?? '';
        
        // Configurar ordenação
        $orderColumn = 'id';
        $orderDir = 'asc';
        
        if (isset($request['order'][0])) {
            $columns = ['id', 'name', 'email', 'NIF', 'level', 'status', 'created_at'];
            $orderColumnIndex = $request['order'][0]['column'];
            $orderColumn = $columns[$orderColumnIndex] ?? 'id';
            $orderDir = $request['order'][0]['dir'] ?? 'asc';
        }

        $result = $this->userModel->getDataTableData($start, $length, $search, $orderColumn, $orderDir);
        
        // Log da consulta de dados
        $detalhes = [
            'search' => $search,
            'order_column' => $orderColumn,
            'order_dir' => $orderDir,
            'records_found' => $result['recordsFiltered']
        ];
        log_user_activity('datatable_query', 'Consultou lista de utilizadores via DataTable', null, null, null);

        // Formatar dados para DataTable
        $data = [];
        foreach ($result['data'] as $user) {
            $statusBadge = $user['status'] == 1 
                ? '<span class="badge bg-success">Ativo</span>' 
                : '<span class="badge bg-danger">Inativo</span>';
            
            $profileImg = '';
            if ($user['profile_img'] && str_starts_with($user['profile_img'], 'http' )) {
                $profileImg = '<img src="' . $user['profile_img'] . '" class="img-circle" width="30" height="30">';
            } else if ($user['profile_img'] && $user['profile_img'] !== 'default.png') {
                $profileImg = '<img src="' . base_url('uploads/profiles/' . $user['profile_img']) . '" class="img-circle" width="30" height="30">';
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
            log_permission_denied('users/getUser', 'non_ajax_request');
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        $user = $this->userModel->find($id);
        
        if (!$user) {
            // Log de tentativa de acesso a utilizador inexistente
            log_user_activity('view_failed', "Tentou visualizar utilizador inexistente (ID: {$id})", $id);
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Utilizador não encontrado']);
        }

        // Log de visualização de utilizador
        log_user_activity('view', "Visualizou dados do utilizador {$user['name']} ({$user['email']})", $id);

        // Sanitizar dados sensíveis antes de enviar
        $userSanitized = sanitize_log_data($user);

        return $this->response->setJSON(['success' => true, 'data' => $user]);
    }

    /**
     * Criar novo utilizador
     */
    public function create()
    {
        if (!$this->request->isAJAX()) {
            log_permission_denied('users/create', 'non_ajax_request');
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $data = $this->request->getPost();
        
        // Sanitizar dados para log
        $dataSanitized = sanitize_log_data($data);
        
        // Validar dados
        $validation = $this->userModel->validateUserData($data);
        
        if (!$validation['success']) {
            // Log de tentativa de criação com dados inválidos
            log_user_activity('create_failed', 'Tentou criar utilizador com dados inválidos', null, null, $dataSanitized);
            
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
            'NIF' => $data['NIF'] ?? null,
            'profile_img' => $data['profile_img'] ?? 'default.png',
            'grupo_id' => $data['grupo_id'] ?? null,
            'level' => $data['level'] ?? 0,
            'status' => $data['status'] ?? 1
        ];

        $userId = $this->userModel->insert($userData);
            
        if ($userId) {
            // Log de criação bem-sucedida
            $userDataSanitized = sanitize_log_data($userData);
            log_user_activity('create', "Criou novo utilizador: {$userData['name']} ({$userData['email']})", $userId, null, $userDataSanitized);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Utilizador criado com sucesso!',
                'data' => ['id' => $userId]
            ]);
        } else {
            // Log de erro na criação
            log_user_activity('create_failed', 'Erro ao criar utilizador na base de dados', null, null, $dataSanitized);
            
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
            log_permission_denied('users/update', 'non_ajax_request');
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        // Verificar se utilizador existe e obter dados anteriores
        $existingUser = $this->userModel->find($id);
        if (!$existingUser) {
            log_user_activity('update_failed', "Tentou atualizar utilizador inexistente (ID: {$id})", $id);
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Utilizador não encontrado']);
        }

        $data = $this->request->getPost();
        $data["id"] = $id; // Esta linha é crucial para a validação
        
        // Sanitizar dados para log
        $dataSanitized = sanitize_log_data($data);
        $existingUserSanitized = sanitize_log_data($existingUser);
        
        // Validar dados
        $validation = $this->userModel->validateUserData($data, $id);
        
        if (!$validation['success']) {
            // Log de tentativa de atualização com dados inválidos
            log_user_activity('update_failed', "Tentou atualizar utilizador {$existingUser['name']} com dados inválidos", $id, $existingUserSanitized, $dataSanitized);
            
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
            $userDataSanitized = sanitize_log_data($userData);
            log_user_activity('update', "Atualizou utilizador: {$userData['name']} ({$userData['email']})", $id, $existingUserSanitized, $userDataSanitized);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Utilizador atualizado com sucesso!'
            ]);
        } else {
            // Log de erro na atualização
            log_user_activity('update_failed', "Erro ao atualizar utilizador {$existingUser['name']} na base de dados", $id, $existingUserSanitized, $dataSanitized);
            
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
            log_permission_denied('users/delete', 'non_ajax_request');
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        // Verificar se utilizador existe e obter dados antes da eliminação
        $user = $this->userModel->find($id);
        if (!$user) {
            log_user_activity('delete_failed', "Tentou eliminar utilizador inexistente (ID: {$id})", $id);
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Utilizador não encontrado']);
        }

        // Soft delete (atualizar status para 0)
        $result = $this->userModel->softDelete($id);
        
        if ($result) {
            // Log de eliminação bem-sucedida
            $userSanitized = sanitize_log_data($user);
            log_user_activity('delete', "Eliminou utilizador: {$user['name']} ({$user['email']})", $id, $userSanitized);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Utilizador eliminado com sucesso!'
            ]);
        } else {
            // Log de erro na eliminação
            log_user_activity('delete_failed', "Erro ao eliminar utilizador {$user['name']} da base de dados", $id);
            
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
            log_permission_denied('users/restore', 'non_ajax_request');
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
            log_user_activity('restore', "Restaurou utilizador: {$userName} ({$userEmail})", $id);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Utilizador restaurado com sucesso!'
            ]);
        } else {
            // Log de erro na restauração
            log_user_activity('restore_failed', "Erro ao restaurar utilizador (ID: {$id})", $id);
            
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
            log_permission_denied('users/updateStatus', 'non_ajax_request');
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
            log_user_activity('update_status_failed', "Tentou alterar status de utilizador inexistente (ID: {$id})", $id);
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Utilizador não encontrado']);
        }

        $result = $this->userModel->updateUserStatus($id, $status);
        
        if ($result) {
            // Log de alteração de status bem-sucedida
            $statusText = $status == 1 ? 'ativado' : 'desativado';
            $dadosAnteriores = ['status' => $user['status']];
            $dadosNovos = ['status' => $status];
            
            log_user_activity('update_status', "Alterou status do utilizador {$user['name']} para {$statusText}", $id, $dadosAnteriores, $dadosNovos);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Utilizador {$statusText} com sucesso!"
            ]);
        } else {
            // Log de erro na alteração de status
            log_user_activity('update_status_failed', "Erro ao alterar status do utilizador {$user['name']}", $id);
            
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
            log_permission_denied('users/uploadProfileImage', 'non_ajax_request');
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $file = $this->request->getFile('profile_image');
        
        if (!$file || !$file->isValid()) {
            log_user_activity('upload_failed', 'Tentou fazer upload de imagem de perfil inválida');
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nenhum ficheiro válido foi enviado'
            ]);
        }

        // Validar tipo de ficheiro
        if (!$file->hasMoved() && in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif'])) {
            $newName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/profiles/';
            
            // Criar diretório se não existir
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            if ($file->move($uploadPath, $newName)) {
                // Log de upload bem-sucedido
                $detalhes = [
                    'original_name' => $file->getClientName(),
                    'new_name' => $newName,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
                
                log_file_upload($file->getClientName(), $uploadPath . $newName, 'users');
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Imagem enviada com sucesso!',
                    'filename' => $newName,
                    'url' => base_url('uploads/profiles/' . $newName)
                ]);
            }
        }

        // Log de erro no upload
        log_user_activity('upload_failed', 'Erro ao fazer upload de imagem de perfil - tipo de ficheiro não permitido');

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao enviar imagem. Apenas ficheiros JPEG, PNG e GIF são permitidos.'
        ]);
    }

    /**
     * Obter estatísticas dos utilizadores
     */
    public function getStats()
    {
        if (!$this->request->isAJAX()) {
            log_permission_denied('users/getStats', 'non_ajax_request');
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso negado']);
        }

        $stats = $this->userModel->getUserStats();
        
        // Log de consulta de estatísticas
        log_user_activity('view_stats', 'Consultou estatísticas de utilizadores');
        
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
            log_permission_denied('users/search', 'non_ajax_request');
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
        log_user_activity('search', "Pesquisou utilizadores com termo: {$search}", null, null, null);
        
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
        $users = $this->userModel->getAllUsers();
        
        // Log de exportação
        log_export_activity('users', 'CSV', count($users));
        
        $filename = 'utilizadores_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        
        // Cabeçalhos
        fputcsv($output, ['ID', 'Nome', 'Email', 'NIF', 'Grupo ID', 'Nível', 'Status', 'Data Criação']);
        
        // Dados
        foreach ($users as $user) {
            fputcsv($output, [
                $user['id'],
                $user['name'],
                $user['email'],
                $user['NIF'],
                $user['grupo_id'],
                $user['level'],
                $user['status'] == 1 ? 'Ativo' : 'Inativo',
                $user['created_at']
            ]);
        }
        
        fclose($output);
        exit;
    }
}helper("LogHelper");

