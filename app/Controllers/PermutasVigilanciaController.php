<?php

namespace App\Controllers;

use App\Models\PermutasVigilanciaModel;
use App\Models\ConvocatoriaModel;
use App\Models\UserModel;
use App\Models\SessaoExameModel;

class PermutasVigilanciaController extends BaseController
{
    protected $permutasModel;
    protected $convocatoriaModel;
    protected $userModel;
    protected $sessaoExameModel;

    public function __construct()
    {
        $this->permutasModel = new PermutasVigilanciaModel();
        $this->convocatoriaModel = new ConvocatoriaModel();
        $this->userModel = new UserModel();
        $this->sessaoExameModel = new SessaoExameModel();
    }

    public function index()
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        if (!$userId) return redirect()->to('/login');

        $data = ['title' => 'Minhas Permutas', 'permutas' => $this->permutasModel->getPermutasProfessor($userId)];
        return view('permutas_vigilancia/index', $data);
    }

    public function pendentesValidacao()
    {
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel < 4 || $userLevel >= 9) return redirect()->back()->with('error', 'Sem permissões');

        $data = ['title' => 'Permutas Pendentes Validação', 'permutas' => $this->permutasModel->getPermutasPendentesSecretariado()];
        return view('permutas_vigilancia/pendentes_validacao', $data);
    }

    public function criar()
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        $convocatoriaId = $this->request->getPost('convocatoria_id');
        $substitutoId = $this->request->getPost('user_substituto_id');
        $motivo = $this->request->getPost('motivo');

        $convocatoria = $this->convocatoriaModel->find($convocatoriaId);
        
        if (!$convocatoria) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Convocatória não encontrada']);
        }
        
        if ($convocatoria['user_id'] != $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Convocatória inválida - não pertence ao utilizador']);
        }

        // Buscar informações da sessão de exame para validar prazo
        $sessaoExame = $this->sessaoExameModel->find($convocatoria['sessao_exame_id']);
        if ($sessaoExame) {
            $dataHoraExame = new \DateTime($sessaoExame['data_exame'] . ' ' . $sessaoExame['hora_exame']);
            $agora = new \DateTime();
            $diferencaHoras = ($dataHoraExame->getTimestamp() - $agora->getTimestamp()) / 3600;
            
            if ($diferencaHoras < 24) {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'message' => 'Pedidos de permuta só podem ser solicitados até 24 horas antes do início da prova'
                ]);
            }
        }

        if ($this->permutasModel->existePermutaAtiva($convocatoriaId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Já existe uma permuta ativa para esta convocatória']);
        }

        if ($this->permutasModel->substitutoJaConvocado($substitutoId, $convocatoria['sessao_exame_id'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Substituto já está convocado para este exame']);
        }

        $data = [
            'convocatoria_id' => $convocatoriaId,
            'user_original_id' => $userId,
            'user_substituto_id' => $substitutoId,
            'motivo' => $motivo,
            'estado' => 'PENDENTE'
        ];

        if ($this->permutasModel->insert($data)) {
            $permutaId = $this->permutasModel->getInsertID();
            $this->enviarEmailPedidoPermuta($permutaId);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Pedido de permuta criado com sucesso']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao criar pedido']);
    }

    public function responder($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        $aceitar = $this->request->getPost('aceitar') == '1';

        $permuta = $this->permutasModel->getPermutaCompleta($id);
        if (!$permuta || $permuta['user_substituto_id'] != $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permuta inválida']);
        }

        // VALIDAÇÕES: Apenas se aceitar
        if ($aceitar) {
            // VALIDAÇÃO 1: Verificar se já existe outra permuta aceite para a mesma convocatória
            $permutaJaAceite = $this->permutasModel
                ->where('convocatoria_id', $permuta['convocatoria_id'])
                ->where('id !=', $id)
                ->whereIn('estado', ['ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO'])
                ->first();

            if ($permutaJaAceite) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Já existe outra permuta aceite para esta convocatória'
                ]);
            }

            // VALIDAÇÃO 2: Verificar se o substituto já tem outra permuta aceite para o mesmo dia e hora
            $dataExame = $permuta['data_exame'];
            $horaExame = $permuta['hora_exame'];
            
            $conflitoHorario = $this->permutasModel->select('permutas_vigilancia.id')
                ->join('convocatoria c', 'c.id = permutas_vigilancia.convocatoria_id')
                ->join('sessao_exame se', 'se.id = c.sessao_exame_id')
                ->where('permutas_vigilancia.user_substituto_id', $userId)
                ->where('permutas_vigilancia.id !=', $id)
                ->whereIn('permutas_vigilancia.estado', ['ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO'])
                ->where('se.data_exame', $dataExame)
                ->where('se.hora_exame', $horaExame)
                ->first();

            if ($conflitoHorario) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Já aceitou outra permuta para o mesmo dia e hora'
                ]);
            }
        }

        $this->permutasModel->update($id, [
            'substituto_aceitou' => $aceitar ? 1 : 0,
            'data_resposta_substituto' => date('Y-m-d H:i:s'),
            'estado' => $aceitar ? 'ACEITE_SUBSTITUTO' : 'RECUSADO_SUBSTITUTO'
        ]);

        // Se aceitou, cancelar automaticamente outras permutas pendentes
        if ($aceitar) {
            $this->permutasModel
                ->where('convocatoria_id', $permuta['convocatoria_id'])
                ->where('id !=', $id)
                ->where('estado', 'PENDENTE')
                ->set(['estado' => 'CANCELADO'])
                ->update();
        }

        $this->enviarEmailRespostaSubstituto($id, $aceitar);

        return $this->response->setJSON(['status' => 'success', 'message' => $aceitar ? 'Permuta aceite' : 'Permuta recusada']);
    }

    public function aceitar($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;

        $permuta = $this->permutasModel->getPermutaCompleta($id);
        if (!$permuta || $permuta['user_substituto_id'] != $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permuta inválida']);
        }

        // VALIDAÇÃO 1: Verificar se já existe outra permuta aceite para a mesma convocatória
        $permutaJaAceite = $this->permutasModel
            ->where('convocatoria_id', $permuta['convocatoria_id'])
            ->where('id !=', $id)
            ->whereIn('estado', ['ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO'])
            ->first();

        if ($permutaJaAceite) {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'Já existe outra permuta aceite para esta convocatória'
            ]);
        }

        // VALIDAÇÃO 2: Verificar se o substituto já tem outra permuta aceite para o mesmo dia e hora
        $dataExame = $permuta['data_exame'];
        $horaExame = $permuta['hora_exame'];
        
        $conflitoHorario = $this->permutasModel->select('permutas_vigilancia.id')
            ->join('convocatoria c', 'c.id = permutas_vigilancia.convocatoria_id')
            ->join('sessao_exame se', 'se.id = c.sessao_exame_id')
            ->where('permutas_vigilancia.user_substituto_id', $userId)
            ->where('permutas_vigilancia.id !=', $id)
            ->whereIn('permutas_vigilancia.estado', ['ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO'])
            ->where('se.data_exame', $dataExame)
            ->where('se.hora_exame', $horaExame)
            ->first();

        if ($conflitoHorario) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Já aceitou outra permuta para o mesmo dia e hora'
            ]);
        }

        $this->permutasModel->update($id, [
            'substituto_aceitou' => 1,
            'data_resposta_substituto' => date('Y-m-d H:i:s'),
            'estado' => 'ACEITE_SUBSTITUTO'
        ]);

        // Cancelar automaticamente outras permutas pendentes para a mesma convocatória
        $this->permutasModel
            ->where('convocatoria_id', $permuta['convocatoria_id'])
            ->where('id !=', $id)
            ->where('estado', 'PENDENTE')
            ->set(['estado' => 'CANCELADO'])
            ->update();

        $this->enviarEmailRespostaSubstituto($id, true);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Permuta aceite com sucesso']);
    }

    public function recusar($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;

        $permuta = $this->permutasModel->getPermutaCompleta($id);
        if (!$permuta || $permuta['user_substituto_id'] != $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permuta inválida']);
        }

        $this->permutasModel->update($id, [
            'substituto_aceitou' => 0,
            'data_resposta_substituto' => date('Y-m-d H:i:s'),
            'estado' => 'RECUSADO_SUBSTITUTO'
        ]);

        $this->enviarEmailRespostaSubstituto($id, false);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Permuta recusada']);
    }

    public function validar($id)
    {
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        
        // Verificar permissões - apenas secretariado (níveis 4, 8, 9)
        if (!in_array($userLevel, [4, 8, 9])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sem permissões para validar permutas']);
        }

        $observacoes = $this->request->getPost('observacoes');
        $convocatoriaId = $this->request->getPost('convocatoria_id');
        
        // Buscar a permuta
        $permuta = $this->permutasModel->find($id);
        if (!$permuta) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permuta não encontrada']);
        }
        
        // Verificar se a permuta está no estado correto
        if ($permuta['estado'] !== 'ACEITE_SUBSTITUTO') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permuta não está no estado correto para validação']);
        }

        // Atualizar a permuta
        $updateData = [
            'validado_secretariado' => 1,
            'data_validacao_secretariado' => date('Y-m-d H:i:s'),
            'validado_por' => $userId,
            'observacoes_validacao' => $observacoes,
            'estado' => 'VALIDADO_SECRETARIADO'
        ];
        
        if (!$this->permutasModel->update($id, $updateData)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao atualizar permuta']);
        }

        // Atualizar a convocatória com o novo vigilante (professor substituto)
        $convocatoriaUpdateData = [
            'user_id' => $permuta['user_substituto_id']
        ];
        
        if (!$this->convocatoriaModel->update($permuta['convocatoria_id'], $convocatoriaUpdateData)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao atualizar convocatória']);
        }

        // Enviar emails de notificação
        $this->enviarEmailValidacaoSecretariado($id, true);

        return $this->response->setJSON([
            'status' => 'success', 
            'message' => 'Permuta validada com sucesso! A convocatória foi atualizada com o novo vigilante.'
        ]);
    }

    public function cancelar($id)
    {
        $userId = session()->get('LoggedUserData')['id'] ?? null;
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        $permuta = $this->permutasModel->find($id);

        if (!$permuta) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permuta não encontrada']);
        }

        // Permitir cancelamento se for o professor original OU se for secretariado (level >= 4 e <= 9)
        $podeAnular = ($permuta['user_original_id'] == $userId) || ($userLevel >= 4 && $userLevel <= 9);
        
        if (!$podeAnular) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Não tem permissão para anular esta permuta']);
        }

        if (!in_array($permuta['estado'], ['PENDENTE', 'RECUSADO_SUBSTITUTO', 'REJEITADO_SECRETARIADO', 'ACEITE_SUBSTITUTO'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Esta permuta não pode ser anulada (estado: ' . $permuta['estado'] . ')']);
        }

        $this->permutasModel->update($id, ['estado' => 'CANCELADO']);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Permuta anulada com sucesso']);
    }

    private function aplicarPermuta($permutaId)
    {
        $permuta = $this->permutasModel->find($permutaId);
        $this->convocatoriaModel->update($permuta['convocatoria_id'], ['user_id' => $permuta['user_substituto_id']]);
    }

    private function enviarEmailPedidoPermuta($permutaId)
    {
        $permuta = $this->permutasModel->getPermutaCompleta($permutaId);
        $email = \Config\Services::email();
        $email->setTo($permuta['email_substituto']);
        $email->setFrom(getenv('email.fromEmail'), getenv('email.fromName'));
        $email->setSubject('Pedido de Permuta de Vigilância');
        $email->setMessage(view('emails/permuta_pedido', ['permuta' => $permuta]));
        $email->send();
    }

    private function enviarEmailRespostaSubstituto($permutaId, $aceite)
    {
        $permuta = $this->permutasModel->getPermutaCompleta($permutaId);
        $email = \Config\Services::email();
        $email->setTo($permuta['email_original']);
        $email->setFrom(getenv('email.fromEmail'), getenv('email.fromName'));
        $email->setSubject('Resposta à Permuta de Vigilância');
        $email->setMessage(view('emails/permuta_resposta', ['permuta' => $permuta, 'aceite' => $aceite]));
        $email->send();
    }

    private function enviarEmailParaSecretariado($permutaId)
    {
        $permuta = $this->permutasModel->getPermutaCompleta($permutaId);
        
        // Enviar apenas para o Secretariado de Exames (níveis 4, 8 e 9)
        $secretariado = $this->userModel->whereIn('level', [4, 8, 9])->findAll();
        
        if (empty($secretariado)) {
            return; // Nenhum membro do secretariado encontrado
        }
        
        $email = \Config\Services::email();
        foreach ($secretariado as $user) {
            $email->setTo($user['email']);
            $email->setFrom(getenv('email.fromEmail'), getenv('email.fromName'));
            $email->setSubject('Permuta Pendente Validação');
            $email->setMessage(view('emails/permuta_secretariado', ['permuta' => $permuta]));
            $email->send();
        }
    }

    private function enviarEmailValidacaoSecretariado($permutaId, $aprovado)
    {
        $permuta = $this->permutasModel->getPermutaCompleta($permutaId);
        $email = \Config\Services::email();
        foreach ([$permuta['email_original'], $permuta['email_substituto']] as $destinatario) {
            $email->setTo($destinatario);
            $email->setFrom(getenv('email.fromEmail'), getenv('email.fromName'));
            $email->setSubject($aprovado ? 'Permuta Aprovada' : 'Permuta Rejeitada');
            $email->setMessage(view('emails/permuta_validacao', ['permuta' => $permuta, 'aprovado' => $aprovado, 'destinatario' => $destinatario]));
            $email->send();
        }
    }

    public function getProfessoresDisponiveis($sessaoExameId)
    {
        $professores = $this->userModel->where('level <=', 9)->findAll();
        $convocados = $this->convocatoriaModel->where('sessao_exame_id', $sessaoExameId)->findAll();
        $convocadosIds = array_column($convocados, 'user_id');
        
        $disponiveis = array_filter($professores, function($p) use ($convocadosIds) {
            return !in_array($p['id'], $convocadosIds);
        });

        return $this->response->setJSON(['status' => 'success', 'professores' => array_values($disponiveis)]);
    }
}
