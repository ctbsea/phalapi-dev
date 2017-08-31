<?php
/**
 * 请在下面放置任何您需要的应用配置
 *
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author dogstar <chanzonghuang@gmail.com> 2017-07-13
 */

return array(

    /**
     * 应用接口层的统一参数
     */
    'apiCommonRules' => array(//'v' => array('name' => 'v', 'require' => true), //接口版本
    ),

    /**
     * 接口服务白名单，格式：接口服务类名.接口服务方法名
     *
     * 示例：
     * - *.*            通配，全部接口服务，慎用！
     * - Site.*      Api_Default接口类的全部方法
     * - *.Index        全部接口类的Index方法
     * - Site.Index  指定某个接口服务，即Api_Default::Index()
     */
    'service_whitelist' => array(
        'Site.Index',
    ),

    /*
     *  Swoole 配置
     */
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
            'pidFile' => 'swoole_task_pid'  ,//pid 文件目录
            'task_ipc_mode' => 3  //模式3是完全争抢模式  1.7.2新增特性
        ],
    ),
);
