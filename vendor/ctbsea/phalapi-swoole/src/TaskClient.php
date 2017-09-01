<?php
/**
 *  基于swoole扩展的Task
 */

namespace ctbsea\phalapiSwoole;

use ctbsea\phalapiSwoole\Log ;
class TaskClient
{

    public $client = "" ;

    public function __construct()
    {
        $this->client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

   }
////设置事件回调函数
//$client->on("connect", function($cli) {
//    global $params;
//    //$params = array();
//    //$params['service'] = 'Default.Index';
//    //$params['username'] = 'swoole';
//    $data = json_encode($params);
//    echo "Send: " . $data . "\n";;
//    $cli->send($data);
//});
//$client->on("receive", function($cli, $data){
//    echo "Received: " . $data . "\n";
//});
//$client->on("error", function($cli){
//    echo "Connect failed\n";
//});
//$client->on("close", function($cli){
//    echo "Connection close\n";
//});
////发起网络连接
//$client->connect($ip, $port, 3);
}