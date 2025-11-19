<?= $this->extend('layout/public') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0"><i class="fas fa-shield-alt"></i> Política de Privacidade</h1>
                </div>
                <div class="card-body">
                    <p class="text-muted"><small>Última atualização: <?= date('d/m/Y') ?></small></p>

                    <h2 class="h4 mt-4 mb-3">1. Introdução</h2>
                    <p>
                        O Sistema de Gestão Escolar do Agrupamento de Escolas João de Barros ("nós", "nosso" ou "sistema") 
                        respeita a sua privacidade e está comprometido em proteger os seus dados pessoais. Esta Política de 
                        Privacidade explica como recolhemos, usamos e protegemos as suas informações quando utiliza o nosso sistema.
                    </p>

                    <h2 class="h4 mt-4 mb-3">2. Dados que Recolhemos</h2>
                    <p>Recolhemos e processamos os seguintes tipos de dados, consoante a área do sistema utilizada:</p>
                    <ul>
                        <li><strong>Área Pública (sem login):</strong> Dados submetidos no formulário de requisição de Kit Digital (nome, número de aluno, turma, NIF, emails, escalão ASE), endereço IP, resposta ao captcha, data/hora do pedido.</li>
                        <li><strong>Área Restrita (com login):</strong> Nome, email institucional, número de utilizador, informações de login via Google OAuth 2.0, tickets criados, ações realizadas, logs de atividade, informações sobre equipamentos, salas, avarias reportadas.</li>
                        <li><strong>Dados Técnicos:</strong> Endereço IP, tipo de navegador, sistema operativo.</li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">3. Como Utilizamos os Seus Dados</h2>
                    <p>Os seus dados são utilizados para:</p>
                    <ul>
                        <li>Processar e gerir requisições de Kit Digital submetidas na área pública</li>
                        <li>Autenticar e gerir o seu acesso à área restrita do sistema</li>
                        <li>Processar e gerir tickets de suporte técnico</li>
                        <li>Enviar notificações por email relacionadas com tickets ou requisições</li>
                        <li>Melhorar a funcionalidade e segurança do sistema</li>
                        <li>Gerar relatórios estatísticos (dados anonimizados)</li>
                        <li>Cumprir obrigações legais e regulamentares</li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">4. Base Legal para o Processamento</h2>
                    <p>Processamos os seus dados pessoais com base em:</p>
                    <ul>
                        <li><strong>Consentimento:</strong> Ao utilizar o sistema, consente o processamento dos seus dados</li>
                        <li><strong>Obrigação Contratual:</strong> Para fornecer os serviços do sistema</li>
                        <li><strong>Interesse Legítimo:</strong> Para manter a segurança e funcionalidade do sistema</li>
                        <li><strong>Cumprimento Legal:</strong> Para cumprir requisitos legais aplicáveis</li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">5. Autenticação Google OAuth 2.0</h2>
                    <p>
                        Este sistema utiliza o Google OAuth 2.0 para autenticação. Quando faz login através do Google:
                    </p>
                    <ul>
                        <li>Solicitamos acesso ao seu email e perfil básico do Google</li>
                        <li>Não armazenamos a sua palavra-passe do Google</li>
                        <li>Não temos acesso a outros dados da sua conta Google</li>
                        <li>Pode revogar o acesso a qualquer momento nas <a href="https://myaccount.google.com/permissions" target="_blank">definições da sua conta Google</a></li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">6. Formulário Público de Kit Digital</h2>
                    <p>
                        O formulário de requisição de Kit Digital está disponível a alunos e encarregados de educação, sem necessidade de login. Os dados recolhidos destinam-se exclusivamente à gestão do processo de entrega e devolução do Kit Digital, conforme as condições gerais apresentadas no próprio formulário.
                    </p>
                    <ul>
                        <li>Os dados são protegidos por mecanismos anti-bot (honeypot e captcha matemático simples).</li>
                        <li>O NIF é validado e só é aceite um pedido por NIF.</li>
                        <li>Os dados são armazenados de forma segura e não são partilhados com terceiros, exceto se exigido por lei.</li>
                        <li>Os dados podem ser eliminados mediante pedido, salvo obrigações legais de retenção.</li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">7. Partilha de Dados</h2>
                    <p>Os seus dados <strong>não são vendidos</strong> a terceiros. Podemos partilhar dados limitados com:</p>
                    <ul>
                        <li><strong>Google:</strong> Para autenticação OAuth (conforme a Política de Privacidade do Google)</li>
                        <li><strong>Administradores do Sistema:</strong> Para gestão e suporte técnico</li>
                        <li><strong>Autoridades Competentes:</strong> Se legalmente obrigados</li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">8. Segurança dos Dados</h2>
                    <p>Implementamos medidas de segurança técnicas e organizacionais para proteger os seus dados:</p>
                    <ul>
                        <li>Conexões encriptadas (HTTPS)</li>
                        <li>Controlo de acesso baseado em funções (RBAC)</li>
                        <li>Logs de auditoria de ações críticas</li>
                        <li>Backup regular de dados</li>
                        <li>Autenticação segura via OAuth 2.0</li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">9. Retenção de Dados</h2>
                    <p>
                        Retemos os seus dados pessoais apenas pelo tempo necessário para cumprir as finalidades descritas 
                        nesta política, ou conforme exigido por lei. Dados de tickets e logs podem ser retidos por:
                    </p>
                    <ul>
                        <li><strong>Tickets:</strong> Durante o ano letivo + 2 anos para fins de auditoria</li>
                        <li><strong>Logs de Sistema:</strong> Até 90 dias</li>
                        <li><strong>Dados de Conta:</strong> Enquanto a conta estiver ativa</li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">10. Os Seus Direitos (RGPD)</h2>
                    <p>De acordo com o Regulamento Geral sobre a Proteção de Dados (RGPD), tem direito a:</p>
                    <ul>
                        <li><strong>Acesso:</strong> Solicitar uma cópia dos seus dados pessoais</li>
                        <li><strong>Retificação:</strong> Corrigir dados incorretos ou incompletos</li>
                        <li><strong>Apagamento:</strong> Solicitar a eliminação dos seus dados ("direito ao esquecimento")</li>
                        <li><strong>Restrição:</strong> Limitar o processamento dos seus dados</li>
                        <li><strong>Portabilidade:</strong> Receber os seus dados num formato estruturado</li>
                        <li><strong>Oposição:</strong> Opor-se ao processamento dos seus dados</li>
                        <li><strong>Revogação:</strong> Retirar o consentimento a qualquer momento</li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">11. Cookies e Tecnologias Semelhantes</h2>
                    <p>
                        Utilizamos cookies de sessão essenciais para o funcionamento do sistema. Estes cookies são 
                        necessários para autenticação e não recolhem informações de rastreamento.
                    </p>

                    <h2 class="h4 mt-4 mb-3">12. Menores de Idade</h2>
                    <p>
                        O formulário de Kit Digital pode ser utilizado por alunos menores de idade, sob responsabilidade do encarregado de educação. Não recolhemos intencionalmente outros dados de menores de 16 anos sem consentimento parental.
                    </p>

                    <h2 class="h4 mt-4 mb-3">13. Alterações a Esta Política</h2>
                    <p>
                        Podemos atualizar esta Política de Privacidade periodicamente. Notificaremos os utilizadores sobre 
                        alterações significativas através do sistema ou por email.
                    </p>

                    <h2 class="h4 mt-4 mb-3">14. Contacto</h2>
                    <p>Para questões sobre esta Política de Privacidade ou para exercer os seus direitos, contacte:</p>
                    <address>
                        <strong>Agrupamento de Escolas João de Barros</strong><br>
                        Email: <a href="mailto:antonioneto@aejoaodebarros.pt">antonioneto@aejoaodebarros.pt</a><br>
                        Encarregado de Proteção de Dados (DPO): [Inserir contacto do DPO]
                    </address>

                    <h2 class="h4 mt-4 mb-3">15. Autoridade de Controlo</h2>
                    <p>
                        Tem o direito de apresentar uma reclamação à Comissão Nacional de Proteção de Dados (CNPD):
                    </p>
                    <address>
                        <strong>CNPD - Comissão Nacional de Proteção de Dados</strong><br>
                        Website: <a href="https://www.cnpd.pt" target="_blank">www.cnpd.pt</a><br>
                        Email: geral@cnpd.pt<br>
                        Telefone: +351 213 928 400
                    </address>

                    <hr class="my-4">

                    <div class="text-center">
                        <a href="<?= site_url('/') ?>" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Voltar ao Sistema
                        </a>
                        <a href="<?= site_url('privacy/terms') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-file-contract"></i> Termos de Serviço
                        </a>
                    </div>
                </div>
                <div class="card-footer text-muted text-center">
                    <small>
                        © <?= date('Y') ?> Agrupamento de Escolas João de Barros. Todos os direitos reservados.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
