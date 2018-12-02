<?php



use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as DB;


/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2018/12/2
 * Time: 15:55
 */


class Conn{

    private $conn;
    private $database = [
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'emoticon',
        'username'  => 'root',
        'password'  => 'Change19980101!',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ];
    
    public function __construct(){
        $this->conn = new DB();
        $this->conn->addConnection($this->database);
        $this->conn->setAsGlobal();
        $this->conn->bootEloquent();

    }
    

    public function insert($path, $author, $category = []){
        $category = json_encode($category);
        return $this->conn->table('emoticon')->insert([
            'path' => $path,
            'author' => $author,
            'category' => $category,
        ]);
    }

    public function search($category = []){
        $list = [];
        $data = [];
        foreach (DB::table('emoticon')->get() as $row){
            foreach ($category as $word){
                $w = json_decode(utf8_encode($row->category));
                print_r($w);
                if (!in_array($word, $w)){
                    goto A;
                }
            }
            $list[] = $row;
            A:
        }
        foreach ($list as $emo){
            $data[] = $emo->path;
        }
        return $data;
    }

}


?>