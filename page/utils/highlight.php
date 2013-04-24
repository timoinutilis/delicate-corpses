<?php
require_once('database.php');

$work_id = $_POST['work_id'];

if (isset($work_id))
{
	highlight_work($work_id);
}

header("Location: ../gallery.php");
?>