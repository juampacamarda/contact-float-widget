<?php
/**
 * Script de desinstalación
 * Se ejecuta cuando el plugin es desinstalado desde WordPress
 *
 * @package ContactFloatWidget
 */

// Si no se llama desde WordPress, salir
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Eliminar todas las opciones del plugin
 */
function cfw_uninstall() {
	// Eliminar la opción principal de configuración
	delete_option( 'cfw_settings' );
	
	// Si hay un sitio multisite, eliminar de todos los sitios
	if ( is_multisite() ) {
		global $wpdb;
		
		// Obtener todos los IDs de blogs
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
		
		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			delete_option( 'cfw_settings' );
			restore_current_blog();
		}
	}
}

// Ejecutar la función de desinstalación
cfw_uninstall();
