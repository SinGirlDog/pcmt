<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><?php echo $name;?>
<ul>
<?php $n=1; if(is_array($data)) foreach($data AS $i => $r) { ?>
<li style="clear:both">
<a href="<?php echo $r['url'];?>"><?php echo $r['title'];?></a><br />
<div style="color:#ccc;"><?php if($r[thumb]) { ?><img src="<?php echo $r['thumb'];?>" style="float:left"><?php } ?><?php echo $r['desc'];?>
</div>
<?php $n++;}unset($n); ?>
</li>
</ul>