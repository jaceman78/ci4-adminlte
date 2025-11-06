/**
 * Correções de Acessibilidade para Modals Bootstrap
 * 
 * Este script resolve avisos comuns de acessibilidade em modais Bootstrap
 */

// Executar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    
    /**
     * Correção 1: Remover foco de elementos antes de fechar modal
     * 
     * Problema: "Blocked aria-hidden on an element because its descendant retained focus"
     * Solução: Remove o foco de qualquer elemento focado dentro do modal antes de fechá-lo
     */
    const modals = document.querySelectorAll('.modal');
    
    modals.forEach(function(modal) {
        modal.addEventListener('hide.bs.modal', function(event) {
            // Verifica se há um elemento focado dentro do modal
            if (document.activeElement && modal.contains(document.activeElement)) {
                // Remove o foco transferindo-o para o body
                document.activeElement.blur();
            }
        });
    });
    
    /**
     * Correção 2: Garantir que o foco retorna ao elemento que abriu o modal
     * 
     * Armazena o elemento que abriu o modal e restaura o foco após fechar
     */
    let lastFocusedElement = null;
    
    modals.forEach(function(modal) {
        modal.addEventListener('show.bs.modal', function(event) {
            // Armazenar o elemento que estava focado (botão que abriu o modal)
            lastFocusedElement = document.activeElement;
        });
        
        modal.addEventListener('hidden.bs.modal', function(event) {
            // Restaurar o foco para o elemento que abriu o modal
            if (lastFocusedElement && lastFocusedElement !== document.body) {
                // Pequeno delay para garantir que o modal foi completamente fechado
                setTimeout(function() {
                    lastFocusedElement.focus();
                    lastFocusedElement = null;
                }, 100);
            }
        });
    });
    
    /**
     * Correção 3: Trap de foco dentro do modal
     * 
     * Garante que ao usar Tab, o foco não sai do modal
     */
    modals.forEach(function(modal) {
        modal.addEventListener('keydown', function(event) {
            // Se o modal está visível e a tecla Tab foi pressionada
            if (modal.classList.contains('show') && event.key === 'Tab') {
                const focusableElements = modal.querySelectorAll(
                    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                );
                
                const firstElement = focusableElements[0];
                const lastElement = focusableElements[focusableElements.length - 1];
                
                // Se Shift + Tab e estamos no primeiro elemento, ir para o último
                if (event.shiftKey && document.activeElement === firstElement) {
                    event.preventDefault();
                    lastElement.focus();
                }
                // Se Tab e estamos no último elemento, ir para o primeiro
                else if (!event.shiftKey && document.activeElement === lastElement) {
                    event.preventDefault();
                    firstElement.focus();
                }
            }
        });
    });
    
    /**
     * Correção 4: Fechar modal com Escape
     * 
     * Garante que ESC funciona corretamente em todos os modais
     */
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                const closeButton = openModal.querySelector('[data-bs-dismiss="modal"]');
                if (closeButton) {
                    closeButton.click();
                }
            }
        }
    });
    
});

/**
 * Versão jQuery (para compatibilidade com código existente)
 */
if (typeof jQuery !== 'undefined') {
    (function($) {
        $(document).ready(function() {
            
            // Remover foco antes de fechar modal
            $('.modal').on('hide.bs.modal', function (e) {
                if (document.activeElement && $(document.activeElement).closest('.modal').length) {
                    document.activeElement.blur();
                }
            });
            
            // Restaurar foco após fechar modal
            let lastFocused = null;
            
            $('.modal').on('show.bs.modal', function (e) {
                lastFocused = document.activeElement;
            });
            
            $('.modal').on('hidden.bs.modal', function (e) {
                if (lastFocused && lastFocused !== document.body) {
                    setTimeout(function() {
                        $(lastFocused).focus();
                        lastFocused = null;
                    }, 100);
                }
            });
            
        });
    })(jQuery);
}

/**
 * Função helper para criar modais acessíveis programaticamente
 */
function createAccessibleModal(options) {
    const defaults = {
        id: 'dynamicModal',
        title: 'Modal',
        content: '',
        buttons: [
            {
                text: 'Fechar',
                class: 'btn btn-secondary',
                dismiss: true
            }
        ],
        size: '', // 'sm', 'lg', 'xl', ou vazio para padrão
        centered: false,
        backdrop: true,
        keyboard: true
    };
    
    const config = Object.assign({}, defaults, options);
    
    // Criar estrutura do modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = config.id;
    modal.setAttribute('tabindex', '-1');
    modal.setAttribute('aria-labelledby', config.id + 'Label');
    modal.setAttribute('aria-hidden', 'true');
    modal.setAttribute('data-bs-backdrop', config.backdrop);
    modal.setAttribute('data-bs-keyboard', config.keyboard);
    
    const dialogClass = 'modal-dialog' + 
        (config.size ? ' modal-' + config.size : '') +
        (config.centered ? ' modal-dialog-centered' : '');
    
    modal.innerHTML = `
        <div class="${dialogClass}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="${config.id}Label">${config.title}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    ${config.content}
                </div>
                <div class="modal-footer">
                    ${config.buttons.map(btn => `
                        <button type="button" class="${btn.class}" ${btn.dismiss ? 'data-bs-dismiss="modal"' : ''}>
                            ${btn.text}
                        </button>
                    `).join('')}
                </div>
            </div>
        </div>
    `;
    
    // Adicionar ao body
    document.body.appendChild(modal);
    
    // Retornar instância do Bootstrap Modal
    return new bootstrap.Modal(modal);
}

console.log('✓ Modal Accessibility Fixes loaded');
