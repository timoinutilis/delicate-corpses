<?php
require_once('utils/config.php');
require_once('utils/page.php');
require_once('utils/database.php');

get_header(PAGE_NEWS);
?>

<h1>News</h1>

<?php
$con = connect_to_db();
if ($con)
{
	$result = mysql_query("SELECT news_id, date, title, text FROM news ORDER BY date DESC", $con);
	if ($result)
	{
		while ($row = mysql_fetch_object($result))
		{
			$time_text = timeText($row->date);
			$html_text = nl2br($row->text);
			echo '<div class="news">';
			echo "<h2>{$row->title}</h2>";
			echo "<div class=\"news-date\">{$time_text}</div>";
			echo "<div class=\"news-text\">{$html_text}</div>";
			echo "<div class=\"news-links\"><a href=\"shownews.php?news_id={$row->news_id}\">Comments</a></div>";
			echo '</div>'.PHP_EOL;
		}
	}
	mysql_close($con);
}

get_footer();
?>