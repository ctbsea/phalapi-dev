#基于PhalApi 2.*的Sswoole-task拓展

![](http://webtools.qiniudn.com/master-LOGO-20150410_50.jpg)

##前言##
***先在这里感谢phalapi框架创始人@dogstar,为我们提供了这样一个优秀的开源框架.***

用过的童鞋都知道PhalApi是一个Api框架不提供view层的功能,但是很多童鞋有开发一个自己管理自己API的web界面的需求,或者是个人后台,那么是否意味着要去在学习另外一种框架来实现呢?**当然不是**在之前也有童鞋放出过一个View拓展,使用之后还是有一些不方便的地方,所以引入一个比较老牌的PHP模版引擎**Smarty**来解决这类问题,本拓展提供了对Smarty的封装,而且Smarty内容比较多在此处不会依依交与大家使用,希望的童鞋可以自己探索关于Smarty的功能,有不便之处需要封装与之联系!

**注:本拓展并没有开发完成,也没进行严格的测试,此版本为还处于开发阶段的鉴赏版.**

附上:

官网地址:[http://www.phalapi.net/](http://www.phalapi.net/ "PhalApi官网")

开源中国Git地址:[http://git.oschina.net/dogstar/PhalApi/tree/release](http://git.oschina.net/dogstar/PhalApi/tree/release "开源中国Git地址")

PhalApi Library:[http://git.oschina.net/dogstar/PhalApi-Library](http://git.oschina.net/dogstar/PhalApi-Library "PhalApi Library")

##安装  
composer.json添加

    "require": {
        "ctbsea/phalapi-swoole": "*"
    },

##初始化Swoole


1. 在项目目录新建server目录(server 可以更换其他名称)
2. 将bin目录的cliServer.php/swoole_task.php 复制到server目录
3. config 为参考配置放到app.php 配置里面


##一个简单的例子

### 使用方法

以默认配置启动swoole-task服务    

```sh
php swoole-task.php start 
```

关于swoole-task.php脚本的详细说明

- 参数

```
--d 以非守护进程模式启动，默认读取配置文件daemonize的值
--help 显示帮忙
--host 指定绑定的ip 默认读取配置文件 http_server.php中的host取值
--port 指定绑定的端口，默认读取配置文件中的 http_server.php中的port的取值
```

- 命令

```
start   //启动服务
stop    //停止服务
status  //查看状态
list    //swoole-task服务列表
```

用例说明:

```
//启动swoole-task服务，以项目根目录下的sw目录为业务目录
php swoole-task.php  start 
//停止swoole-task 服务
php swoole-task.php stop
//启动swoole-task 服务，使用host和port 覆盖默认配置
php swoole-task.php --host 127.0.0.1 --port 9520 start
//显示服务状态
php swoole-task.php status
```

##总结

写的比简单 应该还有很多问题  文档还需后续继续补充  很多也是网上整理出来的 
