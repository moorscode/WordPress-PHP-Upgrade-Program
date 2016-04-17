<?php

class WP_PHP_UP_System_Identifier {
	public function getScrambledIdentifier() {
		return md5( home_url() . '::' . NONCE_SALT . '::' . DB_NAME );
	}
}
