<?php
defined('IN_ADMIN') or exit('No permission resources. - exam_list.tpl.php');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>

<form action="?m=exam&c=exam&a=delete" method="post" name="myform" id="myform">
<table border="0" width="100%">
    <tr>
        <th><input type="checkbox" /></th><th>标题</th><th>内容</th><th>姓名</th><th>发表时间</th><th>是否回复</th><th>管理操作</th>
    </tr>
    <?php
    if(is_array($infos)){
    foreach($infos as $info){
    ?>
    <tr>
        <td align="center" width="35"><input type="checkbox" name="gid[]" value="<?php echo $info['gid']?>"></td><!-- 多选按钮 -->
        <td align="center"><?php echo $info['title']?></td><!-- 标题 -->
        <td align="center" width="30%"><?php echo $info['content']?></td><!-- 内容 -->
        <td align="center" width="100"><?php echo $info['username'];?></td><!-- 姓名 -->
        <td align="center" width="120"><?php echo date('Y-m-d H-i-s',$info['addtime']);?></td><!-- 发表时间 -->
        <td align="center" width="10%"><!-- 是否回复 -->
        <?php if($info['reply']==''){echo '<font color=red>未回复</font>';}else{echo '已回复';}?>
        </td>
        <td align="center" width="12%"><!-- 管理操作 -->
        <a href="?m=exam&c=exam&a=reply&gid=<?php echo $info['gid']; ?>" title="回复留言">回复</a> |
        <a href='?m=exam&c=exam&a=delete&gid=<?php echo $info['gid']?>'
         onClick="return confirm('<?php echo L('confirm', array('message' => new_addslashes($info['title'])))?>')">
         <?php echo L('删除')?>
        </a>
        </td>
    </tr>
    <?php } } ?>
</table>
<br />&nbsp;&nbsp;
<input type="submit" name="dosubmit" id="dosubmit" value="<?php echo L('删除留言')?>" />
</form>