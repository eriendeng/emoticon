<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2018/12/2
 * Time: 15:23
 */

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Emoticon;
use Hanson\Vbot\Message\Text;

require_once __DIR__.'/vendor/autoload.php';
require_once 'mysql.php';
$path = __DIR__.'/tmp/';
$options = [
    'path'     => $path,
    /*
     * swoole 配置项（执行主动发消息命令必须要开启，且必须安装 swoole 插件）
     */
    'swoole'  => [
        'status' => false,
        'ip'     => '127.0.0.1',
        'port'   => '8866',
    ],
    /*
     * 下载配置项
     */
    'download' => [
        'image'         => true,
        'voice'         => true,
        'video'         => true,
        'emoticon'      => true,
        'file'          => true,
        'emoticon_path' => $path.'emoticons', // 表情库路径（PS：表情库为过滤后不重复的表情文件夹）
    ],
    /*
     * 输出配置项
     */
    'console' => [
        'output'  => true, // 是否输出
        'message' => true, // 是否输出接收消息 （若上面为 false 此处无效）
    ],
    /*
     * 日志配置项
     */
    'log'      => [
        'level'         => 'debug',
        'permission'    => 0777,
        'system'        => $path.'log', // 系统报错日志
        'message'       => $path.'log', // 消息日志
    ],
    /*
     * 缓存配置项
     */
    'cache' => [
        'default' => 'file', // 缓存设置 （支持 redis 或 file）
        'stores'  => [
            'file' => [
                'driver' => 'file',
                'path'   => $path.'cache',
            ],
            'redis' => [
                'driver'     => 'redis',
                'connection' => 'emoticon',
            ],
        ],
    ],
    /*
     * 拓展配置
     * ==============================
     * 如果加载拓展则必须加载此配置项
     */
    'extension' => [
        // 管理员配置（必选），优先加载 remark_name
        'admin' => [
            'remark'   => '',
            'nickname' => '',
        ],
    ],
];
$vb = new Vbot($options);
$conn = new Conn();
$messageHandler = $vb->messageHandler;
//

$emotion_mode= [];


//

$messageHandler->setHandler(function ($message) {
    if ($message['fromType'] == 'Group') return;
    global $emotion_mode, $conn;
    if ($message['type'] == 'text'){
        switch ($message['message']){
            case '#保存表情':
                if (!isset($emotion_mode[$message['from']['UserName']])){
                    $emotion_mode[$message['from']['UserName']] = "init";
                    Text::send($message['from']['UserName'], '@Auto:开始保存表情');
                }else{
                    Text::send($message['from']['UserName'], '@Auto:请先保存模式');
                }
                break;
            case '#结束':
                unset($emotion_mode[$message['from']['UserName']]);
                Text::send($message['from']['UserName'], '@Auto:结束保存');
                break;
            default:
                if (isset($emotion_mode[$message['from']['UserName']]) && $emotion_mode[$message['from']['UserName']] != 'init'){
                    $category = explode(' ', $message['message']);
                    $conn->insert($emotion_mode[$message['from']['UserName']], $message['from']['NickName'], $category);
                    $emotion_mode[$message['from']['UserName']] = 'init';
                    Text::send($message['from']['UserName'], '@Auto:成功保存一个表情');
                }
                break;
        }
    }

    if ($message['type'] == 'emoticon'){
        if (isset($emotion_mode[$message['from']['UserName']])){
            Emoticon::download($message, function ($resource) {
                global $emotion_mode, $message;
                $time = time();
                file_put_contents(__DIR__.'/tmp/emoticons/'.$time.'.gif', $resource);
                $emotion_mode[$message['from']['UserName']] = $time.".gif";
            });
        }
    }
});

$vb->server->serve();


?>