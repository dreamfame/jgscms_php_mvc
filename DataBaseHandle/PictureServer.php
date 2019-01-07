<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	require_once 'DBHelper.php';
	require_once '../Extensions/ReturnQueryCondition.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class PictureServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function PictureServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",8,"name");
			$this->conn = $this->db->Open($this->dbase);
		}
		
		public function UpdateContent($id,$content){
		    $sql = "update ".$this->db_table." set content = '$content' where id = '$id'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function GetTotalPages($condition,$param1,$param2){
		    if($condition==""||$condition==null){
		        $sql = "select count(id) as total from ".$this->db_table;
		    }
		    else if($condition=="senddate"){
		        $sql = "select count(id) as total from ".$this->db_table." where ".$condition." > '$param1' and ".$condition." < '$param2'";
		    }
		    else{
		        if($param1==""){
		            $sql = "select count(id) as total from ".$this->db_table;
		        }
		        else{
		            $sql = "select count(id) as total from ".$this->db_table." where ".$condition." = '$param1'";
		        }
		    }
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}

		public function GetPictureByCondition($condition,$conditionText1,$conditionText2,$pageSize,$page){
			$b = ($page-1)*$pageSize;
			if($condition==""){
				$sql = "select * from ".$this->db_table." limit ".$b.",".$pageSize;
			}
			else if($condition=="senddate"){
			     $sql = "select * from ".$this->db_table." where ".$condition." > '$conditionText1' and ".$condition." < '$conditionText2' limit ".$b. ", ".$pageSize;
			}
			else{
			    if($conditionText1==""){
			        $sql = "select * from ".$this->db_table." limit ".$b.",".$pageSize;
			    }
			    else{
			        $sql = "select * from ".$this->db_table." where ".$condition." = '$conditionText1'  limit ".$b. ", ".$pageSize;
			    }
			}
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}
		
		public function GetYesterday($date){
		    $sql = "select * from ".$this->db_table." where senddate <> '$date'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function GetClientPicture($condition,$param1,$param2){
		if($condition==""||$condition==null){
		        $sql = "select * from ".$this->db_table;
		    }
		    else if($condition=="senddate"){
		        $sql = "select * from ".$this->db_table." where ".$condition." >= '$param1' and ".$condition." <= '$param2'";
		    }
		    else{
		        if($param1==""){
		            $sql = "select * from ".$this->db_table;
		        }
		        else{
		            $sql = "select * from ".$this->db_table." where ".$condition." = '$param1'";
		        }
		    }
			$result = $this->db->ExeSql($sql,$this->conn);
			return $result;
		}
		
		public function UpdateImage($p){
			$sql = "update picture set picsrc='$p->src' where id = '$p->id'";
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}
	 	
		public function UpdatePicture($picture)
		{
			$sql = "update ".$this->db_table." set  imgname = '$picture->imgname'   where id = '$picture->id'";
			try{
				$this->db->ExeSql($sql,$this->conn);
				return true;
			}
			catch(Exception $e)
			{
				return false;
			}
			return true;
		}
 
		public function GetPicture($id)
		{
			$sql = "select * from ".$this->db_table." where id = '$id'";
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}

		public function GetPictureList($page)
		{
		    $b = ($page-1)*8;
		    $e = ($page)*8;
			$sql = "select * from ".$this->db_table." limit ".$b.",".$e;
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		
		}

		public function InsertPicture($picture)
		{
			$sql = "insert into ".$this->db_table."(title,content,sender,senddate,picsrc) values('$picture->title','$picture->content','$picture->sender','$picture->senddate','$picture->picsrc')";
			try{
				$this->db->ExeSql($sql,$this->conn);
				return true;
			}
			catch(Exception $e)
			{
				return false;
			}
			return false;
		}
		
		public function validateName($name){
		    $sql = "select id from ".$this->db_table." where title = '$name'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function DeletePicture($dp)
		{
			$sql="delete from ".$this->db_table." where id= '$dp'"; 
			try{
				$this->db->ExeSql($sql,$this->conn);
				return true;
			}
			catch(Exception $e)
			{
				return false;
			}
			return false;
			}
			 
		public function EditPicture($newname,$id)
		{
			$sql = "update ".$this->db_table." set imgname='$newname'  where id='$id'";
			try{
				$this->db->ExeSql($sql,$this->conn);
				return true;
			   }
			catch(Exception $e)
			{
				return false;
			}
			return false;
		}

		
		public function GetTotalRecord($condition,$param1){
			if($condition==""||$condition==null){
				$sql = "select count(id) as total from ".$this->db_table;
			}else{
				if($param1==""){
					$sql = "select count(id) as total from ".$this->db_table;
				}
				else if($param1!=""){
					$sql = "select count(id) as total from ".$this->db_table." where ".$condition." = '$param1'";
				}
	        }
			
			$result = $this->db->ExeSql($sql,$this->conn);
			return $result;
		}
	}
?>
