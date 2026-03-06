/**
 * Modal Accessibility Fix
 * Correções de acessibilidade para modals do Bootstrap/AdminLTE
 *
 * Evita que elementos com foco fiquem dentro de ancestrais com aria-hidden="true",
 * o que gera avisos na consola e é incorreto para tecnologias de apoio.
 */

document.addEventListener('DOMContentLoaded', function () {
	// Garantir que o Bootstrap está disponível (eventos .bs.modal)
	if (typeof bootstrap === 'undefined') {
		return;
	}

	// 1) Corrigir aria-hidden nos próprios modais quando são mostrados
	var modals = document.querySelectorAll('.modal');

	modals.forEach(function (modal) {
		modal.addEventListener('shown.bs.modal', function () {
			// O modal visível não deve estar aria-hidden
			if (modal.hasAttribute('aria-hidden')) {
				modal.removeAttribute('aria-hidden');
			}

			// Garantir atributos ARIA mínimos
			if (!modal.hasAttribute('role')) {
				modal.setAttribute('role', 'dialog');
			}
			modal.setAttribute('aria-modal', 'true');
		});

		modal.addEventListener('hidden.bs.modal', function () {
			// Quando o modal é fechado volta ao estado "escondido"
			modal.setAttribute('aria-hidden', 'true');
			modal.removeAttribute('aria-modal');
		});
	});

	// 2) Impedir que o wrapper principal fique aria-hidden enquanto tem foco dentro
	var appWrapper = document.querySelector('.app-wrapper');

	if (appWrapper && 'MutationObserver' in window) {
		var observer = new MutationObserver(function (mutations) {
			mutations.forEach(function (mutation) {
				if (mutation.type === 'attributes' && mutation.attributeName === 'aria-hidden') {
					// Se alguém tentar pôr aria-hidden="true" no wrapper enquanto há foco dentro,
					// removemos para evitar o aviso do browser.
					if (appWrapper.hasAttribute('aria-hidden')) {
						var active = document.activeElement;
						if (active && appWrapper.contains(active)) {
							appWrapper.removeAttribute('aria-hidden');
						}
					}
				}
			});
		});

		observer.observe(appWrapper, { attributes: true, attributeFilter: ['aria-hidden'] });
	}
});
