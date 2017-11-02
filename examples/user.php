<?php

require_once __DIR__ . '/authorize.php';

$config = new Medialab\Config\OAuth2Config(ML_MEDIALAB_URI);
$config ->setClient(ML_API_CLIENT, ML_API_SECRET)
		->setRedirectUri(ML_REDIRECT_URI)
		->addScope(Medialab\Scopes::SCOPE_USER_INFO);

try {
	ml_api_authenticate($config, 'user.php');
} catch (\Exception $ex) {
	print_r($ex->getMessage());
	die();
}

$user = new Medialab\Service\User($config);

try {
	$info = $user->getUserInfo();
	echo '<pre>';
	print_r($info);
	echo '</pre>';
} catch(Exception $ex) {
	print_r('An error has occured while executing the API command: ' . $ex->getMessage());
}
