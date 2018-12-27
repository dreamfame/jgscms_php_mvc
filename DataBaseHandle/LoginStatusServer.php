<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 * Date: 2018/12/27
 * Time: 15:13
 */

    require_once 'DBHelper.php';
    require_once '../Extensions/Security.php';
    require_once '../Extensions/LoadXmlData.php';
    header("Content-Type: text/html;charset=utf-8");

    Class LoginStatusServer{
        public $db;
        public $conn;
        public $dbase;
        public $db_table;
        public function LoginStatusServer()
        {
            $this->db = new DBHelper("登录状态表");
            $xc = new XmlControl();
            $this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
            $this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",12,"name");
            $this->conn = $this->db->Open($this->dbase);
        }

        public function LogIn($loginStatus){
            $sql = "insert into ".$this->db_table."(username,is_login,client_ip,session_id) values('$loginStatus->username','$loginStatus->is_login','$loginStatus->client_ip','$loginStatus->session_id')";
            $result = $this->db->ExecSql($sql,$this->conn);
            return $result;
        }
    }