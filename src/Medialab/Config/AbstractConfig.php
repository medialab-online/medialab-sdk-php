<?php

namespace Medialab\Config;

/**
 * Class AbstractConfig
 * @package medialab-sdk-php
 *
 * Shared config code.
 */
abstract class AbstractConfig implements ConfigInterface {

	/**
	 * @var \GuzzleHttp\ClientInterface
	 */
	private $http_client;

	/**
	 * @var array
	 */
	private $http_client_options = [];

	/**
	 * @var $url
	 */
	private $url;

	public function __construct() {
	}

	/**
	 * Set the full URL to the MediaLab channel (e.g. https://example.medialab.co)
	 * @param string $url
	 * @return ConfigInterface
	 */
	protected function setURL(string $url): ConfigInterface {
		// make sure we have an ending slash
		$url = rtrim($url, '/') . '/';

		if (strpos($url, 'api/') === false) {
			$url .= 'api/';
		}
		$this->url = $url;
		return $this;
	}

	/**
	 * Get API URL.
	 * @return string
	 */
	public function getURL(): string {
		return $this->url;
	}

	/**
	 * Enable or disable SSL verification (enabled by default).
	 * @param bool $value
	 * @return ConfigInterface
	 */
	public function setHttpVerifySSL(bool $value): ConfigInterface {
		$this->http_client_options['verify'] = $value;
		return $this;
	}

	/**
	 * Set HTTP client.
	 * @param \GuzzleHttp\ClientInterface $http_client
	 * @return ConfigInterface
	 */
	public function setHttpClient(\GuzzleHttp\ClientInterface $http_client): ConfigInterface {
		$this->http_client = $http_client;
		return $this;
	}

	/**
	 * Get instance of the HTTP client.
	 * @return \GuzzleHttp\ClientInterface
	 */
	public function getHttpClient(): \GuzzleHttp\ClientInterface {
		if($this->http_client === null) {
			$this->http_client = new \GuzzleHttp\Client($this->http_client_options);
		}
		return $this->http_client;
	}
}