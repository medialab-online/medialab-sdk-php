<?php

namespace Medialab\OAuth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

class MedialabProvider extends AbstractProvider {
	public $uidKey = 'user_id';

	public $medialabUri;

	function __construct($options = []) {
		parent::__construct($options);

		$this->authorizationHeader = 'Bearer ';
	}

	public function prepareRequest(AccessToken $token, $http_method, $api_method, $options = array(), $body = null)  {
        $headers = $this->getHeaders($token);
		$client = $this->getHttpClient();
		$client->setBaseUrl($this->createUrl($api_method));
		$client->setDefaultOption('headers', $headers);

		if(isset($options['curl'])) {
			// work-around for missing features in old guzzle version
			$client->getConfig()->set('curl.options', $options['curl']);
		}

		switch(strtoupper($http_method)) {
			case 'GET':
				$request = $client->get(null, null, $options);
				break;
			case 'HEAD':
				$request = $client->head(null, null, $options);
				break;
			case 'DELETE':
				$request = $client->delete(null, null, $body, $options);
				break;
			case 'PUT':
				$request = $client->put(null, null, $body, $options);
				break;
			case 'PATCH':
				$request = $client->patch(null, null, $body, $options);
				break;
			case 'POST':
				$request = $client->post(null, null, $body, $options);
				break;
			case 'OPTIONS':
				$request = $client->options(null, $options);
				break;
		}

		return $request;
    }
	public function execute(AccessToken $token, $http_method, $api_method, $options = array(), $body = null)  {
		$request = $this->prepareRequest($token, $http_method, $api_method, $options, $body);
		return $request->send();
    }

	public function urlAuthorize() {
		return $this->createUrl('oauth2/authorize');
	}

	public function urlAccessToken() {
		return $this->createUrl('oauth2/token');
	}

	public function urlUserDetails(AccessToken $token) {

	}

	public function userDetails($response, AccessToken $token) {

	}

	/**
	 * Create API url
	 * @param string $api_method
	 * @return string
	 * @throws \LogicException
	 */
	public function createUrl($api_method) {
		if(empty($this->medialabUri)) {
			throw new \LogicException('No MediaLab URI given.');
		}
		return $this->medialabUri . $api_method;
	}
}