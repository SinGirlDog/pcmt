<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><?php include template("wap","header"); ?>
<h2><a href="#" class="cat">焦点推荐</a>&nbsp;<span style="color:#999; font-size:12px; font-weight:100;"><?php echo date('Y-m-d',SYS_TIME);?></span></h2>
<div>
<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"wap\" data=\"op=wap&tag_md5=bcf15cff06023e7dddcc42ae633f9bd4&action=position&posid=12&order=listorder+DESC&num=6\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$wap_tag = pc_base::load_app_class("wap_tag", "wap");if (method_exists($wap_tag, 'position')) {$data = $wap_tag->position(array('posid'=>'12','order'=>'listorder DESC','limit'=>'6',));}?>
 <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
	<a href="<?php echo $r['url'];?>"><?php echo str_cut($r['title'],26,'');?></a><br/>
   <?php $n++;}unset($n); ?>  
 <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?> 
</div>
<div class="ad1"><a href="#"><img src="<?php echo IMG_PATH;?>wap/ad220x26.gif" alt="phpcms手机网建站利器" width="220" height="26" /></a></div>
<?php $n=1;if(is_array(subtype(0))) foreach(subtype(0) AS $s) { ?>
	<?php $typeid=$s[typeid]?>
	<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"wap\" data=\"op=wap&tag_md5=47c3f9c93ab9c326834cd971c1dc8449&action=lists&typeid=%24typeid&num=10&order=id+DESC&return=info\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$wap_tag = pc_base::load_app_class("wap_tag", "wap");if (method_exists($wap_tag, 'lists')) {$info = $wap_tag->lists(array('typeid'=>$typeid,'order'=>'id DESC','limit'=>'10',));}?>
		<h2><a href="<?php echo list_url($typeid);?>" class="cat"><?php echo $s['typename'];?></a>
		 <?php $n=1;if(is_array(subtype($typeid))) foreach(subtype($typeid) AS $_s) { ?><a href="<?php echo list_url($_s[typeid]);?>"><?php echo $_s['typename'];?></a><?php $n++;}unset($n); ?>
		</h2>
		<div>
		<?php $n=1;if(is_array($info)) foreach($info AS $i) { ?>
			<a href="<?php echo show_url($i[catid],$i[id]);?>"><?php echo str_cut($i['title'],26,'');?></a><br/>
		<?php $n++;}unset($n); ?>
		</div>
	<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
<?php $n++;}unset($n); ?>

<?php include template("wap","footer"); ?>