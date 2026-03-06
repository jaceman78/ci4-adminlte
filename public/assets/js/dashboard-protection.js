/**
 * Script de Proteção para Dashboards
 * Previne erros de getContext quando elementos canvas não existem
 */

(function() {
    'use strict';
    
    // Aguardar o DOM estar pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProtection);
    } else {
        initProtection();
    }
    
    function initProtection() {
        // Proteção contra tentativas de acessar canvas inexistentes
        const originalGetElementById = document.getElementById.bind(document);
        
        document.getElementById = function(id) {
            // Verificar se id é válido antes de chamar métodos nele
            if (!id) {
                return originalGetElementById(id);
            }
            
            const element = originalGetElementById(id);
            
            // Se o elemento não existe e alguém está tentando obter contexto de canvas/chart
            if (!element && typeof id === 'string' && 
                (id.toLowerCase().includes('chart') || id.toLowerCase().includes('canvas'))) {
                
                // Retornar um objeto proxy que silenciosamente ignora as chamadas
                return {
                    getContext: function() {
                        // Retornar null silenciosamente - Chart.js irá falhar gracefully
                        return null;
                    },
                    setAttribute: function() {},
                    getAttribute: function() { return null; },
                    addEventListener: function() {},
                    removeEventListener: function() {},
                    appendChild: function() {},
                    removeChild: function() {},
                    style: {},
                    classList: {
                        add: function() {},
                        remove: function() {},
                        contains: function() { return false; },
                        toggle: function() {}
                    },
                    dataset: {},
                    offsetWidth: 0,
                    offsetHeight: 0,
                    clientWidth: 0,
                    clientHeight: 0
                };
            }
            
            return element;
        };
    }
})();
