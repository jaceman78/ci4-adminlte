-- Script de RENAME de tabelas
-- Adiciona o prefixo: u520317771_
-- Base de Dados: sistema_gestao
-- Data: 2025-10-25 16:15:09

USE sistema_gestao;

RENAME TABLE `ano_letivo` TO `u520317771_ano_letivo`;
RENAME TABLE `blocos_horarios` TO `u520317771_blocos_horarios`;
RENAME TABLE `departamentos` TO `u520317771_departamentos`;
RENAME TABLE `disciplina` TO `u520317771_disciplina`;
RENAME TABLE `equipamentos` TO `u520317771_equipamentos`;
RENAME TABLE `equipamentos_sala` TO `u520317771_equipamentos_sala`;
RENAME TABLE `escolas` TO `u520317771_escolas`;
RENAME TABLE `estados_ticket` TO `u520317771_estados_ticket`;
RENAME TABLE `estados_ticket_transicoes` TO `u520317771_estados_ticket_transicoes`;
RENAME TABLE `horario_aulas` TO `u520317771_horario_aulas`;
RENAME TABLE `logs_atividade` TO `u520317771_logs_atividade`;
RENAME TABLE `materiais` TO `u520317771_materiais`;
RENAME TABLE `materiais_substituidos` TO `u520317771_materiais_substituidos`;
RENAME TABLE `migrations` TO `u520317771_migrations`;
RENAME TABLE `permutas` TO `u520317771_permutas`;
RENAME TABLE `registos_reparacao` TO `u520317771_registos_reparacao`;
RENAME TABLE `salas` TO `u520317771_salas`;
RENAME TABLE `sugestoes` TO `u520317771_sugestoes`;
RENAME TABLE `tickets` TO `u520317771_tickets`;
RENAME TABLE `tipologia` TO `u520317771_tipologia`;
RENAME TABLE `tipos_avaria` TO `u520317771_tipos_avaria`;
RENAME TABLE `tipos_equipamento` TO `u520317771_tipos_equipamento`;
RENAME TABLE `turma` TO `u520317771_turma`;
RENAME TABLE `user` TO `u520317771_user`;