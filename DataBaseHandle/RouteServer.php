<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class RouteServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function RouteServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",5,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function GetAll(){
			$sql = "select route.id,route.scenic_id,scenic.name as scenic_name,route.route,route.name,route.type,route.time,route.created_at from route left join scenic on route.scenic_id=scenic.id";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

        public function GetType(){
            $sql = "select id,name from Route_type";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function GetRouteById($id){
            $sql = "select * from ".$this->db_table." where id = '$id'";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

		public function InsertRoute($route){
            $sql = "insert into ".$this->db_table."(scenic_id,route,type,name,time,created_at) values('$route->scenic_id','$route->route','$route->type','$route->name','$route->time','$route->created_at')";
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

		public function UpdateRoute($route,$field){
            $sql = "";
            if($field=="top"){
                $sql = "update " . $this->db_table . " set ".$field." = '$route->top' where id = '$route->id'";
            }
            else if($field=="isshow"){
                $sql = "update " . $this->db_table . " set ".$field." = '$route->show' where id = '$route->id'";
			}
			else if($field=="all"){
                $sql = "update " . $this->db_table . " set name = '$route->name',route = '$route->route' where id = '$route->id' and scenic_id = '$route->scenic_id'";
			}
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

		public function DeleteRoute($id)
        {
            $sql="delete from ".$this->db_table." where id= '$id'";
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
	}
?>
