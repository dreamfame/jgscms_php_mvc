<?php
require_once 'DBHelper.php';
require_once '../Extensions/Security.php';
require_once '../Extensions/LoadXmlData.php';
header("Content-Type: text/html;charset=utf-8");
Class UserServer
{
    public $db;
    public $conn;
    public $dbase;
    public $db_table;
    public function UserServer()
    {
        $this->db = new DBHelper("用户表");
        $xc = new XmlControl();
        $this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
        $this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",10,"name");
        $this->conn = $this->db->Open($this->dbase);
    }

    public function VerificationUser($user)
    {
        $sql = "select * from ".$this->db_table." where username = '$user->username'";
        $result = $this->db->ExeSql($sql,$this->conn);
        $row = mysqli_fetch_row($result);
        if($row[0]=="")
        {
            $this->db->Close($this->conn);
            return false;
        }
        if(Security::encrypt($user->Password)!=$row[1])
        {
            $this->db->Close($this->conn);
            return false;
        }
        $this->db->Close($this->conn);
        return true;
    }

    public function InsertUser($user)
    {
        $sql = "insert into ".$this->db_table."(openid,wx,avatar,nickname,city,country,gender,created_at) values('$user->openid','$user->wx','$user->avatar','$user->nickname','$user->city','$user->country','$user->gender','$user->created_at') on duplicate key update avatar = values(avatar),nickname = values(nickname),city = values(city),country = values(country),gender = values(gender)";
        $result = $this->db->ExecSql($sql,$this->conn);
        return $result;
    }

    public function UpdateUser($user,$field)
    {
        $sql = "";
        if($field=="role"){
            $sql = "update " . $this->db_table . " set ".$field." = '$user->role' where username = '$user->username'";
        }
        else if($field=="status"){
            $sql = "update " . $this->db_table . " set ".$field." = '$user->status' where username = '$user->username'";
        }
        else if($field=="password"){
            $sql = "update " . $this->db_table . " set ".$field." = '$user->password' where username = '$user->username'";
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

    public function GetUserById($userId)
    {
        $sql = "select id,username,role from ".$this->db_table." where id = '$userId'";
        $result = $this->db->ExeSql($sql, $this->conn);
        return $result;
    }

    public function GetWx($openid){
        $sql = "select wx from ".$this->db_table." where openid = '$openid'";
        $result = $this->db->ExeSql($sql, $this->conn);
        $n = mysqli_fetch_object($result);
        return $n->wx;
    }

    public function GetUser()
    {
        $sql = "select id,openid,wx,nickname,avatar,gender,city,country,created_at from ".$this->db_table;
        $result = $this->db->ExeSql($sql, $this->conn);
        return $result;
    }

    public function GetUserByCondition($condition,$content)
    {
        $sql = "select id,username,password,nickname,phone,email from ".$this->db_table." where ".$condition." = '$content'";
        $result = $this->db->ExeSql($sql, $this->conn);
        return $result;
    }

    public function GetAll(){
        $sql = "select id,openid,wx,nickname,avatar,gender,city,country,created_at from ".$this->db_table;
        $result = $this->db->ExeSql($sql, $this->conn);
        return $result;
    }

    public function QueryUser($where){
        $sql = "select id,openid,wx,nickname,avatar,gender,city,country,created_at from ".$this->db_table.$where;
        $result = $this->db->ExeSql($sql, $this->conn);
        return $result;
    }

    public function DeleteUser($id){
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
