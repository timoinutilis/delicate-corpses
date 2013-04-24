<?php
require_once('config.php');
require_once('page.php');

function share_js()
{
	global $fb_app_id;
	
	return "
<div id=\"fb-root\"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = \"//connect.facebook.net/en_US/all.js#xfbml=1&appId={$fb_app_id}\";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<script type=\"text/javascript\" src=\"//assets.pinterest.com/js/pinit.js\"></script>

<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=\"//platform.twitter.com/widgets.js\";fjs.parentNode.insertBefore(js,fjs);}}(document,\"script\",\"twitter-wjs\");</script>
";

}

function get_share_buttons($work_id)
{
	global $url_base, $path_previews;
	
	$this_url = currentURL();
	echo '<div class="share-buttons-line">';
	echo '<div class="share-button"><div class="fb-like" data-href="'.$this_url.'" data-send="false" data-layout="button_count" data-width="140" data-show-faces="false"></div></div>';
	echo '<div class="share-button"><a href="https://twitter.com/share" class="twitter-share-button">Tweet</a></div>';
	echo '<div class="share-button"><a href="http://pinterest.com/pin/create/button/?url='.urlencode($this_url).'&media='.urlencode("{$url_base}{$path_previews}image_{$work_id}.jpg").'" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a></div>';
	echo '</div>';
}

?>