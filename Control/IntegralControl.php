<?php
	require_once '../Model/Integral.php';
	require_once '../DataBaseHandle/IntegralServer.php';
	require_once '../DataBaseHandle/CustomerServer.php';
	require_once '../DataBaseHandle/InfoServer.php';
	header("Content-Type: text/html;charset=utf-8");
	Class IntegralControl
	{	
		public function JugdeOperate()
		{
			$operate = $_REQUEST["operate"];
			switch($operate)
			{
				case "add":
					$this->AddUser();
					break;
				case "del":
					break;
				case "edit":
					$this->UpdateUser();
					break;
				case "query":
					$this->GetIntegral();
					break;
				case "login":
					$this->ValidateLogin();
					break;
				case "conditionQuery":
					$this->queryCondition();
					break;
				case "headimg":
					$this->changeImg();
					break;
				case "getscore":
				    $this->UpdateIntegral();break;
				case "updatetime":
				    $this->UpdateTime();break;
				case "gettime":
				    $this->GetTime();break;
				case "myrank":
				    $this->GetMyRank();break;
			}
		}
		
		public function GetIntegral(){
		    $userid = $_REQUEST['userid'];
		    $client = $_REQUEST['client'];
		    $s = new IntegralServer();
		    $result = $s->GetIntegrals($userid,$client);
		    $re = array('state'=>'0','content'=>null);
		    $cs = new CustomerServer();
		    $is = new InfoServer();
		    while ($r = mysqli_fetch_array($result))
		    {
		        if($is->GetNick($r['userid'])==""){
		            $name = $cs->getNameByPhone($r['userid']);
		        }else{
		            $name = $is->GetNick($r['userid']);
		        }
		        $re['state'] = '1';
		        $row[]= array('userid'=>$r['userid'],'name'=>$name,'integral'=>$r['integral'],'time'=>$r['time']);
		        $re['content'] = $row;
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}
		
		public function GetMyRank(){
		    $userid = $_REQUEST["userid"];
		    $type = $_REQUEST["type"];
		    $is = new IntegralServer();
		    $num = $is->GetMyRank($userid,$type);
		    $re = array('state'=>'1','content'=>$num);
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}
		
		public function GetTime(){
		    $client = $_REQUEST['client'];
		    $userid = $_REQUEST['userid'];
		    $s = new IntegralServer();
		    $result = $s->GetTimes($userid,$client);
		    $re = array('state'=>'0','content'=>null);
		    $cs = new CustomerServer();
		    $is = new InfoServer();
		    while ($r = mysqli_fetch_array($result))
		    {
		        if($is->GetNick($r['userid'])==""){
		            $name = $cs->getNameByPhone($r['userid']);
		        }else{
		            $name = $is->GetNick($r['userid']);
		        }
		        $re['state'] = '1';
		        $row[]= array('userid'=>$r['userid'],'name'=>$name,'integral'=>$r['integral'],'time'=>$r['time']);
		        $re['content'] = $row;
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}
		
		public function UpdateIntegral(){
		    $userid = $_REQUEST['userid'];
		    $score = $_REQUEST["score"];
		    $s = new IntegralServer();
		    $result = $s->UpdateIntegral($userid,$score);
		    $re = array('state'=>'0','content'=>"积分更新失败");
		    if ($result)
		    {
		        $re['state'] = '1';
		        $re['content'] = "积分更新成功";
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}
		
		public function UpdateTime(){
		    $userid = $_REQUEST['userid'];
		    $time = $_REQUEST["time"];
		    $s = new IntegralServer();
		    $result = $s->UpdateTime($userid,$time);
		    $re = array('state'=>'0','content'=>"时长更新失败");
		    if ($result)
		    {
		        $re['state'] = '1';
		        $re['content'] = "时长更新成功";
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}
	}
	$ic = new IntegralControl();
	$ic->JugdeOperate();
?>
