<?php
require_once('utils/config.php');
require_once('utils/database.php');
require_once('utils/page.php');
require_once('utils/galleries.php');

$days = $admin_default_days;
if (isset($_GET['days']))
{
	$days = $_GET['days'];
}

get_header(PAGE_ADMIN, "", "Admin");

if (isset($_SESSION['user_id']))
{
	$con = connect_to_db();
	if ($con)
	{
		$user = get_user_by_id($con, $_SESSION['user_id']);
		if ($user->admin > 0)
		{
			echo "<h1>Hello {$user->name}!</h1>";

			echo '<form action="'.currentURLnoParams().'">';
			echo '<input type="text" name="days" size="3" value="'.$days.'" />days ';
			echo '<input type="submit" value="Show" />';
			echo '</form>';

			echo "<h2>Statistics</h2>";
			
			echo "<p>";
			echo "New users logged in: ".count_new_entries($con, 'users', $days, 'register_date')."<br />";
			echo "New pieces (User + Guest): ".count_new_entries($con, 'pieces', $days, 'date', "user_id IS NOT NULL");
			echo " + ".count_new_entries($con, 'pieces', $days, 'date', "guest IS NOT NULL")."<br />";
			echo "Finished works (User + Guest): ".count_new_entries($con, 'works', $days, 'date', "finished = TRUE AND user_group = 'U'");
			echo " + ".count_new_entries($con, 'works', $days, 'date', "finished = TRUE AND user_group = 'G'")."<br />";
			echo "New work comments: ".count_new_entries($con, 'comments', $days, 'date', "type = 'W'")."<br />";
			echo "New news comments: ".count_new_entries($con, 'comments', $days, 'date', "type = 'N'")."<br />";
			echo "</p>";

			echo "<h2>New Pieces and Works</h2>";
			echo "<button id=\"buttonShow\" type=\"button\" onclick=\"document.getElementById('works').style.display='block'; document.getElementById('buttonShow').style.display='none';\">Show</button>";
			echo "<div id=\"works\" style=\"display:none\">";
			$filter = "date >= CURRENT_TIMESTAMP() - INTERVAL {$days} DAY";
			get_gallery($con, $filter, 0, 0, $works_per_row);
			echo "</div>";
			
			echo "<h2>New Comments</h2>";
			if (isset($_GET['delete_comment']))
			{
				$result = mysql_query("DELETE FROM comments WHERE comment_id = " . $_GET['delete_comment'], $con);
				if ($result)
				{
					echo '<div class="infobox">Comment deleted.</div>';
				}
			}
			$result = mysql_query("SELECT * FROM comments WHERE date >= CURRENT_TIMESTAMP() - INTERVAL {$days} DAY ORDER BY date DESC", $con);
			if ($result)
			{
				echo "<table class=\"admin\">";
				while ($row = mysql_fetch_object($result))
				{
					$time_text = timeText($row->date);
					$comment = substr($row->text, 0, 50);
					$comment_url = ($row->type == 'N') ? "shownews.php?news_id={$row->id}" : "show.php?work_id=".encrypt_int($row->id);
					echo "<tr><td>{$comment}</td><td>{$time_text}</td><td><a href=\"{$comment_url}\">Show</a> <a href=\"?delete_comment={$row->comment_id}\" onclick=\"return confirmDeleteComment();\">Delete</a><br/></td></tr>";
				}
				echo "</table>";
				echo "<script>function confirmDeleteComment() {return confirm('Do yo really want to delete this comment?');}</script>";
			}

			echo "<h2>Publish News</h2>";
			if (isset($_POST['news_text']) && strlen($_POST['news_text']) > 0 && isset($_POST['news_title']) && strlen($_POST['news_title']) > 0)
			{
				if (isset($_POST['news_id']))
				{
					edit_news($con, $_POST['news_id'], $_POST['news_title'], $_POST['news_text']);
					echo '<div class="infobox">News changes saved.</div>';
				}
				else
				{
					add_news($con, $_POST['news_title'], $_POST['news_text']);
					echo '<div class="infobox">News published.</div>';
				}
			}
			
			if (isset($_GET['edit_news']))
			{
				$edit_news = $_GET['edit_news'];
				$result = mysql_query("SELECT * FROM news WHERE news_id = {$edit_news}", $con);
				if ($result)
				{
					if ($row = mysql_fetch_object($result))
					{
						echo '<form method="post" action="'.currentURLnoParams().'">';
						echo '<input type="hidden" name="news_id" value="'.$edit_news.'">';
						echo '<input type="text" name="news_title" size="100" placeholder="Write a news title..." value="'.htmlspecialchars($row->title).'"/>';
						echo '<textarea name="news_text" rows="10" cols="100" placeholder="Write a news text...">'.htmlspecialchars($row->text).'</textarea><br />';
						echo '<input type="submit" value="Save" />';
						echo '</form>';
					}
				}
			}
			else
			{
				echo '<form method="post" action="'.currentURLnoParams().'" onsubmit="return confirm(\'Do yo really want to publish this?\');">';
				echo '<input type="text" name="news_title" size="100" placeholder="Write a news title..."/>';
				echo '<textarea name="news_text" rows="10" cols="100" placeholder="Write a news text..."></textarea><br />';
				echo '<input type="submit" value="Publish" />';
				echo '</form>';
			}
			
			echo "<h2>Edit News</h2>";
			if (isset($_GET['delete_news']))
			{
				$result = mysql_query("DELETE FROM news WHERE news_id = " . $_GET['delete_news'], $con);
				if ($result)
				{
					echo '<div class="infobox">News deleted.</div>';
				}
			}
			$result = mysql_query("SELECT * FROM news ORDER BY date DESC", $con);
			if ($result)
			{
				echo "<table class=\"admin\">";
				while ($row = mysql_fetch_object($result))
				{
					$time_text = timeText($row->date);
					echo "<tr><td>{$row->title}</td><td>{$time_text}</td><td><a href=\"?edit_news={$row->news_id}\">Edit</a> <a href=\"?delete_news={$row->news_id}\" onclick=\"return confirmDeleteNews();\">Delete</a><br/></td></tr>";
				}
				echo "</table>";
				echo "<script>function confirmDeleteNews() {return confirm('Do yo really want to delete this news?');}</script>";
			}
			
		}
	}
	mysql_close($con);
}

get_footer();
?>