<?php

function random($length, $chars = '0123456789') { 

	$hash = ''; 
	$max = strlen($chars) - 1; 
	for($i = 0; $i < $length; $i++) { 
		$hash .= $chars[mt_rand(0, $max)]; 
	} 
	return $hash; 
} 
//这里重点：只要把random第二参数重新打乱，不要使用以前v9固定的数据 
print_r(random(32, 'abcdefghigklmnopqrstuvwxyz1294567890ABCDEFGHIGKLMNOPQRSTUVWXYZ'));echo '<br/>';//phpsso_auth_key 
print_r(random(20, 'abcdefghigklmnopqrstuvwxyz1294567890ABCDEFGHIGKLMNOPQRSTUVWXYZ'));exit;//auth_key 
?>
