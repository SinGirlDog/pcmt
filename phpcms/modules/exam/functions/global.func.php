<?php
defined('IN_PHPCMS') or exit('No permission resources.');


function my_hidden_funciton($super_arr){
	$wel_hidden = '';
	foreach($super_arr as $key => $val){
		$wel_hidden .= '<input type="hidden" name="'.$key.'" value="'.$val.'">';
	}
	echo $wel_hidden;
}

function my_list_sec_func($list_sec_arr){
	$list_sec = '';
	foreach($list_sec_arr as $key => $val){
		$list_sec .= '<li><a>'.$val['catname'].'</a></li>';
	}
	echo $list_sec;
}
?>