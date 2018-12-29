<?php
    require_once '../Model/System.php';
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
    }
?>
