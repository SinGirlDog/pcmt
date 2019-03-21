<?php
header("Content-Type: text/html; charset=utf-8");
set_time_limit(0);
$conn = mysql_connect('localhost', 'root', 'root') or die("error connecting");
mysql_query("set names 'utf8'");
mysql_select_db('article');
/**/
$control = './collect.txt'; //控制识别文件
$main_url = 'http://www.hqwx.com';//主要目标域名
$cur_dir = '/tiku/map/yjxf';//当前目标路径
$cur_dir = empty($_GET['cur_dir'])? $cur_dir : $_GET['cur_dir'];
$const_status = empty($_REQUEST['status'])? 0 : $_REQUEST['status'];
$cur_parentid = empty($_REQUEST['parentid'])? 0 : $_REQUEST['parentid'];
	// echo var_dump($_POST);var_dump($_GET);die();

if ($_POST) {
	if (!empty($_POST['start']) && !empty($_POST['url'])) {
		if (!file_exists($control)) {
			addTxt(); //开始，条件识别文件
		}
	} else if (!empty($_POST['stop'])) {
		if (file_exists($control)) {
			unlink("collect.txt");
			header("location:./diy_key_knowlage.php");
			exit ;
		}
	} else {
		exit("请输入网址");
	}

	$urlP = $_POST['url'];
	$urlOne = explode('com', $urlP);
	$urlOne = $urlOne[0] . "com"; //域名
	//地址
	$returnP = getCurl($urlP);

	$page = array();
	switch($const_status){
		default:
		case 0:
		case 1:

		$content_arr = explode("<div>", $returnP);
		$content_arr = explode("</div>", $content_arr[1]);
		$arr = array();
		// echo '<pre/>';var_dump($content_arr[0]);die();
		preg_match_all('/<a[^>].*>(.*)<\/a>/isU', $content_arr[0], $arr);
		Insert_knowlage_key($arr);
		break;
		case 2:
		
		break;
		case 3:
		case 4:
		
		case 5:
		break;
	}
	die();
}
function getCurl($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$return = curl_exec($ch);
	curl_close($ch);
	return $return;
}

function addTxt($e = 0) {
	if ($e < 8) {
		if (!fopen("collect.txt", "w")) {
			$e += 1;
			return addTxt($e);
		}
	}
}

function Insert_knowlage_key($arr){
	$id_arr = array();
	$id_arr = Insert_any_one_key($arr);
	// echo sizeof($id_arr);die()
	header("location:./diy_key_knowlage.php?status=2");

}

function Insert_any_one_key($arr){
	$url_arr = $arr[0];
	$name_arr = $arr[1];
	$id_arr = array();
	foreach($url_arr as $key=>$val){
		$href_dir = find_href_dir($val);
		$addtime = date('Y-m-d H:i:s', time());
		$find_sql = "select * from key_knowlage_xf where name = '$name_arr[$key]' and url = '$href_dir' limit 1";
		// echo $find_sql,'<br/>';
		$find_res = mysql_query($find_sql);
		$assoc = mysql_fetch_assoc($find_res);
		// var_dump($assoc).'<br/>';
		if($assoc){
			$id_arr[] = $assoc['id'];
		}
		else{
			$knowlage_sql = "insert into key_knowlage_xf (name,url,addtime) values ('$name_arr[$key]','$href_dir','$addtime')";
			// echo $knowlage_sql,'<br/>';
			mysql_query($knowlage_sql);
			$id_arr[] = mysql_insert_id();
		}		
	}
	return $id_arr;
}



function find_href_dir($href_str){
	$href_arr = array();
	$href_arr = explode("\"", $href_str);
	foreach($href_arr as $hk=>$ha){
		if(stripos($href_arr[$hk],'href=')!==false){
			return $href_arr[$hk+1];
		}
	}
	return 'NoHrefDir';
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>CollectION</title>
</head>
<body>	
	<p>
		CollectION__KeyKnowlage 
		<br/>
		<?php
		if (!empty($_GET['status']))
			echo '(步骤'.$_GET['status'].'-完成！)';
		?>
		<br/>
		<?php echo $main_url.$cur_dir?>		
	</p>
	
	调试
	<form action="diy_key_knowlage.php" method="post">
		<input type="text" name="url" id="url" placeholder="网址" />
		<input type="submit" name="start" value="开始" />
		<input type="submit" name="stop" value="停止"/>
		<input type="hidden" name="status" value="<?php echo $const_status ?>"/>
		<input type="hidden" name="parentid" value="<?php echo $cur_parentid ?>"/>
	</form> 

	<?php
	if (!empty($_GET['status']))
	{
		// $parentid = $cur_parentid;
		$next_status = $_GET['status'] + 1;
		?>
		<p>收集状态</p>
		<?php
		$sqlG = "select * from key_knowlage_xf order by id";
		$rowg = mysql_query($sqlG);
		while($current = mysql_fetch_assoc($rowg)){
			// var_dump($current);
			echo '<a href="./diy_key_knowlage.php?status='.$next_status.'&parentid='.$current['id'].'&cur_dir='.$current['url'].'">'.$current['name'].'</a>';
			echo '<br/>';
		}
		var_dump($current);die();		
		?>
		<div id='mydiv'>
		</div>
		<?php
	}
	?>

</body>
</html>