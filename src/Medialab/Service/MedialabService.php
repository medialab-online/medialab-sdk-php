<?php

namespace Medialab\Service;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\BadResponseException;

class MedialabService {

	/**
	 * @var \Medialab\Client $client
	 */
	protected $client;

	/**
	 * @var string $response_type
	 */
	protected $response_type = 'json';

	function __construct(\Medialab\Config $config) {
		$this->client = $config->getClient();
	}

	/**
	 * Prepare a Guzzle request
	 * @param string $api_method
	 * @param string $http_method
	 * @param array $options
	 * @param mixed $body
	 * @return \GuzzleHTTP\Psr7\Request
	 */
    public function prepare($api_method, $http_method, $options = array(), $body = null)  {
		if($body !== null) {
			$options['body'] = $body;
		}

		return $this->client->getProvider()->getAuthenticatedRequest(
			$http_method,
			$this->client->getProvider()->createUrl($api_method),
			$this->client->getAccessToken(),
			$options
		);
    }

	/**
	 * Execute an API call
	 * @param string $api_method
	 * @param string $http_method
	 * @param array $options
	 * @param mixed $body
	 * @param boolean $parse
	 * @return mixed
	 * @throws \Exception
	 */
    public function execute($api_method, $http_method, $options = array(), $body = null, $parse = true)  {
		if($body !== null) {
			$options['body'] = $body;
		}
		// get authorization headers from oauth provider
		$options['headers'] = $this->client->getProvider()->getHeaders($this->client->getAccessToken());

		try {
			$response = $this->client->getProvider()->getHttpClient()->request(
				$http_method,
				$this->client->getProvider()->createUrl($api_method),
				$options
			);
		} catch(GuzzleException $ex) {
			throw new \Exception($ex->getMessage());
		}

		if($parse) {
			return $this->parse($response);
		}
		return $response;
    }

	/**
	 * Parse guzzle response
	 * @param \GuzzleHttp\Psr7\Response $response
	 * @return mixed
	 * @throws \Exception
	 */
	public function parse(\GuzzleHttp\Psr7\Response $response) {
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
}