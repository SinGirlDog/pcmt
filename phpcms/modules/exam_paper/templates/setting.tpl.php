<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<form action="?m=exam&c=exam&a=setting" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

<tr>
<td align="right">是否允许留言：</td>
<td align="left">
<input name="setting[exam_status]" type="radio" value="1" <?php if($exam_status==1){echo "checked";}?> />&nbsp;<?php echo L('yes')?>&nbsp;&nbsp;
<input name="setting[exam_status]" type="radio" value="0" <?php if($exam_status==0){echo "checked";}?> />&nbsp;<?php echo L('no')?>
</td>
</tr>

<tr>
<td align="right">是否允许游客留言：</td>
<td align="left">
<input name="setting[allow_guest]" type="radio" value="1" <?php if($allow_guest==1){echo "checked";}?> />&nbsp;<?php echo L('yes')?>&nbsp;&nbsp;
<input name="setting[allow_guest]" type="radio" value="0" <?php if($allow_guest==0){echo "checked";}?> />&nbsp;<?php echo L('no')?>
</td>
</tr>

<tr>
<td align="right">是否需要审核：</td>
<td align="left">
<input name="setting[check_pass]" type="radio" value="1" <?php if($check_pass==1){echo "checked";}?> />&nbsp;<?php echo L('yes')?>&nbsp;&nbsp;
<input name="setting[check_pass]" type="radio" value="0" <?php if($check_pass==0){echo "checked";}?> />&nbsp;<?php echo L('no')?>
</td>
</tr>

<tr>
<td align="right">每页条数：</td>
<td align="left">
<input name="setting[pagesize]" type="text" size="20" value="<?php echo $pagesize;?>" />
</td>
</tr>

<tr>
<td align="right">是否开启验证码：</td>
<td align="left">
<input name="setting[enablecheckcode]" type="radio" value="1" <?php if($enablecheckcode==1){echo "checked";}?> />&nbsp;<?php echo L('yes')?>&nbsp;&nbsp;
<input name="setting[enablecheckcode]" type="radio" value="0" <?php if($enablecheckcode==0){echo "checked";}?> />&nbsp;<?php echo L('no')?>
</td>
</tr>

<tr>
<td align="right"><input type="submit" name="dosubmit" id="dosubmit" value=" <?php echo L('submit')?> "></td>
</tr>

</table>
</form>