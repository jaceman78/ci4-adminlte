<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEstadosTicketTable extends Migration
{
    public function up()
    {
        // Criar tabela estados_ticket
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'codigo' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
                'comment'    => 'Código único do estado (ex: novo, em_resolucao)',
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'comment'    => 'Nome amigável do estado (ex: Novo, Em Resolução)',
            ],
            'descricao' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Descrição detalhada do estado',
            ],
            'cor' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'secondary',
                'comment'    => 'Cor do badge Bootstrap (primary, success, danger, warning, info, secondary)',
            ],
            'icone' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'comment'    => 'Classe do ícone FontAwesome (ex: fas fa-clock)',
            ],
            'ordem' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Ordem de exibição/prioridade do estado',
            ],
            'ativo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1 = Ativo, 0 = Inativo',
            ],
            'permite_edicao' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => '1 = Permite edição do ticket, 0 = Não permite',
            ],
            'permite_atribuicao' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1 = Permite atribuição de técnico, 0 = Não permite',
            ],
            'estado_final' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => '1 = Estado final (não pode ser alterado), 0 = Estado intermediário',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('ordem');
        $this->forge->createTable('estados_ticket');

        // Criar tabela de transições permitidas (workflow)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'estado_origem_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'ID do estado de origem',
            ],
            'estado_destino_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'ID do estado de destino',
            ],
            'nivel_minimo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Nível mínimo de utilizador necessário para esta transição',
            ],
            'requer_comentario' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => '1 = Requer comentário obrigatório, 0 = Não requer',
            ],
            'ativo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1 = Transição ativa, 0 = Transição desativada',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['estado_origem_id', 'estado_destino_id']);
        $this->forge->addForeignKey('estado_origem_id', 'estados_ticket', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('estado_destino_id', 'estados_ticket', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('estados_ticket_transicoes');

        // Modificar tabela tickets para adicionar foreign key
        // Primeiro, precisamos garantir que todos os estados atuais são válidos
        $this->db->query("
            UPDATE tickets 
            SET estado = 'novo' 
            WHERE estado NOT IN ('novo', 'em_resolucao', 'aguarda_peca', 'reparado', 'anulado')
        ");
    }

    public function down()
    {
        // Remover tabelas
        $this->forge->dropTable('estados_ticket_transicoes', true);
        $this->forge->dropTable('estados_ticket', true);
    }
}
