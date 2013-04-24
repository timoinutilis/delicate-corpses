<?php
require_once('utils/page.php');

$type = $_GET["type"];
$id = $_GET["id"];
$return_url = $_GET["return_url"];
$page = $_GET["page"];
$login_name = isset($_GET["login_name"]) ? $_GET["login_name"] : "";

get_header($page);
?>

<h1>You are new here.</h1>

<p>
Please enter your name! (You are signed in as <?php echo ($login_name != "") ? $login_name : $id; ?>)
</p>

<form method="post" action="<?php echo $return_url; ?>">
<input type="hidden" name="new_user" value="1" />
<input type="hidden" name="type" value="<?php echo $type; ?>" />
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<p>
<span class="warning" id="nameWarning">Please enter your name!<br /></span>
Name: <input type="text" name="name" value="<?php echo $login_name; ?>"/>
</p>
<p>
<input type="submit" value="Continue" />
</p>
</form>

<?php
get_footer();
?>