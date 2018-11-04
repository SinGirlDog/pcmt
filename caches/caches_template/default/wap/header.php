<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><?php wmlHeader();?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Cache-Control" content="no-cache"/>
		<meta name="viewport" content="width=device-width; initial-scale=1.3; minimum-scale=1.0; maximum-scale=2.0"/>
		<meta name="MobileOptimized" content="240"/>
		<meta name="format-detection" content="telephone=no" />
        <title><?php echo $WAP['sitename'];?></title>
		<style type="text/css">
		body,div,p,table,textarea,form,h2,h3,h4,h5,ul,li{margin:0; padding:0; color:#303030;line-height:160%;}
		select, input, img{vertical-align:middle; border:none;}
		body{background:#FFF;-webkit-user-select:none;-webkit-text-size-adjust:none;}
		a{text-decoration:none;color:#09c;}
		
		.wrap{padding:3px 3px 0px; line-height:130%; font-size:14px;}
		.tc{text-align:center; margin:10px 0px;}
		.nav_logo{line-height:26px; margin-bottom:4px; color:#999; font-weight:bold;}
		.nav_logo img{margin-right:10px;}
		.hot{color:#ff0; background:#09c; padding:3px;}
		.hot a{text-decoration:underline; color:#fff;}
		
		h2{background:#efefef; color:#666; margin:6px 0px; padding:2px 6px; font-size:14px;}
		h2 a{margin-left:0.5em; font-size:12px; font-weight:100; color:#666; text-decoration:underline;}
		h2 a.cat{font-weight:bold; font-size:14px; margin-left:0px; color:#09c; margin-right:4px; text-decoration:none;}
		.ft{ background:#efefef; text-align:center; padding:10px 0px;}
		</style>
</head>
<body>
<div class="wrap">
	<div class="nav_logo" id="hd">
    	<span><a href="<?php echo WAP_SITEURL;?>"><img src="<?php echo $WAP['logo'];?>" width="70" height="50" alt="phpcms" align="left" /></a></span>
<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"wap\" data=\"op=wap&tag_md5=8139de1c2a6ce0708f0adc9fabf84232&action=type&typeid=0&num=10&siteid=%24siteid\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$wap_tag = pc_base::load_app_class("wap_tag", "wap");if (method_exists($wap_tag, 'type')) {$data = $wap_tag->type(array('typeid'=>'0','siteid'=>$siteid,'limit'=>'10',));}?>
 <?php $n=1; if(is_array($data)) foreach($data AS $k => $v) { ?>
	   <a href="<?php echo list_url($v[typeid]);?>"><?php echo $v['typename'];?></a>
	   <?php if($n%2!=0) { ?>&nbsp;|&nbsp;<?php } else { ?><br/><?php } ?>
	  <?php $n++;}unset($n); ?>
<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>		
    </div>
 <div class="hot">
    	热词：<?php echo hotword();?> 
    </div>