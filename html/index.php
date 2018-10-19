<?php
$htp_host = explode('.',$_SERVER['HTTP_HOST']);
// var_dump($htp_host);

switch($htp_host[0]){
	case 'local':
		header('location:../');
	break;
	default:
		header('location:/'.$htp_host[0].'/index.html');
	break;
}