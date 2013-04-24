<?php
require_once('config.php');

function create_thumbnail($work_id, $current_pieces, $max_pieces, $thumb_width, $thumb_height, $subpath)
{
	global $path_local, $path_works;
	
	$tmp_img = imagecreatetruecolor($thumb_width, $thumb_height);
	
	if ($current_pieces < $max_pieces)
	{
		$img = imagecreatefrompng("{$path_local}images/thumb_wip_bg.png");
		imagecopy($tmp_img, $img, 0, 0, 0, 0, $thumb_width, $thumb_height);
	}

	for ($piece = 1; $piece <= $current_pieces; $piece++)
	{
		$filename = "{$path_local}{$path_works}image_{$work_id}_{$piece}.png";
	
		$img = imagecreatefrompng($filename);
		$width = imagesx($img);
		$height = imagesy($img);
	
		imagecopyresampled($tmp_img, $img,
			0, ($piece - 1) * $thumb_height / $max_pieces,
			0, 0,
			$thumb_width, $thumb_height / $max_pieces + 1,
			$width, $height);
	}
	
	$save_filename = thumb_filename($work_id, $current_pieces, $max_pieces, $subpath);

	imagejpeg($tmp_img, $save_filename, 80);
}

function thumb_filename($work_id, $current_pieces, $max_pieces, $subpath)
{
	global $path_local;

	$save_filename = "{$path_local}{$subpath}image_{$work_id}";
	if ($current_pieces < $max_pieces)
	{
		$save_filename .= "_{$current_pieces}";
	}
	$save_filename .= ".jpg";
	
	return $save_filename;
}

?>