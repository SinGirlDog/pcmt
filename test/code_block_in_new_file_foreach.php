<?php
//原来的文章抓取-暂时不用
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
	// header("location:./diy_one.php?status=1");
	?>