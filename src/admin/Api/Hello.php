<?php
namespace Admin\Api;

use PhalApi\Api;
use PhalApi\Crypt;
use PhalApi\Exception;
use PhalApi\Exception\BadRequestException;

/**
 * 默认接口服务类
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */

class Hello extends Api {

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
        \PhalApi\DI()->smarty->setParams(['a' => 1]);
        \PhalApi\DI()->smarty->show();
        exit;
       // \PhalApi\DI()->cookie->set('name' ,'12312312');
        $keyG    = new  \PhalApi\Crypt\RSA\KeyGenerator();
        $privkey = $keyG->getPriKey();
        $pubkey  = $keyG->getPubKey();
        var_dump($privkey ,$pubkey) ;

        \PhalApi\DI()->crypt = new \PhalApi\Crypt\RSA\MultiPri2PubCrypt();

        $data = 'AHA! I have $2.22 dollars!';

        $encryptData = DI()->crypt->encrypt($data, $privkey);

        $decryptData = DI()->crypt->decrypt($encryptData, $pubkey);

        $this->assertEquals($data, $decryptData);
        return array('title' => 'Hello World!'.$this->title);
    }
}
