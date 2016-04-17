<?php

class WP_PHP_UP_Notifier {

	private $notifications = array();

	public function __construct() {
		add_action( 'admin_notices', array( $this, 'display_notification' ) );
	}

	public function add( WP_PHP_UP_Notifier_Interface $subject ) {
		$result = $subject->getNotification();
		if ( empty( $result ) ) {
			return;
		}

		$this->notifications[] = sprintf( '<div class="php-52-upgrade-program__notification notice notice-%s"><h3>WordPress PHP Upgrade Program</h3>%s</div>', $result->getType(), $result->getMessage() );
	}

	public function display_notification() {
		if ( ! $this->notifications ) {
			return;
		}

		echo implode( PHP_EOL, $this->notifications );
	}
}
