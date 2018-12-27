<?php
	require_once '../Model/Message.php';
	require_once '../DataBaseHandle/MessageServer.php';
	require_once '../DataBaseHandle/AdminServer.php';
	header("Content-Type: text/html;charset=utf-8");
	date_default_timezone_set('PRC');
	Class MessageControl
    {
        public function JudgeOperate($operate)
        {
            switch ($operate) {
                case "list":
                    MessageControl::GetAll();
                    break;
                case "add":
                    MessageControl::AddMessage();
                    break;
                case "query":
                    MessageControl::GetMessage();
                    break;
				case "reply":
					MessageControl::ReplyMessage();
					break;
				case "msg":
                    MessageControl::MessageInfo();
                    break;
            }
        }

        public function GetAll()
        {
            $as = new MessageServer();
            $result = $as->GetAll();
            $re = array('state'=>'0','content'=>null);
            $jsonfile = fopen("../View/json/message.json", "w") or die("Unable to open file!");
            while ($u = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $u['id'], 'user_nickname' => $u['user_nickname'],'user_head' => $u['user_head'], 'msg' => $u['msg'],  'msg_time' => $u['msg_time'],  'status' => $u['status']);
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

        public function MessageInfo(){
        	$id = $_REQUEST['id'];
        	$status = $_REQUEST['status'];
        	$as = new MessageServer();
            $result = $as->GetMsg($id);
            $re = array('state'=>'0','content'=>null);
            if($status=="1"){
				while ($u = mysqli_fetch_array($result)) {
					$re['state'] = '1';
					$row[] = array('id' => $u['id'], 'admin_username' => $u['admin_username'],'admin_head' => $u['admin_head'], 'reply' => $u['reply'],  'reply_time' => $u['reply_time']);
					$re['content'] = $row;
				}
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
		}

        public function UpdateMessageJson(){
            $as = new MessageServer();
            $result = $as->GetAll();
            $jsonfile = fopen("../View/json/message.json", "w") or die("Unable to open file!");
            while ($u = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $u['id'], 'user_nickname' => $u['user_nickname'],'user_head' => $u['user_head'], 'msg' => $u['msg'],  'msg_time' => $u['msg_time'],  'status' => $u['status']);
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

		public function ReplyMessage(){
        	$message = new Message();
        	$message->id = $_REQUEST['id'];
            $message->reply = $_REQUEST['reply'];
        	$username = Security::decrypt($_REQUEST['username']);
            $as = new AdminServer();
            $result = $as->GetAdmin($username);
            $re = array('state'=>'0','content'=>'回复失败');
            while ($u = mysqli_fetch_array($result)) {
                $row[] = array('id' => $u['id'], 'username' => $u['username'], 'nickname' => $u['nickname'], 'phone' => $u['phone'], 'email' => $u['email'], 'role' => $u['role'],'head_pic'=>$u['head_pic']);
			}
            $message->admin_id = $row[0]['id'];
            date_default_timezone_set('PRC');
            $message->reply_time = date('Y-m-d H:i:s', time());
            $ms = new MessageServer();
            $r1 = $ms->UpdateMessage($message,"reply");
            if($r1){
                MessageControl::UpdateMessageJson();
                $r2 = $ms->GetMsg($message->id);
                while ($u = mysqli_fetch_array($r2)) {
                    $re['state'] = '1';
                    $r[] = array('id' => $u['id'], 'admin_username' => $u['admin_username'],'admin_head' => $u['admin_head'], 'reply' => $u['reply'],  'reply_time' => $u['reply_time']);
                    $re['content'] = $r;
                }
			}
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}
    }
?>
