<?php

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * The Private Token can be used if you want to use the API with your personal MediaLab account, or need to connect over CLI.
 * A new private token can be generated by logging in to your MediaLab account, browse to "Settings" > "Profile" > "API Access".
 *
 * Since the private token is linked to your personal account, it should never be shared with another party.
 * If you are building an app that requires access to someone else's account, please use the OAuth2 method.
 */

$api_config = new Medialab\Config\PrivateTokenConfig(
	'https://example.medialab.co',
	'PRIVATE_TOKEN_GOES_HERE'
);
$medialab = new Medialab\Service\MedialabService($api_config);

try {
	var_dump($medialab->execute('user/info', 'GET'));
} catch(\Exception $ex) {
	echo 'There was an error accessing the API: ' . $ex->getMessage();
}