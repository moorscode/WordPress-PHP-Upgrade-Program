<?php

interface WP_PHP_UP_Notifier_Interface {

	/**
	 * @return void|WP_PHP_UP_Notification
	 */
	public function getNotification();
}
