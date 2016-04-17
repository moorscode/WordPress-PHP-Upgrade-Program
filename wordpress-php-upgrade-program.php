<?php
/**
Plugin Name: WordPress PHP upgrade program
Plugin URI: https://wordpress.org/plugins/wp-php-upgrade-program/
Description: Allows to collect statistics about the PHP version that is used on your webserver.
Version: 0.1.0
Author: Jip Moors
Author URI: https://yoast.com/about-us/jip-moors/
Text Domain: wp-php-upgrade-program
Domain Path: /languages
*/

if ( ! function_exists('add_action') ) {
	die();
}

add_action('plugins_loaded', array( 'WP_PHP_Upgrade_Program', 'initialise' ) );

class WP_PHP_Upgrade_Program {
	function __construct() {
		$this->loadTextDomain();
		$this->notifier = new WP_PHP_UP_Notifier();
	}

	private function loadTextDomain() {
		load_plugin_textdomain( 'wp-php-upgrade-program', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

	public static function initialise() {

		self::loadClasses();

		new WP_PHP_UP_Options();
		
		$instance = new self();
		add_action( 'admin_init', array( $instance, 'run' ) );
	}

	public function run() {
		$optIn = new WP_PHP_UP_OptIn( $this->notifier );
		if ( ! $optIn->success() ) {
			return;
		}

		new WP_PHP_UP_Collector( new WP_PHP_UP_Statistics(), new WP_PHP_UP_System_Identifier(), $this->notifier );
	}

	private static function loadClasses() {
		$classes = array(
			'notifier-interface',
			'collector',
			'notifier',
			'notification',
			'optin',
			'statistics',
			'system-identifier',
			'options',
		);

		array_map( array( __CLASS__, 'loadClass' ), $classes );
	}

	private static function loadClass( $class ) {
		require_once 'classes/wp-php-up-' . $class . '.php';
	}
}
