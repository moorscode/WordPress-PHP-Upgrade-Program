<?php

class WP_PHP_UP_Notification {
	/** @var string */
	private $type;
	
	/** @var string */
	private $message;

	/**
	 * WP_PHP_UP_Notification constructor.
	 *
	 * @param string $type    Type of the notification.
	 * @param string $message Text of the notification.
	 */
	public function __construct( $type, $message ) {
		$this->type    = $type;
		$this->message = $message;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}
}
