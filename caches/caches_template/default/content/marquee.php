<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?>
<link href="http://local.pct.com/statics_PDW/css/activity.css" rel="stylesheet" type="text/css" />
<link href="http://local.pct.com/statics_PDW/css/public.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://local.pct.com/statics_PDW/js/activity.js"></script>
<style>
.content_piaofu {margin:0 auto;text-align: center;padding: 5px; position: absolute;left:20px;font-size: 25px;background-color: #170255;}
.content_piaofu a{text-decoration:none;color:white;}
</style>
<marquee direction=right behavior=scroll onmouseover=this.stop() onmouseout=this.start()  style="height:60px;z-index: 99;position: fixed; bottom: 10px;">
    <div class="content_piaofu">
        <a id="left_ad" class="join" href="javascript:;">
            2019年MBA西部院校调剂申请
        </a>
    </div>
</marquee>

<div class="main clearfix" style="padding-top: 30px;">
    <style>
    /*  滑动验证样式 */
    .slidemask {
        width: 100%;
        left: 0;
        top: 0;
        background: rgba(0, 0, 0, .6);
        display: none;
        z-index: 100
    }
    .slideimg {
        position: absolute !important;
        left: 50%;
        top: 55%;
        transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        -moz-transform: translate(-50%, -50%);
        -webkit-transform: translate(-50%, -50%);
        -o-transform: translate(-50%, -50%);
    }
</style>
<div id="shadow" style="display: none;"></div>
<div id="activeJoinBox" class="animated bounceInRight" style="display: none;">
    <div class="closeBtn">
        <img src="/statics_PDW/images/close-icon.png" data-bd-imgshare-binded="1">
    </div>
    <p class="t">活动报名</p>
    <form action="" method="post">
        <div class="inputGroup">
            <label>手机：</label>
            <input type="text" name="" id="tel" value="" placeholder="请输入您的手机号" required="">
            <p class="error">请输入您的手机号</p>
        </div>
        <div class="inputGroup">
            <label>姓名：</label>
            <input type="text" name="" id="name" value="" placeholder="请输入姓名" required="">
            <p class="error">请输入您的姓名</p>
        </div>       
        <div class="inputGroup">
            <label>微信：</label>
            <input type="text" name="" id="wxnum" value="" placeholder="请输入微信" required="">
            <p class="error">请输入您的微信</p>
        </div>
        <div class="inputGroup">
            <label>分数：</label>
            <input type="text" id="english" name="english" onblur="counttotal()" onafterpaste="this.value=this.value.replace(/\D/g, '')" onkeyup="this.value=this.value.replace(/\D/g,'');" value="" placeholder="英语" title="英语" maxlength="3" style="width:80px;">
            <input type="text" id="zonghe" name="zonghe" onblur="counttotal()" onafterpaste="this.value=this.value.replace(/\D/g, '')" onkeyup="this.value=this.value.replace(/\D/g,'');" value="" placeholder="综合" title="综合" maxlength="3" style="width:80px;">
            <input type="text" id="total" name="total" value="" placeholder="总分" title="总分" readonly="" maxlength="3" style="width:80px;">
            <p class="error" id="codeError">请输入校验码</p>
        </div>
        <div class="inputGroup">
            <label>校验码：</label>
            <input required="" type="text" name="verify" id="verify" value="" placeholder="请输入校验码" style="width: 130px;">
            <img class="validate" id="code_img" onclick="this.src=this.src+&quot;&amp;&quot;+Math.random()" src="http://local.pct.com/api.php?op=checkcode&amp;code_len=4&amp;font_size=20&amp;width=130&amp;height=50&amp;font_color=&amp;background=">           
        </div>       
    </form>
    <input id="signUp" type="button" value="提交" class="btn">
</div>
<div id="activeJoinReturnBox">
    <div class="closeBtn">
        <img src="/statics_PDW/images/close-icon.png" data-bd-imgshare-binded="1">
    </div>
    <p class="success"><img src="/statics_PDW/images/icon-success.png" data-bd-imgshare-binded="1">活动报名已成功</p>
    <p class="defaut"><img src="/statics_PDW/images/icon-defaut.png" data-bd-imgshare-binded="1">活动报名失败，请重新报名</p>
</div>
<script type="text/javascript">
    function counttotal() {
        var e = $('#english').val();
        var z = $('#zonghe').val();
        var v = e != '' && z != '' ? parseInt(e) + parseInt(z) : '';
        $('#total').attr('value', v);
        var t = '';
        if ($('#total').val() == '总分' || $('#total').val() == 'NaN' || $('#total').val() == '') {
            t = '总分';
        }
        $('#total').attr('value', t == '' ? v : t);
    }
</script>    
