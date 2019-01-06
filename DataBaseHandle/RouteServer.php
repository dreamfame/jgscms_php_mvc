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
			$this->db = new DBHelper("景区路线表");
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",5,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function GetAll(){
			$sql = "select route.pic,route.id,route.area_id,area.name as area_name,route.route,route.name,route.type,route.time,route.created_at from route left join area on route.area_id=area.id order by created_at desc";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

        public function GetType(){
            $sql = "select id,name from Route_type";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function QueryRoutes($where){
            $sql = "select route.pic,route.id,route.area_id,area.name as area_name,route.route,route.name,route.type,route.time,route.created_at from route left join area on route.area_id=area.id".$where." order by created_at desc";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function QueryConditionRoutes($condition){
		    $sql = "";
		    if(count($condition)>1){
		        $type = $condition[0];
		        $name = $condition[1];
		        $time = $condition[2];
                $sql = "select route.pic,route.id,route.area_id,area.name as area_name,route.route,route.name,route.type,route.time,route.created_at from route left join area on route.area_id=area.id where route.name = '$name' and route.type = '$type' and route.time = '$time'  order by created_at desc";
            }
            else{
                $condition = $condition[0];
                $sql = "select route.pic,route.id,route.area_id,area.name as area_name,route.route,route.name,route.type,route.time,route.created_at from route left join area on route.area_id=area.id where route.name = '$condition' or route.type = '$condition'  order by created_at desc";

            }
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function QueryRoute($where){
            $sql = "select route.pic,route.id,route.area_id,area.name as area_name,route.route,route.name,route.type,route.time,route.created_at from route left join area on route.area_id=area.id".$where." order by created_at desc";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function VerifyName($area_id,$name,$time,$type){
            $sql = "select id from ".$this->db_table." where area_id = '$area_id' and name = '$name' and time = '$time' and type = '$type'";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function VerifyIdName($area_id,$name){
            $sql = "select id from ".$this->db_table." where area_id = '$area_id' and name = '$name'";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

		public function InsertRoute($route){
            $sql = "insert into ".$this->db_table."(pic,area_id,route,type,name,time,created_at) values('$route->pic','$route->area_id','$route->route','$route->type','$route->name','$route->time','$route->created_at')";
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
                $sql = "update " . $this->db_table . " set name = '$route->name',route = '$route->route' where id = '$route->id' and area_id = '$route->area_id'";
			}
			else if($field=="pic"){
                $sql = "update " . $this->db_table . " set ".$field." = '$route->pic' where id = '$route->id'";
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

        public function BatchDeleteRoute($str){
            $sql="delete from ".$this->db_table." where id in {$str}";
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
