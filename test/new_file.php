<?php
set_time_limit(0);
$conn = mysql_connect('localhost', 'root', 'root') or die("error connecting");
mysql_query("set names 'utf8'");
mysql_select_db('article');
/**/
$control = './collect.txt'; //控制识别文件
if ($_POST) {
	if (!empty($_POST['start']) && !empty($_POST['url'])) {
		if (!file_exists($control)) {
			addTxt(); //开始，条件识别文件
		}
	} else if (!empty($_POST['stop'])) {
		if (file_exists($control)) {
			unlink("collect.txt");
			header("location:./new_file.php");
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
	preg_match("/<div class=\"page_menu\".*?>.*?<\/div>/ism", $returnP, $page);
	preg_match_all('/<a[^>].*>(.*)<\/a>/isU', $page[0], $arr);
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

	foreach ($pageUrl as $page => $pageV) {
		$returnP = getCurl($pageV);
		/*获取页面内文章的链接地址*/
		if (preg_match_all("/<ul class=\"news\".*?>.*?<\/ul>/ism", $returnP, $matches)) {
			preg_match_all('/href="([^"]*)"/', $matches[0][0], $link, PREG_SET_ORDER);
			$url = '';
			foreach ($link as $k => $v) {
				$url[$k] = $urlOne . mb_substr($v[0], 6, -1);
			}
			/*从新排序页面内文章的链接地址*/
			$url = array_merge(array_unique($url));
			/* 获取文章页面内容*/
			foreach ($url as $k => $v) {
				if (file_exists($control)) {
					$return = getCurl($v);
					preg_match("/<div class=\"leftsidebar\".*?>.*?<\/div>/ism", $return, $navMenu);
					$return = str_replace($navMenu, '', $return);
					/*获取标题*/
					if (!preg_match("/<h1>.*?<\/h1>/ism", $return, $title)) {
						preg_match("/<h2>.*?<\/h2>/ism", $return, $title);
					}
					/*检测是否存在*/
					$title = mb_substr($title[0], 4, -5);
					$checkSql = "select * from article where title = '$title'";
					$checkResult = mysql_query($checkSql);
					$row = mysql_fetch_row($checkResult);
					/*检测是否存在*/
					if (!$row) {
						preg_match("/<div class=\"title_news\".*?>.*?<\/div>/ism", $return, $topInfo); //获取头部内容-标题-描述
						
						/*获取描述部分内容*/
						preg_match("/<p>.*?<\/p>/ism", $topInfo[0], $description);
						$description = mb_substr($description[0], 3, -4);
						
						/*替换页面中所有不需要的内容*/
						@preg_match_all(array("/<div class=\"float\".*?>.*?<\/div>/ism", "/<header>.*?<\/header>/ism", "/<nav>.*?<\/nav>/ism", "/<footer>.*?<\/footer>/ism", "/<div class=\"footer\".*?>.*?<\/div>/ism"), '', $return);
						$return = str_replace($topInfo, '', $return);
						
						/*获取内容*/
						preg_match("/<div class=\"content\".*?>.*?<\/div>/ism", $return, $content);

						preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/', $content[0], $img1);
						
						/*过滤图片标签属性*/
						foreach ($img1[0] as $k => $v) {
							$nv = preg_replace(array('/data-src=".+?"/', '/data-type=".+?"/', '/data-ratio=".+?"/', '/data-w=".+?"/', '/data-fail=".+?"/', '/_width=".+?"/', '/class/'), '', $v);
							$nv = preg_replace(array('/alt=".+?"/', '/title=".+?"/'), array('alt="' . $title . '"', 'title="' . $title . '"'), $nv);
							$content = str_replace($v, $nv, $content);
						}

						preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/', $content[0], $img);
						
						/*把图片下载到本地，替换路径*/
						foreach ($img[1] as $k => $v) {
							$imgSrc = 'http://www.pgdec.com' . $v;
							$newImg = download_image($imgSrc);
							$content = str_replace($v, './' . $newImg['saveDir'] . '/' . $newImg['fileName'], $content);
						}

						$content = mb_substr($content[0], 21, -6); //截取内容，过滤大盒子  可忽略
						$content = str_replace(array('		', '<!--新闻标题 begin-->', '<!--新闻标题 end-->'), '', $content); //过滤部分内容
						$content = preg_replace("#<(/?a.*?)>#si", '', $content);//过滤文章A标签
						preg_match_all("/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/", $content, $cover_img); 
						$cover_img = $cover_img[1][0];//获取第一张图片
						$sql = "insert into article (title,description,content,cover_img) values ('$title','$description','$content','$cover_img')";
						mysql_query($sql);
					}
				}
			}
		}
	}
	unlink("collect.txt");
	header("location:./new_file.php?status=1");
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>文章收集</title>
</head>
<body>	
	<p>全部采集  <?php
	if (!empty($_GET['status']) && $_GET['status'] == 1)
		echo '(收集完成！)';
	?></p>
	<input type="text" name="url" id="url" placeholder="第一页网址" />

	<p id="button" >
		<?php
		if (file_exists($control)) {
			?>
			<input type="button" name="stop" value="停止" onclick="doCollect('stop');" />
		<?php } else { ?>
			<input type="button" name="start" value="开始" onclick="doCollect('start');" />
		<?php } ?>
	</p>
<!--调试
<form action="new_file.php" method="post">
	<input type="text" name="url" id="url" placeholder="第一页网址" />
	<input type="submit" name="start" value="开始" />
	<input type="submit" name="stop" value="停止"/>
</form> 
-->
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
	<input type="hidden" id='lastId' value="<?php echo $firstId; ?>" />
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
			url: "new_file.php",
			dataType: "json",
			data: {
				"url": urlP,
				"start": '1',
				"stop": '0'
			},
			beforeSend: function() {
				window.location = 'new_file.php?status=2';
			},
			success: function(msg) {},
			error: function() {
				console.log("error")
			}
		});
	} else if(type == 'stop') {
		ajax({
			type: "POST",
			url: "new_file.php",
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
			if(div.innerHTML != '' || div.innerHTML != null) {
				if(div.getElementsByTagName("p").length > 0) {
					var firstTitle = div.getElementsByTagName('p')[0].innerHTML;
					var id = '';

				} else {
					var firstTitle = 'null';
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
					if(msg.html) {
						document.getElementById("mydiv").innerHTML = msg.html + document.getElementById("mydiv").innerHTML;
					}
					if(msg.end == '1') {
						window.clearTimeout(auto);
						document.getElementById('num-box').innerHTML = document.getElementById('num-box').innerHTML + '(采集完成)';
						document.getElementById('button').innerHTML = "<input type='button' name='start' value='开始' onclick=doCollect('start'); />";
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