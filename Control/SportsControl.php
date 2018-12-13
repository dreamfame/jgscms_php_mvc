<?php
	require_once '../Model/Sports.php';
	require_once '../DataBaseHandle/SportsServer.php';
	header("Content-Type: text/html;charset=utf-8");
	Class SportsControl
	{
		public function JugdeOperate()
		{
			$operate = $_REQUEST["operate"];
			switch($operate)
			{
				case "record":
					$this->RecordSports();
					break;
				case "del":
				    $this->DelSports();
					break;
				case "edit":
					$this->UpdateSports();
					break;
				case "query":
					$this->GetSports();
					break;
				case "all":
					$this->getallsports();
					break;
				case "conditionQuery":
					$this->queryCondition();
					break;
				case "paging":
				    $this->GetTotalRecord();
				    break;
			}
		}
		
		public function DelSports(){
		    $id = $_REQUEST["id"];
		    $ss = new SportsServer();
		    $result = $ss->DelSports($id);
		    $re = array('state'=>'0','content'=>'删除失败');
		    if($result){
		        $re['state'] = '1';
		        $re['content'] = '删除成功';
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function GetTotalRecord(){
		    $condition = $_REQUEST["condition"];
			$param1 = $_REQUEST["conditionText1"];
			$param2 = $_REQUEST["conditionText2"];
		    $ss = new SportsServer();
		    $result = $ss->GetTotalRecord($condition,$param1,$param2);
		    $re = array('state'=>'0','content'=>'null');
		    while($r = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$re['content'] = $r["total"];
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function RecordSports(){
			$json = $_REQUEST["Data"];
			$phone = $json["phone"];
			$time = str_replace(":","-",$json["dates"]);
			$s = new Sports();
			$s->userId = $phone;
			$s->time = $time;
			$s->sbsz = $json["sports_1"]["score"];
			$s->lbyd = $json["sports_2"]["score"];
			$s->sjhw = $json["sports_3"]["score"];
			$s->zbny = $json["sports_4"]["score"];
			$s->finishtime = $json["times"];
			$ss = new SportsServer();
			if(!$this->validateTimeExist($phone,$time)){
				$result = $ss->InsertSports($s);
			}
			else{
				$result = $ss->UpdateSports($s);
			}
			if($result){
				$re = array('state'=>'1','content'=>'success');
			}
			else{
				$re = array('state'=>'0','content'=>'defailed');
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function validateTimeExist($id,$time){
			$ss = new SportsServer();
			$result = $ss->validateTime($id,$time);
			return $result;
		}
		
		public function queryCondition(){
			$condition = $_REQUEST["condition"];
			$param1 = $_REQUEST["conditionText1"];
			$param2 = $_REQUEST["conditionText2"];
			$page = $_REQUEST["page"];
			$ss = new SportsServer();
			$result = $ss->GetSportsByCondition($condition,$param1,$param2,$page);
			$re = array('state'=>'0','content'=>'null');
			while($s = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$row[]= array('id'=>$s['id'],'userId'=>$s['userId'],'time'=>$s['time'],'sbsz'=>$s['sbsz'],'lbyd'=>$s['lbyd'], 'sjhw'=>$s['sjhw'],'zbny'=>$s['zbny'],'kxyd'=>$s['kxyd'],'ftime'=>$s['finishtime']);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}

		public function UpdateSports()
		{
			$ss = new SportsServer();
			$sport = new Sports();
			$sport->id = $_REQUEST["id"];
			$sport->sbsz = $_REQUEST["a"];
			$sport->lbyd = $_REQUEST["b"];
			$sport->sjhw = $_REQUEST["c"];
			$sport->zbny = $_REQUEST["d"];
			$sport->kxyd = $_REQUEST["e"];
			$re = array('state'=>'0','content'=>'修改失败');
			$result = $ss->UpdateSports($sport);
			if($result){
				$re['state'] = '1';
				$re['content'] = '修改成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function getallsports(){
		    $page = $_REQUEST["page"];
			$re = array('state'=>'0','content'=>null);
			$ss = new SportsServer();
			$result = $ss->GetSportsList($page);
			while ($s = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$row[]= array('id'=>$s['id'],'userId'=>$s['userId'],'time'=>$s['time'],'sbsz'=>$s['sbsz'],'lbyd'=>$s['lbyd'], 'sjhw'=>$s['sjhw'],'zbny'=>$s['zbny'],'kxyd'=>$s['kxyd'],'ftime'=>$s['finishtime']);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
	}
	$uc = new SportsControl();
	$uc->JugdeOperate();
?>
