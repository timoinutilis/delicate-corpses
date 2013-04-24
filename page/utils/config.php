<?php

$mysql_host = "localhost";
$mysql_user = "root";
$mysql_password = "root";
$mysql_database = "corpses";

$fb_app_id = "375233962561898";
$fb_secret = "2236e8c1dfa3335e0c62f9a330d38635";

$url_base = "http://localhost:8888/";

$path_local = "/Users/timokloss/projects/corpses/page/";
$path_works = "works/";
$path_thumbs = "works/thumbs/";
$path_previews = "works/previews/";

$palette_num_colors = 16;
$palettes = array(
	'1' => array('#fff', '#000'),
	'2' => array('#fff', '#ddd', '#bbb', '#999', '#777', '#555', '#333', '#000'),
	'3' => array('#fff', '#000', '#f00', '#fa0', '#ff0', '#af0', '#0f0', '#0ff', '#0af', '#00f', '#a0f', '#f0f'),
	'4' => array('#f5f4f0', '#4fccd9', '#d43580', '#f44d00', '#f67e5c', '#d92b2b', '#077584', '#8f2c76', '#b3d01e', '#3177e6', '#fea381', '#517c34', '#1c2f4f', '#fcd000'),
	'5' => array('#ffffff', '#e7bac5', '#c24868', '#570218', '#774256', '#b07f95', '#633956', '#8a5c7f', '#f2b1a8', '#ffe4d6', '#eeedfe', '#c8caf2', '#987bd0', '#68478e'),
	'6' => array('#ffffff', '#fff0c0', '#f9bf51', '#f19945', '#c06e40', '#9b5f3f', '#ddd2ce', '#a69d9a', '#c86655', '#a1455b', '#7b494d', '#303144', '#4a6575', '#adc9db', '#663f55', '#573050'),
	'7' => array('#f7eedb', '#d6cdbb', '#b8b0a4', '#858175', '#57524c', '#262521', '#000000'),
	'8' => array('#ffffff', '#e0eae1', '#b5cac3', '#6695ad', '#577ba4', '#4c0c4a', '#712d63', '#ad7595', '#ffe1d4', '#fff4d0', '#ffd5bb', '#b37188', '#783c62', '#f3ef95', '#b7a65f', '#3b0238'),
);
$pencils = array(3, 5, 7, 9, 13, 19, 25, 34, 43, 52);

$work_width = 800;
$work_height = 1120;
$connection_size = 30;

$lock_refresh_minutes = 1;
$max_lock_minutes = 30;

$cookie_domain = "delicatecorpses.com";
$cookie_path = "/";
$creator_cookie_seconds = 30 * 24 * 60 * 60;

$thumb_width = 160;
$thumb_height = 224;
$preview_width = 480;
$preview_height = 672;

$gallery_works_per_page = 20;
$home_num_works = 5;
$works_per_row = 5;

$password = 110123;

$admin_default_days = 7;

?>