<?php
$conn = mysql_connect('localhost', 'root', 'root') or die("error connecting");
mysql_query("set names 'utf8'");
mysql_select_db('article');
if (empty($_GET['id'])){
	$sql = "select * from article order by id desc";
	$res = mysql_query($sql);
	$count = 0;
	while($row = mysql_fetch_array($res))
	{
		echo "<a href='list.php?id=".$row['id']."' title='".$row['title']."' >".$row['title']."</a><p></p>";
		$count += 1;
	}
	echo "<br />".$count;
} else if ($_GET['id']){
	$sql = "select * from article where id = ".$_GET['id']." LIMIT 1";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	print_r($row['content']);
}

?>