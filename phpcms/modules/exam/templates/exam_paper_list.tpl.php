<?php
defined('IN_ADMIN') or exit('No permission resources. - exam_paper_list.tpl.php');
$show_dialog = 1;
include $this->admin_tpl('header_exam');
?>

<form name="searchform" action="" method="get" >
   <input type="hidden" value="exam" name="m">
   <input type="hidden" value="exam_paper" name="c">
   <input type="hidden" value="search_paper" name="a">
   <input type="hidden" value="1687" name="menuid">
   <table width="100%" cellspacing="0" class="search-form">
    <tbody>
        <tr>
            <td>
                <div class="explain-col">

                    <?php echo L('addtime')?>：
                    <?php echo form::date('start_time', $start_time)?>-
                    <?php echo form::date('end_time', $end_time)?>
                    <?php echo L('name')?>：
                    <input name="name" type="text" value="<?php if(isset($_GET['name'])) {echo $_GET['name'];}?>" class="input-text" />
                    <?php echo L('mobile')?>：
                    <input name="mobile" type="text" value="<?php if(isset($_GET['mobile'])) {echo $_GET['mobile'];}?>" class="input-text" />
                   
                    <input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
                </div>
            </td>
        </tr>
    </tbody>
</table>
</form>

<form action="?m=exam&c=exam_paper&a=delete_paper" method="post" name="myform" id="myform">
    <table border="0" width="100%">
       <thead>
        <tr>
            <th><input type="checkbox" />全选</th>
            <th>序号</th>
            <th>标题</th>
            <!-- <th>内容</th> -->
            <th>姓名</th>
            <th>移动电话</th>
           <!--  <th>单选</th>
            <th>多选</th> -->
            <th>生成时间</th>
            <!-- <th>是否回复</th> -->
            <th>管理操作</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(is_array($infos)){
            foreach($infos as $info){
                ?>
                <tr>
                    <td align="center" width="35"><input type="checkbox" name="id[]" value="<?php echo $info['id']?>"></td><!-- 多选按钮 -->
                    <td align="center" width="100"><?php echo $info['id'];?></td>
                    <td align="center" width="200"><?php echo $info['title']?></td><!-- 标题 -->
                    <!-- <td align="center" width="30%">
                        <?php echo $info['content']?>
                    </td> -->
                    <td align="center" width="100"><?php echo $info['name'];?></td><!-- 姓名 -->
                    <td align="center" width="100"><?php echo $info['mobile'];?></td>
                   <!--  <td align="center" width="100"><?php echo $info['quest_choice_only'];?></td>
                    <td align="center" width="100"><?php echo $info['quest_choice_more'];?></td> -->
                    <td align="center" width="120"><?php echo date('Y-m-d H-i-s',$info['addtime']);?></td><!-- 发表时间 -->
                    <!-- <td align="center" width="10%">
                        <?php if($info['reply']==''){echo '<font color=red>未回复</font>';}else{echo '已回复';}?>
                    </td> -->
                    <td align="center" width="12%"><!-- 管理操作 -->
                        <a href="?m=exam&c=exam_paper&a=preview_paper&id=<?php echo $info['id']; ?>" title="查看内容">预览</a> |
                        <a href='?m=exam&c=exam_paper&a=delete_paper&id=<?php echo $info['id']?>'
                           onClick="return confirm('<?php echo L('confirm', array('message' => new_addslashes($info['title'])))?>')">
                           <?php echo L('删除')?>
                       </a>
                   </td>
               </tr>
               <?php 
           } 
       } 
       ?>
   </tbody>
</table>
<div id="pages"><?php echo $pages?></div>
<br />&nbsp;&nbsp;
<input type="submit" name="dosubmit" id="dosubmit" value="<?php echo L('删除考卷')?>" />
</form>
<script>
        var chbAll=document.querySelector(
            "thead th:first-child>input"
            );
        var chbs=document.querySelectorAll(
            "tbody td:first-child>input"
            );
        chbAll.onclick=function(){
            for(var i=0;i<chbs.length;i++){
                chbs[i].checked=this.checked;
            }
        }
        for(var i=0;i<chbs.length;i++){
            chbs[i].onclick=function(){
                if(!this.checked)
                    chbAll.checked=false;
                else{
                    var unchecked=
                    document.querySelector(
                        "tbody td:first-child>input:not(:checked)"
                        );
                    if(unchecked===null)
                        chbAll.checked=true;
                }
            }
        }
    </script>