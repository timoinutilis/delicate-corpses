<?php
require_once('database.php');

$work_id = $_POST['work_id'];

if (isset($work_id))
{
	unlock_work($work_id);
}

header("Location: ../index.php");
?>