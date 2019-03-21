<?php
echo "Complete Lastest once Die();";
die();
header("Content-Type: text/html; charset=utf-8");
set_time_limit(0);
$conn = mysql_connect('localhost', 'root', 'root') or die("error connecting");
mysql_query("set names 'utf8'");
mysql_select_db('article');
/**/
$control = './collect.txt'; //控制识别文件
$main_url = 'http://www.hqwx.com';//主要目标域名
$cur_dir = '/tiku/';//当前目标路径
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
			header("location:./diy_one.php");
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
		preg_match_all("/<ul class=\"lesson_con clearfix\".*?>.*?<\/ul>/ism", $returnP, $page);
		$arr = array();
		preg_match_all('/<a[^>].*>(.*)<\/a>/isU', $page[0][0], $arr);
		Insert_top_cat($arr,$cur_parentid);
		break;
		case 2:
		$arr_all = array();
		$ch_arr_all = array();
		preg_match_all("/<span class=\"menu-item-hd\".*?>.*?<\/span>/ism", $returnP, $page);
		$chapter = array();
		preg_match_all("/<ul class=\"menu-level-3 js-item-bd \".*?>.*?<\/ul>/ism", $returnP, $chapter);
		echo '<pre/>';
		foreach($page[0] as $key=>$val){
			$arr = array();
			$ch_arr = array();
			preg_match_all('/<a[^>].*>(.*)<\/a>/isU', $page[0][$key], $arr);
			preg_match_all('/<a[^>].*>(.*)<\/a>/isU', $chapter[0][$key], $ch_arr);
			$arr_all[] = $arr;
			$ch_arr_all[] = $ch_arr;
		}
		Insert_sec_third_cat($arr_all,$ch_arr_all,$cur_parentid);
		break;
		case 3:
		case 4:
		preg_match_all("/<div class=\"labels-list clearfix\".*?>.*?<\/div>/ism", $returnP, $page);
		$arr = array();
		preg_match_all('/<a[^>].*>(.*)<\/a>/isU', $page[0][0], $arr);
		Insert_fourth_cat($arr,$cur_parentid);
		break;
		case 5:
		$question_arr = array();
		$question_arr = collect_question_title_arr($urlP);		
		Insert_question_title($question_arr,$cur_parentid);
		header("location:./diy_one.php?status=6&parentid=".$cur_parentid);
		break;
		case 6:
		$data = array();
		$data = collect_question_data_arr($urlP);
		// var_dump($data);die;
		$id = Insert_question_data($data,$cur_parentid);
		// echo $id;die();
		header("location:./diy_one.php?status=7&parentid=".$cur_parentid);
		break;
		case 7:
		break;

		case 1024:
		$question_arr = array();
		$question_arr = collect_question_title_arr($urlP);
			// echo '<pre/>';var_dump($question_arr);die;			
		$id_arr = array();
		$id_arr = Insert_question_title($question_arr,$cur_parentid);
		echo '<a href="javascript:history.go(-1)">返回上一步</a>';
		foreach($id_arr as $key=>$id){
			$find_q_sql = "select url from question where id = '$id' limit 1";
			$find_q_res = mysql_query($find_q_sql);
			$assoc = mysql_fetch_assoc($find_q_res);
			echo '<br/>';
			$url_long = $main_url.$assoc['url'];
			$data = collect_question_data_arr($url_long);
			$data_id = Insert_question_data($data,$id);
			// echo '<pre/>';var_dump($data_id);die;			
			$find_q_d_sql = "select * from question_data where id = '$id' limit 1";
			$find_q_d_res = mysql_query($find_q_d_sql);
			$assoc = mysql_fetch_assoc($find_q_d_res);
			show_question_data($assoc);			
		}
		echo '<a href="javascript:history.go(-1)">返回上一步</a>';
		// die();
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

function download_image($url, $fileName = '', $dirName = '', $fileType = array('jpg', 'gif', 'png'), $type = 1) {
	$dirName = "upload/images/";
	if ($url == '') {
		return false;
	}
	// 获取文件原文件名
	$defaultFileName = basename($url);
	// 获取文件类型
	$suffix = substr(strrchr($url, '.'), 1);
	if (!in_array($suffix, $fileType)) {
		return false;
	}
	// 设置保存后的文件名
	$fileName = $fileName == '' ? time() . rand(0, 9) . '.' . $suffix : $defaultFileName;
	// 获取远程文件资源
	if ($type) {
		$ch = curl_init();
		$timeout = 30;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file = curl_exec($ch);
		curl_close($ch);
	} else {
		ob_start();
		readfile($url);
		$file = ob_get_contents();
		ob_end_clean();
	}
	// 设置文件保存路径
	$dirName = $dirName . '/' . date('Ym', time());
	if (!file_exists($dirName)) {
		mkdir($dirName, 0777, true);
	}
	// 保存文件
	$res = fopen($dirName . '/' . $fileName, 'a');
	fwrite($res, $file);
	fclose($res);
	return array('fileName' => $fileName, 'saveDir' => $dirName);
}

//edit by FalsySun
function show_question_data($qdata){
	echo $qdata['id'];
	echo '<br/>';
	echo $qdata['content'];
	echo '<br/>';
	echo $qdata['answer'];
	echo '<br/>';
	echo $qdata['true_answer'];
	echo '<br/>';
	echo $qdata['analysis'];
	echo '<br/>';
}
function my_filter($tx){
	preg_match_all('/[a-zA-Z]/u',$tx,$jg);
	return join('',$jg[0]);
}
function myTrim($str){
	$search = array(" ","　","\n","\r","\t");
	$replace = array("","","","","");
	return str_replace($search, $replace, $str);
}
function my_arr_filter($arr){
	foreach($arr as $key=>&$val){
		if(strpos($val,'<img') !== false){
			continue;
		}
		else{
			$val = myTrim($val);
			if(empty($val)){
				unset($val);
				array_splice($arr, $key,1);
			}
		}		
	}
	return $arr;
}

function my_arr_filter_another($arr){
	foreach($arr as $key=>&$val){
		if($key == 0 && strlen($val)<8){
			$val = my_filter($val);
		}
		else{
			if(strpos($val,'<style') !== false || strpos($val,'<divclass') !== false){
				unset($val);
				array_splice($arr, $key,1);
			}
		}
	}
	$after_data = array();
	$after_data[0] = $arr[0];
	array_shift($arr);
	$after_data[1] = implode('',$arr);
	return $after_data;
}

function collect_question_data_arr($url){
	$returnP = getCurl($url);
	$page = array();
	$data = array();
	$arr = array();
	preg_match_all("/<ul class=\"option - list\".*?>.*?<\/ul>/ism", $returnP, $page);
	if(strpos($page[0][0],'<li>')===false){
		if(strpos($page[0][0],'<p>')!==false){
			$page[0][0] = str_replace('</p>', '</li>', $page[0][0]);
			$page[0][0] = str_replace('<p>', '<li>', $page[0][0]);
		}
		else if(strpos($page[0][0],'<br />')!==false){
			$page[0][0] = str_replace('<br />', '<li>', $page[0][0]);
			$page[0][0] = str_replace('<ul class="option - list">', '<li>', $page[0][0]);
		}		
	}
	$after_str = str_replace('</li>', '', $page[0][0]);
	$after_str = str_replace('</ul>', '', $after_str);
	$after_arr = explode("<li>", $after_str);
	array_shift($after_arr);

	$after_arr = my_arr_filter($after_arr);
	
	$data['content'] = array_shift($after_arr);
	$data['answer'] = implode(";", $after_arr);

	$hidden = array();
	preg_match_all("/<div class=\"topic-analysis\".*?>.*?<div class=\"mod-step-detail\">/ism", $returnP, $hidden);

	$later_str = str_replace('</p>', '<p>', $hidden[0][0]);
	$later_str = str_replace('<br />', '<p>', $later_str);
	$later_str = str_replace('</div>', '<p>', $later_str);
	$later_arr = explode("<p>", $later_str);
	array_shift($later_arr);
	// array_pop($later_arr);
	
	$later_arr = my_arr_filter($later_arr);
	$later_arr = my_arr_filter_another($later_arr);

	// var_dump($later_arr);die;
	if(sizeof($later_arr)>1 && strlen($later_arr[0])<=4){
		$data['true_answer'] = $later_arr[0];
		$data['analysis'] = empty($later_arr[1])? '' : $later_arr[1];
	}
	else{
		$data['true_answer'] = '';
		$data['analysis'] = empty($later_arr[0])? '' : implode('', $later_arr);

	}
	
	return $data;
}
function collect_question_title_arr($url){
	$returnP = getCurl($url);
	$page = array();
	$yema = array();
	preg_match_all("/<div class=\"pagination\".*?>.*?<\/div>/ism", $returnP, $page);
	if(!empty($page[0])){
		preg_match_all('/<a[^>].*>(.*)<\/a>/isU', $page[0][0], $yema);
		$true_yema = array();
		array_map(function($val) use(&$true_yema){
			array_shift($val);
			array_pop($val);
			$true_yema[] = $val;
		},$yema);
	}

	$question_arr = array();
	preg_match_all("/<a class=\"ll\".*?>.*?<\/a>/ism", $returnP, $question_arr);

	if(!empty($true_yema)){		
		foreach($true_yema[0] as $tk=>$tv){
			$question_temp = array();
			$tv_arr = explode("\"", $tv);
			$next_page = getCurl("http://www.hqwx.com".$tv_arr[3]);
			preg_match_all("/<a class=\"ll\".*?>.*?<\/a>/ism", $next_page, $question_temp);
			array_splice($question_arr[0],sizeof($question_arr[0]),0,$question_temp[0]);
		}
	}
	return $question_arr;
}

function Insert_question_data($data,$parentid){
	extract($data);
	$find_q_d_sql = "select * from question_data where content = '$content' and answer = '$answer' limit 1";
	$find_q_d_res = mysql_query($find_q_d_sql);
	$assoc = mysql_fetch_assoc($find_q_d_res);
	if($assoc){
		$id = $assoc['id'];
	}
	else{
		$addtime = date('Y-m-d H:i:s', time());
		$ins_q_d_sql = "insert into question_data (id,content,answer,true_answer,analysis,addtime) values ('$parentid','$content','$answer','$true_answer','$analysis','$addtime')";
		mysql_query($ins_q_d_sql);
		$id = mysql_insert_id();
	}
	return $id;
}

function Insert_question_title($arr,$parentid){
	$arrparentid = get_parentid($parentid).','.$parentid;
	$catid = $parentid;
	// echo $arrparentid;
	$id_arr = array();
	foreach($arr[0] as $key=>$val){
		$cur_quest_arr = array();
		$cur_quest_arr = explode("\"", $val);
		$url = $cur_quest_arr[3];
		$title = $cur_quest_arr[7];
		$find_q_sql = "select * from question where title = '$title' and url = '$url' limit 1";
		$find_q_res = mysql_query($find_q_sql);
		$assoc = mysql_fetch_assoc($find_q_res);
		// var_dump($assoc);
		if($assoc){
			$id_arr[] = $assoc['id'];
		}
		else{
			$addtime = date('Y-m-d H:i:s', time());
			$ins_q_sql = "insert into question (catid,arrparentid,title,url,addtime) values ('$catid','$arrparentid','$title','$url','$addtime')";
			// echo $top_cat_sql,'<br/>';
			mysql_query($ins_q_sql);
			$id_arr[] = mysql_insert_id();
		}	
	}
	return $id_arr;
	// var_dump($id_arr);
	// die;
}

function Insert_top_cat($arr,$parentid){
	$id_arr = Insert_any_one_cat($arr,$parentid);
	// var_dump($id_arr);die();
	header("location:./diy_one.php?status=1&parentid=".$parentid);
}

function Insert_fourth_cat($arr,$parentid){
	$id_arr = Insert_any_one_cat($arr,$parentid);
	// var_dump($id_arr);die();
	header("location:./diy_one.php?status=5&parentid=".$parentid);
}

function Insert_any_one_cat($arr,$parentid){
	$url_arr = $arr[0];
	$name_arr = $arr[1];
	$id_arr = array();
	foreach($url_arr as $key=>$val){
		$href_dir = find_href_dir($val);
		$addtime = date('Y-m-d H:i:s', time());
		$find_sql = "select * from category where parentid = '$parentid' and catname = '$name_arr[$key]' and url = '$href_dir' limit 1";
		// echo $find_sql,'<br/>';
		$find_res = mysql_query($find_sql);
		$assoc = mysql_fetch_assoc($find_res);
		// var_dump($assoc).'<br/>';
		if($assoc){
			$id_arr[] = $assoc['id'];
		}
		else{
			$arrparentid = get_parentid($parentid);
			$arrparentid = ($arrparentid == $parentid) ? $arrparentid : $arrparentid.','.$parentid;
			$top_cat_sql = "insert into category (parentid,arrparentid,catname,url,addtime) values ('$parentid','$arrparentid','$name_arr[$key]','$href_dir','$addtime')";
			// echo $top_cat_sql,'<br/>';
			mysql_query($top_cat_sql);
			$id_arr[] = mysql_insert_id();
		}		
	}
	return $id_arr;
}

function get_parentid($pid){
	if($pid == 0){
		return  $pid;
	}
	else{
		$pid_sql = "select arrparentid from category where id = '$pid' limit 1";
		$res = mysql_query($pid_sql);
		$dat = mysql_fetch_assoc($res);
		if(!empty($dat)){
			return $dat['arrparentid'];
		}
	}
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

function Insert_sec_third_cat($sec_all_arr,$third_all_arr,$sec_parenid){
	foreach($sec_all_arr as $key=>$val){
		$cur_id_arr = Insert_any_one_cat($sec_all_arr[$key],$sec_parenid);
		$thi_id_arr = Insert_any_one_cat($third_all_arr[$key],$cur_id_arr[0]);
	}
	header("location:./diy_one.php?status=3&parentid=".$sec_parenid);
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
		CollectION 
		<br/>
		<?php
		if (!empty($_GET['status']))
			echo '(步骤'.$_GET['status'].'-完成！)';
		?>
		<br/>
		<?php echo $main_url.$cur_dir?>		
	</p>
	
	调试
	<form action="diy_one.php" method="post">
		<input type="text" name="url" id="url" placeholder="网址" />
		<input type="submit" name="start" value="开始" />
		<input type="submit" name="stop" value="停止"/>
		<input type="hidden" name="status" value="<?php echo $const_status ?>"/>
		<input type="hidden" name="parentid" value="<?php echo $cur_parentid ?>"/>
	</form> 

	<?php
	if (!empty($_GET['status']))
	{
		$parentid = $cur_parentid;
		$next_status = $_GET['status'] + 1;
		?>
		<p>收集状态</p>
		<?php
		if($next_status==6){
			$sqlG = "select * from question where catid = ".$parentid." order by id";
			$rowg = mysql_query($sqlG);
			while($current = mysql_fetch_assoc($rowg)){
				echo $current['id'].'_#_'.'<a href="./diy_one.php?status='.$next_status.'&parentid='.$current['id'].'&cur_dir='.$current['url'].'">'.$current['title'].'</a>';
				echo '<br/>';
			}
		}
		else if($next_status==7 || $next_status==8){
			$sqlG = "select * from question_data where id = ".$parentid." order by id";
			$rowg = mysql_query($sqlG);
			while($current = mysql_fetch_assoc($rowg)){
			// var_dump($current);
				echo $current['content'];
				echo '<br/>';
				echo $current['answer'];
				echo '<br/>';
				echo $current['true_answer'];
				echo '<br/>';
				echo $current['analysis'];
				echo '<br/>';
			}
		}
		else{
			$sqlG = "select * from category where parentid = ".$parentid." order by id";
			$rowg = mysql_query($sqlG);
			while($current = mysql_fetch_assoc($rowg)){
				echo '<a href="./diy_one.php?status='.$next_status.'&parentid='.$current['id'].'&cur_dir='.$current['url'].'">'.$current['catname'].'</a>';
				if($next_status==5){
					echo '----'.'<a href="./diy_one.php?status=1024&parentid='.$current['id'].'&cur_dir='.$current['url'].'" >循环</a>';
				}
				echo '<br/>';
			}
		}
		
		var_dump($current);echo "-End-";die();		
		?>
		<div id='mydiv'>
		</div>
		<?php
	}
	?>

	<p></p>
	
	<script>function doCollect(type) {
		var urlP = document.getElementById("url").value;
		if(type == 'start') {
			if(urlP.length == 0) {
				alert('请输入网址');
				return false;
			}
			ajax({
				type: "POST",
				url: "diy_one.php",
				dataType: "json",
				data: {
					"url": urlP,
					"start": '1',
					"stop": '0'
				},
				beforeSend: function() {
					window.location = 'diy_one.php?status=2';
				},
				success: function(msg) {
					console.log(msg,253);
				},
				error: function() {
					console.log("error")
				}
			});
		} else if(type == 'stop') {
			ajax({
				type: "POST",
				url: "diy_one.php",
				dataType: "json",
				data: {
					"url": urlP,
					"start": '0',
					"stop": '1'
				},
				beforeSend: function() {},
				success: function(msg) {
					window.location.reload();
				},
				error: function() {
					console.log("error")
				}
			});
		}
	}

	function ajax() {
		var ajaxData = {
			type: arguments[0].type || "GET",
			url: arguments[0].url || "",
			async: arguments[0].async || "true",
			data: arguments[0].data || null,
			dataType: arguments[0].dataType || "text",
			contentType: arguments[0].contentType || "application/x-www-form-urlencoded",
			beforeSend: arguments[0].beforeSend || function() {},
			success: arguments[0].success || function() {},
			error: arguments[0].error || function() {}
		}
		ajaxData.beforeSend()
		var xhr = createxmlHttpRequest();
		xhr.responseType = ajaxData.dataType;
		xhr.open(ajaxData.type, ajaxData.url, ajaxData.async);
		xhr.setRequestHeader("Content-Type", ajaxData.contentType);
		xhr.send(convertData(ajaxData.data));
		xhr.onreadystatechange = function() {
			if(xhr.readyState == 4) {
				if(xhr.status == 200) {
					ajaxData.success(xhr.response)
				} else {
					ajaxData.error()
				}
			}
		}
	}

	function createxmlHttpRequest() {
		if(window.ActiveXObject) {
			return new ActiveXObject("Microsoft.XMLHTTP");
		} else if(window.XMLHttpRequest) {
			return new XMLHttpRequest();
		}
	}

	function convertData(data) {
		if(typeof data === 'object') {
			var convertResult = "";
			for(var c in data) {
				convertResult += c + "=" + data[c] + "&";
			}
			convertResult = convertResult.substring(0, convertResult.length - 1)
			return convertResult;
		} else {
			return data;
		}
	}</script>
	<?php
	if (!empty($_GET['status']) && $_GET['status'] == 2)
	{
		?>
		<script>window.onload = function() {
			var div = document.getElementById("mydiv");
			if(div) {
				var auto = setInterval(function() {
					changeH();
				}, 100);
			}

			function changeH() {
				var div = document.getElementById("mydiv");
				console.log(div,343);
				if(div.innerHTML != '' || div.innerHTML != null) {
					if(div.getElementsByTagName("p").length > 0) {
						var firstTitle = div.getElementsByTagName('p')[0].innerHTML;
						var id = '';

					} else {
						var firstTitle = 'noPmark';
						var id = document.getElementById("lastId").value;
					}
				}
				ajax({
					type: "POST",
					url: "ajax.php",
					dataType: "json",
					data: {
						"title": firstTitle,
						"id": id
					},
					beforeSend: function() {
					},
					success: function(msg) {
						console.log(msg,365);
						if(msg.html) {
							document.getElementById("mydiv").innerHTML = msg.html + document.getElementById("mydiv").innerHTML;
						}
						console.log(msg,369);
						if(msg.end == '1') {
							window.clearTimeout(auto);
						// document.getElementById('num-box').innerHTML = document.getElementById('num-box').innerHTML + '(采集完成)';
						document.getElementById('button').innerHTML = "<input type='button' name='start' value='开&始' onclick=doCollect('start'); />";
					}
				},
				error: function() {
					console.log("error")
				}
			});
			}
		}<?php } ?>
	</script>
</body>
</html>