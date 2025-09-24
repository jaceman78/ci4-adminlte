<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogsAtividadeTable extends Migration
{
    public function up()
    {
        /**
         * LOGS DE ATIVIDADE - Versão Melhorada
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, // Permitir NULL para ações do sistema
            ],
            'modulo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'Módulo da aplicação (users, escolas, salas, auth, etc.)',
            ],
            'acao' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'comment'    => 'Ação realizada (create, update, delete, login, logout, etc.)',
            ],
            'registro_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'ID do registo afetado (se aplicável)',
            ],
            'descricao' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => false,
                'comment'    => 'Descrição legível da ação realizada',
            ],
            'dados_anteriores' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Dados antes da alteração (formato JSON)',
            ],
            'dados_novos' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Dados após a alteração (formato JSON)',
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45, // Suporta IPv6
                'null'       => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Informações do navegador/cliente',
            ],
            'detalhes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Detalhes adicionais da ação (formato JSON)',
            ],
            'criado_em' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
        ]);

        // Chaves
        $this->forge->addKey('id', true); // Chave primária
        
        // Índices para melhor performance
        $this->forge->addKey('user_id');
        $this->forge->addKey('modulo');
        $this->forge->addKey('acao');
        $this->forge->addKey('registro_id');
        $this->forge->addKey('criado_em');
        $this->forge->addKey(['modulo', 'acao']); // Índice composto
        $this->forge->addKey(['user_id', 'criado_em']); // Índice composto

        // Foreign key
        $this->forge->addForeignKey('user_id', 'user', 'id', 'CASCADE', 'SET NULL');

        $this->forge->createTable('logs_atividade');
    }

    public function down()
    {
        $this->forge->dropTable('logs_atividade');
    }
}



