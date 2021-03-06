<?php

/*
 *  Swoole 配置

'Swoole' => array(
    'task' => [
        'timezone' => 'Asia/Shanghai' , //时区
        'host' => '127.0.0.1',  //默认监听ip
        'port' => '9523',   //默认监听端口
        'ps_name' => 'swTask',  //默认swoole 进程名称
        'daemonize' => 0,   //是否守护进程 1=>守护进程| 0 => 非守护进程
        'worker_num' => 2,    //worker进程 cpu核数 1-4倍,一般选择和cpu核数一致
        'task_worker_num' => 2,    //task进程,根据实际情况配置
        'task_max_request' => 10000,    //当task进程处理请求超过此值则关闭task进程,保障进程无内存泄露,
        'runtime' =>  API_ROOT . '/runtime' , //日志目录
        'pidFile' => 'swoole_task_pid' //pid 文件目录
    ],
);
*/