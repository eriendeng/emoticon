<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2018/12/2
 * Time: 15:55
 */


class Conn{
    private $servername = "127.0.0.1";
    private $username = "root";
    private $password = "Change19980101";
    private $dbname = "emoticon";

    private $conn;
    
    public function __construct(){
        $conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
    }
    

    public function insert($path, $author, $category = []){
        $staff_json = json_encode($category);
        $sql = "INSERT INTO emoticon (path, author, category, created_at, updated_at)VALUES ('$path', '$author', $staff_json, '".date('Y-m-d H:m:s')."', '".date('Y-m-d H:m:s')."')";
        mysqli_query($this->conn, $sql);
    }
}


?>