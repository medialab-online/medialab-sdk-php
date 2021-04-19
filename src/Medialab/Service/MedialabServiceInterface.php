<?php

namespace Medialab\Service;

/**
 * Class MedialabService
 * @package medialab-sdk-php
 */
interface MedialabServiceInterface {

	/**
	 * Prepare a new request
	 * @param string $api_method
	 * @param string $http_method
	 * @param array $options request options compatible with GuzzleHttp client,
	 *              e.g. 'headers', 'query' (GET vars), 'form_params' (POST vars), 'timeout', ...
	 * @param mixed $body
	 * @return \Psr\Http\Message\RequestInterface
	 */
	public function prepare(string $api_method, string $http_method, array $options = [], $body = null): \Psr\Http\Message\RequestInterface;

	/**
	 * Execute an API call
	 * @param string $api_method
	 * @param string $http_method
	 * @param array $options request options compatible with GuzzleHttp client,
	 *              e.g. 'headers', 'query' (GET vars), 'form_params' (POST vars), 'timeout', ...
	 * @param mixed $body
	 * @param boolean $parse
	 * @return mixed
	 * @throws \Exception
	 */
	public function execute(string $api_method, string $http_method, array $options = [], $body = null, bool $parse = true);

	/**
	 * Execute an API call
	 * @param string $url
	 * @param string $http_method
	 * @param array $options request options compatible with GuzzleHttp client,
	 *              e.g. 'headers', 'query' (GET vars), 'form_params' (POST vars), 'timeout', ...
	 * @param mixed $body
	 * @param boolean $parse
	 * @return mixed
	 * @throws \Exception
	 */
	public function executeURL(string $url, string $http_method, array $options = [], $body = null, bool $parse = true);

	/**
	 * Parse guzzle response
	 * @param \Psr\Http\Message\ResponseInterface $response
	 * @return mixed
	 * @throws \Exception
	 */
	public function parse(\Psr\Http\Message\ResponseInterface $response);
}
