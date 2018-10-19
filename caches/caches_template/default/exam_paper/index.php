<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>exam_info_index_html</title>
	<script type="text/javascript" src="<?php echo APP_PATH;?>statics_EXAM/js/jquery1.7.1.js"></script>
	<script type="text/javascript" src="<?php echo APP_PATH;?>statics_EXAM/js/ajax.js"></script>
	<script type="text/javascript" src="<?php echo APP_PATH;?>statics_EXAM/js/low_validate.js"></script>
	<!-- 首尾样式 -->
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH_XHC;?>18xhc/style.css">
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH_XHC;?>18xhc/index.css">
	<link rel="stylesheet" type="text/css" href="<?php echo APP_PATH;?>statics_EXAM/css/exam_index.css">

</head>
<body>
	<?php include template("../xhc/content","header_NEW"); ?>
	<div id="exam_form">
		<form method="post" name="get_info_form" id="get_info_form" action="index.php?m=exam&c=index&a=collect_exam_visitor_info">
			<ul>
				<li>
					<span>考试科目:</span>
					<select name="cat_level_1" id="select_1" class="select_ajax">
						<option value ="">-请选择-</option>
						<?php foreach($category_one as $key=>$val){?>
						<option value ="<?php echo $val['catid'];?>"><?php echo $val['catname'];?></option>
						<?php }?>
					</select>
				</li>
				<li>
					<span>考生姓名:</span>
					<input type="text" name="name" id="name" value="" placeholder="请填写姓名" />
				</li>
				<li>
					<span>手机号码:</span>
					<input type="text" name="mobile" id="mobile" value="" placeholder="请填11位手机号码" />
				</li>
				<li>
					<input type="button" id="get_info_ajax_button" value="确定">
				</li>
			</ul>
		</form>
	</div>
	<?php include template("../xhc/content","footer_NEW"); ?>

</body>
</html>
