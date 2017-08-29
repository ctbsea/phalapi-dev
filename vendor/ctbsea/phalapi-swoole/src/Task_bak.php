<?php

namespace PhalApi\Swoole;

/**
 * Created by PhpStorm.
 * alert:  chentb
 * CreateTime: 2017/8/25 14:04
 * Description: 基于Swoole的消息队列 必须安装swoole 扩展
 * Versioncode: 2.0.0
 */
class Task_bak
{
    private $serv;
    private $setLog = 1; // 1记录日志 0不记录

    /*
     *  配置
     *  array(
            'worker_num' => 2,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode'=> 1,
            'task_worker_num' => 2
        )
     *
     * 参数
     * [
     *     taskName => ''   ,   //队列名称 命名参照  {API}.Queue_{class}.{function}
     *     params =  [] ,       //队列参数
     * ]
     */
    public function __construct()
    {
        //加载配置
        $config = \PhalApi\DI()->config->get('app.Swoole.task');
        $ip = isset($config['ip']) ? $config['ip'] : '127.0.0.1';
        $port = isset($config['port']) ? $config['port'] : 9502;
        $this->setLog = isset($config['log']) ? $config['log'] : 1;
        $this->serv = new swoole_server($ip, $port);
        unset($config['ip']);
        unset($config['port']);
        unset($config['log']);
        $this->serv->set($config);

        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));
        // bind callback
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Finish', array($this, 'onFinish'));
        $this->serv->start();
    }

    public function onStart($serv)
    {
        //echo "Start\n";
    }

    public function onConnect($serv, $fd, $from_id)
    {
        // echo "Client {$fd} connect\n";
    }

    public function onReceive(swoole_server $serv, $fd, $from_id, $data)
    {
        $taskId = $serv->task($data);
        $this->_log("Receive send to asynctask [$taskId]", $data);
    }

    public function onTask($serv, $taskId, $from_id, $data)
    {
        $params = json_decode($data, TRUE);
        if (!is_array($params)) {
            $params = array();
        }
        if (!empty($params) && isset($params['taskName'])) {
            $result = $this->_doTask($params['taskName'], $params);
            $result['receive'] = $params;
            $this->serv->finish($result);
        }
        $this->serv->finish('empty !!!!');
    }

    public function onFinish($serv, $taskId, $data)
    {
        $this->_log("asynctask finish [$taskId]", $data);
    }

    public function onClose($serv, $fd, $from_id)
    {
        //echo "Client {$fd} close connection\n";
    }

    /*
     *   处理任务
     */
    private function _doTask($taskName, $params)
    {
        try {
            list($module, $api, $action) = explode('.', $taskName);
            $apiClass = '\\' . str_replace('_', '\\', $module)
                . '\\Api\\' . str_replace('_', '\\', ucfirst($api));
            return call_user_func(array($apiClass, $action));

        } catch (Exception $ex) {
            //错误日志
            echo $ex->getTraceAsString();
            \PhalApi\DI()->logger->error("asynctask exception in swoole", $ex->getMessage());
            $this->serv->finish("Exception: " . $ex->getMessage());
        }
    }

    /*
     *   日志处理
     */
    private function _log($msg, $data)
    {
        if ($this->setLog) {
            \PhalApi\DI()->logger->info($msg, $data);
        }
    }
}
new Task();
