<?php
require_once('config.php');

function connect_to_db()
{
	global $mysql_host, $mysql_user, $mysql_password, $mysql_database;

	$con = mysql_connect($mysql_host, $mysql_user, $mysql_password);
	if ($con)
	{
		mysql_select_db($mysql_database, $con);
	}
	else
	{
		echo mysql_error();
	}
	return $con;
}

function create_new_work($title, $group, $pieces, $palette, $pencils, $bg_color)
{
	$work_id = 0;
	$title = mysql_escape_string(strip_tags($title));
	
	$con = connect_to_db();
	if ($con)
	{
		$pencils_string = implode(',', $pencils);
		$result = mysql_query("INSERT INTO works (title, user_group, num_pieces, max_pieces, finished, lock_date, date, palette, pencils, bg_color) VALUES ('{$title}', '{$group}', 0, {$pieces}, FALSE, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), {$palette}, '{$pencils_string}', {$bg_color})", $con);
		if ($result)
		{
			$work_id = mysql_insert_id();
		}
	}
	mysql_close($con);
	return $work_id;
}

function add_piece($work_id, $piece, $guest, $user_id)
{
	$ip = $_SERVER['REMOTE_ADDR'];
	if (isset($guest))
	{
		$guest = "'".mysql_escape_string(strip_tags($guest))."'";
		$user_id = "NULL";
	}
	else
	{
		$guest = "NULL";
	}
	
	$con = connect_to_db();
	if ($con)
	{
		mysql_query("UPDATE works SET num_pieces = num_pieces + 1, finished = IF(num_pieces=max_pieces,TRUE,FALSE), lock_date = NULL, date = CURRENT_TIMESTAMP() WHERE work_id = {$work_id}", $con);
		mysql_query("INSERT INTO pieces (work_id, piece, guest, user_id, ip, date) VALUES ({$work_id}, {$piece}, {$guest}, {$user_id}, '{$ip}', CURRENT_TIMESTAMP())", $con);
	}
	mysql_close($con);
}

function continue_open_work($group, $user_id)
{
	global $max_lock_minutes;
	
	$work_object = null;
	$ip = $_SERVER['REMOTE_ADDR'];
	
	$con = connect_to_db();
	if ($con)
	{
		if ($group == 'U')
		{
			$subquery = "SELECT work_id FROM pieces WHERE (ip = '{$ip}' OR user_id = {$user_id}) GROUP BY work_id";
		}
		else
		{
			$subquery = "SELECT work_id FROM pieces WHERE ip = '{$ip}' GROUP BY work_id";
		}
		$result = mysql_query("SELECT * FROM works ".
				"WHERE user_group = '{$group}' ".
				"AND num_pieces < max_pieces ".
				"AND (lock_date IS NULL OR lock_date < CURRENT_TIMESTAMP() - INTERVAL {$max_lock_minutes} MINUTE) ".
				"AND work_id NOT IN ({$subquery}) ".
				"LIMIT 1", $con);
//		$result = mysql_query("SELECT * FROM works WHERE user_group = '{$group}' AND num_pieces < max_pieces LIMIT 1", $con);
		if ($result && mysql_num_rows($result) > 0)
		{
			$work_object = mysql_fetch_object($result);
			
			lock_work($con, $work_object->work_id);
		}
	}
	mysql_close($con);
	return $work_object;
}

function lock_work($con, $work_id)
{
	mysql_query("UPDATE works SET lock_date = CURRENT_TIMESTAMP() WHERE work_id = {$work_id}", $con);
}

function unlock_work($work_id)
{
	$con = connect_to_db();
	if ($con)
	{
		mysql_query("UPDATE works SET lock_date = NULL WHERE work_id = {$work_id}", $con);
	}
	mysql_close($con);
}

function highlight_work($work_id)
{
	$con = connect_to_db();
	if ($con)
	{
		mysql_query("UPDATE works SET highlight = TRUE WHERE work_id = {$work_id}", $con);
	}
	mysql_close($con);
}

function count_works($con, $filter)
{
	$result = mysql_query("SELECT COUNT(work_id) AS num_works FROM works WHERE {$filter}", $con);
	if ($result)
	{
		if ($row = mysql_fetch_object($result))
		{
			return $row->num_works;
		}
	}
	return 0;
}

function add_comment($con, $type, $id, $guest, $user_id, $text)
{
	$guest = isset($guest) ? "'".mysql_escape_string(strip_tags($guest))."'" : "NULL";
	$text = mysql_escape_string(strip_tags($text));
	$user_id = isset($user_id) ? $user_id : "NULL";
	mysql_query("INSERT INTO comments (type, id, date, guest, user_id, text) VALUES ('{$type}', {$id}, CURRENT_TIMESTAMP(), {$guest}, {$user_id}, '{$text}')", $con);
}

function get_user($login_type, $login_id)
{
	$user = null;
	$con = connect_to_db();
	if ($con)
	{
		$login_id = mysql_escape_string($login_id);
		$result = mysql_query("SELECT user_id, name, admin FROM users WHERE login_type = '{$login_type}' AND login_id = '{$login_id}'", $con);
		if ($row = mysql_fetch_object($result))
		{
			$user = $row;
		}
	}
	mysql_close($con);
	return $user;
}

function add_user($login_type, $login_id, $name)
{
	$user_id = 0;
	$con = connect_to_db();
	if ($con)
	{
		$name = mysql_escape_string(strip_tags($name));
		$login_id = mysql_escape_string($login_id);
		$result = mysql_query("INSERT INTO users (login_type, login_id, name, register_date) VALUES ('{$login_type}', '{$login_id}', '{$name}', CURRENT_TIMESTAMP())", $con);
		if ($result)
		{
			$user_id = mysql_insert_id();
		}
	}
	mysql_close($con);
	return $user_id;
}

function get_user_by_id($con, $user_id)
{
	$result = mysql_query("SELECT user_id, name, admin FROM users WHERE user_id = {$user_id}", $con);
	if ($row = mysql_fetch_object($result))
	{
		return $row;
	}
	return NULL;
}

function add_news($con, $title, $text)
{
	$title = mysql_escape_string($title);
	$text = mysql_escape_string($text);
	mysql_query("INSERT INTO news (date, title, text) VALUES (CURRENT_TIMESTAMP(), '{$title}', '{$text}')", $con);
}

function edit_news($con, $id, $title, $text)
{
	$title = mysql_escape_string($title);
	$text = mysql_escape_string($text);
	mysql_query("UPDATE news SET title = '{$title}', text = '{$text}' WHERE news_id = {$id}", $con);
}

function count_new_entries($con, $table, $days, $date_column, $filter = NULL)
{
	$real_filter = "{$date_column} >= CURRENT_TIMESTAMP() - INTERVAL {$days} DAY";
	if (isset($filter))
	{
		$real_filter .= " AND ($filter)";
	}
	$result = mysql_query("SELECT COUNT(*) num FROM {$table} WHERE {$real_filter}", $con);
	if ($row = mysql_fetch_object($result))
	{
		return $row->num;
	}
	else
	{
		echo mysql_error();
	}
	return "?";
}

function encrypt_int($id)
{
	global $password;
	$id = (($id << 8) & 0x00FFFF00) | (($id >> 16) & 0x000000FF) | ($id & 0xFF000000);
	return ($id ^ $password);
}

function decrypt_int($id)
{
	global $password;
	$id = ($id ^ $password);
	return (($id >> 8) & 0x0000FFFF) | (($id << 16) & 0x00FF0000) | ($id & 0xFF000000);
}

?>