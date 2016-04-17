<?php

class WP_PHP_UP_Statistics implements WP_PHP_UP_Notifier_Interface {

	/**
	 * Get the PHP Version
	 * 
	 * @return string PHP Version
	 */
	public function PHPVersion() {
		static $php_version;

		if ( ! isset( $php_version ) ) {
			$php_version = phpversion();
		}

		return $php_version;
	}

	/**
	 * Get the notification
	 * 
	 * @return void|WP_PHP_UP_Notification
	 */
	public function getNotification() {

		$php_version = $this->PHPVersion();

		foreach ( $this->getRanges() as $type => $version ) {
			if ( version_compare( $php_version, $version, '<=' ) ) {
				return $this->generateNotification( $type );
			}
		}

		return $this->generateNotification( 'good' );
	}

	/**
	 * @param string $type Status of the version
	 *
	 * @return WP_PHP_UP_Notification
	 */
	private function generateNotification( $type ) {
		switch ( $type ) {
			case 'bad':
				$type = 'error';
				$format = __( 'Warning! Your server is running PHP version %s. This is a very old version!', 'wp-php-upgrade-program' );
				break;

			case 'acceptable':
				$type = 'warning';
				$format = __( 'Your server is running PHP version %s. This version is not the latest though acceptable.', 'wp-php-upgrade-program' );
				break;

			case 'good':
				$type = 'info';
				$format = __( 'Your server is running PHP version %s which is good enough for now!', 'wp-php-upgrade-program' );
				break;
		}

		return new WP_PHP_UP_Notification( $type, sprintf( '<p>' . $format . '</p>', '<code>' . $this->PHPVersion() . '</code>' ) );
	}

	/**
	 * The PHP versions that are bad and acceptable
	 * 
	 * @return array
	 */
	private function getRanges() {
		$ranges = array(
			'bad'        => '5.4',
			'acceptable' => '5.6',
		);

		return $ranges;
	}
}
