<?php

namespace Medialab\Config;

/**
 * Class PrivateTokenConfig
 * @package medialab-sdk-php
 *
 * Config class for authentication using the PrivateToken.
 */
class PrivateTokenConfig extends AbstractConfig {

	/**
	 * @var \Medialab\Client\PrivateTokenClient
	 */
	private $client;

	/**
	 * @var string
	 */
	private $private_token;

	function __construct(string $medialab_url, string $privateToken) {
		parent::__construct();

		$this->setURL($medialab_url);
		$this->private_token = $privateToken;
	}

	/**
	 * Get the private token
	 * @return string
	 */
	public function getPrivateToken(): string {
		return $this->private_token;
	}

	/**
	 * Get client
	 * @return \Medialab\Client\PrivateTokenClient
	 */
	public function getClient(): \Medialab\Client\PrivateTokenClient {
		if($this->client === null) {
			$this->client = new \Medialab\Client\PrivateTokenClient($this);
		}
		return $this->client;
	}
}