<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	require_once '../Model/Recommend.php';
	require_once '../DataBaseHandle/RecommendServer.php';
	require_once '../DataBaseHandle/VideoServer.php';
	header("Content-Type: text/html;charset=utf-8");
	Class RecommendControl
	{
		public function JugdeOperate()
		{
			$operate = $_REQUEST["operate"];
			switch($operate)
			{
				case "add":
					$this->AddRecommend();
					break;
				case "del":
					break;
				case "edit":
					$this->UpdateRecommend();
					break;
				case "conditionQuery":
					$this->queryCondition();
					break;
				case "query":
				    $this->queryPlanlist();
				    break;
				case "paging":
					$this->GetTotalRecord();break;
				case "deleterecommend":
						$this->DeleteRecommend();break;
				case "addplan":
				    $this->AddPlan();
				    break;
				case "delplanlist":
				    $this->DelPlan();
				    break;
				case "get":
				    $this->GetList();
				    break;
			}
		}	
		
		public function GetTotalRecord(){
			$condition = $_REQUEST["condition"];
			$param1 = $_REQUEST["conditionText1"];
			$ss = new  RecommendServer();
			$result = $ss->GetTotalRecord($condition,$param1);
			$re = array('state'=>'0','content'=>'null');
			while($r = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$re['content'] = $r["total"];
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function queryCondition(){
			$page = $_REQUEST["page"];
			$pageSize = $_REQUEST["pageSize"];
			$condition = $_REQUEST["condition"];
			$conditionText = $_REQUEST["conditionText"];
			$rs = new RecommendServer();
			$result = $rs->GetRecommendByCondition($condition,$conditionText,$page,$pageSize);
			$result1 = $rs->GetTotalPages($condition,$conditionText);
			$re = array('state'=>'0','content'=>null,'totalPages'=>0);
			while($r = mysqli_fetch_array($result1))
			{
			    $re['totalPages'] = $r["total"];
			}
			while ($r = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$row[]= array('id'=>$r['id'],'planlistid'=>$r['planlistid'],'type'=>$r['type']);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}		

		public function AddRecommend()
		{
			$recommend = new Recommend();
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
		
		public function queryPlanlist(){
		    $planlistid = $_REQUEST["planlistid"];
		    $rs = new RecommendServer();
		    $result = $rs->GetPlanlist($planlistid);
		    $result1 = $rs->GetPTotalPages($planlistid);
		    $vs = new VideoServer();
		    while($r = mysqli_fetch_array($result1))
		    {
		        $re['totalPages'] = $r["total"];
		    }
		    while ($p = mysqli_fetch_array($result))
		    {
		        $re['state'] = '1';
		        $row[]= array('id'=>$p['id'],'planlistid'=>$p['planlistid'],'video'=>$vs->getNameById($p['videoid']),'times'=>$p['times']);
		        $re['content'] = $row;
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}
		
		public function AddPlan(){
		    $vid = $_REQUEST["video"];
		    $times = $_REQUEST["times"];
		    $pid = $_REQUEST["planlistid"];
		    $rs = new RecommendServer();
		    $result1 = $rs->ValidatePlan($vid,$pid);
		    $re = array('state'=>'0','content'=>'添加失败');
		    $s = mysqli_fetch_row($result1);
		    if($s[0]!=null||$s[0]!=""){
		        $re['state'] = '2';
		        $re['content'] = '已存在计划';
		    }
		    else{
		        $result = $rs->AddPlan($vid,$times,$pid);
		        if($result){
    		      $re['state'] = '1';
    		      $re['content'] = '添加成功';
		        }
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}
		
		public function DelPlan(){
		    $id = $_REQUEST["id"];
		    $rs = new RecommendServer();
		    $result = $rs->DelPlan($id);
		    if($result){
		        echo "删除成功";
		    }
		    else{
		        echo "删除失败";
		    }
		}
		
		public function GetList(){
		    $rs = new RecommendServer();
		    $vs = new VideoServer();
		    $result = $rs->GetList($condition,$conditionText);
		    while ($r = mysqli_fetch_array($result))
		    {
		        $result1 = $rs->GetPlanlist($r['planlistid']);
		        while($rr = mysqli_fetch_array($result1)){
		            $re['state'] = '1';
		            $row[]= array('id'=>$r['id'],'planlistid'=>$r['planlistid'],'type'=>$r['type'],'video'=>$rr['videoid'],'times'=>$rr['times']);
		            $re['content'] = $row;
		        } 
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}
	}
	$uc = new RecommendControl();
	$uc->JugdeOperate();
?>