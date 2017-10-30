<?php

namespace Medialab\Client;

/**
 * Interface ClientInterface
 * @package medialab-sdk-php
 *
 * An interface for clients that are compatible with a MedialabService.
 */
interface ClientInterface {

	/**
	 * Prepare a new HTTP request.
	 *
	 * @param string $url
	 * @param string $http_method
	 * @param array $options
	 * @return \Psr\Http\Message\RequestInterface
	 */
	public function prepareRequest(string $url, string $http_method, array $options = []): \Psr\Http\Message\RequestInterface;

	/**
	 * Execute a new HTTP request and return its response.
	 *
	 * @param string $url
	 * @param string $http_method
	 * @param array $options
	 * @return \Psr\Http\Message\ResponseInterface
	 * @throws \Exception
	 */
	public function executeRequest(string $url, string $http_method, array $options = []): \Psr\Http\Message\ResponseInterface;

}