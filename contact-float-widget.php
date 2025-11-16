<?php
/**
 * Plugin Name: Contact Float Widget
 * Description: Widget flotante de contacto configurable con WhatsApp, email y enlaces personalizados.
 * Version: 1.0.0
 * Author: Juampa Camarda
 * Text Domain: contact-float-widget
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Evitar acceso directo
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Definir constantes del plugin
define( 'CFW_VERSION', '1.0.0' );
define( 'CFW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CFW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CFW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Cargar clases del plugin
 */
require_once CFW_PLUGIN_DIR . 'includes/assets.php';
require_once CFW_PLUGIN_DIR . 'includes/admin.php';
require_once CFW_PLUGIN_DIR . 'includes/renderer.php';

/**
 * Inicializar el plugin
 */
function cfw_init() {
	// Cargar archivos de traducción
	load_plugin_textdomain( 'contact-float-widget', false, dirname( CFW_PLUGIN_BASENAME ) . '/languages' );
	
	// Inicializar clases
	new CFW_Assets();
	new CFW_Admin();
	new CFW_Renderer();
}
add_action( 'plugins_loaded', 'cfw_init' );

/**
 * Activación del plugin
 */
function cfw_activate() {
	// Configuración por defecto
	$default_options = array(
		'primary_color' => '#25d366',
		'side' => 'right',
		'offset_top' => '110',
		'show_open' => '0',
		'auto_display' => '1', // Activado por defecto
		'sections' => array(),
	);
	
	if ( ! get_option( 'cfw_settings' ) ) {
		add_option( 'cfw_settings', $default_options );
	}
}
register_activation_hook( __FILE__, 'cfw_activate' );
