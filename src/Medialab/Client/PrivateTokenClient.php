<?php

namespace Medialab\Client;

use GuzzleHttp\Exception\GuzzleException;

/**
 * Class PrivateTokenClient
 * @package medialab-sdk-php
 *
 * A client to handle requests that are authorized using a private token.
 */
class PrivateTokenClient implements ClientInterface {

	/**
	 * @var \Medialab\Config\PrivateTokenConfig
	 */
	private $config;

	public function __construct(\Medialab\Config\PrivateTokenConfig $config) {
		$this->config = $config;
	}

	/**
	 * Prepare a new HTTP request.
	 *
	 * @param string $url
	 * @param string $http_method
	 * @param array $options
	 * @return \Psr\Http\Message\RequestInterface
	 */
	public function prepareRequest(string $url, string $http_method, array $options = []): \Psr\Http\Message\RequestInterface {
		$headers = isset($options['headers']) ? $options['headers'] : [];

		return $this->getRequest($http_method, $url, $headers);
	}

	/**
	 * Execute a new HTTP request and return its response.
	 *
	 * @param string $url
	 * @param string $http_method
	 * @param array $options
	 * @return \Psr\Http\Message\ResponseInterface
	 * @throws \Exception
	 */
	public function executeRequest(string $url, string $http_method, array $options = []): \Psr\Http\Message\ResponseInterface {
		if(!isset($options['headers'])) {
			$options['headers'] = [];
		}
		if(!isset($options['headers']['Authorization'])) {
			$options['headers']['Authorization'] = $this->getAuthorizationHeader();
		}

		try {
			return $this->getHttpClient()->request($http_method, $url, $options);
		} catch(GuzzleException $ex) {
			throw new \Exception($ex->getMessage());
		}
	}

	/**
	 * Get guzzle http client
	 * @return \GuzzleHttp\ClientInterface
	 */
	public function getHttpClient(): \GuzzleHttp\ClientInterface {
		return $this->config->getHttpClient();
	}

	/**
	 * Get authorization header for private token
	 * @return string
	 */
	private function getAuthorizationHeader(): string {
		return 'Private-Token ' . $this->config->getPrivateToken();
	}

	/**
	 * Creates a PSR-7 Request instance.
	 *
	 * @param  null|string $method HTTP method for the request.
	 * @param  null|string $uri URI for the request.
	 * @param  array $headers Headers for the message.
	 * @param  string|resource|StreamInterface $body Message body.
	 * @param  string $version HTTP protocol version.
	 *
	 * @return \Psr\Http\Message\RequestInterface
	 */
	private function getRequest(string $method, string $uri, array $headers = [], $body = null, $version = '1.1'): \Psr\Http\Message\RequestInterface {

		$headers['Authorization'] = $this->getAuthorizationHeader();

		return new \GuzzleHttp\Psr7\Request($method, $uri, $headers, $body, $version);
	}
}