<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
    require_once '../Model/System.php';
    include '../PHPMailer-master/class.phpmailer.php';
    include '../PHPMailer-master/class.smtp.php';
    require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class SystemControl
    {
        public function JudgeOperate($operate)
        {
            switch ($operate) {
                case "update":
                    SystemControl::UpdateSystemJson();
                    break;
                case "notice":
                    SystemControl::UpdateNoticeJson();
                    break;
                case "area":
                    SystemControl::UpdateAreaJson();
                    break;
                case "bug":
                    SystemControl::SendBugToEmail();
                    break;
                case "getOpenid":
                    SystemControl::GetWxOpenid();
                    break;
            }
        }

        public function UpdateSystemJson(){
            $jsonfile = fopen("../View/json/systeminfo.json", "w") or die("Unable to open file!");
            $system = new System();
            $system->webName = $_REQUEST['webName'];
            $system->webTitle = $_REQUEST['webTitle'];
            $system->version = $_REQUEST['version'];
            $system->defaultHeadPic = $_REQUEST['defaultHeadPic'];
            $system->defaultPic = $_REQUEST['defaultPic'];
            $system->server = $_REQUEST['server'];
            $system->dataBase = $_REQUEST['dataBase'];
            $system->powerby = $_REQUEST['powerby'];
            $system->description = $_REQUEST['description'];
            $system->record = $_REQUEST['record'];
            $jsonarray = array('webName'=>$system->webName,'webTitle'=>$system->webTitle,'version'=>$system->version,'defaultHeadPic'=>$system->defaultHeadPic,'defaultPic'=>$system->defaultPic,'server'=>$system->server,'dataBase'=>$system->dataBase,'powerby'=>$system->powerby,'description'=>$system->description,'record'=>$system->record);
            if (flock($jsonfile, LOCK_EX)) {//加写锁 
                ftruncate($jsonfile, 0); // 将文件截断到给定的长度 
                rewind($jsonfile); // 倒回文件指针的位置 
                fwrite($jsonfile, json_encode($jsonarray,JSON_UNESCAPED_UNICODE));
                flock($jsonfile, LOCK_UN); //解锁 
            }
            fclose($jsonfile);
        }

        public function UpdateNoticeJson(){
            $jsonfile = fopen("../View/json/systemnotice.json", "w") or die("Unable to open file!");
            $jsonarray = array('systemNotice'=>$_REQUEST['content']);
            if (flock($jsonfile, LOCK_EX)) {//加写锁 
                ftruncate($jsonfile, 0); // 将文件截断到给定的长度 
                rewind($jsonfile); // 倒回文件指针的位置 
                fwrite($jsonfile, json_encode($jsonarray,JSON_UNESCAPED_UNICODE));
                flock($jsonfile, LOCK_UN); //解锁 
            }
            fclose($jsonfile);
        }

        public function UpdateAreaJson(){
            $jsonfile = fopen("../View/json/area.json", "w") or die("Unable to open file!");
            $jsonarray = array('point1'=>$_REQUEST['point1'],'point2'=>$_REQUEST['point2'],'point3'=>$_REQUEST['point3'],'point4'=>$_REQUEST['point4']);
            if (flock($jsonfile, LOCK_EX)) {//加写锁 
                ftruncate($jsonfile, 0); // 将文件截断到给定的长度 
                rewind($jsonfile); // 倒回文件指针的位置 
                fwrite($jsonfile, json_encode($jsonarray,JSON_UNESCAPED_UNICODE));
                flock($jsonfile, LOCK_UN); //解锁 
            }
            fclose($jsonfile);
        }

        public function SendBugToEmail()
        {
            $xc = new XmlControl();
            $re = array('state'=>'0','content'=>null);
            $title = $_REQUEST['title'];
            $content = $_REQUEST['content'];
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = "smtp.163.com";
            $mail->SMTPSecure = "ssl";
            $mail->SMTPAuth = true;
            $mail->Username = $xc->GetXmlAttribute("../ProjectConfig/SysConfig.xml","sendemail",0,"name");
            $mail->Password = $xc->GetXmlAttribute("../ProjectConfig/SysConfig.xml","sendemail",0,"password");
            $mail->From = $xc->GetXmlAttribute("../ProjectConfig/SysConfig.xml","sendemail",0,"name");
            $mail->Port = 465;
            $mail->FromName = "系统邮件";
            $mail->CharSet = "UTF-8";
            $mail->Encoding = "base64";
            $toEmail = $xc->GetXmlAttribute("../ProjectConfig/SysConfig.xml","toemail",0,"name");
            $mail->addAddress($toEmail);
            $mail->WordWrap = 50;
            $mail->isHTML(true);
            $mail->Subject = $title;
            $body = $content;
            $mail->Body= $body;
            $mail->AltBody ="text/html";
            $result = $mail->send();
            if($result)
            {
                $re["state"] = "1";
                $re["content"] = "邮箱发送成功！";
                echo json_encode($re);
            }
            else{
                $re["state"] = "0";
                $re["content"] = "邮件发送有误，邮件错误信息：".$mail->ErrorInfo;
                echo json_encode($re);
                exit;
            }
        }

        public function GetWxOpenId(){
            $xc = new XmlControl();
            $re = array('state'=>'0','content'=>"获取openid失败");
            if(!isset($_REQUEST['code'])) {
                $appid = $xc->GetXmlAttribute("../ProjectConfig/SysConfig.xml", "appid", 0, "name");
                $appsecret = $xc->GetXmlAttribute("../ProjectConfig/SysConfig.xml", "appsecret", 0, "name");
                $code = $_REQUEST['code'];
                $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$appsecret."&js_code=" . $code . "&grant_type=authorization_code";
                $info = file_get_contents($url);//发送HTTPs请求并获取返回的数据，推荐使用curl
                $json = json_decode($info);//对json数据解码
                $arr = get_object_vars($json);
                $openid = $arr['openid'];
                $re["state"] = "1";
                $re["content"] = $openid;
            }
            else{
                $re["state"] = "0";
                $re["content"] = "获取openid失败，为获取到code值";
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }
    }
?>
