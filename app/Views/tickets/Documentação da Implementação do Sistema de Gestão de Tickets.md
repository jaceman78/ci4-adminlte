## Documentação da Implementação do Sistema de Gestão de Tickets

Este documento detalha a implementação do sistema de gestão de tickets, desenvolvido com CodeIgniter 4, AdminLTE4 e Bootstrap5, conforme as especificações fornecidas. O sistema abrange a criação, visualização, tratamento e gestão de tickets de avaria, com diferentes níveis de acesso e funcionalidades de e-mail.

### 1. Estrutura da Base de Dados

A tabela `tickets` foi projetada para armazenar todas as informações relativas aos tickets de avaria. A estrutura proposta é a seguinte:

```sql
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipamento_id INT NOT NULL,
    sala_id INT NOT NULL,
    tipo_avaria_id INT NOT NULL,
    user_id INT NOT NULL, -- ID do utilizador que criou o ticket
    atribuido_user_id INT NULL, -- ID do utilizador a quem o ticket foi atribuído (pode ser NULL)
    ticket_aceite BOOLEAN DEFAULT FALSE,
    descricao TEXT NOT NULL,
    estado ENUM(\'novo\',\'em_resolucao\',\'aguarda_peca\',\'reparado\',\'anulado\') DEFAULT \'novo\',
    prioridade ENUM(\'baixa\',\'media\',\'alta\',\'critica\') DEFAULT \'media\',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (equipamento_id) REFERENCES equipamentos(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (sala_id) REFERENCES salas(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (tipo_avaria_id) REFERENCES tipos_avaria(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (atribuido_user_id) REFERENCES user(id) ON DELETE SET NULL ON UPDATE CASCADE
);
```

**Justificativas e Melhorias:**

*   **`ENUM` para `estado` e `prioridade`**: Garante a integridade dos dados e restringe os valores a um conjunto predefinido, facilitando a gestão e a lógica de negócio.
*   **`atribuido_user_id`**: Definido como `NULL` para permitir que tickets sejam criados sem atribuição imediata. A chave estrangeira `ON DELETE SET NULL` assegura que, se um utilizador atribuído for eliminado, o ticket não seja perdido, mas sim desatribuído.
*   **`created_at` e `updated_at`**: Campos essenciais para auditoria e rastreamento de alterações, geridos automaticamente pelo CodeIgniter Model.
*   **`user` (tabela de utilizadores)**: Assumiu-se a existência de uma tabela `user` com campos `id`, `name`, `email` e `level` para gerir as permissões e o envio de e-mails.

### 2. Modelos (Models)

#### `app/Models/TicketsModel.php`

Este modelo (`TicketsModel`) é responsável pela interação com a tabela `tickets`. Inclui métodos para operações CRUD básicas e métodos personalizados para buscar tickets com detalhes de outras tabelas (`equipamentos`, `salas`, `tipos_avaria`, `user`), essenciais para as DataTables e e-mails.

**Principais Métodos:**

*   `getTicketDetails($id)`: Obtém todos os detalhes de um ticket, incluindo informações relacionadas de equipamento, sala, tipo de avaria e utilizadores (criador e atribuído).
*   `getMyTickets($userId)`: Retorna os tickets criados por um utilizador específico, com os campos necessários para a vista `meus_tickets.php`.
*   `getTicketsForTreatment()`: Retorna tickets nos estados 'novo', 'em_resolucao' e 'aguarda_peca', para a vista `tratamento_tickets.php`.
*   `getAllTicketsOrdered()`: Retorna todos os tickets, ordenados por estado (novo, em_resolucao, aguarda_peca, reparado, anulado), para a vista `tickets.php`.

#### `app/Models/UserModel.php` (Ajustes)

O `UserModel` existente foi verificado e confirmou-se que já possui os campos `email` e `level` (`nivel` nas especificações), que são cruciais para as funcionalidades de e-mail e controlo de acesso. Não foram necessárias alterações diretas a este modelo.

### 3. Controladores (Controllers)

#### `app/Controllers/TicketsController.php`

Este controlador gere todas as operações relacionadas com tickets, incluindo a renderização das vistas, operações CRUD via AJAX, e o envio de e-mails.

**Principais Funcionalidades:**

*   **`novoTicket()`**: Exibe o formulário para criação de um novo ticket. Carrega listas de equipamentos, salas e tipos de avaria.
*   **`meusTickets()`**: Exibe a lista de tickets criados pelo utilizador logado.
*   **`tratamentoTickets()`**: Exibe a lista de tickets para tratamento (estados 'novo', 'em_resolucao', 'aguarda_peca') para utilizadores de nível 5 ou superior. Inclui uma lista de utilizadores técnicos para atribuição.
*   **`todosTickets()`**: Exibe todos os tickets para utilizadores de nível 9 ou superior, com estatísticas e opções de edição/eliminação.
*   **`create()`**: Lógica AJAX para criar um novo ticket, incluindo validação e envio de e-mail de confirmação ao criador.
*   **`update($id)`**: Lógica AJAX para atualizar um ticket. Permite edição apenas pelo criador e se o ticket estiver no estado 'novo'. Envia e-mail de atualização.
*   **`delete($id)`**: Lógica AJAX para eliminar um ticket. Permite eliminação apenas pelo criador e se o ticket estiver no estado 'novo'. Envia e-mail de notificação de eliminação.
*   **`assignTicket()`**: Lógica AJAX para atribuir um ticket a um utilizador técnico e/ou alterar o seu estado. Envia e-mail de atribuição ao técnico.
*   **`acceptTicket($ticketId)`**: Método para aceitar um ticket (geralmente via link no e-mail). Atualiza o estado para 'em_resolucao' e `ticket_aceite` para `true`. Envia e-mails de notificação ao criador e ao técnico.
*   **Métodos `DataTable`**: Funções para fornecer dados formatados para as DataTables de cada vista (`getMyTicketsDataTable`, `getTicketsForTreatmentDataTable`, `getAllTicketsDataTable`).
*   **Métodos de E-mail**: Funções privadas (`sendEmail`, `sendTicketConfirmationEmail`, etc.) para encapsular a lógica de envio de e-mails, utilizando as configurações do ficheiro `.env.email_config`.

#### `app/Controllers/EquipamentosController.php`, `SalaController.php`, `TiposAvariaController.php` (Ajustes)

Foram adicionados métodos `getAll()` a estes controladores para permitir que as vistas de tickets obtenham listas completas de equipamentos, salas e tipos de avaria para preencher os dropdowns nos formulários de forma dinâmica via AJAX.

#### `app/Controllers/UserController.php` (Ajustes)

Foi adicionado o método `getTechnicians()` para retornar uma lista de utilizadores com `level >= 5`, que é usada no dropdown de atribuição de tickets.

### 4. Vistas (Views)

Todas as vistas utilizam o layout `layouts/master.php` e integram AdminLTE4 e Bootstrap5 para manter a consistência do design.

#### `app/Views/tickets/novo_ticket.php`

*   Formulário simples para o utilizador criar um novo ticket.
*   Dropdowns para selecionar equipamento, sala e tipo de avaria, preenchidos dinamicamente.
*   Submissão via AJAX com feedback de `toastr`.

#### `app/Views/tickets/meus_tickets.php`

*   Exibe uma DataTable com os tickets criados pelo utilizador logado.
*   Colunas: Equipamento, Sala, Tipo de Avaria, Descrição, Estado, Prioridade, Criado em, Atualizado em, Opções.
*   Botões de 
