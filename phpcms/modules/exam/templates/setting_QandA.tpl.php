<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header_exam');
?>
<script type="text/javascript" src="/statics_EXAM/js/jquery1.7.1.js"></script>
<script type="text/javascript" src="/statics_EXAM/js/ajax_admin_qanda.js"></script>
<b>库存设置：</b>
 <table border="2" width="100%">
       <thead>
        <tr>
            <th>序号</th>
            <th>标题</th>
            <th>单选题数</th>
            <th>单选分数</th>
            <th>多选题数</th>
            <th>多选分数</th>
            <th>生成时间</th>
            <th>更新时间</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(is_array($infos)){
            foreach($infos as $info){
                ?>
                <tr>
                    <td align="center" width="100"><?php echo $info['id'];?></td>
                    <td align="center" width="200"><?php echo $info['title']?></td>
                    <td align="center" width="100"><?php echo $info['num_choice_only'];?></td>
                    <td align="center" width="100"><?php echo $info['fenshu_choice_only'];?></td>
                    <td align="center" width="100"><?php echo $info['num_choice_more'];?></td>
                    <td align="center" width="100"><?php echo $info['fenshu_choice_more'];?></td>
                    <td align="center" width="120"><?php echo date('Y-m-d H-i-s',$info['addtime']);?></td>
                    <td align="center" width="120"><?php echo date('Y-m-d H-i-s',$info['updatetime']);?></td>
               </tr>
               <?php 
           } 
       } 
       ?>
   </tbody>
</table>
<b>更新设置：</b>
<form action="?m=exam&c=exam_paper&a=setting_QandA" method="post" name="myform" id="myform">
	<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
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
                    <!--  <select name="cat_level_3" id="select_3" class="select_ajax">
                        <option value ="">-请选择-</option>
                    </select> -->
                </td>
		</tr>

		<tr>
			<td align="right">单选题数：</td>
			<td align="left">
				<input name="setting[num_choice_only]" type="text" value="<?php echo $setting['num_choice_only'];?>" />
			</td>
		</tr>
		<tr>
			<td align="right">单选分数：</td>
			<td align="left">
				<input name="setting[fenshu_choice_only]" type="text" value="<?php echo $setting['fenshu_choice_only'];?>" />分/每题
			</td>
		</tr>

		<tr>
			<td align="right">多选题数：</td>
			<td align="left">
				<input name="setting[num_choice_more]" type="text" value="<?php echo $setting['num_choice_more'];?>" />
			</td>
		</tr>

		<tr>
			<td align="right">多选分数：</td>
			<td align="left">
				<input name="setting[fenshu_choice_more]" type="text" value="<?php echo $setting['fenshu_choice_more'];?>" />分/每题
			</td>
		</tr>

		<tr>
			<td align="right"><input type="submit" name="dosubmit" id="dosubmit" value="<?php echo L('submit')?> "></td>
			<td align="right">&nbsp;</td>
		</tr>

	</table>
</form>