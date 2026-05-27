<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Configuração do Sistema de Férias
 * Personalizar dados da escola e parâmetros do sistema
 */
class FeriasConfig extends BaseConfig
{
    /**
     * ==============================================
     * DADOS DA ESCOLA (para documentos PDF)
     * ==============================================
     */
    
    // Código da escola (ex: 171268)
    public string $escola_codigo = '171268';
    
    // Nome completo do agrupamento
    public string $escola_nome = 'Agrupamento de Escolas João de Barros';
    
    // Morada completa
    public string $escola_endereco = '460050 - Escola Secundária João de Barros, 2855 - 713 Corroios';
    
    // Número Mecanográfico
    public string $escola_nmec = '212589180';
    
    // Telefone
    public string $escola_tel = '212531167';
    
    // Contribuinte
    public string $escola_contrib = '600078422';
    
    // Nome completo da escola (para campo "Escola")
    public string $escola_completo = 'Escola Secundária João de Barros, Corroios, Seixal';
    
    /**
     * ==============================================
     * CATEGORIA PADRÃO DOS PROFESSORES
     * ==============================================
     */
    
    // Categoria padrão que aparece no documento
    public string $categoria_default = 'Professores do 2º e 3º Ciclos e Sec. - Quadro Escola - Nomeação';
    
    /**
     * ==============================================
     * PARÂMETROS DE FÉRIAS
     * ==============================================
     */
    
    // Dias base de férias (padrão: 22 dias)
    public int $dias_base_default = 22;
    
    // Máximo de dias de ajuste positivo
    public int $max_dias_ajuste_positivo = 7;
    
    // Máximo de dias de ajuste negativo
    public int $max_dias_ajuste_negativo = -7;
    
    // Máximo de dias extra (por mérito/acordo)
    public int $max_dias_extra = 3;
    
    // Dias mínimos para antecedência de pedido
    public int $dias_antecedencia_minima = 15;
    
    /**
     * ==============================================
     * NOTIFICAÇÕES EMAIL
     * ==============================================
     */
    
    // Email padrão para cópia de notificações
    public string $email_copia_secretaria = '';
    
    // Enviar email ao submeter pedido
    public bool $notificar_submissao = true;
    
    // Enviar email ao aprovar pedido
    public bool $notificar_aprovacao = true;
    
    // Enviar email ao rejeitar pedido
    public bool $notificar_rejeicao = true;
    
    // Enviar email ao fazer upload de documento
    public bool $notificar_upload = true;
    
    /**
     * ==============================================
     * DOCUMENTOS PDF
     * ==============================================
     */
    
    // Incluir logo no cabeçalho (caminho relativo)
    public string $logo_escola = 'public/img/logo_escola.png';
    
    // Usar logo no PDF (true/false)
    public bool $usar_logo = false;
    
    // Rodapé do documento
    public string $rodape_sistema = 'Processado por computador pelo software Sistema de Gestão Escolar • Copyright©';
    
    /**
     * ==============================================
     * DIRETÓRIOS
     * ==============================================
     */
    
    // Pasta base para documentos (relativo a FCPATH)
    // NOTA: Não usar 'public' — conflito com a rota CI4 /public
    public string $pasta_documentos = 'docs';
    
    // Permissões para pastas criadas
    public int $permissoes_pasta = 0755;
    
    /**
     * ==============================================
     * VALIDAÇÕES
     * ==============================================
     */
    
    // Permitir períodos sobrepostos (mesmo professor, mesmo ano)
    public bool $permitir_sobreposicoes = false;
    
    // Permitir pedidos sem saldo suficiente
    public bool $permitir_saldo_negativo = false;
    
    // Tamanho máximo para upload de documento (em MB)
    public int $max_upload_size_mb = 5;
    
    // Extensões permitidas para documento assinado
    public array $extensoes_permitidas = ['pdf', 'jpg', 'jpeg', 'png'];
    
    /**
     * ==============================================
     * LOGS E AUDITORIA
     * ==============================================
     */
    
    // Dias para manter logs antigos (0 = manter indefinidamente)
    public int $dias_manter_logs = 365;
    
    // Registrar todas as ações (mesmo consultas)
    public bool $log_completo = false;
    
    /**
     * ==============================================
     * MÉTODOS AUXILIARES
     * ==============================================
     */
    
    /**
     * Obter array com dados da escola para PDF
     */
    public function getDadosEscola(): array
    {
        return [
            'escola_codigo' => $this->escola_codigo,
            'escola_nome' => $this->escola_nome,
            'escola_endereco' => $this->escola_endereco,
            'escola_nmec' => $this->escola_nmec,
            'escola_tel' => $this->escola_tel,
            'escola_contrib' => $this->escola_contrib,
            'escola_completo' => $this->escola_completo,
            'categoria_default' => $this->categoria_default,
            'logo_escola' => $this->logo_escola,
            'usar_logo' => $this->usar_logo,
            'rodape_sistema' => $this->rodape_sistema
        ];
    }
    
    /**
     * Validar se extensão é permitida
     */
    public function isExtensaoPermitida(string $extensao): bool
    {
        return in_array(strtolower($extensao), $this->extensoes_permitidas);
    }
    
    /**
     * Obter tamanho máximo upload em bytes
     */
    public function getMaxUploadBytes(): int
    {
        return $this->max_upload_size_mb * 1024 * 1024;
    }
}
