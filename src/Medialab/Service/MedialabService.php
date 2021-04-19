<?php

namespace Medialab\Service;
use GuzzleHttp\Exception\BadResponseException;

/**
 * Class MedialabService
 * @package medialab-sdk-php
 *
 * Service class to execute requests against the API.
 * Depending on the loaded config, the requests will be authenticated accordingly (either OAuth2 or Private Token).
 */
class MedialabService implements MedialabServiceInterface {

	/**
	 * @var \Medialab\Config\ConfigInterface
	 */
	private $config;

	/**
	 * @var string $response_type
	 */
	private $response_type = 'json';

	function __construct(\Medialab\Config\ConfigInterface $config) {
		$this->config = $config;
	}

	/**
	 * Prepare a new request
	 * @param string $api_method
	 * @param string $http_method
	 * @param array $options request options compatible with GuzzleHttp client,
	 *              e.g. 'headers', 'query' (GET vars), 'form_params' (POST vars), 'timeout', ...
	 * @param mixed $body
	 * @return \Psr\Http\Message\RequestInterface
	 */
    public function prepare(string $api_method, string $http_method, array $options = [], $body = null): \Psr\Http\Message\RequestInterface {
		if($body !== null) {
			$options['body'] = $body;
		}

		return $this->getClient()->prepareRequest(
			$this->generateURL($api_method),
			$http_method,
			$options
		);
    }

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
    public function execute(string $api_method, string $http_method, array $options = [], $body = null, bool $parse = true)  {
		return $this->executeURL($this->generateURL($api_method), $http_method, $options, $body, $parse);
    }

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
    public function executeURL(string $url, string $http_method, array $options = [], $body = null, bool $parse = true)  {
		if($body !== null) {
			$options['body'] = $body;
		}

		$response = $this->getClient()->executeRequest(
			$url,
			$http_method,
			$options
		);

		if($parse) {
			return $this->parse($response);
		}

		return $response;
    }

	/**
	 * Parse guzzle response
	 * @param \Psr\Http\Message\ResponseInterface $response
	 * @return mixed
	 * @throws \Exception
	 */
	public function parse(\Psr\Http\Message\ResponseInterface $response) {
		try {
			$response_string = $response->getBody();
		} catch (BadResponseException $ex) {
			throw new \Exception($ex->getMessage());
		}

		switch ($this->response_type) {
			 case 'json':
				$result = json_decode($response_string, true);
				break;
			 case 'string':
				parse_str($response_string, $result);
				break;
			 default:
				 $result = '';
				 break;
		 }

		if (isset($result['error']) && ! empty($result['error'])) {
			throw new \Exception($result['error']);
		}
		return $result;
	}

	/**
	 * Get instance of the loaded config object.
	 * @return \Medialab\Config\ConfigInterface
	 */
	protected function getConfig(): \Medialab\Config\ConfigInterface {
		return $this->config;
	}

	/**
	 * Generate a full URL to the given API method.
	 * @param string $api_method
	 * @return string
	 */
	private function generateURL(string $api_method): string {
		return $this->config->getURL() . $api_method;
	}

	/**
	 * Get the client.
	 * @return \Medialab\Client\ClientInterface
	 */
	private function getClient(): \Medialab\Client\ClientInterface {
		return $this->config->getClient();
	}
}
