<?php

namespace Medialab\Service;

class User extends MedialabService {

	function __construct($client) {
		parent::__construct($client);
	}

	/**
	 * Get information about the user
	 * @return array
	 */
	public function getUserInfo() {
		return $this->execute('user/info', 'GET');
	}
}