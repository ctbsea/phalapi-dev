<?php

namespace PhalApi\Swoole;

/**
 * Created by PhpStorm.
 * alert:  chentb
 * CreateTime: 2017/8/25 14:04
 * Description: 基于Swoole的消息队列 必须安装swoole 扩展
 * Versioncode: 2.0.0
 */

class Error
{
    /**
     * error handler function
     */
    public static function myErrorHandler($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            return;
        }
        switch ($errno) {
            case E_USER_ERROR:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            default:
                \PhalApi\DI()->logger->error('error in swoole',
                    array('errno' => $errno, 'errstr' => $errstr, 'errline' => $errline, 'errfile' => $errfile));
                break;
        }
        /* Don't execute PHP internal error handler */
        return true;
    }
}