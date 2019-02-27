<?php
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
	$arr = array();
	switch($const_status){
		default:
		case 0:
		case 1:
		preg_match_all("/<ul class=\"lesson_con clearfix\".*?>.*?<\/ul>/ism", $returnP, $page);
		preg_match_all('/<a[^>].*>(.*)<\/a>/isU', $page[0][0], $arr);
		Insert_top_cat($arr);
		break;
		case 2:
		var_dump($returnP);die();	

		break;
	}
	die(123);
	

	
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
function Insert_top_cat($arr){
	// echo "Already Done";die();
	$url_arr = $arr[0];
	$name_arr = $arr[1];
	foreach($url_arr as $key=>$val){
		$href_arr = explode("\"", $val);
		$addtime = date('Y-m-d H:i:s', time());
		$find_sql = "select * from category where parentid = 0 and catname = '$name_arr[$key]' and url = '$href_arr[3]' limit 1";
		$find_res = mysql_query($find_sql);
		if(!mysql_fetch_assoc($find_res)){
			$top_cat_sql = "insert into category (parentid,catname,url,addtime) values (0,'$name_arr[$key]','$href_arr[3]','$addtime')";
			mysql_query($top_cat_sql);
		}		
	}
	header("location:./diy_one.php?status=1");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>CollectION</title>
</head>
<body>	
	<p>CollectION </p>
	 <?php
	if (!empty($_GET['status']) && $_GET['status'] == 1)
		echo '(步骤'.$_GET['status'].'-收集完成！)';
	?></p>
	<!-- <input type="text" name="url" id="url" value="" placeholder="网址" /> -->
	<?php echo $main_url.$cur_dir?>
	<p id="button" >
		<?php
		if (file_exists($control)) {
			?>
			<!-- <input type="button" name="stop" value="停止" onclick="doCollect('stop');" /> -->
		<?php } else { ?>
			<!-- <input type="button" name="start" value="开始" onclick="doCollect('start');" /> -->
		<?php } ?>
	</p>
	调试
	<form action="diy_one.php" method="post">
		<input type="text" name="url" id="url" placeholder="网址" />
		<input type="submit" name="start" value="开始" />
		<input type="submit" name="stop" value="停止"/>
		<input type="hidden" name="status" value="<?php echo $const_status ?>"/>
	</form> 

	<?php
	if (!empty($_GET['status']) && $_GET['status'] == 1)
	{
		$parentid = $_GET['status'] - 1;
		?>
		<p>收集状态</p>
		<?php
		$sqlG = "select * from category where parentid = ".$parentid." order by id";
		$rowg = mysql_query($sqlG);
		while($current = mysql_fetch_assoc($rowg)){
			echo '<a href="./diy_one.php?status=2&cur_dir='.$current['url'].'">'.$current['catname'].'</a>';
			echo '<br/>';
		}
		var_dump($current);die();		
		?>
		<input type="text" readonly id='lastId' value="<?php echo $firstId; ?>" />
		<div id='mydiv'>
		</div>
		<?php
	}
	?>

	<p></p>
	<?php
	if (!empty($_GET['status']) && $_GET['status'] == 2)
	{
		?>
		<p>收集状态</p>
		<?php
		$sqlG = "select * from article order by id desc limit 1";
		$rowg = mysql_query($sqlG);
		$first = mysql_fetch_assoc($rowg);
		if ($first) {
			$firstId = $first['id'];
		} else {
			$firstId = '0';
		}
		?>
		<input type="text" readonly id='lastId' value="<?php echo $firstId; ?>" />
		<div id='mydiv'>
		</div>
		<?php
	}
	?>
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