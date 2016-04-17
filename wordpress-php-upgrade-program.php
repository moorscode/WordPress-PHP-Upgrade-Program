<?php
/**
 * Plugin Name: WordPress PHP upgrade program
 * Plugin URI: https://wordpress.org/plugins/wp-php-upgrade-program/
 * Description: Allows to collect statistics about the PHP version that is used on your webserver.
 * Version: 0.1.0
 * Author: Jip Moors
 * Author URI: https://yoast.com/about-us/jip-moors/
 * Text Domain: wp-php-upgrade-program
 * Domain Path: /languages
 *
 * Requires: 2.7.0 (wp_remote_get)
 */

if ( ! function_exists( 'add_action' ) ) {
	die();
}

add_action( 'plugins_loaded', array( 'WP_PHP_Upgrade_Program', 'initialise' ) );

class WP_PHP_Upgrade_Program {

	/**
	 * WP_PHP_Upgrade_Program constructor.
	 */
	function __construct() {
		$this->loadTextDomain();
	}

	/**
	 * Load translations
	 */
	private function loadTextDomain() {
		load_plugin_textdomain( 'wp-php-upgrade-program', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Initialise the class
	 */
	public static function initialise() {

		self::loadClasses();

		new WP_PHP_UP_Options();

		add_action( 'admin_init', array( new self(), 'run' ) );
	}

	/**
	 * Run the program
	 */
	public function run() {
		$notifier = new WP_PHP_UP_Notifier();

		$optIn = new WP_PHP_UP_OptIn( $notifier );
		if ( ! $optIn->success() ) {
			return;
		}

		new WP_PHP_UP_Collector( new WP_PHP_UP_Statistics(), new WP_PHP_UP_System_Identifier(), $notifier );
	}

	/**
	 * Load required classes
	 */
	private static function loadClasses() {
		$classes = array(
			'notifier-interface',
			'collector',
			'notification',
			'notifier',
			'optin',
			'options',
			'statistics',
			'system-identifier',
		);

		array_map( array( __CLASS__, 'loadClass' ), $classes );
	}

	/**
	 * @param string $class Class to load.
	 */
	private static function loadClass( $class ) {
		require_once 'classes/wp-php-up-' . $class . '.php';
	}
}
