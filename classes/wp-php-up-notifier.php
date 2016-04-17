<?php

class WP_PHP_UP_Notifier {

	private $notifications = array();

	/**
	 * @param WP_PHP_UP_Notifier_Interface $subject Subject to get a notification from.
	 */
	public function add( WP_PHP_UP_Notifier_Interface $subject ) {
		$notification = $subject->getNotification();
		if ( empty( $notification ) ) {
			return;
		}

		if ( empty( $this->notifications ) ) {
			add_action( 'admin_notices', array( $this, 'display_notification' ) );
		}

		$this->notifications[] = $notification;
	}

	/**
	 * Show the notifications with admin notices
	 */
	public function display_notification() {
		if ( empty( $this->notifications ) ) {
			return;
		}

		$notifications = array_map( array( $this, 'format_notification' ), $this->notifications );

		echo implode( PHP_EOL, $notifications );
	}

	private function format_notification( WP_PHP_UP_Notification $notification ) {
		return sprintf(
			'<div class="wp-php-upgrade-program__notification notice notice-%1$s"><h3>%3$s</h3>%2$s</div>',
			$notification->getType(),
			$notification->getMessage(),
			__( 'WordPress PHP Upgrade Program', 'wp-php-upgrade-program' )
		);
	}
}
