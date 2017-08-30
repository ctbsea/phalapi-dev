<?php

/*
 *  命令服务
 */

class cliServer
{

    public $config = array();
    public $runtimePath = '';
    public $logPath = '';
    public $pidFile = '';

    public function __construct($config)
    {
        //创建swoole-task 实际业务所需的目录
        try {
            $this->config = $config;
            //runtime 目录
            $this->runtimePath = $config['runtime'];
            if (!is_writable(dirname($this->runtimePath))) {
                exit("文件需要目录的写入权限runtime");
            }
            $this->logPath = $this->runtimePath . '/swooleLog/';
            if (!is_dir($this->logPath)) {
                mkdir($this->logPath, 0777);
            }
            //pid文件
            $this->config['pidFile'] = $this->pidFile = $this->logPath . $config['pidFile'];
        } catch (Exception $e) {
            //记录错误日志
            throw $e;
        }
    }

    /*
      *  swoole启动
      */
    public function start()
    {
        echo "正在启动 swoole-task 服务" . PHP_EOL;
        if (file_exists($this->pidFile)) {
            $pid = explode("\n", file_get_contents($this->pidFile));
            $cmd = "ps ax | awk '{ print $1 }' | grep -e \"^{$pid[0]}$\"";
            exec($cmd, $out);
            if (!empty($out)) {
                exit("swoole-task pid文件存在，swoole-task 服务器已经启动，进程pid为:{$pid[0]}" . PHP_EOL);
            } else {
                echo "警告:swoole-task pid文件存在，可能swoole-task服务上次异常退出(非守护模式ctrl+c终止造成是最大可能)" . PHP_EOL;
                unlink($this->pidFile);
            }
        }


        $bind = $this->checkPort($this->config['port']);
        if ($bind) {
            foreach ($bind as $k => $v) {
                if ($v['ip'] == '*' || $v['ip'] == $this->config['host']) {
                    exit("端口已经被占用 {$this->config['host']}:{$this->config['port']}, 占用端口进程ID {$k}" . PHP_EOL);
                }
            }
        }
        date_default_timezone_set($this->config['timezone']);
	$server = new \ctbsea\phalapiSwoole\Task($this->config);
        $server->run();
        //确保服务器启动后swoole-task-pid文件必须生成
        /*if (!empty(portBind($port)) && !file_exists(SWOOLE_TASK_PID_PATH)) {
            exit("swoole-task pid文件生成失败( " . SWOOLE_TASK_PID_PATH . ") ,请手动关闭当前启动的swoole-task服务检查原因" . PHP_EOL);
        }*/
        exit("启动 swoole-task 服务成功" . PHP_EOL);
    }

    /*
     *  swoole停止
     */
    public function stop()
    {
        echo "正在停止 swoole-task 服务" . PHP_EOL;
        if (!file_exists($this->pidFile)) {
            exit('swoole-task-pid文件不存在' . PHP_EOL);
        }
        $pid = explode("\n", file_get_contents($this->pidFile));
        $bind = $this->checkPort($this->config['port']);
        if (empty($bind) || !isset($bind[$pid[0]])) {
            exit("指定端口占用进程不存在 port:{$this->config['port']}, pid:{$pid[0]}" . PHP_EOL);
        }
        $cmd = "kill {$pid[0]}";
        exec($cmd);
        do {
            $out = [];
            $c = "ps ax | awk '{ print $1 }' | grep -e \"^{$pid[0]}$\"";
            exec($c, $out);
            if (empty($out)) {
                break;
            }
        } while (true);
        //确保停止服务后swoole-task-pid文件被删除
        if (file_exists($this->pidFile)) {
            unlink($this->pidFile);
        }
        $msg = "执行命令 {$cmd} 成功，端口 {$this->config['host']}:{$this->config['port']} 进程结束" . PHP_EOL;
        exit($msg);
    }

    /*
     *  查看swoole的状态
     */
    public function status()
    {
        echo "swoole-task {$this->config['host']}:{$this->config['port']} 运行状态" . PHP_EOL;
        $cmd = "curl -s '{$this->config['host']}:{$this->config['port']}?cmd=status'";
        exec($cmd, $out);
        if (empty($out)) {
            exit("{$this->config['host']}:{$this->config['port']} swoole-task服务不存在或者已经停止" . PHP_EOL);
        }
        foreach ($out as $v) {
            $a = json_decode($v);
            foreach ($a as $k1 => $v1) {
                echo "$k1:\t$v1" . PHP_EOL;
            }
        }
        exit();
    }


    /*
      *  查看swoole进程列表  macos不支持
      */
    public function lists()
    {
        echo "本机运行的swoole-task服务进程" . PHP_EOL;
        $cmd = "ps aux|grep " . $this->config['ps_name'] . "|grep -v grep|awk '{print $1, $2, $6, $8, $9, $11}'";
        exec($cmd, $out);
        if (empty($out)) {
            exit("没有发现正在运行的swoole-task服务" . PHP_EOL);
        }
        echo "USER PID RSS(kb) STAT START COMMAND" . PHP_EOL;
        foreach ($out as $v) {
            echo $v . PHP_EOL;
        }
        exit();
    }
    /*
     *   检查端口是否被占用
     */
    public function checkPort($port)
    {
        $ret = [];
        $cmd = "lsof -i :{$port}|awk '$1 != \"COMMAND\"  {print $1, $2, $9}'";
        exec($cmd, $out);
        if ($out) {
            foreach ($out as $v) {
                $a = explode(' ', $v);
                list($ip, $p) = explode(':', $a[2]);
                $ret[$a[1]] = [
                    'cmd' => $a[0],
                    'ip' => $ip,
                    'port' => $p,
                ];
            }
        }
        return $ret;
    }
}



