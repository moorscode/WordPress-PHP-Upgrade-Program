<?php

class WP_PHP_UP_Collector implements WP_PHP_UP_Notifier_Interface {
	const OPTION_KEY = 'php_52_collector_sent';

	/** @var WP_PHP_UP_Statistics */
	private $statistics;

	/** @var WP_PHP_UP_System_Identifier */
	private $identifier;

	/** @var bool Has the data been sent this execution */
	private $sent_now = false;
	
	/** @var Error that was thrown in the remote request */
	private $error;

	/**
	 * WP_PHP_UP_Collector constructor.
	 *
	 * @param WP_PHP_UP_Statistics             $statistics
	 * @param WP_PHP_UP_System_Identifier|null $identifier
	 * @param WP_PHP_UP_Notifier|null          $notifier
	 */
	public function __construct( WP_PHP_UP_Statistics $statistics, WP_PHP_UP_System_Identifier $identifier = null, WP_PHP_UP_Notifier $notifier = null ) {

		$this->statistics = $statistics;
		$this->identifier = is_null( $identifier ) ? new WP_PHP_UP_System_Identifier() : $identifier;

		if ( $this->sent() ) {
			return;
		}

		$this->send();

		if ( $notifier ) {
			$notifier->add( $this->statistics );
			$notifier->add( $this );
		}
	}

	/**
	 * Have we sent our version yet?
	 * 
	 * @return bool
	 */
	public function sent() {
		$last_php_version = get_option( self::OPTION_KEY, false );

		return ( $last_php_version === $this->statistics->PHPVersion() );
	}

	/**
	 * Send the data
	 */
	public function send() {

		/**
		 * @todo add collection request
		 */
		// Build args...

		$args   = array( 'hoi' );
		$response = wp_remote_get( 'https://keen.io', $args );
		
		if ( $response && ! is_wp_error($response)) {
			update_option( self::OPTION_KEY, $this->statistics->PHPVersion() );
			$this->sent_now = true;	
		}
		else {
			$this->error = $response;
		}
	}

	/**
	 * Get the notification
	 * 
	 * @param bool $get_success Always get the success message.
	 *
	 * @return WP_PHP_UP_Notification|void
	 */
	public function getNotification( $get_success = false ) {
		if ( ! $this->sent() ) {
			return $this->getFailureNotification();
		}

		if ( $get_success || $this->sent_now ) {
			return $this->getSuccessNotification();
		}
	}

	/**
	 * @return WP_PHP_UP_Notification
	 */
	private function getSuccessNotification() {
		/**
		 * @todo add a link to the page of the statistics results / overview
		 */
		$output = '';
		$output .= __( 'The statistics about your server have been sent successfully!', 'wp-php-upgrade-program' );
		$output .= '<br>';
		$output .= __( 'Thank you for allowing us to collect your information, stay informed about the project at ##insert link here##', 'wp-php-upgrade-program' );

		return new WP_PHP_UP_Notification( 'success', '<p>' . $output . '</p>' );
	}

	/**
	 * @return WP_PHP_UP_Notification
	 */
	private function getFailureNotification() {
		/**
		 * @todo add reason of failed, communication problem?
		 */
		$output = __( 'The statistics of your server could not be sent.', 'wp-php-upgrade-program' );

		return new WP_PHP_UP_Notification( 'error', '<p>' . $output . '</p>' );
	}
}
