<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><?php include template("content","header"); ?>
<link href="<?php echo CSS_PATH;?>vote.css" rel="stylesheet" type="text/css" />
<!--main-->
<div class="main tps">
<div class="crumbs"><a href="<?php echo siteurl($siteid);?>">首页</a><span> &gt; </span> <a href="<?php echo APP_PATH;?>index.php?m=vote&c=index&a=lists&siteid=<?php echo $siteid;?>">投票</a>  </div>
<div class="vote_result">
    	<div class="tit"><span class="r">总票数：<strong><?php echo $vote_data['total'];?></strong></span><h5><?php echo $subject;?></h5></div>
        <div class="c_box wrap">
        	<table width="100%" border="1" cellspacing="0" cellpadding="0">
        	
        	<?php $n=1;if(is_array($options)) foreach($options AS $r) { ?>
       		<?php $i++; ?>
       		<?php if($vote_data['total']==0 ) { ?>
       		<?php $per=0;?>
       		<?php } else { ?>
       		<?php $per=intval($vote_data[$r['optionid']]/$vote_data['total']*100);?>
       		<?php } ?>
       		
       		 
              <tr>
              	<th><?php echo $i;?></th>
                <td class="tp_tit"><?php echo $r['option'];?></td>
                <td class="tdcol3"><?php echo $per;?> %</td>
                <td><div><img src="statics/images/vote/tit_cs.jpg" width="<?php echo $per;?>%" /></div></td>
                <td class="tdcol3"><?php echo $vote_data[$r['optionid']]==''? 0:$vote_data[$r['optionid']]?> 票</td>
              </tr>
            <?php $n++;}unset($n); ?>  
          
            </table>
        </div>
    </div>
    <div class="vote_listt">
    	<div class="tit"><span class="r"><a href="<?php echo APP_PATH;?>index.php?m=vote&c=index&a=lists&siteid=<?php echo $siteid;?>">查看更多>></a></span><h5>其他投票</h5></div>
        <ul class="wrap licol2 icon3j">
              <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"vote\" data=\"op=vote&tag_md5=c75e152c0a1182ad38dc2d73dee8a48b&action=other_vote&catid=%24catid&num=8&order=subjectid+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$vote_tag = pc_base::load_app_class("vote_tag", "vote");if (method_exists($vote_tag, 'other_vote')) {$data = $vote_tag->other_vote(array('catid'=>$catid,'order'=>'subjectid DESC','limit'=>'8',));}?>
				<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
				 <li><a title="<?php echo $r['subject'];?>" href="<?php echo APP_PATH;?>index.php?m=vote&c=index&a=show&show_type=1&subjectid=<?php echo $r['subjectid'];?>&siteid=<?php echo $siteid;?>"><?php echo $r['subject'];?></a><span><font color="#1E50A0">(得票数: <?php echo $r['votenumber'];?>)</font></span></li>
				<?php $n++;}unset($n); ?>
			 <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
        </ul>
    </div>
</div>
<?php include template("content","footer"); ?>