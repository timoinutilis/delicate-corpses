<?php
require_once('config.php');
require_once('database.php');
require_once('thumbs.php');

if (isset($_POST['image_data']) && $_POST['image_data'] != "")
{
	$data = $_POST['image_data'];
	$work_id = $_POST['work_id'];
	$piece = $_POST['piece'];
	$pieces = $_POST['pieces'];
	$creator = NULL;
	$user_id = NULL;
	if (isset($_POST['creator']))
	{
		$creator = $_POST['creator'];
		setcookie("user_name", $creator, time() + $creator_cookie_seconds, $cookie_path, $cookie_domain);
	}
	if (isset($_POST['user_id']))
	{
		$user_id = $_POST['user_id'];
	}
	
	if (substr($data, 0, 14) != "data:image/png")
	{
		echo "Invalid Image Format.";
	}
	else
	{
		$imageData = substr($data, 22, strlen($data) - 22);

		$finalImageData = base64_decode($imageData);
		
		$filename = "{$path_local}{$path_works}image_{$work_id}_{$piece}.png";

		$handle = fopen($filename, 'wb');
		if ($handle)
		{
			if (fwrite($handle, $finalImageData) === FALSE)
			{
				echo "Cannot write to file ($filename)";
			}
			else
			{
				add_piece($work_id, $piece, $creator, $user_id);
				
				// WIP thumbnail
				create_thumbnail($work_id, $piece, $pieces, $thumb_width, $thumb_height, $path_thumbs);

				if ($piece == $pieces)
				{
					// work finished
					create_thumbnail($work_id, $piece, $pieces, $preview_width, $preview_height, $path_previews);
				}
				
				// delete old WIP thumb
				if ($piece > 1)
				{
					$old_thumb = thumb_filename($work_id, $piece - 1, $pieces, $path_thumbs);
					if (file_exists($old_thumb))
					{
						unlink($old_thumb);
					}
				}
				
				$encrypt_id = encrypt_int($work_id);
				header("Location: ../show.php?work_id={$encrypt_id}");
			}
			fclose($handle);
		}
		else
		{
			echo "Cannot open file ($filename)";
		}
	}
}
else
{
	echo "There is no data.";
}
?>