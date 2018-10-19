<?php
defined('IN_PHPCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');

$parentid = $menu_db->insert(array('name'=>'exam', 'parentid'=>29, 'm'=>'exam', 'c'=>'exam_paper', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'exam_paper', 'parentid'=>$parentid, 'm'=>'exam', 'c'=>'exam_paper', 'a'=>'init', 'data'=>'', 'listorder'=>1, 'display'=>'1'));
$menu_db->insert(array('name'=>'exam_answer', 'parentid'=>$parentid, 'm'=>'exam', 'c'=>'exam_answer', 'a'=>'init', 'data'=>'', 'listorder'=>2, 'display'=>'1'));
$menu_db->insert(array('name'=>'exam_file', 'parentid'=>$parentid, 'm'=>'exam', 'c'=>'exam_file', 'a'=>'init', 'data'=>'', 'listorder'=>3, 'display'=>'1'));
$menu_db->insert(array('name'=>'setting_QandA', 'parentid'=>$parentid, 'm'=>'exam', 'c'=>'exam_paper', 'a'=>'setting_QandA', 'data'=>'', 'listorder'=>4, 'display'=>'1'));
$menu_db->insert(array('name'=>'setting', 'parentid'=>$parentid, 'm'=>'exam', 'c'=>'exam_paper', 'a'=>'setting', 'data'=>'', 'listorder'=>5, 'display'=>'1'));

$language = array('exam_paper'=>'考试卷', 'exam_answer'=>'答题卡', 'exam_file'=>'习题卷宗', 'setting_QandA'=>'问答设置', 'setting'=>'配置');
?>