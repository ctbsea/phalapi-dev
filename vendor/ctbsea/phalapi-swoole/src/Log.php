<?php
/**
 *  基于swoole扩展的Task
 */

namespace PhalApi\Swoole;
class Task
{
    /**
     * swoole http-server 实例
     *
     * @var null | swoole_http_server
     */
    private $server = null;
    /**
     * swoole 配置
     *
     * @var array
     */
    private $setting = [];

    public function __construct($conf)
    {
        $this->setting = $conf;
    }

    public function run()
    {
        $this->server = new swoole_server($this->setting['host'], $this->setting['port']);
        $this->server->set($this->setting);
        //回调函数
        $call = [
            'start',
            'workerStart',
            'managerStart',
            'receive',
            'task',
            'finish',
            'workerStop',
            'shutdown',
        ];
        //事件回调函数绑定
        foreach ($call as $v) {
            $m = 'on' . ucfirst($v);
            if (method_exists($this, $m)) {
                $this->server->on($v, [$this, $m]);
            }
        }

        $this->server->start();
    }

    /**
     * swoole-server master start
     *
     * @param $server
     */
    public function onStart($server)
    {
        echo 'Date:' . date('Y-m-d H:i:s') . "\t swoole_task_server master worker start\n";
        $this->setProcessName($server->setting['ps_name'] . '-master');
        //记录进程id,脚本实现自动重启
        $pid = "{$this->server->master_pid}\n{$this->server->manager_pid}";
        file_put_contents($this->setting['pid_file'], $pid);
    }

    /**
     * manager worker start
     *
     * @param $server
     */
    public function onManagerStart($server)
    {
        echo 'Date:' . date('Y-m-d H:i:s') . "\t swoole_task_server manager worker start\n";
        $this->setProcessName($server->setting['ps_name'] . '-manager');
    }

    /**
     * swoole-server master shutdown
     */
    public function onShutdown()
    {
        unlink($this->setting['pid_file']);
        echo 'Date:' . date('Y-m-d H:i:s') . "\t swoole_task_server shutdown\n";
    }

    /**
     * worker start 加载业务脚本常驻内存
     *
     * @param $server
     * @param $workerId
     */
    public function onWorkerStart($server, $workerId)
    {
        if ($workerId >= $this->setting['worker_num']) {
            $this->setProcessName($server->setting['ps_name'] . '-task');
        } else {
            $this->setProcessName($server->setting['ps_name'] . '-work');
        }
    }

    /**
     * worker 进程停止
     *
     * @param $server
     * @param $workerId
     */
    public function onWorkerStop($server, $workerId)
    {
        echo 'Date:' . date('Y-m-d H:i:s') . "\t swoole_task_server[{$server->setting['ps_name']}] worker:{$workerId} shutdown\n";
    }

    public function onReceive(swoole_server $serv, $fd, $from_id, $data)
    {
        $taskId = $serv->task($data);
        $this->_log("Receive send to swoole_task_server [$taskId]", $data);
    }
    /**
     * 任务处理
     *
     * @param $server
     * @param $taskId
     * @param $fromId
     * @param $request
     *
     * @return mixed
     */
    public function onTask($server, $taskId, $fromId, $ret)
    {
        $params = json_decode($ret, TRUE);
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

    /**
     * 任务结束回调函数
     *
     * @param $server
     * @param $taskId
     * @param $ret
     */
    public function onFinish($server, $taskId, $ret)
    {
        $this->_log("swoole_task_server finish [$taskId]", $ret);
    }

    /**
     * 修改swooleTask进程名称，如果是macOS 系统，则忽略(macOS不支持修改进程名称)
     *
     * @param $name 进程名称
     *
     * @return bool
     * @throws \Exception
     */
    private function setProcessName($name)
    {
        if (PHP_OS == 'Darwin') {
            return false;
        }

        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($name);
        } else {
            if (function_exists('swoole_set_process_name')) {
                swoole_set_process_name($name);
            } else {
                throw new \Exception(__METHOD__ . "failed,require cli_set_process_title|swoole_set_process_name");
            }
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
}