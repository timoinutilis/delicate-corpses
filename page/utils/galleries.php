<?php
require_once('config.php');
require_once('database.php');
require_once('page.php');

function get_gallery($con, $filter, $offset, $limit, $num_per_line)
{
	global $path_thumbs;
	
	if ($con)
	{
		$query = "SELECT work_id, title, num_pieces, finished FROM works WHERE {$filter} ORDER BY date DESC";
		if ($limit != 0)
		{
			$query .= " LIMIT {$offset}, {$limit}";
		}
		$result = mysql_query($query, $con);
		
		if ($result)
		{
			$i = 0;
			while ($row = mysql_fetch_object($result))
			{
				if ($i % $num_per_line == 0)
				{
					if ($i > 0)
					{
						echo "</div>".PHP_EOL;
					}
					echo '<div class="gallery-line">'.PHP_EOL;
				}
				if ($row->finished)
				{
					$thumb = "{$path_thumbs}image_{$row->work_id}.jpg";
				}
				else
				{
					$thumb = "{$path_thumbs}image_{$row->work_id}_{$row->num_pieces}.jpg";
				}
				$encrypt_id = encrypt_int($row->work_id);
				echo "<div class=\"thumbnail\"><a href=\"show.php?work_id={$encrypt_id}\"><img src=\"{$thumb}\" /><div>{$row->title}</div></a></div>".PHP_EOL;
				$i++;
			}
			if ($i > 0)
			{
				echo "</div>".PHP_EOL;
			}
		}
	}
}

?>