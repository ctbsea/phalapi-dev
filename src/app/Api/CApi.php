<?php
namespace App\Api;

use PhalApi\Api;
use PhalApi\Crypt\MultiMcryptCrypt;

/**
 * 默认接口服务类
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */

class CApi extends Api {

    //检查登录
	public function userCheck() {



	}

	//检查token有效
	public  function  checkToken(){
        $token = \PhalApi\DI()->request->getHeader('token') ;

        //加redis存储 记录token的登录时间  绑定设备 更换设备提示过期重新登录
        // 超过1个星期提示重新登录
        //操作token的有效期 保持几分钟有效
        // 过了短时间传值token重新登录  过了长时间重新验证帐号密码
        $sign = \PhalApi\DI()->request->getHeader('sign') ;
        $timestamp = time() ;
        $sign = md5( 'sss'.'$uid' . time())  ;

    }

    //token续期
    public function setTokenTime(){

    }
}
