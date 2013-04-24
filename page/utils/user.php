<?php

require_once('config.php');
require_once('openid.php');
require_once('database.php');
require_once('fb/facebook.php');

function get_login($return_url)
{
	global $url_base;
	
	$params = array('namePerson/first');
	
	$openid_google = new LightOpenID($url_base);
    $openid_google->identity = 'https://www.google.com/accounts/o8/id';
    $openid_google->required = $params;
    $openid_google->returnUrl = $return_url;
    
    $openid_yahoo = new LightOpenID($url_base);
    $openid_yahoo->identity = 'http://me.yahoo.com/';
    $openid_yahoo->required = $params;
    $openid_yahoo->returnUrl = $return_url;
    
    echo '<a href="utils/loginfacebook.php?return_url=' . urlencode($return_url) . '">Log in with Facebook</a><br />';
    echo '<a href="' . $openid_google->authUrl() . '">Sign in with a Google Account</a><br />';
	echo '<a href="' . $openid_yahoo->authUrl() . '">Sign in with Yahoo!</a><br />';
	echo '<form action="utils/loginopenid.php" method="post">';
	echo '<input type="hidden" name="return_url" value="' . $return_url . '" />';
	echo 'OpenID: <input type="text" name="openid" />';
	echo '<button>Sign in</button>';
	echo '</form>';
}

function check_login($return_url, $page, $ask_login)
{
	global $url_base, $fb_app_id, $fb_secret;
	
	$error = FALSE;

	if (isset($_GET["guest"]))
	{
		return;
	}
	
	if (isset($_SESSION['user_id']))
	{
		return;
	}
	
	if (isset($_POST['new_user']))
	{
		$user_id = add_user($_POST['type'], $_POST['id'], $_POST['name']);
		if ($user_id > 0)
		{
			$_SESSION['user_id'] = $user_id;
			$_SESSION['user_name'] = $_POST['name'];
			return;
		}
		else
		{
			$error = TRUE;
		}
	}
	else if (isset($_GET["fb_login"]))
	{
		$facebook = new Facebook(array(
			'appId'  => $fb_app_id,
			'secret' => $fb_secret
		));
		$fbUser = $facebook->getUser();
		if ($fbUser)
		{
			$user = get_user('F', $fbUser);
			if ($user != null)
			{
				set_user($user);
				return;
			}
			$login_name = "unknown";
			try
			{
				$user_profile = $facebook->api('/me');
				$login_name = $user_profile['name'];
			}
			catch (FacebookApiException $e)
			{
			}
			header("Location: newuser.php?return_url=".urlencode($return_url)."&type=F&id=".urlencode($fbUser)."&page=".$page."&login_name=".urlencode($login_name));
			exit();
		}
		else
		{
			$error = TRUE;
		}
	}
	else
	{
		$openid = new LightOpenID($url_base);
		if ($openid->mode)
		{
			if ($openid->validate())
			{
				$user = get_user('O', $openid->identity);
				if ($user != null)
				{
					set_user($user);
					return;
				}
				$redirect_url = "newuser.php?return_url=".urlencode($return_url)."&type=O&id=".urlencode($openid->identity)."&page=".$page;
				$attrs = $openid->getAttributes();
				if ($attrs['namePerson'])
				{
					$redirect_url .= "&login_name=".urlencode($attrs['namePerson']);
				}
				else if ($attrs['namePerson/first'])
				{
					$redirect_url .= "&login_name=".urlencode($attrs['namePerson/first']);
				}
				header("Location: ".$redirect_url);
				exit();
			}
		}
	}

	if ($error)
	{
		//error
		header("Location: ".$url_base);
		exit();
	}

	if ($ask_login)
	{
		header("Location: asklogin.php?return_url=".urlencode($return_url)."&page=".$page);
		exit();
	}
}

function set_user($user)
{
	$_SESSION['user_id'] = $user->user_id;
	$_SESSION['user_name'] = $user->name;
	$_SESSION['user_admin'] = $user->admin;
}

?>