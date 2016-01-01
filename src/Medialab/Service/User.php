<?php

namespace Medialab\Service;

class User extends MedialabService {

	function __construct($config) {
		parent::__construct($config);
	}

	/**
	 * Get information about the user
	 * @return array
	 */
	public function getUserInfo() {
		return $this->execute('user/info', 'GET');
	}
}