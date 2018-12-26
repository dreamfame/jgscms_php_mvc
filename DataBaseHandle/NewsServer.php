<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class NewsServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function NewsServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",1,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function GetAll(){
			$sql = "select news.id,news_type.name as type,news.title,news.content,news.isshow,news.top,news.created_at,news.operator,news.see,news.updated_at,abstract,keyword,pic from news left join news_type on news.type=news_type.id order by created_at desc";
            //$sql = "select * from ".$this->db_table;
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

        public function GetShow(){
            $sql = "select news.id,news_type.name as type,news.title,news.content,news.isshow,news.top,news.created_at,news.operator,news.see,news.updated_at,abstract,keyword,pic from news left join news_type on news.type=news_type.id where isshow = 1 order by created_at desc";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function GetType(){
            $sql = "select id,name from news_type";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function QueryNews($where){
            $sql = "select * from ".$this->db_table.$where."order by created_at desc";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

		public function InsertNews($news){
            $sql = "insert into ".$this->db_table."(type,title,content,isshow,top,updated_at,created_at,operator,see,keyword,abstract,pic) values('$news->type','$news->title','$news->content','$news->show','$news->top','$news->updated_at','$news->created_at','$news->operator','$news->see','$news->keyword','$news->abstract','$news->pic')";
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

		public function UpdateNews($news,$field){
            $sql = "";
            if($field=="top"){
                $sql = "update " . $this->db_table . " set ".$field." = '$news->top' where id = '$news->id'";
            }
            else if($field=="isshow"){
                $sql = "update " . $this->db_table . " set ".$field." = '$news->show' where id = '$news->id'";
			}
			else if($field=="all"){
                $sql = "update " . $this->db_table . " set title = '$news->title',operator = '$news->operator',created_at = '$news->created_at',type = '$news->type',keyword = '$news->keyword',abstract = '$news->abstract',content = '$news->content',pic = '$news->pic' where id = '$news->id'";
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

		public function DeleteNews($id)
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

        public function BatchDeleteNews($str){
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
