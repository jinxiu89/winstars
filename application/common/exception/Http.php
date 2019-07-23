<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/17
 * Time: 14:02
 */

namespace app\common\exception;

use Exception;
use think\exception\Handle;
use think\exception\HttpException;

class Http extends Handle
{
    public function render(Exception $exception)
    {
        if (config('app_debug')) {
            return parent::render($exception);
        } else {
            if ($exception instanceof HttpException) {
               return parent::render($exception->getMessage());
//                header('Location:/en_us/error/' . $exception->getStatusCode() . '.html');
            }
        }
    }
}