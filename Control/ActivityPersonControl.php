<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	require_once '../Model/ActivityPerson.php';
	require_once '../DataBaseHandle/ActivityPersonServer.php';
	header("Content-Type: text/html;charset=utf-8");
	error_reporting(0);
	//session_start();
	Class ActivityPersonControl
	{
		public function JudgeOperate($operate)
		{
			switch($operate)
			{
				case "list":
                    ActivityPersonControl::GetList();
					break;
                case "all":
                    ActivityPersonControl::GetAll();
                    break;
				case "join":
                    ActivityPersonControl::AddActivityPerson();
					break;
				case "del":
                    ActivityPersonControl::DelActivityPerson();
					break;
				case "query":
                    ActivityPersonControl::GetActivityPerson();
					break;
                case "prize":
                    ActivityPersonControl::PrizeStatus();
                    break;
			}
		}

		public function GetList()
		{
            $activity_id = $_REQUEST['activity_id'];
            $ss = new ActivityPersonServer();
            $result = $ss->GetAll($activity_id);
            $re = array('state'=>'0','content'=>"未获取数据");
            $jsonfile = fopen("../View/json/activityPersonList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'activity_id' => $n['activity_id'],'phone'=>$n['phone'],'nickname' => $n['nickname'], 'time' => $n['time'], 'prize' => $n['prize']);
                $re['content'] = $row;
                if (flock($jsonfile, LOCK_EX)) {//加写锁 
                    ftruncate($jsonfile, 0); // 将文件截断到给定的长度 
                    rewind($jsonfile); // 倒回文件指针的位置 
                    fwrite($jsonfile, json_encode($row, JSON_UNESCAPED_UNICODE));
                    flock($jsonfile, LOCK_UN); //解锁 
                }
            }
            fclose($jsonfile);
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
		}

        public function GetAll()
        {
            $activity_id = $_REQUEST['activity_id'];
            $re = array('state'=>'0','content'=>"未获取数据");
            if(empty($activity_id)){
                $re['content'] = "参数有误";
                echo json_encode($re,JSON_UNESCAPED_UNICODE);
                return;
            }
            $ss = new ActivityPersonServer();
            $result = $ss->GetAll($activity_id);
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'activity_id' => $n['activity_id'],'phone'=>$n['phone'],'nickname' => $n['nickname'], 'time' => $n['time'], 'prize' => $n['prize']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

        public function GetActivityPerson(){
		    $id = $_REQUEST['id'];
		    $ss = new ActivityPersonServer();
            $result = $ss->GetActivityPersonById($id);
            $re = array('state'=>'0','content'=>null);
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'name' => $n['name'], 'brief' => $n['brief'], 'intro' => $n['intro'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'recommend'=>$n['recommend']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

		public function UpdateActivityPersonJson($activity_id){
            $ss = new ActivityPersonServer();
            $result = $ss->GetAll($activity_id);
            $jsonfile = fopen("../View/json/activityPersonList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'activity_id' => $n['activity_id'],'phone'=>$n['phone'],'nickname' => $n['nickname'], 'time' => $n['time'], 'prize' => $n['prize']);
                $re['content'] = $row;
                if (flock($jsonfile, LOCK_EX)) {//加写锁 
                    ftruncate($jsonfile, 0); // 将文件截断到给定的长度 
                    rewind($jsonfile); // 倒回文件指针的位置 
                    fwrite($jsonfile, json_encode($row, JSON_UNESCAPED_UNICODE));
                    flock($jsonfile, LOCK_UN); //解锁 
                }
            }
            fclose($jsonfile);
		}

		public function AddActivityPerson()
		{
			$activityperson = new ActivityPerson();
            $activityperson->activity_id = $_REQUEST['activity_id'];
            $activityperson->phone = $_REQUEST['phone'];
            $activityperson->nickname = $_REQUEST['nickname'];
            $activityperson->prize = 0;
            date_default_timezone_set('PRC');
            $activityperson->time = date('Y-m-d H:i:s', time());
			$ss = new ActivityPersonServer();
            $re = array('state'=>'0','content'=>'添加失败');
            $result = $ss->InsertActivityPerson($activityperson);
            if($result==""){
				$re['state'] = '1';
				$re['content'] = '添加成功';
			}
			else{
                $re['content'] = '添加失败,错误信息：'.$result;
            }
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
		}

		public function UpdateActivityPerson()
		{
            $activity = new ActivityPerson();
            $activity->id = $_REQUEST['id'];
            $activity->name = $_REQUEST['name'];
            $activity->created_at = $_REQUEST['created_at'];
            $activity->brief = $_REQUEST['brief'];
            $activity->recommend = $_REQUEST['recommend'];
            $activity->intro = $str = str_replace('\'', '\"', $_REQUEST['intro']);
			$re = array('state'=>'0','content'=>'修改失败');
			$ss = new ActivityPersonServer();
			$result = $ss->UpdateActivityPerson($activity,"all");
			if($result){
				$re['state'] = '1';
				$re['content'] = '修改成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}

		public function DelActivityPerson(){
			$id = $_REQUEST['id'];
            $ss = new ActivityPersonServer();
            $result=$ss->DeleteActivityPerson($id);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
            	ActivityPersonControl::UpdateActivityPersonJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}

        public function PrizeStatus(){
            $id = $_REQUEST['id'];
            $activity_id = $_REQUEST['activity_id'];
            $prize = $_REQUEST['prize'];
            $ss = new ActivityPersonServer();
            $activityperson = new ActivityPerson();
            $activityperson->id = $id;
            $activityperson->activity_id = $activity_id;
            $activityperson->prize = $prize;
            $result = $ss->UpdateActivityPerson($activityperson,"prize");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result) {
                ActivityPersonControl::UpdateActivityPersonJson($activity_id);
                $re['state']='1';
                $re['content']='修改成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }
	}
?>
