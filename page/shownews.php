<?php
require_once('utils/config.php');
require_once('utils/page.php');
require_once('utils/database.php');
require_once('utils/comments.php');

$news_id = $_GET['news_id'];

$comment_name = isset($_POST['comment_name']) ? $_POST['comment_name'] : NULL;
$comment_text = isset($_POST['comment_text']) ? $_POST['comment_text'] : NULL;

$con = connect_to_db();
if ($con)
{
	// add comment
	if (isset($news_id) && isset($comment_text))
	{
		add_comment($con, 'N', $news_id, $comment_name, isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL, $comment_text);
		if (isset($comment_name))
		{
			setcookie('user_name', $comment_name, time() + $creator_cookie_seconds, $cookie_path, $cookie_domain);
		}
	}
	else
	{
		$comment_name = isset($_COOKIE['user_name']) ? $_COOKIE['user_name'] : NULL;
	}

	// page
	get_header(PAGE_OTHER);
	
	$result = mysql_query("SELECT date, title, text FROM news WHERE news_id = {$news_id}", $con);
	if ($result)
	{
		if ($row = mysql_fetch_object($result))
		{
			$time_text = timeText($row->date);
			$html_text = nl2br($row->text);
			
			echo "<h1>{$row->title} ({$time_text})</h1>".PHP_EOL;
			echo "<p>{$html_text}</p>".PHP_EOL;
		}
	}
	
	get_comments($con, "N", $news_id, $comment_name);

	mysql_close($con);
}

get_footer();
?>