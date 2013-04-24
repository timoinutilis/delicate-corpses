<?php

require_once('utils/config.php');
require_once('utils/page.php');
require_once('utils/user.php');

get_header(PAGE_LOGIN);
?>
<h1>Log in</h1>
<?php

get_login($url_base.'usergallery.php');

get_footer();
?>