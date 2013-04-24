<?php
require_once('utils/config.php');
require_once('utils/database.php');
require_once('utils/page.php');
require_once('utils/galleries.php');

$group = 'U';
$page = 1;

if (isset($_GET['group']))
{
	$group = $_GET['group'];
}
if (isset($_GET['page']))
{
	$page = $_GET['page'];
}

get_header(PAGE_GALLERY);
?>
<h1>Gallery</h1>
<?php


echo '<ul id="group">';
echo '<li '.($group == 'U' ? 'class="current-page"' : '').'><a href="gallery.php?group=U">User Works</a></li> ';
echo '<li '.($group == 'G' ? 'class="current-page"' : '').'><a href="gallery.php?group=G">Guest Works</a></li> ';
echo '</ul>'.PHP_EOL;

$con = connect_to_db();
if ($con)
{
	$offset = ($page - 1) * $gallery_works_per_page;
	$num_pages = 0;
	
	$filter = "finished = TRUE AND user_group = '{$group}'";

	$num_pages = max(1, ceil(count_works($con, $filter) / $gallery_works_per_page));
	
	get_gallery($con, $filter, $offset, $gallery_works_per_page, $works_per_row);
	
	echo "<p>";
	if ($page > 1)
	{
		echo "<a href=\"gallery.php?group={$group}&page=".($page - 1)."\">Previous «</a> ";
	}
	echo "Page {$page} of {$num_pages}";
	if ($page < $num_pages)
	{
		echo " <a href=\"gallery.php?group={$group}&page=".($page + 1)."\">» Next</a>";
	}
	echo "</p>";
}
mysql_close($con);


get_footer();
?>