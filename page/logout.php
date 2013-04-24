<?php

require_once('utils/config.php');

session_start();
session_destroy();
header('Location: ' . $url_base);

?>