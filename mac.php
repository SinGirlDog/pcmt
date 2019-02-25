<?php
class GetMacAddr{

    var $return_array = array(); // 返回带有MAC地址的字串数组
    var $mac_addr;

    function GetMacAddr($os_type){
        switch ( strtolower($os_type) ){
            case "linux":
            $this->forLinux();
            break;
            case "solaris":
            break;
            case "unix":
            break;
            case "aix":
            break;
            default:
            $this->forWindows();
            break;

        }


        $temp_array = array();
        foreach ( $this->return_array as $value ){

            if (
                preg_match("/[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f]/i",$value,
                    $temp_array ) ){
                $this->mac_addr = $temp_array[0];
                break;
            }

        }
        unset($temp_array);
        return $this->mac_addr;
    }


    function forWindows(){
        @exec("ipconfig /all", $this->return_array);
        if ( $this->return_array )
            return $this->return_array;
        else{
            $ipconfig = $_SERVER["WINDIR"]."\system32\ipconfig.exe";
            if ( is_file($ipconfig) )
                @exec($ipconfig." /all", $this->return_array);
            else
                @exec($_SERVER["WINDIR"]."\system\ipconfig.exe /all", $this->return_array);
            return $this->return_array;
        }
    }



    function forLinux(){
        @exec("ifconfig -a", $this->return_array);
        return $this->return_array;
    }

    function client(){
        @exec("arp -g",$array); //执行arp -a命令，结果放到数组$array中 
        foreach($array as $value){ 
            //匹配结果放到数组$mac_array 
            if(strpos($value,$_SERVER["REMOTE_ADDR"]) && preg_match("/(:?[0-9A-F]{2}[:-]){5}[0-9A-F]{2}/i",$value,$mac_array)){ 
                $mac = $mac_array[0]; 
                break; 
            } 
        } 
        echo $mac;
    }

}
 //方法使用
$mac = new GetMacAddr(PHP_OS);
echo $mac->mac_addr;
echo '<br/>Client?:',$mac->client();
/**
*ip地址的获取
**/
$a="IP:";
$b="<<>>进入时间:";
$user_IP =$_SERVER["REMOTE_ADDR"];
$user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];
date_default_timezone_set("Etc/GMT-8");
$showtime=date('Y-m-d H:i:s');
echo "$a $user_IP";
echo "$b $showtime";

?>