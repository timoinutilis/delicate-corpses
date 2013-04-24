<?php
require_once('database.php');

$work_id = $_GET['work_id'];

if (isset($work_id))
{
	$work_id = decrypt_int($work_id);
	$con = connect_to_db();
	if ($con)
	{
		lock_work($con, $work_id);
	}
	mysql_close($con);
}

?>