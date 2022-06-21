<?php
/**
 * Plugin Name: Valu Cookie Plugin
 * Version: 0.1.0
 * Plugin URI: https://www.valu.fi
 * Description: List cookies used on site via Cookiebot API.
 * Author: Valu Digital Oy
 * Author URI: https://www.valu.rocks
 * Requires at least: 5.7
 * Tested up to: 5.7.3
 *
 * Text Domain: valu-cookie-plugin
 * Domain Path: /lang
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load plugin class files.
require_once 'includes/class-valu-cookie-plugin.php';
require_once 'includes/class-valu-cookie-plugin-settings.php';

// Load plugin libraries.
require_once 'includes/class-valu-cookie-dashboard.php';

/**
 * Returns the main instance of Valu_Cookie_Plugin to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Valu_Cookie_Plugin
 */
function valu_cookie_plugin() {
	$instance = Valu_Cookie_Plugin::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Valu_Cookie_Plugin_Settings::instance( $instance );
	}

	return $instance;
}
valu_cookie_plugin();

Valu\Valu_Cookieplugin_Dashboard::get_instance();

if (class_exists('WP_CLI')) {
	WP_CLI::add_command( 'valu-cookies', array('Valu_Cookie_Plugin','cliCommand') );
}