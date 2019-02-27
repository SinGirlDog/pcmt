<?php
$conn = mysql_connect('localhost', 'root', 'root') or die("error connecting");
mysql_query("set names 'utf8'");
mysql_select_db('article');

$html = '';
if (!empty($_POST['title']) || !empty($_POST['id'])) {

	if ($_POST['title'] != 'noPmark' && $_POST['id'] == '') {

		$title = mb_substr($_POST['title'], 1, -1);
		$sql = "select id from article where title = '" . $title . "' order by id desc limit 1";
		$row = mysql_query($sql);
		$first = mysql_fetch_assoc($row);
		$firstId = $first['id'];
	} else if ($_POST['title'] == 'noPmark' && $_POST['id'] != '') {

		$firstId = $_POST['id'];
	}
	else{

	}
	$sqlG = "select id,title from article where id > '" . $firstId . "' order by id desc";

	$rowg = mysql_query($sqlG);

	if ($rowg) {
		while ($res = mysql_fetch_array($rowg)) {
			$html .= "<p style='font-size:14px;color:#f00;' >《" . $res['title'] . "》</p><span style='font-size:12px;'><a href='list.php?id=" . $res['id'] . "' target='iew_frame'>采集成功...(点击查看)</a></span><br /><br />";
		}
	}
	$ajax['html'] = $html;
	$control = './collect.txt';
	if (!file_exists($control)) {
		$ajax['end'] = '1';
	} else {
		$ajax['end'] = '0';
	}
	echo json_encode($ajax);
	exit ;
}
?>