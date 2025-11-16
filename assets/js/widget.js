/**
 * JavaScript del widget flotante de contacto
 * Contact Float Widget - Frontend Scripts
 * JavaScript Vanilla - Sin dependencias
 */

(function() {
	'use strict';

	/**
	 * Inicializar el widget cuando el DOM esté listo
	 */
	function initWidget() {
		const widget = document.querySelector('.cfw-widget');
		if (!widget) {
			return;
		}

		const toggle = widget.querySelector('.cfw-toggle');
		const panel = widget.querySelector('.cfw-panel');

		if (!toggle || !panel) {
			return;
		}

		/**
		 * Toggle del panel con animación suave
		 */
		function togglePanel() {
			const isOpen = panel.hasAttribute('hidden');
			
			if (isOpen) {
				// Abrir con animación
				panel.removeAttribute('hidden');
				// Pequeño delay para que se active la transición CSS
				requestAnimationFrame(() => {
					toggle.setAttribute('aria-expanded', 'true');
					widget.classList.add('is-open');
				});
			} else {
				// Cerrar con animación
				toggle.setAttribute('aria-expanded', 'false');
				widget.classList.remove('is-open');
				// Esperar a que termine la animación antes de ocultar
				setTimeout(() => {
					panel.setAttribute('hidden', '');
				}, 400); // Mismo tiempo que la transición CSS
			}
		}

		/**
		 * Cerrar el panel con animación
		 */
		function closePanel() {
			if (!panel.hasAttribute('hidden')) {
				toggle.setAttribute('aria-expanded', 'false');
				widget.classList.remove('is-open');
				// Esperar a que termine la animación antes de ocultar
				setTimeout(() => {
					panel.setAttribute('hidden', '');
				}, 400);
			}
		}

		// Event listener para el botón de toggle
		toggle.addEventListener('click', function(e) {
			e.preventDefault();
			togglePanel();
		});

		// Cerrar con la tecla Escape
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape' || e.keyCode === 27) {
				closePanel();
			}
		});

		// Cerrar al hacer clic fuera del widget (opcional)
		document.addEventListener('click', function(e) {
			// Si el click fue fuera del widget y el panel está abierto
			if (!widget.contains(e.target) && !panel.hasAttribute('hidden')) {
				closePanel();
			}
		});

		// Prevenir que los clicks dentro del widget lo cierren
		widget.addEventListener('click', function(e) {
			e.stopPropagation();
		});

		// Aplicar configuración desde PHP
		if (typeof cfwConfig !== 'undefined') {
			// Aplicar color primario
			if (cfwConfig.primaryColor) {
				widget.style.setProperty('--primary', cfwConfig.primaryColor);
			}

			// Aplicar offset top
			if (cfwConfig.offsetTop) {
				widget.style.top = cfwConfig.offsetTop + 'px';
			}

			// Si debe mostrarse abierto al cargar
			if (cfwConfig.showOpen === '1' && panel.hasAttribute('hidden')) {
				panel.removeAttribute('hidden');
				toggle.setAttribute('aria-expanded', 'true');
				widget.classList.add('is-open');
			}
		}
	}

	// Inicializar cuando el DOM esté listo
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initWidget);
	} else {
		// DOM ya está listo
		initWidget();
	}

})();
