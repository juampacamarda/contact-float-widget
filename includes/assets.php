<?php
/**
 * Gestión de assets (CSS y JS)
 *
 * @package ContactFloatWidget
 */

// Evitar acceso directo
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clase para gestionar la carga de CSS y JavaScript
 */
class CFW_Assets {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Cargar assets en el frontend
	 */
	public function enqueue_frontend_assets() {
		// CSS del widget
		wp_enqueue_style(
			'cfw-widget',
			CFW_PLUGIN_URL . 'assets/css/widget.css',
			array(),
			CFW_VERSION,
			'all'
		);

		// JavaScript del widget
		wp_enqueue_script(
			'cfw-widget',
			CFW_PLUGIN_URL . 'assets/js/widget.js',
			array(),
			CFW_VERSION,
			true
		);

		// Pasar configuración al JavaScript
		$settings = get_option( 'cfw_settings', array() );
		wp_localize_script(
			'cfw-widget',
			'cfwConfig',
			array(
				'primaryColor' => isset( $settings['primary_color'] ) ? $settings['primary_color'] : '#25d366',
				'side' => isset( $settings['side'] ) ? $settings['side'] : 'right',
				'offsetTop' => isset( $settings['offset_top'] ) ? $settings['offset_top'] : '110',
				'showOpen' => isset( $settings['show_open'] ) ? $settings['show_open'] : '0',
			)
		);
	}

	/**
	 * Cargar assets en el admin
	 */
	public function enqueue_admin_assets( $hook ) {
		// Solo cargar en la página de ajustes del plugin
		if ( 'settings_page_contact-float-widget' !== $hook ) {
			return;
		}

		// Cargar el color picker de WordPress
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		// Cargar la librería de medios de WordPress
		wp_enqueue_media();

		// CSS del admin
		wp_enqueue_style(
			'cfw-admin',
			CFW_PLUGIN_URL . 'assets/css/admin.css',
			array( 'wp-color-picker' ),
			CFW_VERSION,
			'all'
		);

		// JavaScript del admin
		wp_enqueue_script(
			'cfw-admin',
			CFW_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery', 'wp-color-picker' ),
			CFW_VERSION,
			true
		);

		// Traducciones para JavaScript
		wp_localize_script(
			'cfw-admin',
			'cfwAdmin',
			array(
				'confirmDelete' => __( '¿Estás seguro de que deseas eliminar este elemento?', 'contact-float-widget' ),
				'selectImage' => __( 'Seleccionar imagen', 'contact-float-widget' ),
				'useImage' => __( 'Usar esta imagen', 'contact-float-widget' ),
			)
		);
	}
}
