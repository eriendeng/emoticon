<?php


/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2018/12/2
 * Time: 15:55
 */


class Conn{

    private $conn;
    
    public function __construct(){
        $this->conn = new medoo([
            'database_type' => 'mysql',
            'database_name' => 'emoticon',
            'server' => 'localhost',
            'username' => 'root',
            'password' => 'Change19980101!',
            'charset' => 'utf8'
        ]);
    }
    

    public function insert($path, $author, $category = []){
        $this->conn->insert('emoticon', [
            'path' => $path,
            'author' => $author,
            'category' => $category
        ]);
    }

    public function error(){
        return $this->conn->error();
    }
}


?>