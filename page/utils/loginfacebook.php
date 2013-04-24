<?php

require_once('config.php');
require_once('fb/facebook.php');

$returnUrl = $_GET["return_url"];

$facebook = new Facebook(array(
  'appId'  => $fb_app_id,
  'secret' => $fb_secret
));

$needsLogin = TRUE;

// Get User ID
$user = $facebook->getUser();

if ($user)
{
	try
	{
		// Proceed knowing you have a logged in user who's authenticated.
		$userProfile = $facebook->api('/me');
		$needsLogin = FALSE;
	}
	catch (FacebookApiException $e)
	{
	}
}

$query = parse_url($returnUrl, PHP_URL_QUERY);
if ($query)
{
    $returnUrl .= '&';
}
else
{
    $returnUrl .= '?';
}
$returnUrl .= 'fb_login=1';

if ($needsLogin)
{
	$params = array(
		'redirect_uri' => $returnUrl
	);

	$loginUrl = $facebook->getLoginUrl($params);

	header('Location: ' . $loginUrl);
}
else
{
	header('Location: ' . $returnUrl);
}

?>