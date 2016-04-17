<?php

class WP_PHP_UP_System_Identifier {
	
	/**
	 * @return string
	 */
	public function getScrambledIdentifier() {
		return md5( md5( home_url() . '::' . ABSPATH . '::' . DB_NAME ) . '::' . NONCE_SALT );
	}
}
