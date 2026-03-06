<?php

namespace App\Models;

use CodeIgniter\Model;

class PermutasVigilanciaModel extends Model
{
    protected $table = 'permutas_vigilancia';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'convocatoria_id', 'user_original_id', 'user_substituto_id', 'estado',
        'substituto_aceitou', 'data_resposta_substituto', 'validado_secretariado',
        'data_validacao_secretariado', 'validado_por', 'observacoes_validacao', 'motivo', 'observacoes'
    ];

    protected $validationRules = [
        'convocatoria_id' => 'required|integer',
        'user_original_id' => 'required|integer',
        'user_substituto_id' => 'required|integer',
        'motivo' => 'required|min_length[10]'
    ];

    public function getPermutaCompleta($id)
    {
        return $this->select('permutas_vigilancia.*, 
            u_orig.name as nome_original, u_orig.email as email_original,
            u_subst.name as nome_substituto, u_subst.email as email_substituto,
            u_valid.name as nome_validador,
            conv.funcao, conv.sessao_exame_id, conv.sessao_exame_sala_id,
            ex.codigo_prova, ex.nome_prova, se.data_exame, se.hora_exame, se.fase,
            sala.codigo_sala')
            ->join('convocatoria conv', 'conv.id = permutas_vigilancia.convocatoria_id')
            ->join('user u_orig', 'u_orig.id = permutas_vigilancia.user_original_id')
            ->join('user u_subst', 'u_subst.id = permutas_vigilancia.user_substituto_id')
            ->join('user u_valid', 'u_valid.id = permutas_vigilancia.validado_por', 'left')
            ->join('sessao_exame se', 'se.id = conv.sessao_exame_id')
            ->join('exame ex', 'ex.id = se.exame_id')
            ->join('sessao_exame_sala ses', 'ses.id = conv.sessao_exame_sala_id')
            ->join('salas sala', 'sala.id = ses.sala_id')
            ->where('permutas_vigilancia.id', $id)
            ->first();
    }

    public function getPermutasPendentesSubstituto($userId)
    {
        return $this->select('permutas_vigilancia.*, u_orig.name as nome_original,
            ex.codigo_prova, se.data_exame, se.hora_exame, sala.codigo_sala')
            ->join('user u_orig', 'u_orig.id = permutas_vigilancia.user_original_id')
            ->join('convocatoria conv', 'conv.id = permutas_vigilancia.convocatoria_id')
            ->join('sessao_exame se', 'se.id = conv.sessao_exame_id')
            ->join('exame ex', 'ex.id = se.exame_id')
            ->join('sessao_exame_sala ses', 'ses.id = conv.sessao_exame_sala_id')
            ->join('salas sala', 'sala.id = ses.sala_id')
            ->where('permutas_vigilancia.user_substituto_id', $userId)
            ->where('permutas_vigilancia.estado', 'PENDENTE')
            ->findAll();
    }

    public function getPermutasProfessor($userId)
    {
        return $this->select('permutas_vigilancia.*,
            u_orig.name as nome_original, u_subst.name as nome_substituto,
            ex.codigo_prova, se.data_exame, sala.codigo_sala')
            ->join('user u_orig', 'u_orig.id = permutas_vigilancia.user_original_id')
            ->join('user u_subst', 'u_subst.id = permutas_vigilancia.user_substituto_id')
            ->join('convocatoria conv', 'conv.id = permutas_vigilancia.convocatoria_id')
            ->join('sessao_exame se', 'se.id = conv.sessao_exame_id')
            ->join('exame ex', 'ex.id = se.exame_id')
            ->join('sessao_exame_sala ses', 'ses.id = conv.sessao_exame_sala_id')
            ->join('salas sala', 'sala.id = ses.sala_id')
            ->groupStart()
                ->where('permutas_vigilancia.user_original_id', $userId)
                ->orWhere('permutas_vigilancia.user_substituto_id', $userId)
            ->groupEnd()
            ->orderBy('permutas_vigilancia.criado_em', 'DESC')
            ->findAll();
    }

    public function getPermutasPendentesSecretariado()
    {
        return $this->select('permutas_vigilancia.*,
            u_orig.name as nome_original, u_subst.name as nome_substituto,
            ex.codigo_prova, se.data_exame, sala.codigo_sala')
            ->join('user u_orig', 'u_orig.id = permutas_vigilancia.user_original_id')
            ->join('user u_subst', 'u_subst.id = permutas_vigilancia.user_substituto_id')
            ->join('convocatoria conv', 'conv.id = permutas_vigilancia.convocatoria_id')
            ->join('sessao_exame se', 'se.id = conv.sessao_exame_id')
            ->join('exame ex', 'ex.id = se.exame_id')
            ->join('sessao_exame_sala ses', 'ses.id = conv.sessao_exame_sala_id')
            ->join('salas sala', 'sala.id = ses.sala_id')
            ->where('permutas_vigilancia.estado', 'ACEITE_SUBSTITUTO')
            ->orderBy('permutas_vigilancia.criado_em', 'ASC')
            ->findAll();
    }

    public function existePermutaAtiva($convocatoriaId)
    {
        return $this->where('convocatoria_id', $convocatoriaId)
            ->whereIn('estado', ['PENDENTE', 'ACEITE_SUBSTITUTO'])
            ->countAllResults() > 0;
    }

    public function substitutoJaConvocado($userId, $sessaoExameId)
    {
        $db = \Config\Database::connect();
        return $db->table('convocatoria')
            ->where('user_id', $userId)
            ->where('sessao_exame_id', $sessaoExameId)
            ->countAllResults() > 0;
    }
}
