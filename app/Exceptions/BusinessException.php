<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    //
    public function __construct($code_msg, $in_msg='')
    {
        list($code,$msg) = $code_msg;

        parent::__construct($in_msg ?: $msg, $code);
    }
}
