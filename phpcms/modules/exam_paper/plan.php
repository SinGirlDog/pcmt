<?php
//模拟考试
class exam{

	const $exam_jianzao = array(
		'0' => 'fagui',
		'1' => 'jingji',
		'2' => 'guanli',
		'3' => 'jidian',
		'4' => 'jianzhu',
		'5' => 'shizheng',
		'6' => 'gonglu',
		'7' => 'shuili',
		'8' => 'tongxin',
	);
	const $exam_xiaofang = array(
		'0' => 'jishushiwu',
		'1' => 'zonghenengli',
		'2' => 'anlifenxi',
	);

	const $exam_quest_type = array(
		'1' => 'choice_only',
		'2' => 'choice_more',
		'3' => 'fillinblank',
		'4' => 'objective',
	);

	const $exam_db = array(
		'exam_admin' => '',
		'exam_user' => '',
		'exam_category' => '',
		'exam_question_category' => '',
		'exam_question' => '',
		'exam_paper' => '试卷',
		'exam_answer' => '答题卡',
	);
}
?>