<?php

namespace Medialab\OAuth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

class MedialabProvider extends AbstractProvider {
	/**
	 * @var string Key used in the access token response to identify the resource owner.
	 */
	const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'user_id';

	protected $medialabUri;

	protected $scopes = array();

	public function __construct(array $options = [], array $collaborators = []) {
		parent::__construct($options, $collaborators);
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
		return $this->createUrl('oauth2/token');
	}

	public function getBaseAuthorizationUrl() {
		return $this->createUrl('oauth2/authorize');
	}

	public function getResourceOwnerDetailsUrl(AccessToken $token) {
		return $this->createUrl('user/info');

	}

	protected function getAuthorizationHeaders($token = null) {
		return array('Authorization' => 'Bearer ' . $token);
	}
}