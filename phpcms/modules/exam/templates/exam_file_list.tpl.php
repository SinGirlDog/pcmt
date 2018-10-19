<?php
defined('IN_ADMIN') or exit('No permission resources. - exam_file_list.tpl.php');
$show_dialog = 1;
include $this->admin_tpl('header_exam');
?>
<script type="text/javascript" src="/statics_EXAM/js/jquery1.7.1.js"></script>
<script type="text/javascript" src="/statics_EXAM/js/ajax_admin.js"></script>

<form action="" method="" name="myform" id="myform">
    <table border="0" width="100%">
     <thead>
        <tr>
            <!-- <th><input type="checkbox" />全选</th> -->
            <th>序号</th>
            <th>科目</th>
            <th>标题</th>
            <th>习题列表</th>
            <th>生成时间</th>
            <th>管理操作</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(is_array($infos))
        {
            foreach($infos as $info)
            {
                ?>
                <tr>
                    <td align="center" width="100"><?php echo $info['id'];?></td>
                    <td align="center" width="200"><?php echo $info['cattitle']?></td>
                    <td align="center" width="200"><?php echo $info['title']?></td>
                    <td align="center" width="200"><?php echo substr($info['quest_ids'], 0,60); ?></td>
                    <td align="center" width="100"><?php echo date('Y-m-d H-i-s',$info['addtime']);?></td>
                    <td align="center" width="100">
                        <a href="?m=exam&c=exam_file&a=preview_file&id=<?php echo $info['id']; ?>" title="查看内容">预览</a> |
                        <?php if(empty($info['quest_ids'])){
                            echo '<a href="?m=exam&c=exam_file&a=parsing_file&id='.$info['id'].'" title="习题入库">入库</a> |';
                        }
                        ?>
                        <a href='?m=exam&c=exam_file&a=delete_file&id=<?php echo $info['id']?>'
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
</form>

<form style="margin-left:30px;" action="?m=exam&c=exam_file&a=upload_xml" method="post" enctype="multipart/form-data" name="myform" id="myform">
    <table border="0" width="100%">
        <tbody>
            <tr>
                <td align="right">科目：</td>
                <td align="left">
                    <select name="cat_level_1" id="select_1" class="select_ajax">
                        <option value ="">-请选择-</option>
                        <?php foreach($category_one as $key=>$val){
                            echo "<option value =".$val['catid'].">".$val['catname']."</option>";
                        }?>
                    </select>

                    <select name="cat_level_2" id="select_2" class="select_ajax">
                        <option value ="">-请选择-</option>
                    </select>
                     <select name="cat_level_3" id="select_3" class="select_ajax">
                        <option value ="">-请选择-</option>
                    </select>
                </td>

                <td>
                    <span>卷宗标题：</span>
                    <input name="title" value="未命名">
                </td>
                <td>
                    <span>习题文件：</span>
                    <input type="file" id="file" name="file"/>
                    (目前仅支持XML电子表格)
                    <input type="submit" id="submit" name='editsubmit' value="上船" class="shangchuan"/>
                    (文件大小最好小于1MB)
                </td>
            </tr>
        </tbody>
    </table>
</form>