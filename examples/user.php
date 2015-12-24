<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/authenticate.php';

$medialab_uri = '<YOUR_MEDIALAB_URI>';
$client_id = '<YOUR_CLIENT_ID>';
$client_secret = '<YOUR_CLIENT_SECRET>';
$redirect_uri = '<YOUR_REDIRECT_URI>';

$config = new Medialab\Config();
$config ->setMedialab($medialab_uri)
		->setClient($client_id, $client_secret)
		->setRedirectUri($redirect_uri)
		->addScope(Medialab\Scopes::SCOPE_USER_INFO);


$client = new Medialab\Client($config);

try {
	$token = ml_api_authenticate($client);
} catch (\InvalidArgumentException $ex) {
	print_r($ex->getMessage().PHP_EOL);
	die();
}

try {
	$user = new Medialab\Service\User($client);
	$info = $user->getUserInfo();
	echo '<pre>';
	print_r($info);
	echo '</pre>';
} catch(Exception $ex) {
	print_r('An error has occured while executing the API command: ' . $ex->getMessage());
}
