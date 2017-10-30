<?php

namespace Medialab\Service;

/**
 * Class User
 * @package medialab-sdk-php
 *
 * User-related API methods.
 */
class User extends MedialabService {

	function __construct($config) {
		parent::__construct($config);
	}

	/**
	 * Get information about the user.
	 * @return array
	 */
	public function getUserInfo() {
		return $this->execute('user/info', 'GET');
	}
}