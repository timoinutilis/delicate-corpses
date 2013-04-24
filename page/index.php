<?php
require_once('utils/config.php');
require_once('utils/page.php');
require_once('utils/galleries.php');

get_header(PAGE_HOME);
?>

<h1>Draw surrealistic pictures with other people.</h1>
<p>
Check the <a href="gallery.php">gallery</a> to see what others did or <a href="draw.php">contribute</a> your own drawings!
</p>

<?php
$con = connect_to_db();
if ($con)
{
	$filter = "highlight = TRUE";
	$num_highlights = count_works($con, $filter);
	$offset = rand(0, max(0, $num_highlights - $home_num_works));
	get_gallery($con, $filter, $offset, $home_num_works, $works_per_row);
}
?>

<?php
$result = mysql_query("SELECT news_id, date, title, text FROM news ORDER BY date DESC LIMIT 1", $con);
if ($result)
{
	if ($row = mysql_fetch_object($result))
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
	else
	{
		echo "<p>No news yet.</p>";
	}
}

get_footer();

mysql_close($con);
?>