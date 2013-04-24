<?php

require_once('config.php');
session_start();
	
define('PAGE_OTHER', '0');
define('PAGE_HOME', '1');
define('PAGE_NEWS', '2');
define('PAGE_GALLERY', '3');
define('PAGE_DRAW', '4');
define('PAGE_NEW', '5');
define('PAGE_LOGIN', '6');
define('PAGE_USER', '7');
define('PAGE_ADMIN', '8');

function get_header($page, $head_tags = "", $title = "")
{
	global $url_base;
	
	echo '<!DOCTYPE HTML>
<html>
<head>
<title>Delicate Corpses'.($title != "" ? " - {$title}" : "" ).'</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="title" content="Delicate Corpses'.($title != "" ? " - {$title}" : "" ).'" />
<meta name="description" content="Draw surrealistic pictures with other people or just enjoy other\'s works in the gallery." />
<link href="style.css" rel="stylesheet" type="text/css" />
'.$head_tags.'
</head>

<body>

<div id="container">

<nav>
<ul>
<a href="'.$url_base.'"><img src="images/logo.png" /></a>
<li '.($page == PAGE_NEWS ? 'class="current-page"' : '').'><a href="'.$url_base.'news.php">News</a></li>
<li '.($page == PAGE_GALLERY ? 'class="current-page"' : '').'><a href="'.$url_base.'gallery.php">Gallery</a></li>
<li '.($page == PAGE_DRAW ? 'class="current-page"' : '').'><a href="'.$url_base.'draw.php">Draw!</a></li>
<li '.($page == PAGE_NEW ? 'class="current-page"' : '').'><a href="'.$url_base.'new.php">New Work</a></li>';

	echo "<br />";
	if (isset($_SESSION['user_id']))
	{
		$user_name = $_SESSION['user_name'];
		echo '<li '.($page == PAGE_USER ? 'class="current-page"' : '').'><a href="'.$url_base.'usergallery.php">'.$user_name.'</a></li>';
		if ($_SESSION['user_admin'] > 0)
		{
			echo '<li '.($page == PAGE_ADMIN ? 'class="current-page"' : '').'><a href="'.$url_base.'admin.php">Admin</a></li>';
		}
		echo '<li><a href="'.$url_base.'logout.php">Log Out</a></li>';
	}
	else
	{
		echo '<li '.($page == PAGE_LOGIN ? 'class="current-page"' : '').'><a href="'.$url_base.'login.php">Log In</a></li>';
	}
	
echo '</ul>
</nav>

<div id="content">

';
}

function get_footer($end_body_tags = "")
{
	echo '<footer>
Website developed by <a href="http://www.inutilis.de">Inutilis</a>
</footer>

</div>

</div>

'. $end_body_tags .'
</body>
</html>';
}

function currentURL()
{
	$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
	return (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
}

function currentURLnoParams()
{
	$url = currentURL();
	$pos = strpos($url, "?");
	if ($pos === false)
	{
		return $url;
	}
	return substr($url, 0, $pos);
}

function timeText($sql_date)
{
	$time = strtotime($sql_date);
	$age = time() - $time;
	if ($age < 60)
	{
		return "some seconds ago";
	}
	else if ($age < 60 * 60)
	{
		$minutes = round($age / 60);
		return ($minutes > 1) ? $minutes." minutes ago" : "1 minute ago";
	}
	else if ($age < 24 * 60 * 60)
	{
		$hours = round($age / 60 / 60);
		return ($hours > 1) ? $hours." hours ago" : "1 hour ago";
	}
	else if ($age < 7 * 24 * 60 * 60)
	{
		$days = round($age / 60 / 60 / 24);
		return ($days > 1) ? $days." days ago" : "1 day ago";
	}
	
	return date('F j, Y', $time);
}

function get_palettes_js()
{
	global $palettes;
	
	echo '<script type="text/javascript">';
	echo 'var palettes = ' . json_encode($palettes) . ';';
	echo '</script>';
}

?>