<?php
namespace App\Api;

use PhalApi\Api;
use PhalApi\Crypt;
use PhalApi\Exception;
use PhalApi\Exception\BadRequestException;

/**
 * 默认接口服务类
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */

class Hello extends CApi {

	public function getRules() {
        return array(
            'world' => array(
                'title' => array('name' => 'title', 'type' => 'string','require' => true, 'min' => 1, 'max' => '20', 'desc' => '标题' ,'default' => '11111'),
            ),
        );
	}
	
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
        //测试加密解密验证登录



        return array('title' => 'Hello World!'.$this->title);
    }
}
