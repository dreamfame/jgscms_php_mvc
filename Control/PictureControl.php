 <?php
	header('Access-Control-Allow-Origin：*');
	header("Content-Type: text/html;charset=utf-8");
	Class PictureControl
    {
        public function JudgeOperate($operate)
        {
            switch ($operate) {
                case "uploadImg":
                    PictureControl::AcceptImg();
                    break;
            }
        }

        public function AcceptImg()
        {
        	$data = array('src'=>'','title'=>'');
            $result = array('code' => 1, 'msg' => '服务器故障', 'data' => $data);
            if($_FILES["file"]["error"])
            {
            	$result['msg'] = $_FILES["file"]["error"];
            }
            else{
            	if(($_FILES["file"]["type"]=="image/png"||$_FILES["file"]["type"]=="image/jpeg")&&$_FILES["file"]["size"]<1024000)
    			{
                    $filename ="../View/images/".time().$_FILES["file"]["name"];
                    $src = "/images/".time().$_FILES["file"]["name"];
                    $filename =iconv("UTF-8","gb2312",$filename);
                    //检查文件或目录是否存在
                    if(file_exists($filename))
                    {
                        $result['msg'] = "文件已存在";
                    }
                    else
                    {
                        //保存文件,   move_uploaded_file 将上传的文件移动到新位置
                        move_uploaded_file($_FILES["file"]["tmp_name"],$filename);//将临时地址移动到指定地址
						$result['code'] = 0;
                        $result['msg'] = "上传成功";
                        $data['src'] = $src;
                        $result['data'] = $data;
                    }
                }
                else{
                    $result['msg'] = "文件类型错误";
				}
			}
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }
?>
 