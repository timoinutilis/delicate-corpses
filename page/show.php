<?php
require_once('utils/database.php');
require_once('utils/config.php');
require_once('utils/page.php');
require_once('utils/comments.php');
require_once('utils/share.php');

if (!isset($_GET['work_id']))
{
	header("Location: ".$url_base);
	exit();
}

$work_id = decrypt_int($_GET['work_id']);
$user = NULL;

$comment_name = isset($_POST['comment_name']) ? $_POST['comment_name'] : NULL;
$comment_text = isset($_POST['comment_text']) ? $_POST['comment_text'] : NULL;

$con = connect_to_db();
if ($con)
{
	// add comment
	if (isset($comment_text))
	{
		add_comment($con, 'W', $work_id, $comment_name, isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL, $comment_text);
		if (isset($comment_name))
		{
			setcookie('user_name', $comment_name, time() + $creator_cookie_seconds, $cookie_path, $cookie_domain);
		}
	}
	else
	{
		$comment_name = isset($_COOKIE['user_name']) ? $_COOKIE['user_name'] : NULL;
	}
	
	if (isset($_SESSION['user_id']))
	{
		$user = get_user_by_id($con, $_SESSION['user_id']);
	}

	// page
	$result = mysql_query("SELECT work_id, title, user_group, num_pieces, finished FROM works WHERE work_id = {$work_id}", $con);
	if ($result && mysql_num_rows($result) > 0)
	{
		$work_object = mysql_fetch_object($result);

		if ($work_object->finished)
		{
			get_header(PAGE_OTHER, "<link rel=\"image_src\" href=\"{$url_base}{$path_previews}image_{$work_id}.jpg\" />", $work_object->title);
		}
		else
		{
			get_header(PAGE_OTHER, "", $work_object->title);
		}

		$piece_objects = array();
		
		if ($work_object->user_group == 'U')
		{
			$result = mysql_query("SELECT p.piece, p.user_id, u.name, p.date FROM pieces p LEFT JOIN users u ON p.user_id = u.user_id WHERE p.work_id = {$work_id}", $con);
		}
		else
		{
			$result = mysql_query("SELECT piece, guest, date FROM pieces WHERE work_id = {$work_id}", $con);
		}

		if ($result)
		{
			while ($row = mysql_fetch_object($result))
			{
				$piece_objects[$row->piece] = $row;
			}
		}
		
		$borderClass = "border-piece-top";
		if ($work_object->finished)
		{
			$borderClass = "border-work";
		}

		echo "<h1>{$work_object->title}</h1>";
		echo "<table class=\"work {$borderClass}\">";
		for ($i = 1; $i <= $work_object->num_pieces; $i++)
		{
			echo "<tr><td><div class=\"piece\"><img src=\"{$path_works}image_{$work_id}_{$i}.png\" />";
			$piece_object = $piece_objects[$i];
			$time_text = timeText($piece_object->date);
			if ($work_object->user_group == 'U')
			{
				echo "<div>Created by <span><a href=\"usergallery.php?user_id={$piece_object->user_id}\">{$piece_object->name}</a></span> ({$time_text})</div>";
			}
			else
			{
				echo "<div>Created by <span>{$piece_object->guest}</span> ({$time_text})</div>";
			}
			echo "</div></td></tr>";
		}
		echo "</table>";
		
		$end_tags = "";
		
		if ($work_object->finished)
		{
			// admin
			if (isset($user) && $user->admin > 0)
			{
?>
<form method="post" action="utils/highlight.php">
<input type="hidden" name="work_id" value="<?php echo $work_id; ?>" />
<input type="submit" value="Highlight" />
</form>
<?php
				echo "<a href=\"{$url_base}{$path_previews}image_{$work_id}.jpg\">Preview Image</a>";
			}

			// share buttons
			$end_tags = share_js();
			get_share_buttons($work_id);
			
			// comments
			get_comments($con, "W", $work_id, $comment_name);
		}
		
		get_footer($end_tags);
	}
	else
	{
		header("Location: index.php");
	}
}
mysql_close($con);

?>