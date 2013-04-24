<?php
require_once('utils/config.php');
require_once('utils/database.php');
require_once('utils/page.php');
require_once('utils/galleries.php');
require_once('utils/user.php');

check_login($url_base.'usergallery.php', PAGE_OTHER, false);

$page = 1;
$is_self = false;
$user_id = NULL;
$session_user_id = NULL;

if (isset($_GET['user_id']))
{
	$user_id = $_GET['user_id'];
	if (isset($_SESSION['user_id']) && $user_id == $_SESSION['user_id'])
	{
		$is_self = true;
	}
}
else if (isset($_SESSION['user_id']))
{
	$user_id = $_SESSION['user_id'];
	$is_self = true;
}
else
{
	header("Location: gallery.php");
	exit();
}

if (isset($_GET['page']))
{
	$page = $_GET['page'];
}

$con = connect_to_db();
if ($con)
{
	if ($is_self)
	{
		$user_name = $_SESSION['user_name'];
		get_header(PAGE_USER, "", $user_name);
	}
	else
	{
		$user = get_user_by_id($con, $user_id);
		$user_name = $user->name;
		get_header(PAGE_OTHER, "", $user_name);
	}

	echo "<h1>{$user_name}</h1>";
	
	$offset = ($page - 1) * $gallery_works_per_page;
	$num_pages = 0;
	
	$filter = "user_group = 'U' AND work_id IN (SELECT work_id FROM pieces WHERE user_id = {$user_id} GROUP BY work_id) AND " . ($is_self ? "num_pieces > 0" : "finished = TRUE");

	$num_pages = max(1, ceil(count_works($con, $filter) / $gallery_works_per_page));
	
	get_gallery($con, $filter, $offset, $gallery_works_per_page, $works_per_row);
	
	echo "<p>";
	if ($page > 1)
	{
		echo "<a href=\"usergallery.php?user_id={$user_id}&page=".($page - 1)."\">Previous «</a> ";
	}
	echo "Page {$page} of {$num_pages}";
	if ($page < $num_pages)
	{
		echo " <a href=\"usergallery.php?user_id={$user_id}&page=".($page + 1)."\">» Next</a>";
	}
	echo "</p>";

	get_footer();
}
mysql_close($con);

?>