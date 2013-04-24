<?php

require_once('config.php');
require_once('openid.php');

if (isset($_POST['openid']))
{
	$openid = new LightOpenID($url_base);
	$openid->identity = $_POST['openid'];
	$openid->returnUrl = $_POST['return_url'];
	$openid->required = array('namePerson', 'namePerson/first');
	header('Location: ' . $openid->authUrl());
}

?>