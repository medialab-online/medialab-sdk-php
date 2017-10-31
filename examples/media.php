<?php

require_once __DIR__ . '/authorize.php';

$config = new Medialab\Config\OAuth2Config(ML_MEDIALAB_URI);
$config ->setClient(ML_API_CLIENT, ML_API_SECRET)
		->setRedirectUri(ML_REDIRECT_URI)
		->addScope(Medialab\Scopes::SCOPE_BASIC)
		->addScope(Medialab\Scopes::SCOPE_SHARE)
		->addScope(Medialab\Scopes::SCOPE_MANAGE);

try {
	ml_api_authenticate($config, 'media.php');
} catch (\Exception $ex) {
	print_r($ex->getMessage());
	die();
}

$media = new Medialab\Service\Media($config);

try {
	$info = $media->getFolderContents();
	echo '<pre>';
	print_r($info);
	echo '</pre>';
} catch(Exception $ex) {
	print_r('An error has occured while executing the API command: ' . $ex->getMessage());
}
