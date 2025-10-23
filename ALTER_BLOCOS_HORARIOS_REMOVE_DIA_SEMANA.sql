-- =====================================================
-- SCRIPT PARA REMOVER COLUNA dia_semana DA TABELA blocos_horarios
-- Execute este script via phpMyAdmin ou MySQL Workbench
-- =====================================================

USE sistema_gestao;

-- Remover a coluna dia_semana da tabela blocos_horarios
ALTER TABLE `blocos_horarios` 
DROP COLUMN `dia_semana`;

-- Verificar a estrutura da tabela após a alteração
DESCRIBE blocos_horarios;
