/**
 * JavaScript para la página de administración
 * Contact Float Widget - Admin Scripts
 */

(function($) {
	'use strict';

	$(document).ready(function() {

		// Inicializar color picker
		$('.cfw-color-picker').wpColorPicker();

		// Índices para secciones e items
		let sectionIndex = $('#cfw-sections-container .cfw-section').length;

		/**
		 * Agregar nueva sección
		 */
		$(document).on('click', '.cfw-add-section', function(e) {
			e.preventDefault();
			
			// Obtener template
			let template = $('#cfw-section-template').html();
			
			// Reemplazar índice
			template = template.replace(/\{\{INDEX\}\}/g, sectionIndex);
			
			// Agregar al contenedor
			$('#cfw-sections-container').append(template);
			
			sectionIndex++;
		});

		/**
		 * Eliminar sección
		 */
		$(document).on('click', '.cfw-remove-section', function(e) {
			e.preventDefault();
			
			if (confirm(cfwAdmin.confirmDelete)) {
				$(this).closest('.cfw-section').remove();
			}
		});

		/**
		 * Agregar nuevo item
		 */
		$(document).on('click', '.cfw-add-item', function(e) {
			e.preventDefault();
			
			const sectionIndex = $(this).data('section-index');
			const itemsContainer = $(this).siblings('.cfw-items-container');
			const itemIndex = itemsContainer.find('.cfw-item-wrapper').length;
			
			// Obtener template
			let template = $('#cfw-item-template').html();
			
			// Reemplazar índices
			template = template.replace(/\{\{SECTION_INDEX\}\}/g, sectionIndex);
			template = template.replace(/\{\{ITEM_INDEX\}\}/g, itemIndex);
			
			// Agregar al contenedor
			itemsContainer.append(template);
		});

		/**
		 * Eliminar item
		 */
		$(document).on('click', '.cfw-remove-item', function(e) {
			e.preventDefault();
			
			if (confirm(cfwAdmin.confirmDelete)) {
				$(this).closest('.cfw-item-wrapper').remove();
			}
		});

		/**
		 * Cambiar tipo de item (mostrar/ocultar campos específicos)
		 */
		$(document).on('change', '.cfw-item-type', function() {
			const type = $(this).val();
			const itemWrapper = $(this).closest('.cfw-item-wrapper');
			
			// Ocultar todos los campos específicos
			itemWrapper.find('.cfw-item-field').hide();
			
			// Mostrar solo el campo correspondiente al tipo
			itemWrapper.find('.cfw-field-' + type).show();
		});

		/**
		 * Seleccionar icono personalizado
		 */
		$(document).on('click', '.cfw-select-icon', function(e) {
			e.preventDefault();
			
			const button = $(this);
			const itemWrapper = button.closest('.cfw-item-wrapper');
			const imageIdInput = itemWrapper.find('.cfw-icon-image-id');
			const imagePreview = itemWrapper.find('.cfw-icon-preview');
			const removeButton = itemWrapper.find('.cfw-remove-icon');
			
			// Abrir la librería de medios
			const mediaUploader = wp.media({
				title: cfwAdmin.selectImage,
				button: {
					text: cfwAdmin.useImage
				},
				multiple: false
			});
			
			// Cuando se selecciona una imagen
			mediaUploader.on('select', function() {
				const attachment = mediaUploader.state().get('selection').first().toJSON();
				
				// Actualizar el input oculto con el ID
				imageIdInput.val(attachment.id);
				
				// Mostrar preview
				imagePreview.attr('src', attachment.url).show();
				removeButton.show();
			});
			
			mediaUploader.open();
		});

		/**
		 * Eliminar icono personalizado
		 */
		$(document).on('click', '.cfw-remove-icon', function(e) {
			e.preventDefault();
			
			const button = $(this);
			const itemWrapper = button.closest('.cfw-item-wrapper');
			const imageIdInput = itemWrapper.find('.cfw-icon-image-id');
			const imagePreview = itemWrapper.find('.cfw-icon-preview');
			
			// Limpiar valores
			imageIdInput.val('');
			imagePreview.attr('src', '').hide();
			button.hide();
		});

		/**
		 * Drag and drop para reordenar secciones e items (opcional)
		 * Requeriría jQuery UI Sortable, que WordPress incluye
		 */
		if (typeof $.fn.sortable !== 'undefined') {
			// Hacer las secciones ordenables
			$('#cfw-sections-container').sortable({
				handle: '.cfw-section-header h3',
				placeholder: 'cfw-section-placeholder',
				cursor: 'move',
				opacity: 0.7
			});
			
			// Hacer los items ordenables dentro de cada sección
			$(document).on('mouseenter', '.cfw-items-container', function() {
				if (!$(this).hasClass('ui-sortable')) {
					$(this).sortable({
						handle: '.cfw-item-header strong',
						placeholder: 'cfw-item-placeholder',
						cursor: 'move',
						opacity: 0.7
					});
				}
			});
		}

		/**
		 * Validación del formulario antes de guardar
		 */
		$('form').on('submit', function(e) {
			let isValid = true;
			const errors = [];
			
			// Validar que cada sección tenga al menos un item
			$('.cfw-section').each(function() {
				const sectionTitle = $(this).find('input[name*="[title]"]').val();
				const itemsCount = $(this).find('.cfw-item-wrapper').length;
				
				if (sectionTitle && itemsCount === 0) {
					errors.push('La sección "' + sectionTitle + '" debe tener al menos un item.');
					isValid = false;
				}
			});
			
			// Validar que cada item tenga label
			$('.cfw-item-wrapper').each(function() {
				const label = $(this).find('input[name*="[label]"]').val();
				
				if (!label) {
					errors.push('Todos los items deben tener un label.');
					isValid = false;
					return false; // Salir del each
				}
			});
			
			if (!isValid) {
				alert(errors.join('\n'));
				e.preventDefault();
				return false;
			}
		});

		/**
		 * Mostrar mensaje de importación exitosa
		 */
		const urlParams = new URLSearchParams(window.location.search);
		if (urlParams.get('import') === 'success') {
			const message = $('<div class="notice notice-success is-dismissible"><p>Configuración importada correctamente.</p></div>');
			$('.wrap > h1').after(message);
			
			// Auto-dismiss después de 5 segundos
			setTimeout(function() {
				message.fadeOut();
			}, 5000);
		}

	});

})(jQuery);
