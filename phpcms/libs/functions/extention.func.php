<?php
/**
 *  extention.func.php 用户自定义函数库
 *
 * @copyright			(C) 2005-2010 PHPCMS
 * @license				http://www.phpcms.cn/license/
 * @lastmodify			2010-10-27
 */


/**
* 调整栏目链接
* 
* @param	string	$url	路径
* @param	string	$site_name	站点目录
* @return	string	路径
*/
function cat_url($url,$site_name) {
	$find = '/html/';
	$html_dir = 'html';
	$site_block = '/'.$html_dir.'/'.$site_name.'/'.$html_dir.'/';
	$new_url = str_replace($find,$site_block, $url);
	return $new_url;
}

function check_wap()
{
	if (isset($_SERVER['HTTP_VIA'])) return true;
	if (isset($_SERVER['HTTP_X_NOKIA_CONNECTION_MODE'])) return true;
	if (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID'])) return true;
	if (strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML") > 0) {
        // Check whether the browser/gateway says it accepts WML.
		$br = "WML";
	} else {
		$browser = isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';
		if (empty($browser)) return true;
		$clientkeywords = array(
			'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-'
			, 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu',
			'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini',
			'operamobi', 'opera mobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile'
		);
		if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", $browser) && strpos($browser, 'ipad') === false) {
			$br = "WML";
		} else {
			$br = "HTML";
		}
	}
	if ($br == "WML") {
		return TRUE;
	} else {
		return FALSE;
	}
}


?>