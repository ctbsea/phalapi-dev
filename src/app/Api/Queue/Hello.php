<?php
namespace App\Api\Queue;
use PhalApi\Api;


/**
 * 默认接口服务类
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */

class Hello extends Api {


	/**
	 * 测试的
     * @desc 测试第一次接口3
     * @params  strting m  标签
	 * @return string title 标题
	 * @return string content 内容
	 * @return string version 版本，格式：X.X.X
	 * @return int time 当前时间戳
	 */
    public function world() {
        return array('title' => 'Hello World!11111111111');
    }
}
