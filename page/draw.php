<?php
require_once('utils/config.php');
require_once('utils/page.php');
require_once('utils/database.php');
require_once('utils/user.php');

$work_id = 0;
$title = null;
$piece = 0;
$pieces = 1;
$palette = 1;
$allowed_pencils = null;
$bg_color = 0;

if (isset($_POST["new_work"]))
{
	$group = isset($_SESSION["user_id"]) ? 'U' : 'G';
	
	$title = $_POST["title"];
	$pieces = 4; //$_POST["pieces"];
	$palette = $_POST["palette"];
	$allowed_pencils = $_POST["pencils"];
	$bg_color = $_POST["bg_color"];
	
	$work_id = create_new_work($title, $group, $pieces, $palette, $allowed_pencils, $bg_color);
	$piece = 1;
}
else
{
	check_login($url_base.'draw.php', PAGE_DRAW, true);
	
	$group = isset($_SESSION["user_id"]) ? 'U' : 'G';

	$work_object = continue_open_work($group, isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : 0);
	if ($work_object)
	{
		$work_id = $work_object->work_id;
		$piece = $work_object->num_pieces + 1;
		$pieces = $work_object->max_pieces;
		$title = $work_object->title;
		$palette = $work_object->palette;
		$allowed_pencils = explode(',', $work_object->pencils);
		$bg_color = $work_object->bg_color;
	}
	else
	{
		$new_url = "new.php?no_continue=1";
		if (isset($_GET['guest']))
		{
			$new_url .= "&guest=1";
		}
		header("Location: ".$new_url);
		exit();
	}
}

$colors = $palettes[$palette];

$canvas_width = $work_width;
$canvas_height = ($work_height / $pieces);
$needsTop = "false";
$needsBottom = "false";

if ($piece == 1)
{
	$needsBottom = "true";
	$borderClass = "border-piece-top";
	$pieceExplanation = "This is the first piece of this work. Draw something down to the bottom border, so the next piece can be connected to it.";
}
elseif ($piece == $pieces)
{
	$needsTop = "true";
	$borderClass = "border-piece-bottom";
	$pieceExplanation = "This is the last piece of this work. Connect your drawing to the piece at the top border.";
}
else
{
	$needsTop = "true";
	$needsBottom = "true";
	$borderClass = "border-piece-center";
	$pieceExplanation = "This is a piece in the middle of this work. Connect your drawing to the piece at the top border and draw something down to the bottom border, so the next piece can be connected to it.";
}

get_header($piece == 1 ? PAGE_NEW : PAGE_DRAW, "", $title);
?>

<script type="text/javascript" src="canvas.js"></script>
<script type="text/javascript" src="interface.js"></script>
<script type="text/javascript">

var refreshLockInterval;
function onSubmitClick()
{
	if (validateDrawing(<?php echo $needsTop.", ".$needsBottom; ?>))
	{
		window.clearInterval(refreshLockInterval);
		document.getElementById('imageDataField').value = getCanvasDataURL();
		return true;
	}
	return false;
}

function onTimerRefreshLock()
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open("GET", "utils/refreshlock.php?work_id=<?php echo encrypt_int($work_id); ?>", true);
	xmlhttp.send();
}

</script>

<h1><?php echo $title; ?></h1>

<p>
<?php
echo "{$pieceExplanation}<br />".PHP_EOL;
?>
<span class="warning" id="topWarning">Please connect your drawing to the top border!<br /></span>
<span class="warning" id="bottomWarning">Please draw something down to the bottom border!<br /></span>
</p>

<div class="<?php echo $borderClass; ?>">
<table id="drawArea" class="work" style="cursor:crosshair;">
<?php
if ($piece > 1)
{
	echo("<tr><td><div style=\"width:{$canvas_width}px; height:{$connection_size}px; background-image:url('{$path_works}image_{$work_id}_".($piece - 1).".png'); background-position:bottom;\"></div></td></tr>");
}
?>
<tr><td>
<canvas id="drawCanvas" width="<?php echo $canvas_width; ?>" height="<?php echo $canvas_height; ?>">
This site uses HTML5, please use a newer browser.
</canvas>
</td></tr>
</table>
</div>

<div class="draw-actions">
<button type="button" accesskey="z" onclick="return undo();">Undo</button>
</div>

<div class="draw-pencils">
<?php
foreach ($allowed_pencils as $pencil_index)
{
	$pencil = $pencils[$pencil_index];
	echo '<label class="selection"><input type="radio" name="pencil" value="'.$pencil.'" onclick="onSelectionClick(event); setLineWidth('.$pencil.');" /><span class="pencil-image" style="background-image:url(\'images/pencil_'.$pencil.'.jpg\')"></span></label>'.PHP_EOL;
}
?>
</div>

<div class="draw-colors">
<?php
foreach ($colors as $color_index => $color)
{
	echo '<label class="selection"><input type="radio" name="color" value="'.$color_index.'" onclick="onSelectionClick(event); setColor(\''.$color.'\');" /><span class="color-image" style="background-image:url(\'images/color.png\'); background-color:'.$color.'"></span></label>'.PHP_EOL;
}
?>
</div>

<div class="draw-form">

<form method="post" action="utils/upload.php" name="drawForm" onsubmit="removeCloseCheck(); setLoading('Uploading...')">
<input type="hidden" name="work_id" value="<?php echo $work_id; ?>" />
<input type="hidden" name="piece" value="<?php echo $piece; ?>" />
<input type="hidden" name="pieces" value="<?php echo $pieces; ?>" />
<input type="hidden" name="image_data" id="imageDataField" />

<?php
if (isset($_SESSION['user_id']))
{
	echo '<input type="hidden" name="user_id" value="'.$_SESSION['user_id'].'" />';
}
else
{
	$user_name = isset($_COOKIE['user_name']) ? $_COOKIE['user_name'] : "";
?>

<p>
<span class="warning" id="creatorWarning">Please enter your name!<br /></span>
Your Name: <input type="text" name="creator" value="<?php echo $user_name; ?>" />
</p>

<?php
}
?>

<p>
<input type="submit" value="Save and finish" onclick="return onSubmitClick();" />
</p>
</form>

<form method="post" action="utils/cancel.php" onsubmit="removeCloseCheck(); setLoading('Canceling...')">
<input type="hidden" name="work_id" value="<?php echo $work_id; ?>" />
<input type="submit" value="Delete and cancel" />
</form>

</div>

<script type="text/javascript">
<?php
	$preselectedColor = ($bg_color == 0) ? 1 : 0;
	echo "initDrawCanvas('drawCanvas', 'drawArea', '{$colors[$bg_color]}', {$pencils[$allowed_pencils[0]]}, '{$colors[$preselectedColor]}');".PHP_EOL;
	echo "document.getElementsByName('color')[{$preselectedColor}].checked = true;".PHP_EOL;
	echo "refreshSelection('color');".PHP_EOL;
?>
document.getElementsByName("pencil")[0].checked = true;
refreshSelection("pencil");
addCloseCheck("You will lose your work, if you close this window without clicking 'Save and finish' before.");

refreshLockInterval = window.setInterval(onTimerRefreshLock, <?php echo ($lock_refresh_minutes * 60 * 1000) ?>);
</script>

<?php
get_footer();
?>