<?php
$page = array();
	$arr = array();
	preg_match("/<span class=\"menu-item-hd\".*?>.*?<\/span>/ism", $returnP, $page);
	// var_dump($page);die();
	preg_match_all('/<a[^>].*>(.*)<\/a>/isU', $page[0], $arr);
	// var_dump($arr);die();
	$pageUrl[] = $urlP;
	/*构建全部分页*/
	$end = end($arr[1]);
	if ($end != '<<' && $end != '>>') {
		$pageNum = end($arr[1]);
	} else {
		$c = count($arr[1]);
		$pageNum = $arr[1][$c - 2];
	}
	$Burl = explode(".html", $urlP);
	$Burl = $Burl[0];
	for ($i = 2; $i < $pageNum + 1; $i++) {
		$pageUrl[] = $Burl . '/p/' . $i . '.html';
	}
?>