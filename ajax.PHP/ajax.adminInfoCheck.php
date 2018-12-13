<?php
	include 'ajax_template.php';
	include '../Extensions/LoadXmlData.php';
	$xc = new XmlControl();
	$db = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
	$db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",0,"name");
	$type = $_REQUEST["type"];
	$str = $_REQUEST["str"];
	$query = "";
	if($type==0)
	{
		$query = "username";
	}
	else if($type==1)
	{
		$query = "phone";
	}
	$sql = "select * from ".$db_table." where ".$query."='$str'";
	$result = AjaxGetData::RunSqlTwoParam($db, $sql);
	$row = mysqli_fetch_row($result);
	if($type==0){
		if($row[0]!="")
		{
			echo "1";
		}
		else{
			echo "0";
		}
	}
	else if($type==1)
	{
		if($row[0]!="")
		{
			echo "1";
		}
		else{
			if(!is_numeric($str))
			{
				echo "1";
			}
			else{
				echo "0";
			}
		}
	}
?>