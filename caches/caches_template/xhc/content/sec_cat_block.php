<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><ul>
	<?php
	foreach($list_sec as $item)
	{
		if($sec_cat == $item['catid'])
		{
			$choosed = "style='box-shadow: -2px -4px 4px #2d4c5a;'";
		}
		else
		{
			$choosed = "style='box-shadow: 2px 4px 4px #2d4c5a;'";
		}
		?>
		<li id="<?php echo $item['catid'] ?>">
			<div <?php echo $choosed;?>>
				<a><?php echo $item['catname'] ?></a>
			</div>
		</li>
		<?php
	}
	if($allow_rand)
	{
		?>
		<li id="rand_paper">
			<a>自动组卷</a>
		</li>
		<li id="answer_history">
			<a>答题记录</a>
		</li>
		<?php
	}
	?>
</ul>
<input type="hidden" name="sec_cat" id="sec_cat" value="<?php echo $sec_cat; ?>">
