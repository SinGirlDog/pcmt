<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>sec_cat_paper_list</title>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="太奇兴宏程,一级建造师,二级建造师"/>
	<meta name="description" content="太奇兴宏程作为建造师精准信息专家，一级建造师培训、二级建造师培训高通过率,零基础拿证,在中国每两个建造师就有一个来自太奇兴宏程"/>
	<meta name="viewport" content="width=device-width,initial-scale=1">

	<link rel="stylesheet" href="http://www.xhcedu.com/wap/style/new_mip.css" />
	<link rel="stylesheet" href="http://www.xhcedu.com/wap/style/exam_mobile.css" />

	<script type="text/javascript" src="<?php echo APP_PATH;?>statics_EXAM/js/jquery1.7.1.js"></script>
	<script type="text/javascript" src="<?php echo APP_PATH;?>statics_EXAM/js/welcome.js"></script>
</head>
<body>
	<?php include template("mobile","header_m"); ?>
	<div id="welcome">
		<form method="post" name="put_cat_form" id="put_cat_form" action="index.php?m=exam&c=index&a=choose_sec_cat">
			<?php include template("../xhc/content","position_exam"); ?>
			<?php include template("../xhc/content","sec_cat_block"); ?>
			
			<input type="hidden" name="thi_cat" id="thi_cat" value="<?php echo $thi_cat; ?>">
		</form>
	</div>
	<div id="third_commen_list">
		<div id="third_left">
			<ul>
				<li>
					科目：
					<select>
						<option>-请选择-</option>
						<?php
						foreach($list_thi as $rditem)
						{
							if($rditem['catid'] == $thi_cat)
							{
								$selected = "selected='true'";
							}
							else
							{
								$selected = "";
							}
							?>
							<option value="<?php echo $rditem['catid'];?>" <?php echo $selected;?>><?php echo $rditem['catname'];?></option>
							<?php
						}
						?>
					</select>
				</li>
			</ul>
		</div>
		<div id="third_file_list">
			<form method="post" name="put_fileid_form" id="put_fileid_form" action="index.php?m=exam&c=index&a=make_one_paper_by_fileid">
				<input type="hidden" name="fileid" id="fileid" value="">
			</form>
			<ul>
				<?php
				foreach($file_list as $file)
				{
					echo '<li id="'.$file['id'].'"><em style="color:#0f78b5;font-weight: 400;">$=> </em><a>'.$file['cattitle'].$file['title'].'</a></li>';
				}
				?>
			</ul>
		</div>
	</div>
	<div style="clear:both;"></div>
	<?php include template("mobile","footer_m"); ?>
</body>
</html>
