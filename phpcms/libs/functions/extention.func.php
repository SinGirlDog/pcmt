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
 
?>