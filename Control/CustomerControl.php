<?php
	require_once '../Model/Customer.php';
	require_once '../DataBaseHandle/CustomerServer.php';
	require_once '../DataBaseHandle/InfoServer.php';
	header("Content-Type: text/html;charset=utf-8");
	session_start();
	Class CustomerControl
	{
		public function JugdeOperate()
		{
			$operate = $_REQUEST["operate"];
			switch($operate)
			{
				case "query":
					$this->GetCustomer();
					break;
				case "conditionQuery":
				    $this->queryCondition();
					break;
				case "paging":
					$this->GetTotalRecord();
					break;
			}
		}
		
		public function GetTotalRecord(){
		    $condition = $_REQUEST["condition"];
		    $param1 = $_REQUEST["conditionText1"];
		    $param2 = $_REQUEST["conditionText2"];
		    $cs = new CustomerServer();
		    $result = $cs->GetTotalRecord($condition,$param1,$param2);
		    $re = array('state'=>'0','content'=>'null');
		    while($r = mysqli_fetch_array($result))
		    {
		        $re['state'] = '1';
		        $re['content'] = $r["total"];
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}

		public function GetCustomer()
		{
			$page = $_REQUEST["page"];
			$cs = new CustomerServer();
			$result = $cs->GetCustomer($page);
			$re = array('state'=>'0','content'=>null);
			while ($c = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				if($c['birth']==null||$c['birth']==""){
				    $age = "无";
				}
				else{
				    $age = (int)(date("Y"))-(int)(substr($c['birth'],0,4));
				}
				if($c['phone']==""||$c['phone']==null){
				    $phone = "无";
				}
				else{
				    $phone = $c['phone'];
				}
			    if($c['telephone']==""||$c['telephone']==null){
				    $telephone = "无";
				}
				else{
				    $telephone = $c['telephone'];
				}
				if($c['name']==""||$c['name']==null){
				    $is = new InfoServer();
				    $name = $is->GetNick($phone);
				    if($name == ""){
				        $name = $phone;
				    }
				}
				else{
				    $name = $c['name'];
				}
				$row[]= array('id'=>$c['id'],'name'=>$name,'sex'=>$c['sex'],'phone'=>$phone,'telephone'=>$telephone,'age'=>$age);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}
		
		public function queryCondition(){
		    $condition = $_REQUEST["condition"];
		    $conditionText = $_REQUEST["conditionText"];
		    $page = $_REQUEST["page"];
		    $cs = new CustomerServer();
		    $result = $cs->GetCustomerByCondition($condition,$conditionText,$page);
		    $re = array('state'=>'0','content'=>null);
		    while ($c = mysqli_fetch_array($result))
		    {
		        $re['state'] = '1';
		        if($c['birth']==null||$c['birth']==""){
				    $age = "无";
				}
				else{
				    $age = (int)(date("Y"))-(int)(substr($c['birth'],0,4));
				}
				if($c['phone']==""||$c['phone']==null){
				    $phone = "无";
				}
				else{
				    $phone = $c['phone'];
				}
				if($c['telephone']==""||$c['telephone']==null){
				    $telephone = "无";
				}
				else{
				    $telephone = $c['telephone'];
				}
				if($c['name']==""||$c['name']==null){
				    $is = new InfoServer();
				    $name = $is->GetNick($phone);
				    if($name == ""){
				        $name = $phone;
				    }
				}
				else{
				    $name = $c['name'];
				}
				$row[]= array('id'=>$c['id'],'name'=>$name,'sex'=>$c['sex'],'phone'=>$phone,'telephone'=>$telephone,'age'=>$age);
		        $re['content'] = $row;
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
	}
	$cc = new CustomerControl();
	$cc->JugdeOperate();
?>
