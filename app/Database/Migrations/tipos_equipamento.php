<?php 

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDatabaseTables extends Migration
{
    public function up()
    {
        /**
         * USERS
         */
    $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'oauth_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'NIF' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'profile_img' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => false,
                'default'    => 'default.png',
            ],
            'grupo_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'level' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                //'default' => 'CURRENT_TIMESTAMP',
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
               // 'default' => null,
                'on_update' => 'CURRENT_TIMESTAMP', // Atualiza automaticamente
            ],
        ]);

        $this->forge->addKey('id', true); // Chave primária
        $this->forge->createTable('user');

        /**
         * ESCOLAS
         */
        $this->forge->addField([
            'id'    => ['type' => 'INT', 'auto_increment' => true],
            'nome'  => ['type' => 'VARCHAR', 'constraint' => 150],
            'morada'=> ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('escolas');

        /**
         * SALAS
         */
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'auto_increment' => true],
            'escola_id'  => ['type' => 'INT'],
            'codigo_sala'=> ['type' => 'VARCHAR', 'constraint' => 50],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('escola_id', 'escolas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('salas');

        /**
         * TIPOS DE EQUIPAMENTO
         */
        $this->forge->addField([
    'id'        => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
    'nome'      => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true, 'null' => false],
    'descricao' => ['type' => 'TEXT', 'null' => true],
    'created_at'=> ['type' => 'DATETIME', 'null' => true],
    'updated_at'=> ['type' => 'DATETIME', 'null' => true],
    ]);

        // 2. Definir a Chave Primária
        $this->forge->addKey('id', true); // O segundo parâmetro 'true' define como chave primária

        // 3. Criar a Tabela
        $this->forge->createTable('tipos_equipamento');

        /**
         * EQUIPAMENTOS
         */
        $this->forge->addField([
        'id'             => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
        'sala_id'        => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'null' => true],
        'tipo_id'        => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'null' => false],
        'marca'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'modelo'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'numero_serie'   => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true, 'null' => true],
        'estado'         => ['type' => 'ENUM', 'constraint' => ['ativo', 'inativo', 'pendente'], 'default' => 'ativo', 'null' => false],
        'data_aquisicao' => ['type' => 'DATE', 'null' => true],
        'observacoes'    => ['type' => 'TEXT', 'null' => true],
        'created_at'     => ['type' => 'DATETIME', 'null' => true],
        'updated_at'     => ['type' => 'DATETIME', 'null' => true],

        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sala_id', 'salas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tipo_id', 'tipos_equipamento', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('equipamentos');

        /**
         * TIPOS AVARIA
         */
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'auto_increment' => true],
            'descricao' => ['type' => 'VARCHAR', 'constraint' => 150],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tipos_avaria');

        /**
         * TICKETS
         */
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'auto_increment' => true],
            'equipamento_id'    => ['type' => 'INT', 'null' => false],
            'sala_id'           => ['type' => 'INT', 'null' => false],
            'tipo_avaria_id'    => ['type' => 'INT', 'null' => false],
            'user_id'           => ['type' => 'INT', 'null' => false],
            'atribuido_user_id' => ['type' => 'INT', 'null' => true],
            'ticket_aceite'     => ['type' => 'BOOLEAN', 'default' => false],
            'descricao'         => ['type' => 'TEXT', 'null' => false],
            'estado'            => [
                'type'       => 'ENUM',
                'constraint' => ['novo','em_resolucao','aguarda_peca','reparado','anulado'],
                'default'    => 'novo',
                'null'       => false
            ],
            'prioridade'        => [
                'type'       => 'ENUM',
                'constraint' => ['baixa','media','alta','critica'],
                'default'    => 'media',
                'null'       => false
            ],
            'created_at'        => ['type' => 'DATETIME', 'null' => false, 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at'        => ['type' => 'DATETIME', 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'on_update' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('equipamento_id', 'equipamentos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('sala_id', 'salas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tipo_avaria_id', 'tipos_avaria', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('atribuido_user_id', 'user', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('tickets');

        /**
         * REGISTOS DE REPARAÇÃO
         */
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'auto_increment' => true],
            'ticket_id'      => ['type' => 'INT'],
            'user_id'     => ['type' => 'INT'],
            'descricao'      => ['type' => 'TEXT', 'null' => true],
            'tempo_gasto_min'=> ['type' => 'INT', 'null' => true],
            'criado_em'      => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('ticket_id', 'tickets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'user', 'id', 'CASCADE', 'CASCADE');    // Corrigido!
        $this->forge->createTable('registos_reparacao');

        /**
         * MATERIAIS
         */
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'nome'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'referencia'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'stock_atual' => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('materiais');

        /**
         * MATERIAIS SUBSTITUIDOS
         */
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'auto_increment' => true],
            'registo_id' => ['type' => 'INT'],
            'material_id'=> ['type' => 'INT'],
            'quantidade' => ['type' => 'INT', 'default' => 1],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('registo_id', 'registos_reparacao', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('material_id', 'materiais', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('materiais_substituidos');

        /**
         * LOGS DE ATIVIDADE (opcional)
         */
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'auto_increment' => true],
            'user_id'         => ['type' => 'INT'],
            'modulo'          => ['type' => 'VARCHAR', 'constraint' => 50],
            'acao'            => ['type' => 'VARCHAR', 'constraint' => 100],
            'registro_id'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'descricao'       => ['type' => 'VARCHAR', 'constraint' => 500],
            'dados_anteriores'=> ['type' => 'TEXT', 'null' => true],
            'dados_novos'     => ['type' => 'TEXT', 'null' => true],
            'ip_address'      => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'detalhes'        => ['type' => 'TEXT', 'null' => true],
            'criado_em'       => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('logs_atividade');
    }

    public function down()
    {
        $this->forge->dropForeignKey('equipamentos', 'equipamentos_tipo_equipamento_id_foreign');
        
        $this->forge->dropTable('logs_atividade');
        $this->forge->dropTable('materiais_substituidos');
        $this->forge->dropTable('materiais');
        $this->forge->dropTable('registos_reparacao');
        $this->forge->dropTable('tickets');
        $this->forge->dropTable('tipos_avaria');
        $this->forge->dropTable('equipamentos');
        $this->forge->dropTable('tipos_equipamento'); // Adiciona aqui!
        $this->forge->dropTable('salas');
        $this->forge->dropTable('escolas');
    
    }
}
