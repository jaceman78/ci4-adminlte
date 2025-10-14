/**
 * Sistema Global de Notificações Toast
 * Usando Toastr.js
 * 
 * @author HardWork550
 * @version 1.0
 */

// Configuração padrão do Toastr
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": true,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

/**
 * Exibir notificação de sucesso
 * @param {string} message - Mensagem a exibir
 * @param {string} title - Título opcional (padrão: "Sucesso!")
 */
function showSuccess(message, title = 'Sucesso!') {
    toastr.success(message, title);
}

/**
 * Exibir notificação de erro
 * @param {string} message - Mensagem a exibir
 * @param {string} title - Título opcional (padrão: "Erro!")
 */
function showError(message, title = 'Erro!') {
    toastr.error(message, title);
}

/**
 * Exibir notificação de aviso
 * @param {string} message - Mensagem a exibir
 * @param {string} title - Título opcional (padrão: "Atenção!")
 */
function showWarning(message, title = 'Atenção!') {
    toastr.warning(message, title);
}

/**
 * Exibir notificação de informação
 * @param {string} message - Mensagem a exibir
 * @param {string} title - Título opcional (padrão: "Informação")
 */
function showInfo(message, title = 'Informação') {
    toastr.info(message, title);
}

/**
 * Exibir toast genérico (compatibilidade com código existente)
 * @param {string} type - Tipo: success, error, warning, info
 * @param {string} message - Mensagem a exibir
 * @param {string} title - Título opcional
 */
function showToast(type, message, title = null) {
    switch(type) {
        case 'success':
            showSuccess(message, title || 'Sucesso!');
            break;
        case 'error':
            showError(message, title || 'Erro!');
            break;
        case 'warning':
            showWarning(message, title || 'Atenção!');
            break;
        case 'info':
            showInfo(message, title || 'Informação');
            break;
        default:
            toastr.info(message, title);
    }
}

/**
 * Limpar todas as notificações
 */
function clearToasts() {
    toastr.clear();
}

/**
 * Remover toast específico
 * @param {object} toast - Objeto toast retornado ao criar
 */
function removeToast(toast) {
    toastr.remove(toast);
}

/**
 * Notificação persistente (não fecha automaticamente)
 * @param {string} type - Tipo: success, error, warning, info
 * @param {string} message - Mensagem
 * @param {string} title - Título
 */
function showPersistentToast(type, message, title = null) {
    const originalTimeout = toastr.options.timeOut;
    const originalExtended = toastr.options.extendedTimeOut;
    
    toastr.options.timeOut = 0;
    toastr.options.extendedTimeOut = 0;
    toastr.options.closeButton = true;
    
    showToast(type, message, title);
    
    // Restaurar configurações originais
    toastr.options.timeOut = originalTimeout;
    toastr.options.extendedTimeOut = originalExtended;
}

/**
 * Toast com callback ao clicar
 * @param {string} type - Tipo
 * @param {string} message - Mensagem
 * @param {function} callback - Função a executar ao clicar
 * @param {string} title - Título opcional
 */
function showToastWithAction(type, message, callback, title = null) {
    const originalOnClick = toastr.options.onclick;
    
    toastr.options.onclick = callback;
    showToast(type, message, title);
    
    // Restaurar callback original
    toastr.options.onclick = originalOnClick;
}

/**
 * Toast de confirmação com botões
 * @param {string} message - Mensagem
 * @param {function} onConfirm - Função ao confirmar
 * @param {function} onCancel - Função ao cancelar (opcional)
 */
function showConfirmToast(message, onConfirm, onCancel = null) {
    const toast = toastr.warning(
        `<div>${message}</div>
         <div class="mt-2">
             <button type="button" class="btn btn-sm btn-success me-2" id="confirmToastBtn">
                 <i class="fas fa-check"></i> Confirmar
             </button>
             <button type="button" class="btn btn-sm btn-secondary" id="cancelToastBtn">
                 <i class="fas fa-times"></i> Cancelar
             </button>
         </div>`,
        'Confirmação Necessária',
        {
            closeButton: true,
            allowHtml: true,
            timeOut: 0,
            extendedTimeOut: 0,
            tapToDismiss: false
        }
    );
    
    // Aguardar renderização
    setTimeout(() => {
        $('#confirmToastBtn').on('click', function() {
            toastr.clear(toast);
            if (onConfirm) onConfirm();
        });
        
        $('#cancelToastBtn').on('click', function() {
            toastr.clear(toast);
            if (onCancel) onCancel();
        });
    }, 100);
}

/**
 * Toast de progresso
 * @param {string} message - Mensagem
 * @param {number} progress - Progresso de 0 a 100
 */
function showProgressToast(message, progress = 0) {
    const progressBar = `<div class="progress mt-2" style="height: 5px;">
        <div class="progress-bar" role="progressbar" style="width: ${progress}%" 
             aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100"></div>
    </div>`;
    
    return toastr.info(
        `${message}${progressBar}`,
        'Processando...',
        {
            closeButton: false,
            allowHtml: true,
            timeOut: 0,
            extendedTimeOut: 0,
            tapToDismiss: false,
            progressBar: false
        }
    );
}

/**
 * Atualizar toast de progresso
 * @param {object} toast - Toast retornado por showProgressToast
 * @param {number} progress - Novo progresso
 */
function updateProgressToast(toast, progress) {
    if (toast && toast.find) {
        toast.find('.progress-bar').css('width', progress + '%')
            .attr('aria-valuenow', progress);
    }
}

/**
 * Toast de loading
 * @param {string} message - Mensagem (padrão: "Carregando...")
 */
function showLoadingToast(message = 'Carregando...') {
    return toastr.info(
        `<i class="fas fa-spinner fa-spin me-2"></i>${message}`,
        '',
        {
            closeButton: false,
            allowHtml: true,
            timeOut: 0,
            extendedTimeOut: 0,
            tapToDismiss: false,
            progressBar: false
        }
    );
}

/**
 * Fechar toast de loading
 * @param {object} toast - Toast retornado por showLoadingToast
 */
function hideLoadingToast(toast) {
    if (toast) {
        toastr.clear(toast);
    }
}

// Atalhos globais
window.toast = {
    success: showSuccess,
    error: showError,
    warning: showWarning,
    info: showInfo,
    show: showToast,
    clear: clearToasts,
    persistent: showPersistentToast,
    confirm: showConfirmToast,
    progress: showProgressToast,
    updateProgress: updateProgressToast,
    loading: showLoadingToast,
    hideLoading: hideLoadingToast
};

// Log de inicialização
console.log('🍞 Sistema de Toast inicializado com sucesso!');
