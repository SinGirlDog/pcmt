<?php
defined('IN_PHPCMS') or exit('No permission resources.');
class xml {
	function init(){
		$dir = "index.xml";
		 if (file_exists($dir)){
			$xml=simplexml_load_file("index.xml");
			echo $xml;
			// print_r($xml);
		 }
		 else{
		 	echo 'no file xml'.$dir;
		 }
		
	}
}


?>