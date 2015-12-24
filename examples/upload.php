<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/authenticate.php';

$medialab_uri = '<YOUR_MEDIALAB_URI>';
$client_id = '<YOUR_CLIENT_ID>';
$client_secret = '<YOUR_CLIENT_SECRET>';
$redirect_uri = '<YOUR_REDIRECT_URI>';
$folder_id_target = 100;

$config = new Medialab\Config();
$config ->setMedialab($medialab_uri)
		->setClient($client_id, $client_secret)
		->setRedirectUri($redirect_uri)
		->addScope(Medialab\Scopes::SCOPE_UPLOAD);

$client = new Medialab\Client($config);

try {
	$token = ml_api_authenticate($client);
} catch (\InvalidArgumentException $ex) {
	print_r($ex->getMessage().PHP_EOL);
	die();
}

try {
	$service = new Medialab\Service\Upload($client);
	$service->startUpload();
	$file = $service->uploadFile($folder_id_target, __DIR__.'/../README.md');
	echo '<pre>';
	pre($file);
	echo '</pre>';
	$service->finishUpload();
} catch(\Exception $ex) {
	print_r('An error has occured while executing the API command: ' . $ex->getMessage());
}
