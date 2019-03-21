<?php
session_start();

defined('IN_ADMIN') or exit('No permission resources.');

include $this->admin_tpl('header','admin');


?>

<div id="closeParentTime" style="display:none"></div>

<div class="pad-10">

	<div class="content-menu ib-a blue line-x" style="display:none;">

		<a href="?m=content&c=content&a=init&catid=<?php echo $catid;?>&pc_hash=<?php echo $pc_hash;?>" <?php if($steps==0 && !isset($_GET['reject'])) echo 'class=on';?>><em><?php echo L('check_passed');?></em></a><span>|</span>

		<?php echo $workflow_menu;?> <a href="javascript:;" onclick="javascript:$('#searchid').css('display','');"><em><?php echo L('search');?></em></a> 

		<?php if($category['ishtml']) {?>

			<span>|</span><a href="?m=content&c=create_html&a=category&pagesize=30&dosubmit=1&modelid=0&catids[0]=<?php echo $catid;?>&pc_hash=<?php echo $pc_hash;?>&referer=<?php echo urlencode($_SERVER['QUERY_STRING']);?>"><em><?php echo L('update_htmls',array('catname'=>$category['catname']));?></em></a>

		<?php }?>

	</div>

	<div id="searchid">

		<form name="searchform" action="" method="get" >

			<input type="hidden" value="other" name="m">

			<input type="hidden" value="activityadmin" name="c">

			<input type="hidden" value="init" name="a">

			<input type="hidden" value="1" name="search">

			<input type="hidden" value="<?php echo $pc_hash;?>" name="pc_hash">

			<table width="100%" cellspacing="0" class="search-form">

				<tbody>

					<tr>

						<td>

							<div class="explain-col">

								<?php echo L('添加时间');?>：

								<?php echo form::date('start_time',$_GET['start_time'],0,0,'false');?>- 

								&nbsp;<?php echo form::date('end_time',$_GET['end_time'],0,0,'false');?>

								手机:<input name="mobile" type="text" value="" class="input-text" style="width:100px;"/>

								姓名:<input name="name" type="text" value="" class="input-text" style="width:100px;"/>

								<!-- 项目：<select name="bkxm">
									<option value="kindergarten" selected="selected">幼儿</option>
									<option value="primary">小学</option>
									<option value="middle">中学</option>
								</select> -->

								<input type="submit" class="button" value="<?php echo L('搜索');?>" />

							</div>

						</td>

					</tr>

				</tbody>

			</table>

		</form>

	</div>

	<form name="myform" id="myform" action="" method="post" >

		<div class="table-list">

			<table width="100%">

				<thead>

					<tr>

						<th width="16" style="display:none;"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>

						<th width="37" style="display:none;"><?php echo L('listorder');?></th>

						<th>ID</th>

						<th><?php echo L('电话');?></th>
						<th><?php echo L('姓名');?></th>
						<th><?php echo L('微信号');?></th>
						<th><?php echo L('英语成绩');?></th>
						<th><?php echo L('综合成绩');?></th>
						<th><?php echo L('总分');?></th>
						<th><?php echo L('添加时间');?></th>

						<th style="display:none;"><?php echo L('operations_manage');?></th>

					</tr>

				</thead>

				<tbody>

					<?php

					if(is_array($datas)) {

						$sitelist = getcache('sitelist','commons');

						$release_siteurl = $sitelist[$category['siteid']]['url'];

						$path_len = -strlen(WEB_PATH);

						$release_siteurl = substr($release_siteurl,0,$path_len);

						$this->hits_db = pc_base::load_model('hits_model');



						foreach ($datas as $r) {

							$hits_r = $this->hits_db->get_one(array('hitsid'=>'c-'.$modelid.'-'.$r['id']));


							if($r['city']==$city1 || $city1==""){

								?>

								<tr>

									<td align="center" style="display:none;"><input class="inputcheckbox " name="ids[]" value="<?php echo $r['id'];?>" type="checkbox"></td>

									<td align='center' style="display:none;"><input name='listorders[<?php echo $r['id'];?>]' type='text' size='3' value='<?php echo $r['listorder'];?>' class='input-text-c'></td>
									<td align='center' ><?php echo $r['id'];?></td>
									<td align='center' ><?php echo $r['name'];?></td>
									<td align='center' ><?php echo $r['mobile'];?></td>
									<td align='center' ><?php echo $r['wxnum'];?></td>
									<td align='center' ><?php echo $r['english'];?></td>
									<td align='center' ><?php echo $r['zonghe'];?></td>
									<td align='center' ><?php echo $r['total'];?></td>
									<td align='center'><?php echo $r['addtime'];?></td>
									<!-- <td align='center'><?php echo format::date($r['addtime'],1);?></td> -->
									<td align='center' style="display:none;"></td>
								</tr>
							<?php }
						}
					}
					?>
				</tbody>

			</table>

			<div class="btn" style="display:none;"><label for="check_box"><?php echo L('selected_all');?>/<?php echo L('cancel');?></label>

				<input type="hidden" value="<?php echo $pc_hash;?>" name="pc_hash">

				<input type="button" class="button" value="<?php echo L('listorder');?>" onclick="myform.action='?m=content&c=content&a=listorder&dosubmit=1&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>';myform.submit();"/>

				<?php if($category['content_ishtml']) {?>

					<input type="button" class="button" value="<?php echo L('createhtml');?>" onclick="myform.action='?m=content&c=create_html&a=batch_show&dosubmit=1&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>';myform.submit();"/>

				<?php }

				if($status!=99) {?>

					<input type="button" class="button" value="<?php echo L('passed_checked');?>" onclick="myform.action='?m=content&c=content&a=pass&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>';myform.submit();"/>

				<?php }?>

				<input type="button" class="button" value="<?php echo L('delete');?>" onclick="myform.action='?m=content&c=content&a=delete&dosubmit=1&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>';return confirm_delete()"/>

				<?php if(!isset($_GET['reject'])) { ?>

					<input type="button" class="button" value="<?php echo L('push');?>" onclick="push();"/>

					<?php if($workflow_menu) { ?><input type="button" class="button" value="<?php echo L('reject');?>" onclick="reject_check()"/>

					<div id='reject_content' style='background-color: #fff;border:#006699 solid 1px;position:absolute;z-index:10;padding:1px;display:none;'>

						<table cellpadding='0' cellspacing='1' border='0'><tr><tr><td colspan='2'><textarea name='reject_c' id='reject_c' style='width:300px;height:46px;'  onfocus="if(this.value == this.defaultValue) this.value = ''" onblur="if(this.value.replace(' ','') == '') this.value = this.defaultValue;"><?php echo L('reject_msg');?></textarea></td><td><input type='button' value=' <?php echo L('submit');?> ' class="button" onclick="reject_check(1)"></td></tr>

						</table>

					</div>

				<?php }}?>

				<input type="button" class="button" value="<?php echo L('remove');?>" onclick="myform.action='?m=content&c=content&a=remove&catid=<?php echo $catid;?>';myform.submit();"/>

				<?php echo runhook('admin_content_init')?>

			</div>

			<div id="pages"><?php echo $pages;?></div>



		</div>

	</form>

	<form name="searchform" action="" method="get" >

		<input type="hidden" value="other" name="m">

		<input type="hidden" value="teacherquality" name="c">

		<input type="hidden" value="excel_export" name="a">

		<input type="hidden" value="1" name="search">

		<input type="hidden" value="<?php echo $pc_hash;?>" name="pc_hash">

		<table width="100%" cellspacing="0" class="search-form">

		</table>

	</form>

</div>

<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>cookie.js"></script>

<script type="text/javascript"> 

	<!--

		function push() {

			var str = 0;

			var id = tag = '';

			$("input[name='ids[]']").each(function() {

				if($(this).attr('checked')=='checked') {

					str = 1;

					id += tag+$(this).val();

					tag = '|';

				}

			});

			if(str==0) {

				alert('<?php echo L('you_do_not_check');?>');

				return false;

			}

			window.top.art.dialog({id:'push'}).close();

	window.top.art.dialog({title:'<?php echo L('push');?>：',id:'push',iframe:'?m=content&c=push&action=position_list&catid=<?php echo $catid?>&modelid=<?php echo $modelid?>&id='+id,width:'800',height:'500'}, function(){var d = window.top.art.dialog({id:'push'}).data.iframe;// 使用内置接口获取iframe对象

		var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'push'}).close()});

}

function confirm_delete(){

	if(confirm('<?php echo L('confirm_delete', array('message' => L('selected')));?>')) $('#myform').submit();

}

function view_comment(id, name) {

	window.top.art.dialog({id:'view_comment'}).close();

	window.top.art.dialog({yesText:'<?php echo L('dialog_close');?>',title:'<?php echo L('view_comment');?>：'+name,id:'view_comment',iframe:'index.php?m=comment&c=comment_admin&a=lists&show_center_id=1&commentid='+id,width:'800',height:'500'}, function(){window.top.art.dialog({id:'edit'}).close()});

}

function reject_check(type) {

	if(type==1) {

		var str = 0;

		$("input[name='ids[]']").each(function() {

			if($(this).attr('checked')=='checked') {

				str = 1;

			}

		});

		if(str==0) {

			alert('<?php echo L('you_do_not_check');?>');

			return false;

		}

		document.getElementById('myform').action='?m=content&c=content&a=pass&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&reject=1';

		document.getElementById('myform').submit();

	} else {

		$('#reject_content').css('display','');

		return false;

	}	

}

setcookie('refersh_time', 0);

function refersh_window() {

	var refersh_time = getcookie('refersh_time');

	if(refersh_time==1) {

		window.location.reload();

	}

}

setInterval("refersh_window()", 3000);

//-->

</script>

</body>

</html>