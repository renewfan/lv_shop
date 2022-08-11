<?php


namespace App\Http\Controllers\Wx;

use App\Http\Controllers\Controller;
use App\ReturnCode;
use Illuminate\Http\JsonResponse;

class WxController extends Controller
{
    // 中间件拦截范围
    protected $only;
    protected $except;

    /**
     * 控制器中所有方法使用中间件
     * AuthController constructor.
     */
    public function __construct()
    {
        // 中间件拦截
        $option = [];
        if (!is_null($this->only)) {
            $option['only'] = $this->only; // 只有访问user方法经过中间件处理
        }
        if (!is_null($this->except)) {
            $option['except'] = $this->except;
        }
        $this->middleware('auth:wx', $option);
    }

    /**
     * 统一返回形式
     * @param $code_msg
     * @param null $data
     * @param string $in_msg
     * @return JsonResponse
     */
    protected function codeReturn($code_msg, $data = null, string $in_msg = ''): JsonResponse
    {
        list($code, $msg) = $code_msg;
        $all = [
            'code' => $code,
            'msg'  => $in_msg ?: $msg
        ];
        if (!is_null($data)) {
            $all['data'] = $data;
        }
        return response()->json($all);
    }

    protected function success($data = null): JsonResponse
    {
        return $this->codeReturn(ReturnCode::SUCCESS, $data);
    }

    protected function fail($code_msg = ReturnCode::FAIL, $in_msg = ''): JsonResponse
    {
        return $this->codeReturn($code_msg, null, $in_msg);
    }

    protected function failOrSuccess($is_success,$code_msg = ReturnCode::FAIL,$data =null, $in_msg = ''): JsonResponse
    {
        if ($is_success) {
            return $this->success($data);
        }
        return $this->fail($code_msg, $in_msg);
    }
}
