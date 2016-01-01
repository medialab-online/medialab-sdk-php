<?php

require_once __DIR__ . '/authorize.php';

$config = new Medialab\Config();
$config ->setMedialab(ML_MEDIALAB_URI)
		->setClient(ML_API_CLIENT, ML_API_SECRET)
		->setRedirectUri(ML_REDIRECT_URI)
		->addScope(Medialab\Scopes::SCOPE_BASIC)
		->addScope(Medialab\Scopes::SCOPE_UPLOAD);

try {
	ml_api_authenticate($config, 'upload.php');
} catch (\Exception $ex) {
	print_r($ex->getMessage());
	die();
}

$media = new Medialab\Service\Media($config);

try {
	$media->startUpload();
	$file = $media->uploadFile(1, __DIR__.'/../README.md');
	$media->finishUpload();
	echo '<pre>';
	print_r($file);
	echo '</pre>';
} catch(Exception $ex) {
	print_r('An error has occured while executing the API command: ' . $ex->getMessage());
}
