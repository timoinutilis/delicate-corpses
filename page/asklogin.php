<?php
require_once('utils/config.php');
require_once('utils/page.php');
require_once('utils/user.php');

get_header($_GET["page"]);
?>
<h1>Do you want to log in?</h1>
<p>
If you really want to draw, please log in!
</p>
<?php
get_login($_GET["return_url"]);
?>
<p>
or
</p>
<p>
If you just want to try,
<a href="<?php echo $_GET["return_url"].'?guest=1'; ?>">draw as guest</a>!;
</p>

<?php
get_footer();
?>