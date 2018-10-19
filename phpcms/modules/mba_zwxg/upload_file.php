<?php
defined('IN_PHPCMS') or exit('No permission resources.');
class upload_file {

    public function upload_file()
    {
        $dir_base = "/uploadfile/zwxg/zw_img/";   
        $document_root = $_SERVER['DOCUMENT_ROOT'];
        $index = 0; 
        $output = "";
        $msg = '';
        
        $upload_file_name = 'upload_file' . $index;        
        $filename = $_FILES[$upload_file_name]['name'];
        $gb_filename = iconv('utf-8','gb2312',$filename);   
        $rand_filename = rand(100,999);   
        $gb_arr = explode('.', $gb_filename);
        $gb_arr[0] = date('Ymd_His_').$rand_filename;
        $gb_filename = implode('.', $gb_arr);    
        if(!file_exists($document_root.'/'.$dir_base)) 
        {  
            mkdir($document_root.'/'.$dir_base);  
        }

        $isMoved = false;  //默认上传失败
        $MAXIMUM_FILESIZE = 1 * 1024 * 1024;     //文件大小限制    1M = 1 * 1024 * 1024 B;
        $rEFileTypes = "/^\.(jpg|jpeg|gif|png){1}$/i"; 
        if ($_FILES[$upload_file_name]['size'] <= $MAXIMUM_FILESIZE)
        {
            if(preg_match($rEFileTypes, strrchr($gb_filename, '.')))
            {
                $isMoved = move_uploaded_file ( $_FILES[$upload_file_name]['tmp_name'], $document_root.'/'.$dir_base.$gb_filename);        //上传文件
            }
            else
            {
                $msg = 'undefined_file_type';
            }
        }
        else
        {
            $msg = 'more_than_one_M';
        }

        if($isMoved)
        {//输出文件路径
            $output = $dir_base.$gb_filename;
            $msg = 'upload_well';
        }
        else
        {
            $output = "error";
        }
        $index++;

        echo $msg . '!@#$' . $output;
    }
}
?>

