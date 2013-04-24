<?php
require_once('database.php');
require_once('page.php');

function get_comments($con, $type, $id, $comment_name)
{
	echo '<div>'.PHP_EOL;
	echo '<h3>Comments</h3>';

	$result = mysql_query("SELECT c.date, c.guest, c.user_id, u.name, c.text FROM comments c LEFT JOIN users u ON c.user_id = u.user_id WHERE type = '{$type}' AND id = {$id} ORDER BY date", $con);
	if ($result)
	{
		while ($row = mysql_fetch_object($result))
		{
			$time_text = timeText($row->date);
			$html_text = nl2br($row->text);
			echo '<div class="comment">';
			if (isset($row->user_id))
			{
				echo "<div class=\"comment-info\"><a href=\"usergallery.php?user_id={$row->user_id}\">{$row->name}</a> <span>({$time_text})</span></div>";
			}
			else
			{
				echo "<div class=\"comment-info\">{$row->guest} <span>({$time_text})</span></div>";
			}
			echo "<div class=\"comment-text\">{$html_text}</div>";
			echo '</div>'.PHP_EOL;
		}
	}
	else
	{
		echo mysql_error();
	}
	
	echo '<form method="post" action="'.currentURL().'" class="comment-form">';
	echo '<textarea name="comment_text" rows="4" cols="50" maxlength="1000" placeholder="Write a comment to this work..."></textarea><br />';
	if (!isset($_SESSION['user_id']))
	{
		echo '<label>Name: <input type="text" name="comment_name" value="'.(isset($comment_name) ? $comment_name : '').'" /></label>';
	}
	echo '<input type="submit" value="Publish" />';
	echo '</form>'.PHP_EOL;
	
	echo '</div>'.PHP_EOL;
}

?>