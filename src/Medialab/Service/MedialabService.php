<?php

namespace Medialab\Service;

use Guzzle\Http\Exception\BadResponseException;

class MedialabService {

	/**
	 * @var \Medialab\Client $client
	 */
	protected $client;

	function __construct(\Medialab\Client $client) {
		$this->client = $client;
	}

	/**
	 * Prepare an API call and returns the Request object
	 * @param string $api_method
	 * @param string $http_method
	 * @param array $options
	 * @param mixed $body
	 * @param boolean $parse
	 * @return mixed
	 * @throws \Exception
	 */
    public function prepare($api_method, $http_method, $options = array(), $body = null, $parse = true)  {
		try {
			$request = $this->getProvider()->prepareRequest(
				$this->client->getAccessToken(),
				$http_method,
				$api_method,
				$options,
				$body
			);
		} catch(\Guzzle\Http\Exception\ClientErrorResponseException $ex) {
			throw new \Exception($ex->getMessage());
		}

		return $request;
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
		try {
			$request = $this->getProvider()->execute(
				$this->client->getAccessToken(),
				$http_method,
				$api_method,
				$options,
				$body
			);
		} catch(\Guzzle\Http\Exception\ClientErrorResponseException $ex) {
			throw new \Exception($ex->getMessage());
		}

		if($parse) {
			return $this->parse($request);
		}
		return $request;
    }

	public function parse(\Guzzle\Http\Message\Response $response) {
		try {
			$response_string = $response->getBody();
		} catch (BadResponseException $ex) {
			throw new \Exception($ex->getMessage());
		}
		return $this->parseResponse($response_string);
	}

	/**
	 * Get the Medialab Provider
	 * @return \Medialab\Oauth\MedialabProvider
	 */
	public function getProvider() {
		return $this->client->getConfig()->getProvider();
	}

	/**
	 * Parse response from API
	 * @param string $response
	 * @return mixed
	 * @throws \Exception
	 */
	protected function parseResponse($response) {
		switch ($this->getProvider()->responseType) {
			 case 'json':
				$result = json_decode($response, true);
				break;
			 case 'string':
				parse_str($response, $result);
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