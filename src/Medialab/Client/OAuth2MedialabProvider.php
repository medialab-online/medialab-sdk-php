<?php

namespace Medialab\Client;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

/**
 * Class OAuth2MedialabProvider
 * @package medialab-sdk-php
 *
 * This is a MediaLab-specific implementation for the League\OAuth2 client library.
 */
class OAuth2MedialabProvider extends AbstractProvider {
	/**
	 * @var string Key used in the access token response to identify the resource owner.
	 */
	const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'user_id';

	/**
	 * @var array
	 */
	protected $scopes = array();

	/**
	 * @var string
	 */
	private $api_url;

	public function __construct(array $options = [], array $collaborators = []) {
		parent::__construct($options, $collaborators);
	}

	/**
	 * Create API url
	 * @param string $api_url
	 * @return OAuth2MedialabProvider
	 */
	public function setMedialabApiURL(string $api_url): OAuth2MedialabProvider {
		$this->api_url = $api_url;
		return $this;
	}

	public function getAuthorizationUrl(array $options = []) {
		// this one is a tough one, it's impossible to provide the scopes and state anywhere else
		// than in this options array,
		$options['state'] = $this->getState();

		if(!empty($this->scopes)) {
			$options['scope'] = $this->scopes;
		}

		return parent::getAuthorizationUrl($options);

	}

	protected function checkResponse(\Psr\Http\Message\ResponseInterface $response, $data) {
	}

	protected function createResourceOwner(array $response, AccessToken $token) {
	}

	protected function getDefaultScopes() {
		return \Medialab\Scopes::SCOPE_USER_INFO;
	}

	public function getBaseAccessTokenUrl(array $params) {
		return $this->getMedialabApiURL('oauth2/token');
	}

	public function getBaseAuthorizationUrl() {
		return $this->getMedialabApiURL('oauth2/authorize');
	}

	public function getResourceOwnerDetailsUrl(AccessToken $token) {
		return $this->getMedialabApiURL('user/info');

	}

	protected function getAuthorizationHeaders($token = null) {
		return array('Authorization' => 'Bearer ' . $token);
	}

	protected function getScopeSeparator() {
		return ' ';
	}

	/**
	 * Generate URL to the API.
	 * @param string $api_method
	 * @return string
	 */
	private function getMedialabApiURL(string $api_method): string {
		return $this->api_url . $api_method;
	}
}