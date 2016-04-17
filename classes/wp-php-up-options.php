<?php

class WP_PHP_UP_Options {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_options_menu' ) );

		$optIn = new WP_PHP_UP_OptIn();
		$optIn->getUserInput();
	}

	function add_options_menu() {
		add_options_page(
			__( 'PHP Upgrade Program', 'php-wp-upgrade-program' ),
			__( 'PHP Upgrade Program', 'php-wp-upgrade-program' ),
			'manage_options',
			'wp-php-upgrade-program',
			array( $this, 'handle_options_page' )
		);
	}

	function handle_options_page() {

		$optIn = new WP_PHP_UP_OptIn();
		$statistics = new WP_PHP_UP_Statistics();
		$collector = new WP_PHP_UP_Collector( $statistics );

		?>
		<div class="wrap">
			<h2><?php _e( 'WordPress PHP Upgrade Program', 'php-wp-upgrade-program' ); ?></h2>
			<?php echo $statistics->getNotification()->getMessage(); ?>
			<hr>
			<?php echo $collector->getNotification( false )->getMessage(); ?>
			<hr>
			<?php echo $optIn->getOptInText(); ?>

		</div>
		<?php
	}
}
