<?php
/**
 * Página de administración del plugin
 *
 * @package ContactFloatWidget
 */

// Evitar acceso directo
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clase para gestionar la página de ajustes del plugin
 */
class CFW_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_post_cfw_export_settings', array( $this, 'export_settings' ) );
		add_action( 'admin_post_cfw_import_settings', array( $this, 'import_settings' ) );
	}

	/**
	 * Agregar página de ajustes al menú
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'Contact Float Widget', 'contact-float-widget' ),
			__( 'Contact Float Widget', 'contact-float-widget' ),
			'manage_options',
			'contact-float-widget',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Registrar ajustes
	 */
	public function register_settings() {
		register_setting(
			'cfw_settings_group',
			'cfw_settings',
			array( $this, 'sanitize_settings' )
		);
	}

	/**
	 * Sanitizar ajustes antes de guardar
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		// Color principal
		$sanitized['primary_color'] = isset( $input['primary_color'] ) ? sanitize_hex_color( $input['primary_color'] ) : '#25d366';

		// Lado
		$sanitized['side'] = isset( $input['side'] ) && in_array( $input['side'], array( 'left', 'right' ) ) ? $input['side'] : 'right';

		// Offset top
		$sanitized['offset_top'] = isset( $input['offset_top'] ) ? absint( $input['offset_top'] ) : 110;

		// Mostrar abierto
		$sanitized['show_open'] = isset( $input['show_open'] ) ? '1' : '0';

		// Mostrar automáticamente
		$sanitized['auto_display'] = isset( $input['auto_display'] ) ? '1' : '0';

		// Secciones
		$sanitized['sections'] = array();
		if ( isset( $input['sections'] ) && is_array( $input['sections'] ) ) {
			foreach ( $input['sections'] as $section ) {
				$sanitized_section = array(
					'title' => isset( $section['title'] ) ? sanitize_text_field( $section['title'] ) : '',
					'items' => array(),
				);

				if ( isset( $section['items'] ) && is_array( $section['items'] ) ) {
					foreach ( $section['items'] as $item ) {
						$sanitized_item = array(
							'type' => isset( $item['type'] ) && in_array( $item['type'], array( 'whatsapp', 'email', 'link' ) ) ? $item['type'] : 'whatsapp',
							'label' => isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '',
							'icon_image_id' => isset( $item['icon_image_id'] ) ? absint( $item['icon_image_id'] ) : 0,
						);

						// Campos específicos según el tipo
						if ( 'whatsapp' === $sanitized_item['type'] ) {
							$sanitized_item['phone'] = isset( $item['phone'] ) ? sanitize_text_field( $item['phone'] ) : '';
							$sanitized_item['prefill'] = isset( $item['prefill'] ) ? sanitize_textarea_field( $item['prefill'] ) : '';
						} elseif ( 'email' === $sanitized_item['type'] ) {
							$sanitized_item['address'] = isset( $item['address'] ) ? sanitize_email( $item['address'] ) : '';
						} elseif ( 'link' === $sanitized_item['type'] ) {
							$sanitized_item['url'] = isset( $item['url'] ) ? esc_url_raw( $item['url'] ) : '';
						}

						$sanitized_section['items'][] = $sanitized_item;
					}
				}

				$sanitized['sections'][] = $sanitized_section;
			}
		}

		return $sanitized;
	}

	/**
	 * Renderizar la página de ajustes
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'No tienes permisos suficientes para acceder a esta página.', 'contact-float-widget' ) );
		}

		$settings = get_option( 'cfw_settings', array() );
		$primary_color = isset( $settings['primary_color'] ) ? $settings['primary_color'] : '#25d366';
		$side = isset( $settings['side'] ) ? $settings['side'] : 'right';
		$offset_top = isset( $settings['offset_top'] ) ? $settings['offset_top'] : 110;
		$show_open = isset( $settings['show_open'] ) ? $settings['show_open'] : '0';
		$auto_display = isset( $settings['auto_display'] ) ? $settings['auto_display'] : '0';
		$sections = isset( $settings['sections'] ) ? $settings['sections'] : array();
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'cfw_settings_group' );
				wp_nonce_field( 'cfw_settings_action', 'cfw_settings_nonce' );
				?>

				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="cfw_primary_color"><?php esc_html_e( 'Color principal', 'contact-float-widget' ); ?></label>
						</th>
						<td>
							<input type="text" id="cfw_primary_color" name="cfw_settings[primary_color]" value="<?php echo esc_attr( $primary_color ); ?>" class="cfw-color-picker" />
							<p class="description"><?php esc_html_e( 'Color para el botón y títulos del widget.', 'contact-float-widget' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="cfw_side"><?php esc_html_e( 'Lado', 'contact-float-widget' ); ?></label>
						</th>
						<td>
							<select id="cfw_side" name="cfw_settings[side]">
								<option value="right" <?php selected( $side, 'right' ); ?>><?php esc_html_e( 'Derecha', 'contact-float-widget' ); ?></option>
								<option value="left" <?php selected( $side, 'left' ); ?>><?php esc_html_e( 'Izquierda', 'contact-float-widget' ); ?></option>
							</select>
							<p class="description"><?php esc_html_e( 'Posición del widget en la pantalla.', 'contact-float-widget' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="cfw_offset_top"><?php esc_html_e( 'Offset top (px)', 'contact-float-widget' ); ?></label>
						</th>
						<td>
							<input type="number" id="cfw_offset_top" name="cfw_settings[offset_top]" value="<?php echo esc_attr( $offset_top ); ?>" min="0" step="1" />
							<p class="description"><?php esc_html_e( 'Distancia desde la parte superior de la pantalla.', 'contact-float-widget' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="cfw_show_open"><?php esc_html_e( 'Mostrar abierto al cargar', 'contact-float-widget' ); ?></label>
						</th>
						<td>
							<label>
								<input type="checkbox" id="cfw_show_open" name="cfw_settings[show_open]" value="1" <?php checked( $show_open, '1' ); ?> />
								<?php esc_html_e( 'Sí', 'contact-float-widget' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Si está marcado, el widget se mostrará abierto al cargar la página.', 'contact-float-widget' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="cfw_auto_display"><?php esc_html_e( 'Mostrar automáticamente', 'contact-float-widget' ); ?></label>
						</th>
						<td>
							<label>
								<input type="checkbox" id="cfw_auto_display" name="cfw_settings[auto_display]" value="1" <?php checked( $auto_display, '1' ); ?> />
								<?php esc_html_e( 'Sí, mostrar en todo el sitio automáticamente', 'contact-float-widget' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Si está marcado, el widget se mostrará automáticamente en todas las páginas. Si está desmarcado, deberás usar el shortcode [contact_float_widget] manualmente.', 'contact-float-widget' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label><?php esc_html_e( 'Secciones', 'contact-float-widget' ); ?></label>
						</th>
						<td>
							<div id="cfw-sections-container">
								<?php
								if ( ! empty( $sections ) ) {
									foreach ( $sections as $section_index => $section ) {
										$this->render_section_fields( $section_index, $section );
									}
								}
								?>
							</div>
							<button type="button" class="button cfw-add-section"><?php esc_html_e( 'Agregar sección', 'contact-float-widget' ); ?></button>
							<p class="description"><?php esc_html_e( 'Organiza tus contactos en secciones.', 'contact-float-widget' ); ?></p>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>

			<hr>

			<h2><?php esc_html_e( 'Importar / Exportar configuración', 'contact-float-widget' ); ?></h2>

			<h3><?php esc_html_e( 'Exportar', 'contact-float-widget' ); ?></h3>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="cfw_export_settings" />
				<?php wp_nonce_field( 'cfw_export_action', 'cfw_export_nonce' ); ?>
				<p>
					<button type="submit" class="button"><?php esc_html_e( 'Exportar configuración (JSON)', 'contact-float-widget' ); ?></button>
				</p>
			</form>

			<h3><?php esc_html_e( 'Importar', 'contact-float-widget' ); ?></h3>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
				<input type="hidden" name="action" value="cfw_import_settings" />
				<?php wp_nonce_field( 'cfw_import_action', 'cfw_import_nonce' ); ?>
				<p>
					<input type="file" name="cfw_import_file" accept=".json" required />
				</p>
				<p>
					<button type="submit" class="button"><?php esc_html_e( 'Importar configuración (JSON)', 'contact-float-widget' ); ?></button>
				</p>
			</form>

			<hr>

			<h2><?php esc_html_e( 'Uso del shortcode', 'contact-float-widget' ); ?></h2>
			<p><?php esc_html_e( 'Copia este shortcode para mostrar el widget en cualquier página o entrada:', 'contact-float-widget' ); ?></p>
			<code>[contact_float_widget]</code>
		</div>

		<!-- Template para nueva sección -->
		<script type="text/html" id="cfw-section-template">
			<?php $this->render_section_fields( '{{INDEX}}', array() ); ?>
		</script>

		<!-- Template para nuevo item -->
		<script type="text/html" id="cfw-item-template">
			<?php $this->render_item_fields( '{{SECTION_INDEX}}', '{{ITEM_INDEX}}', array() ); ?>
		</script>
		<?php
	}

	/**
	 * Renderizar campos de una sección
	 */
	private function render_section_fields( $section_index, $section ) {
		$title = isset( $section['title'] ) ? $section['title'] : '';
		$items = isset( $section['items'] ) ? $section['items'] : array();
		?>
		<div class="cfw-section" data-section-index="<?php echo esc_attr( $section_index ); ?>">
			<div class="cfw-section-header">
				<h3><?php esc_html_e( 'Sección', 'contact-float-widget' ); ?></h3>
				<button type="button" class="button cfw-remove-section"><?php esc_html_e( 'Eliminar sección', 'contact-float-widget' ); ?></button>
			</div>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label><?php esc_html_e( 'Título de la sección', 'contact-float-widget' ); ?></label>
					</th>
					<td>
						<input type="text" name="cfw_settings[sections][<?php echo esc_attr( $section_index ); ?>][title]" value="<?php echo esc_attr( $title ); ?>" class="regular-text" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label><?php esc_html_e( 'Items', 'contact-float-widget' ); ?></label>
					</th>
					<td>
						<div class="cfw-items-container">
							<?php
							if ( ! empty( $items ) ) {
								foreach ( $items as $item_index => $item ) {
									$this->render_item_fields( $section_index, $item_index, $item );
								}
							}
							?>
						</div>
						<button type="button" class="button cfw-add-item" data-section-index="<?php echo esc_attr( $section_index ); ?>"><?php esc_html_e( 'Agregar item', 'contact-float-widget' ); ?></button>
					</td>
				</tr>
			</table>
		</div>
		<?php
	}

	/**
	 * Renderizar campos de un item
	 */
	private function render_item_fields( $section_index, $item_index, $item ) {
		$type = isset( $item['type'] ) ? $item['type'] : 'whatsapp';
		$label = isset( $item['label'] ) ? $item['label'] : '';
		$icon_image_id = isset( $item['icon_image_id'] ) ? $item['icon_image_id'] : 0;
		$icon_image_url = $icon_image_id ? wp_get_attachment_url( $icon_image_id ) : '';
		?>
		<div class="cfw-item-wrapper" data-item-index="<?php echo esc_attr( $item_index ); ?>">
			<div class="cfw-item-header">
				<strong><?php esc_html_e( 'Item', 'contact-float-widget' ); ?></strong>
				<button type="button" class="button cfw-remove-item"><?php esc_html_e( 'Eliminar', 'contact-float-widget' ); ?></button>
			</div>

			<p>
				<label><?php esc_html_e( 'Tipo:', 'contact-float-widget' ); ?></label>
				<select name="cfw_settings[sections][<?php echo esc_attr( $section_index ); ?>][items][<?php echo esc_attr( $item_index ); ?>][type]" class="cfw-item-type">
					<option value="whatsapp" <?php selected( $type, 'whatsapp' ); ?>><?php esc_html_e( 'WhatsApp', 'contact-float-widget' ); ?></option>
					<option value="email" <?php selected( $type, 'email' ); ?>><?php esc_html_e( 'Email', 'contact-float-widget' ); ?></option>
					<option value="link" <?php selected( $type, 'link' ); ?>><?php esc_html_e( 'Link', 'contact-float-widget' ); ?></option>
				</select>
			</p>

			<p>
				<label><?php esc_html_e( 'Label:', 'contact-float-widget' ); ?></label><br>
				<input type="text" name="cfw_settings[sections][<?php echo esc_attr( $section_index ); ?>][items][<?php echo esc_attr( $item_index ); ?>][label]" value="<?php echo esc_attr( $label ); ?>" class="regular-text" />
			</p>

			<!-- Campos específicos para WhatsApp -->
			<div class="cfw-item-field cfw-field-whatsapp" style="display: <?php echo 'whatsapp' === $type ? 'block' : 'none'; ?>;">
				<p>
					<label><?php esc_html_e( 'Teléfono (E.164):', 'contact-float-widget' ); ?></label><br>
					<input type="text" name="cfw_settings[sections][<?php echo esc_attr( $section_index ); ?>][items][<?php echo esc_attr( $item_index ); ?>][phone]" value="<?php echo isset( $item['phone'] ) ? esc_attr( $item['phone'] ) : ''; ?>" class="regular-text" placeholder="5491138637868" />
					<span class="description"><?php esc_html_e( 'Ejemplo: 5491138637868', 'contact-float-widget' ); ?></span>
				</p>
				<p>
					<label><?php esc_html_e( 'Mensaje prellenado:', 'contact-float-widget' ); ?></label><br>
					<textarea name="cfw_settings[sections][<?php echo esc_attr( $section_index ); ?>][items][<?php echo esc_attr( $item_index ); ?>][prefill]" class="large-text" rows="3"><?php echo isset( $item['prefill'] ) ? esc_textarea( $item['prefill'] ) : ''; ?></textarea>
				</p>
			</div>

			<!-- Campos específicos para Email -->
			<div class="cfw-item-field cfw-field-email" style="display: <?php echo 'email' === $type ? 'block' : 'none'; ?>;">
				<p>
					<label><?php esc_html_e( 'Dirección de email:', 'contact-float-widget' ); ?></label><br>
					<input type="email" name="cfw_settings[sections][<?php echo esc_attr( $section_index ); ?>][items][<?php echo esc_attr( $item_index ); ?>][address]" value="<?php echo isset( $item['address'] ) ? esc_attr( $item['address'] ) : ''; ?>" class="regular-text" />
				</p>
			</div>

			<!-- Campos específicos para Link -->
			<div class="cfw-item-field cfw-field-link" style="display: <?php echo 'link' === $type ? 'block' : 'none'; ?>;">
				<p>
					<label><?php esc_html_e( 'URL:', 'contact-float-widget' ); ?></label><br>
					<input type="url" name="cfw_settings[sections][<?php echo esc_attr( $section_index ); ?>][items][<?php echo esc_attr( $item_index ); ?>][url]" value="<?php echo isset( $item['url'] ) ? esc_url( $item['url'] ) : ''; ?>" class="regular-text" />
				</p>
			</div>

			<p>
				<label><?php esc_html_e( 'Icono personalizado (imagen):', 'contact-float-widget' ); ?></label><br>
				<input type="hidden" name="cfw_settings[sections][<?php echo esc_attr( $section_index ); ?>][items][<?php echo esc_attr( $item_index ); ?>][icon_image_id]" class="cfw-icon-image-id" value="<?php echo esc_attr( $icon_image_id ); ?>" />
				<img src="<?php echo esc_url( $icon_image_url ); ?>" class="cfw-icon-preview" style="max-width: 50px; <?php echo $icon_image_url ? '' : 'display:none;'; ?>" />
				<button type="button" class="button cfw-select-icon"><?php esc_html_e( 'Seleccionar imagen', 'contact-float-widget' ); ?></button>
				<button type="button" class="button cfw-remove-icon" style="<?php echo $icon_image_url ? '' : 'display:none;'; ?>"><?php esc_html_e( 'Eliminar', 'contact-float-widget' ); ?></button>
				<span class="description"><?php esc_html_e( 'Si no se selecciona imagen, se usará un SVG por defecto.', 'contact-float-widget' ); ?></span>
			</p>
		</div>
		<?php
	}

	/**
	 * Exportar configuración como JSON
	 */
	public function export_settings() {
		// Verificar permisos y nonce
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'No tienes permisos suficientes.', 'contact-float-widget' ) );
		}

		check_admin_referer( 'cfw_export_action', 'cfw_export_nonce' );

		// Obtener configuración
		$settings = get_option( 'cfw_settings', array() );

		// Generar JSON
		$json = wp_json_encode( $settings, JSON_PRETTY_PRINT );

		// Enviar headers para descarga
		header( 'Content-Type: application/json' );
		header( 'Content-Disposition: attachment; filename="contact-float-widget-settings-' . date( 'Y-m-d' ) . '.json"' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		echo $json;
		exit;
	}

	/**
	 * Importar configuración desde JSON
	 */
	public function import_settings() {
		// Verificar permisos y nonce
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'No tienes permisos suficientes.', 'contact-float-widget' ) );
		}

		check_admin_referer( 'cfw_import_action', 'cfw_import_nonce' );

		// Verificar que se subió un archivo
		if ( ! isset( $_FILES['cfw_import_file'] ) || UPLOAD_ERR_OK !== $_FILES['cfw_import_file']['error'] ) {
			wp_die( __( 'Error al subir el archivo.', 'contact-float-widget' ) );
		}

		// Leer el archivo
		$file_content = file_get_contents( $_FILES['cfw_import_file']['tmp_name'] );
		$settings = json_decode( $file_content, true );

		// Verificar que sea JSON válido
		if ( null === $settings || ! is_array( $settings ) ) {
			wp_die( __( 'El archivo no es un JSON válido.', 'contact-float-widget' ) );
		}

		// Sanitizar y guardar
		$sanitized_settings = $this->sanitize_settings( $settings );
		update_option( 'cfw_settings', $sanitized_settings );

		// Redirigir con mensaje de éxito
		wp_redirect( add_query_arg(
			array(
				'page' => 'contact-float-widget',
				'import' => 'success',
			),
			admin_url( 'options-general.php' )
		) );
		exit;
	}
}
