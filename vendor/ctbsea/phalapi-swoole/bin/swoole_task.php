#!/bin/env php
<?php
/**
 * 设置错误报告模式
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once dirname(__FILE__) . '/../public/init.php';
require_once __DIR__ . '/cliServer.php';


/**
 * 检查exec 函数是否启用  检查环境
 * 检查命令 lsof 命令是否存在
 */

if (!function_exists('exec')) {
    exit('exec function is disabled' . PHP_EOL);
}

exec("whereis lsof", $out);
if ($out[0] == 'lsof:') {
    exit('lsof is not found' . PHP_EOL);
}

/**
 * @var array swoole-http_server支持的进程管理命令
 */
$cmds = [
    'start',
    'stop',
    'restart',
    'status',
    'list',
];
/**
 * @var array 命令行参数，
 * FIXME: getopt 函数的长参数 格式 requried:, optionnal::,novalue 三种格式，可选参数这个有问题
 */
$longopt = [
    'help',//显示帮助文档
    'nodaemon',//以守护进程模式运行,不指定读取配置文件
    'host:',//监听主机ip, 0.0.0.0 表示所有ip
    'port:',//监听端口
];

$opts = getopt('', $longopt);

//不正确的命名显示帮助
if (isset($opts['help']) || $argc < 2) {
    echo <<<HELP
用法：php swoole-task.php 选项[help|d|host|port]  命令[start|stop|status|list]
管理swoole-task服务,确保系统 lsof 命令有效
如果不指定监听host或者port，使用配置参数
参数说明
    --help      显示本帮助说明
    --d         指定此参数，以非守护进程模式运行,不指定则读取配置文件值
    --host      指定监听ip 不指定则读取配置文件值,例如 php swoole.php -h 127.0.0.1
    --port      指定监听端口port 不指定则读取配置文件值， 例如 php swoole.php --host 127.0.0.1 --port 9520
    
启动swoole-task 如果不指定 host和port，读取http-server中的配置文件
关闭swoole-task 必须指定port,没有指定host，关闭的监听端口是  *:port, 指定了host，关闭 host:port端口
重启swoole-task 必须指定端口
获取swoole-task 状态，必须指定port(不指定host默认127.0.0.1), tasking_num是正在处理的任务数量(0表示没有待处理任务)
HELP;
    exit;
}

//获取项目配置
$config = \PhalApi\DI()->config->get('app.Swoole.task');

//命令检查
$cmd = $argv[$argc - 1];
if (!in_array($cmd, $cmds)) {
    exit("输入命令有误 : {$cmd}, 请查看帮助文档\n");
}
/**
 * @var string 监听主机ip, 0.0.0.0 表示监听所有本机ip, 如果命令行提供 ip 则覆盖配置项
 */
if (!empty($opts['host'])) {
    if (!filter_var($host, FILTER_VALIDATE_IP)) {
        exit("输入host有误:{$host}");
    }
    $config['host'] = $opts['host'];
}
/**
 * @var int 监听端口
 */
if (!empty($opts['port'])) {
    $port = (int)$opts['port'];
    if ($port <= 0) {
        exit("输入port有误:{$port}");
    }
    $config['port'] = $opts['port'];
}

/**
 * @var int 是否守护进程
 */
if (isset($opts['d']) && in_array($opts['d'], array(1, 0))) {
    $config['daemonize'] = $opts['d'];
}

//初始化
$ser = new cliServer($config);
//执行
switch ($cmd) {
    case 'start' :
        $ser->start();
        break;
    case 'stop' :
        $ser->stop();
        break;
    case 'status' :
        $ser->status();
        break;
    case 'list' :
        $ser->lists();
        break;
}