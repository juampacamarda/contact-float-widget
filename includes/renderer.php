<?php
/**
 * Renderizado del widget
 *
 * @package ContactFloatWidget
 */

// Evitar acceso directo
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clase para renderizar el widget en el frontend
 */
class CFW_Renderer {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_shortcode( 'contact_float_widget', array( $this, 'render_widget' ) );
		
		// Si está activada la visualización automática, agregar al footer
		add_action( 'wp_footer', array( $this, 'auto_display_widget' ) );
	}

	/**
	 * Mostrar el widget automáticamente si está activada la opción
	 */
	public function auto_display_widget() {
		$settings = get_option( 'cfw_settings', array() );
		$auto_display = isset( $settings['auto_display'] ) ? $settings['auto_display'] : '0';
		
		// Solo mostrar si está activada la opción
		if ( '1' === $auto_display ) {
			echo $this->render_widget( array() );
		}
	}

	/**
	 * Renderizar el widget
	 */
	public function render_widget( $atts ) {
		// Obtener configuración
		$settings = get_option( 'cfw_settings', array() );
		$primary_color = isset( $settings['primary_color'] ) ? $settings['primary_color'] : '#25d366';
		$side = isset( $settings['side'] ) ? $settings['side'] : 'right';
		$offset_top = isset( $settings['offset_top'] ) ? $settings['offset_top'] : '110';
		$show_open = isset( $settings['show_open'] ) ? $settings['show_open'] : '0';
		$sections = isset( $settings['sections'] ) ? $settings['sections'] : array();

		// Si no hay secciones, no renderizar nada
		if ( empty( $sections ) ) {
			return '';
		}

		// Clases CSS
		$widget_classes = array( 'cfw-widget' );
		$widget_classes[] = 'cfw-widget--' . esc_attr( $side );
		if ( '1' === $show_open ) {
			$widget_classes[] = 'is-open';
		}

		// Iniciar output buffering
		ob_start();
		?>
		<div class="<?php echo esc_attr( implode( ' ', $widget_classes ) ); ?>" style="--primary: <?php echo esc_attr( $primary_color ); ?>; top: <?php echo esc_attr( $offset_top ); ?>px;">
			<button class="cfw-toggle" aria-expanded="<?php echo '1' === $show_open ? 'true' : 'false'; ?>" aria-controls="cfw-panel">
				<span class="cfw-icon" aria-hidden="true"><?php echo $this->get_default_icon( 'toggle' ); ?></span>
				<span><?php esc_html_e( 'Contactar', 'contact-float-widget' ); ?></span>
			</button>
			<div id="cfw-panel" class="cfw-panel" role="region" <?php echo '1' !== $show_open ? 'hidden' : ''; ?>>
				<div class="cfw-panel-body">
					<?php foreach ( $sections as $section ) : ?>
						<?php if ( ! empty( $section['title'] ) ) : ?>
							<h4 class="cfw-section-title"><?php echo esc_html( $section['title'] ); ?></h4>
						<?php endif; ?>

						<?php if ( ! empty( $section['items'] ) ) : ?>
							<?php foreach ( $section['items'] as $item ) : ?>
								<?php echo $this->render_item( $item ); ?>
							<?php endforeach; ?>
						<?php endif; ?>

						<hr class="cfw-hr">
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Renderizar un item individual
	 */
	private function render_item( $item ) {
		$type = isset( $item['type'] ) ? $item['type'] : 'whatsapp';
		$label = isset( $item['label'] ) ? $item['label'] : '';
		$icon_image_id = isset( $item['icon_image_id'] ) ? $item['icon_image_id'] : 0;

		// Construir URL según el tipo
		$url = '';
		$target = '_blank';
		$rel = 'noopener noreferrer';

		if ( 'whatsapp' === $type ) {
			$phone = isset( $item['phone'] ) ? $item['phone'] : '';
			$prefill = isset( $item['prefill'] ) ? $item['prefill'] : '';
			if ( $phone ) {
				$url = 'https://api.whatsapp.com/send?phone=' . urlencode( $phone );
				if ( $prefill ) {
					$url .= '&text=' . urlencode( $prefill );
				}
			}
		} elseif ( 'email' === $type ) {
			$address = isset( $item['address'] ) ? $item['address'] : '';
			if ( $address ) {
				$url = 'mailto:' . esc_attr( $address );
				$target = '_self';
				$rel = '';
			}
		} elseif ( 'link' === $type ) {
			$url = isset( $item['url'] ) ? $item['url'] : '';
		}

		// Si no hay URL, no renderizar
		if ( empty( $url ) ) {
			return '';
		}

		// Icono
		$icon_html = '';
		if ( $icon_image_id ) {
			$icon_url = wp_get_attachment_url( $icon_image_id );
			if ( $icon_url ) {
				$icon_html = '<div class="cfw-item__media" style="background-image: url(' . esc_url( $icon_url ) . ');"></div>';
			}
		}

		// Si no hay icono personalizado, usar SVG por defecto
		if ( empty( $icon_html ) ) {
			$icon_html = '<div class="cfw-item__media">' . $this->get_default_icon( $type ) . '</div>';
		}

		// Construir HTML del item
		ob_start();
		?>
		<a href="<?php echo esc_url( $url ); ?>" class="cfw-item" target="<?php echo esc_attr( $target ); ?>" <?php echo $rel ? 'rel="' . esc_attr( $rel ) . '"' : ''; ?>>
			<?php echo $icon_html; ?>
			<div class="cfw-item__text">
				<h5><?php echo esc_html( $label ); ?></h5>
			</div>
		</a>
		<?php
		return ob_get_clean();
	}

	/**
	 * Obtener icono SVG por defecto según el tipo
	 */
	private function get_default_icon( $type ) {
		$icons = array(
			'whatsapp' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>',
			'email' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>',
			'link' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>',
			'toggle' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="26" height="26"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 9h12v2H6V9zm8 5H6v-2h8v2zm4-6H6V6h12v2z"/></svg>',
		);

		return isset( $icons[ $type ] ) ? $icons[ $type ] : $icons['link'];
	}
}