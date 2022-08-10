<?php


namespace App\Http\Controllers\Wx;

use App\Http\Controllers\Controller;
use App\ReturnCode;

class WxController extends Controller
{
    protected function codeReturn($code_msg, $data = null, $in_msg='')
    {
        list($code,$msg) = $code_msg;
        $all = [
            'code' => $code,
            'msg'  => $in_msg ?: $msg
        ];
        if (!is_null($data)) {
            $all['data'] = $data;
        }
        return response()->json($all);
    }

    protected function Success($data=null)
    {
        return $this->codeReturn(ReturnCode::SUCCESS, $data);
    }

    protected function Fail($code_msg=ReturnCode::FAIL, $in_msg='')
    {
        return $this->codeReturn($code_msg, null, $in_msg);
    }
}
