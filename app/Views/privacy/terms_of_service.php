<?= $this->extend('layout/public') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h1 class="h3 mb-0"><i class="fas fa-file-contract"></i> Termos de Serviço</h1>
                </div>
                <div class="card-body">
                    <p class="text-muted"><small>Última atualização: <?= date('d/m/Y') ?></small></p>

                    <h2 class="h4 mt-4 mb-3">1. Aceitação dos Termos</h2>
                    <p>
                        Ao aceder e utilizar o Sistema de Gestão Escolar do Agrupamento de Escolas João de Barros 
                        ("Sistema"), concorda em ficar vinculado a estes Termos de Serviço. Se não concordar com 
                        estes termos, não deve utilizar o Sistema.
                    </p>

                    <h2 class="h4 mt-4 mb-3">2. Descrição do Serviço</h2>
                    <p>
                        O Sistema é uma plataforma interna de gestão escolar que permite:
                    </p>
                    <ul>
                        <li>Gestão de tickets de suporte técnico</li>
                        <li>Registo e acompanhamento de avarias de equipamentos</li>
                        <li>Gestão de salas, equipamentos e utilizadores</li>
                        <li>Notificações por email sobre estado de tickets</li>
                        <li>Relatórios e estatísticas de gestão</li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">3. Elegibilidade</h2>
                    <p>
                        Este Sistema destina-se exclusivamente a:
                    </p>
                    <ul>
                        <li>Funcionários do Agrupamento de Escolas João de Barros</li>
                        <li>Pessoal docente e não docente autorizado</li>
                        <li>Administradores do sistema designados</li>
                    </ul>
                    <p>
                        O acesso é concedido mediante aprovação da administração escolar.
                    </p>

                    <h2 class="h4 mt-4 mb-3">4. Conta de Utilizador e Autenticação</h2>
                    <ul>
                        <li>É necessário autenticar-se através do Google OAuth 2.0 com email institucional</li>
                        <li>É responsável por manter a segurança da sua conta Google</li>
                        <li>Não deve partilhar as suas credenciais de acesso</li>
                        <li>Deve notificar imediatamente qualquer uso não autorizado da sua conta</li>
                        <li>A administração reserva-se o direito de suspender ou encerrar contas</li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">5. Uso Aceitável</h2>
                    <p>Ao utilizar o Sistema, concorda em:</p>
                    <ul>
                        <li>Utilizar o Sistema apenas para fins profissionais legítimos</li>
                        <li>Fornecer informações precisas e atualizadas</li>
                        <li>Respeitar os direitos de privacidade de outros utilizadores</li>
                        <li>Não tentar aceder a áreas restritas do sistema</li>
                        <li>Não utilizar o Sistema para atividades ilegais ou não autorizadas</li>
                        <li>Não transmitir vírus, malware ou código malicioso</li>
                        <li>Não fazer engenharia reversa ou tentar explorar vulnerabilidades</li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">6. Uso Proibido</h2>
                    <p>É expressamente proibido:</p>
                    <ul>
                        <li>Criar tickets falsos ou fraudulentos</li>
                        <li>Abusar do sistema de notificações</li>
                        <li>Tentar aceder a dados de outros utilizadores sem autorização</li>
                        <li>Modificar ou eliminar dados sem permissão adequada</li>
                        <li>Fazer spam ou flooding de tickets</li>
                        <li>Utilizar o sistema para assédio ou discriminação</li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">7. Propriedade Intelectual</h2>
                    <p>
                        O Sistema, incluindo todo o código, design, texto, gráficos e interfaces, é propriedade do 
                        Agrupamento de Escolas João de Barros. É protegido por direitos de autor e outras leis de 
                        propriedade intelectual.
                    </p>

                    <h2 class="h4 mt-4 mb-3">8. Dados e Privacidade</h2>
                    <p>
                        O processamento dos seus dados pessoais é regido pela nossa 
                        <a href="<?= site_url('privacy') ?>">Política de Privacidade</a>. Ao utilizar o Sistema, 
                        concorda com a recolha e uso dos seus dados conforme descrito na política.
                    </p>

                    <h2 class="h4 mt-4 mb-3">9. Logs e Auditoria</h2>
                    <p>
                        Todas as ações no Sistema são registadas em logs para fins de:
                    </p>
                    <ul>
                        <li>Segurança e prevenção de fraudes</li>
                        <li>Auditoria de conformidade</li>
                        <li>Resolução de disputas</li>
                        <li>Melhoria do serviço</li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">10. Disponibilidade do Serviço</h2>
                    <p>
                        Empenhamo-nos em manter o Sistema disponível, mas não garantimos:
                    </p>
                    <ul>
                        <li>Disponibilidade ininterrupta (podem ocorrer manutenções)</li>
                        <li>Ausência de erros ou bugs</li>
                        <li>Compatibilidade com todos os navegadores ou dispositivos</li>
                    </ul>
                    <p>
                        Reservamo-nos o direito de suspender o serviço temporariamente para manutenção ou atualizações.
                    </p>

                    <h2 class="h4 mt-4 mb-3">11. Modificações ao Serviço</h2>
                    <p>
                        Podemos, a qualquer momento:
                    </p>
                    <ul>
                        <li>Adicionar, modificar ou remover funcionalidades</li>
                        <li>Atualizar a interface ou design</li>
                        <li>Alterar os requisitos de acesso</li>
                        <li>Descontinuar o serviço (com aviso prévio razoável)</li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">12. Limitação de Responsabilidade</h2>
                    <p>
                        O Sistema é fornecido "como está". Na medida máxima permitida pela lei:
                    </p>
                    <ul>
                        <li>Não garantimos resultados específicos</li>
                        <li>Não nos responsabilizamos por perda de dados</li>
                        <li>Não somos responsáveis por danos indiretos ou consequenciais</li>
                        <li>A responsabilidade total está limitada ao valor dos serviços prestados</li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">13. Rescisão</h2>
                    <p>
                        Podemos suspender ou encerrar o seu acesso se:
                    </p>
                    <ul>
                        <li>Violar estes Termos de Serviço</li>
                        <li>Utilizar o sistema de forma abusiva</li>
                        <li>Deixar de ser funcionário do Agrupamento</li>
                        <li>Houver suspeita de atividade fraudulenta</li>
                    </ul>

                    <h2 class="h4 mt-4 mb-3">14. Alterações aos Termos</h2>
                    <p>
                        Reservamo-nos o direito de modificar estes termos a qualquer momento. As alterações 
                        entram em vigor imediatamente após a publicação. O uso continuado do Sistema após 
                        alterações constitui aceitação dos novos termos.
                    </p>

                    <h2 class="h4 mt-4 mb-3">15. Lei Aplicável</h2>
                    <p>
                        Estes Termos são regidos pelas leis de Portugal. Qualquer disputa será resolvida nos 
                        tribunais portugueses competentes.
                    </p>

                    <h2 class="h4 mt-4 mb-3">16. Contacto</h2>
                    <p>Para questões sobre estes Termos de Serviço, contacte:</p>
                    <address>
                        <strong>Agrupamento de Escolas João de Barros</strong><br>
                        Email: <a href="mailto:antonioneto@aejoaodebarros.pt">antonioneto@aejoaodebarros.pt</a>
                    </address>

                    <hr class="my-4">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Nota:</strong> Ao clicar em "Login com Google" ou ao utilizar o Sistema, 
                        confirma que leu, compreendeu e concorda com estes Termos de Serviço e com a nossa 
                        <a href="<?= site_url('privacy') ?>" class="alert-link">Política de Privacidade</a>.
                    </div>

                    <div class="text-center">
                        <a href="<?= site_url('/') ?>" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Voltar ao Sistema
                        </a>
                        <a href="<?= site_url('privacy') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-shield-alt"></i> Política de Privacidade
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
