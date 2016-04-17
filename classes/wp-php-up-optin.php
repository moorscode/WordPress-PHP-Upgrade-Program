<?php

class WP_PHP_UP_OptIn implements WP_PHP_UP_Notifier_Interface {
	const NONCE_ACTION = 'php_52_optin';
	const OPTION_KEY = 'php_upgrade_optin_success';
	const NONCE = 'wp-php-nonce';

	public function __construct( WP_PHP_UP_Notifier $notifier = null ) {

		if ( $this->saved() ) {
			return;
		}

		$this->getUserInput();

		if ( $notifier ) {
			$notifier->add( $this );
		}
	}

	public function getUserInput() {
		if ( ! empty( $_POST[ self::NONCE ] ) ) {
			$nonce = $_POST[ self::NONCE ];
			if ( wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
				$this->setSuccess( ! empty( $_POST['allowed'] ) && $_POST['allowed'] === 'on' );
			}

			wp_redirect( $_POST['_wp_http_referer'] );
			exit();
		}
	}

	public function success() {
		return '1' === get_option( self::OPTION_KEY, null );
	}

	public function saved() {
		$option = get_option( self::OPTION_KEY, null );
		if ( $option === '1' || $option === '0' ) {
			return true;
		}

		return false;
	}

	public function setSuccess( $allowed ) {
		update_option( self::OPTION_KEY, $allowed ? '1' : '0' );
	}

	public function getNotification() {
		if ( $this->success() ) {
			return;
		}

		return new WP_PHP_UP_Notification( 'info', $this->getOptInText() );
	}

	public function getOptInText() {

		$optIn = $this->success();
		$currentSetting = $optIn ? ' checked="checked" disabled="disabled"' : '';

		$output = '';

		if ( ! $optIn ) {
			$output .= '<form method="post">';
			$output .= wp_nonce_field( self::NONCE_ACTION, self::NONCE, true, false );
		}

		$output .= '<label><input type="checkbox" name="allowed" value="on"' . $currentSetting . ' /> ';
		$output .= sprintf( __( 'I want to participate in the %s.', 'wp-php-upgrade-program' ), __( 'WordPress PHP Upgrade Program', 'wp-php-upgrade-program' ) );
		$output .= '</label><br><br>';

		if ( ! $optIn ) {
			$output .= '<button type="submit" class="button button-primary button-large">';
			$output .= __( 'Save preference', 'wp-php-upgrade-program' );
			$output .= '</button><br><br>';
			$output .= '</form>';

			$output .= '<p>';
			$output .= __( 'The information will be send to keen.io and the results can be viewed ##here##.', 'wp-php-upgrade-program' );
			$output .= '</p>';
		}
		else {
			$output .= '<p>Thank you for helping!</p>';
			$output .= '<p>';
			$output .= __( 'The information is stored on keen.io and the results can be viewed ##here##.', 'wp-php-upgrade-program' );
			$output .= '</p>';
		}



		$output .= '<p><em>';
		$output .= __( 'We scramble any identifiable information, but we need to uniquely identifier the source of the sent statistics.', 'wp-php-upgrade-program' );
		$output .= '<br>';
		$output .= __( 'If we don\'t do that, information that is sent twice from the same server will be shown as two different servers, which will give a wrong idea of the situation.', 'wp-php-upgrade-program' );
		$output .= '</em></p>';

		return $output;
	}
}
