<?php
	require_once '../Model/Message.php';
	require_once '../DataBaseHandle/MessageServer.php';
	require_once '../DataBaseHandle/CustomerServer.php';
	require_once '../DataBaseHandle/InfoServer.php';
	header("Content-Type: text/html;charset=utf-8");
	session_start();
	Class MessageControl
	{
		public function JugdeOperate()
		{
			$operate = $_REQUEST["operate"];
			switch($operate)
			{
				case "add":
					$this->IntoMessage();
					break;
				case "del":
					break;
				case "edit":
					$this->UpdateAdmin();
					break;
				case "query":
					$this->GetMessage();
					break;
				case "reply":
				    $this->ReplyMessage();
				    break;
				case "getReply":
				    $this->GetReply();
				    break;
				case "get":
				    $this->isGet();
				    break;
				case "remind":
				    $this->Remind();
				    break;
				case "conditionQuery":
				    $this->queryCondition();
				    break;
			}
		}
		
		public function queryCondition(){
		    $condition = $_REQUEST["condition"];
		    $param1 = $_REQUEST["conditionText"];
		    $page = $_REQUEST["page"];
		    $ms = new MessageServer();
		    $result = $ms->GetMessageByCondition($condition,$param1,$page,20);
		    $re = array('state'=>'0','content'=>'null');
		    while($s = mysqli_fetch_array($result))
		    {
		        $re['state'] = '1';
		        $row[]= array('id'=>$m['id'],'customer'=>$cname,'special'=>$sname,'message'=>$m['message'],'reply'=>$m['reply'],'sendtime'=>$m['sendtime'],'replytime'=>$m['replytime']);
		        $re['content'] = $row;
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function Remind(){
		    $ms = new MessageServer();
		    $result = $ms->GetRemind();
		    $cs = new CustomerServer();
		    $re = array('state'=>'0','content'=>null,'total'=>0);
		    $is = new InfoServer();
		    while($m = mysqli_fetch_array($result)){
		        $re['state'] = "1";
		        $total = $ms->GetTotalRemind();
		        $re['total'] = $total;
		        $phone = $cs->getPhoneById($m['customerid']);
		        if($is->GetNick($phone)==""){
		            $cname = $cs->getNameByPhone($phone);
		        }else{
		            $cname = $is->GetNick($phone);
		        }
		        $remindNum = $ms->GetRemindNum($m['customerid']);
		        $row[]= array('cid'=>$m['customerid'],'customer'=>$cname,'remindNum'=>$remindNum);
		        $re['content'] = $row;
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}

		public function AddAdmin()
		{
			$admin = new Admin();
			$admin->userId = $_REQUEST['userid'];
			$admin->password = $_REQUEST['password'];
			$as = new AdminServer();
			$result = $as->GetAdmin($admin->userId);
			$re = array('state'=>'0','content'=>'已存在该用户');
			$temp = false;
			while ($u = mysqli_fetch_array($result))
			{
				$temp = true;
				$row[]= array('userId'=>$u['userId'],'password'=>$u['password']);
			}
			if(!$temp){
				$row = $as->InsertAdmin($admin);
				$re['state'] = '1';
				$re['content'] = '注册成功';
				echo json_encode($re,JSON_UNESCAPED_UNICODE);
				return;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}

		public function UpdateAdmin()
		{
			$admin = new Admin();
			$admin->userId = $_REQUEST['userid'];
			$admin->password = $_REQUEST['password'];
			$re = array('state'=>'0','content'=>'修改失败');
			$as = new AdminServer();
			$result = $as->UpdateAdmin($admin);
			if($result){
				$re['state'] = '1';
				$re['content'] = '修改成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}
		
		public function ReplyMessage(){
		    $id = $_REQUEST["id"];
		    $reply = $_REQUEST["replyMsg"];
		    session_start();
		    $sid=$_SESSION["sid"];
		    $ms = new MessageServer();
		    $result = $ms->ReplyMessage($id,$sid,$reply);
		    $re = array('state'=>'0','content'=>"回复失败");
		    if($result){
		        $re['state'] = "1";
		        $re['content'] = "回复成功";
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}
		
		public function GetReply(){
		    $cs = new CustomerServer();
		    $cid = $cs->getIdByPhone($_REQUEST['id']);
		    $ms = new MessageServer();
		    $result = $ms->GetReply($cid);
		    $re = array('state'=>'0','content'=>null);
		    $cs = new CustomerServer();
		    while($m = mysqli_fetch_array($result)){
		        $re['state'] = "1";
		        $cname = $cs->getNameById($m['customerid']);
				$sname = $cs->getSpNameById($m['specialid']);
				$row[]= array('id'=>$m['id'],'customer'=>$cname,'special'=>$sname,'message'=>$m['message'],'reply'=>$m['reply'],'sendtime'=>$m['sendtime'],'replytime'=>$m['replytime']);
				$re['content'] = $row;
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}
		
		public function isGet(){
		    $No = $_REQUEST["recID"];
		    $n = explode(',',$No);
		    $ms = new MessageServer();
		    for($index=0;$index<count($n);$index++)
		    {
		         $ms->ChangeState($n[$index]);
		    }
		}
		
		public function IntoMessage(){
		    $message = new Message();
		    $cs = new CustomerServer();
		    $message->customerid = $cs->getIdByPhone($_REQUEST["customer"]);
		    $message->message = $_REQUEST["msg"];
		    date_default_timezone_set('PRC');
		    $message->sendtime = date('y-m-d g:i:s',time());
		    $ms = new MessageServer();
		    $result = $ms->AddMessage($message);
		    $re = array('state'=>'0','content'=>"留言失败");
		    if($result){
		        $re['state'] = "1";
		        $re['content'] = "留言成功";
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}

		public function GetMessage()
		{
			$userid = $_REQUEST['userid'];
			$page = $_REQUEST["page"];
			$pageSize = $_REQUEST["pageSize"];
			$ms = new MessageServer();
			$result = $ms->GetMessage($userid,$page,$pageSize);
			$result1 = $ms->GetTotalPages($userid);
			$cs = new CustomerServer();
			$re = array('state'=>'0','content'=>null,'totalPages'=>0);
			while($r = mysqli_fetch_array($result1))
			{
			    $re['totalPages'] = $r["total"];
			}
			while ($m = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$cname = $cs->getNameById($m['customerid']);
				$sname = $cs->getSpNameById($m['specialid']);
				$row[]= array('id'=>$m['id'],'customer'=>$cname,'special'=>$sname,'message'=>$m['message'],'reply'=>$m['reply'],'sendtime'=>$m['sendtime'],'replytime'=>$m['replytime']);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}
		
		public function ValidateLogin(){
			$userid = $_REQUEST['userid'];
			$password = $_REQUEST['pwd'];
			$as = new AdminServer();
			$result = $as->GetAdmin($userid);
			$re = array('state'=>'0','content'=>null);
			$a = mysqli_fetch_row($result);
			if($a[0]==""){
				$re['content']="用户名不存在";
			}
			else{
				if($a[1] == $password){ 
				    $_SESSION["name"] = $userid;
					$re['state'] = "1";
					$re['content'] = "";
				} 
				else{
					$re['content'] = "密码错误";
				}
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
	}
	$mc = new MessageControl();
	$mc->JugdeOperate();
?>
