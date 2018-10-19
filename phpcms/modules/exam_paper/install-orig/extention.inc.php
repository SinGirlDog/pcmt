<?php
defined('IN_PHPCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');

$parentid = $menu_db->insert(array('name'=>'exam_paper', 'parentid'=>29, 'm'=>'exam_paper', 'c'=>'exam_paper', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'setting', 'parentid'=>$parentid, 'm'=>'exam_paper', 'c'=>'exam_paper', 'a'=>'setting', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'unreply', 'parentid'=>$parentid, 'm'=>'exam_paper', 'c'=>'exam_paper', 'a'=>'unreplylist', 'data'=>'', 'listorder'=>0, 'display'=>'1'));

$language = array('exam_paper'=>'模拟考试', 'setting'=>'考试配置', 'unreply'=>'暂时留空');
?>