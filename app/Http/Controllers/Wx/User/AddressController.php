<?php


namespace App\Http\Controllers\Wx\User;

use App\Http\Controllers\Wx\WxController;
use App\Models\User\Address;
use App\ReturnCode;
use App\Services\User\AddressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends WxController
{
    // 中间件使用范围
    protected $only = ['list', 'delete', 'detail'];

    /**
     * 地址列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $user_id = Auth::id();
        $list    = AddressService::getInstance()->getListByUserId($user_id);

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
     * 详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Request $request)
    {
        $id = $request->input('id', 0);
        if (empty($id) && !is_numeric($id)) {
            return $this->fail(ReturnCode::PARAM_ILLEGAL);
        }

        $data = AddressService::getInstance()->getById($id);
        return $this->success($data);
    }

    /**
     * 新建、编辑
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $input = $request->input();

        $user_id = Auth::id();

        $data = AddressService::getInstance()->saveAddress($input, $user_id);
        return $this->success($data->id);
    }


    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function delete(Request $request)
    {
        $id = $request->input('id', 0);
        if (empty($id) && !is_numeric($id)) {
            return $this->fail(ReturnCode::PARAM_ILLEGAL);
        }
        AddressService::getInstance()->delete(Auth::id(), $id);
        return $this->success();
    }
}
