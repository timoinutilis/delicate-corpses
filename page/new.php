<?php
require_once('utils/config.php');
require_once('utils/page.php');
require_once('utils/user.php');

check_login($url_base.'new.php', PAGE_NEW, true);

get_header(PAGE_NEW);

get_palettes_js();

?>
<h1>Start a new work.</h1>
<?php

if (isset($_GET['no_continue']))
{
	echo '<div class="infobox">No open work was found. On this page you can start a new one.</div>';
}
?>

<script type="text/javascript" src="interface.js"></script>

<form method="post" action="draw.php" name="newForm" onsubmit="return validateNewForm();">

<?php /*

<div>
In how many pieces should the work be separated?<br />
<span class="warning" id="piecesWarning">Please select one of the options!<br /></span>
<div class="selection-block">
<?php
for ($i = 3; $i <= 8; $i++)
{
	echo '<label class="selection"><input type="radio" name="pieces" value="'.$i.'" onclick="onSelectionClick(event)" /><span class="pieces-image" style="background-image:url(\'images/pieces_'.$i.'.jpg\')"></span></label>'.PHP_EOL;
}
?>
</div>
</div>
*/ ?>

<div>
<h2>What should be the title for your work?</h2>
<span class="warning" id="titleWarning">Please enter a name for this work!<br /></span>
<input type="text" name="title" size="60" />
</div>

<div>
<h2>Which pencil sizes do you want to use?</h2>
<span class="warning" id="pencilsWarning">Please select at least one pencil size!<br /></span>
<div class="selection-block">
<?php
foreach ($pencils as $index => $pencil)
{
	echo '<label class="selection"><input type="checkbox" name="pencils[]" value="'.$index.'" onclick="onSelectionClick(event)" /><span class="pencil-image" style="background-image:url(\'images/pencil_'.$pencil.'.jpg\')"></span></label>'.PHP_EOL;
}
?>
</div>

<div>
<h2>Which palette do you want to use?</h2>
<span class="warning" id="colorsWarning">Please select a palette!<br /></span>
<div class="selection-block">
<?php
foreach ($palettes as $palette => $colors)
{
	echo '<label class="selection"><input type="radio" name="palette" value="'.$palette.'" onclick="onPaletteSelectionClick(event)" /><span class="palette-image" style="background-image:url(\'images/palette_'.$palette.'.jpg\')"></span></label>'.PHP_EOL;
}
?>
</div>
</div>

<div>
<h2>Which color do you want for the background?</h2>
<span class="warning" id="bgColorWarning">Please select a background color!<br /></span>
<div class="selection-block">
<?php
for ($index = 0; $index < $palette_num_colors; $index++)
{
	echo '<label class="selection"><input type="radio" name="bg_color" value="'.$index.'" onclick="onSelectionClick(event)" /><span name="bg_color_image" class="color-image" style="visibility:hidden"></span></label>'.PHP_EOL;
}
?>
</div>
</div>

<input type="hidden" name="new_work" value="1" />
<input type="submit" value="Start!" />

</form>

<?php
get_footer();
?>