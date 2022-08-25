<?php


namespace App\Http\Controllers\Wx;

use App\Models\Address;
use App\ReturnCode;
use App\Services\AddressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AddressController extends WxController
{
    // 中间件使用范围
    protected $only = ['list','deleted','detail'];
    /**
     * 地址列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $user_id = Auth::id();
        $list    = AddressService::getInstance()->getListByUserId($user_id);
        $list    = $list->map(function (Address $address) {
            $address = $address->toArray();
            $item    = [];
            foreach ($address as $k => $v) {
                $k        = lcfirst(Str::studly($k));
                $item[$k] = $v;
            }
            return $item;
        });

        return $this->success(
            [
                'total' => $list->count(),
                'page'  => 1,
                'list'  => $list->toArray(),
                'limit' => $list->count()
            ]
        );
    }

    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function deleted(Request $request)
    {
        $id = $request->input('id',0);
        if (empty($id) && !is_numeric($id)) {
            return $this->fail(ReturnCode::PARAM_ILLEGAL);
        }
        AddressService::getInstance()->delete(Auth::id(),$id);
        return $this->success();
    }

    /**
     * 详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Request $request)
    {
        $id = $request->input('id',0);
        if (empty($id) && !is_numeric($id)) {
            return $this->fail(ReturnCode::PARAM_ILLEGAL);
        }

        $data = AddressService::getInstance()->getById($id);
        return $this->success($data);
    }
}
