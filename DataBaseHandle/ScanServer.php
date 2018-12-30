<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 * Date: 2018/12/30
 * Time: 11:44
 */

    require_once 'DBHelper.php';
    require_once '../Extensions/Security.php';
    require_once '../Extensions/LoadXmlData.php';
    header("Content-Type: text/html;charset=utf-8");
    Class ScanServer
    {
        public $db;
        public $conn;
        public $dbase;
        public $news_db_table;
        public $scenic_db_table;
        public $area_db_table;
        public function ScanServer()
        {
            $this->db = new DBHelper("浏览表");
            $xc = new XmlControl();
            $this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
            $this->news_db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",1,"name");
            $this->scenic_db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",2,"name");
            $this->area_db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",4,"name");
            $this->conn = $this->db->Open($this->dbase);
        }

        public function Scan($id,$type){
            $sql = "";
            if($type=="news")
            {
                $sql="update " . $this->news_db_table . " set see = see+1 where id = '$id'";
            }
            else if($type=="scenic"){
                $sql="update " . $this->scenic_db_table . " set see = see+1 where id = '$id'";
            }
            else if($type=="area"){
                $sql="update " . $this->area_db_table . " set see = see+1 where id = '$id'";
            }
            $result = $this->db->ExecSql($sql,$this->conn);
            return $result;
        }
    }