<?php


namespace App\Http\Controllers\Wx;


use App\Models\Address;
use App\ReturnCode;
use App\Services\AddressService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AddressController extends WxController
{
    /**
     * 地址列表
     * @param Request $request
     */
    public function list(Request $request)
    {
        $user_id = $request['user_id'];
        $list    = AddressService::getInstance()->getListByUserId($user_id);
        $list->map(function (Address $address) {
            $address = $address->toArray();
            $item    = [];
            foreach ($address as $k => $v) {
                $k        = lcfirst(Str::studly($k));
                $item[$k] = $v;
            }
            return $item;
        });
        $this->success(
            [
                'total' => $list->count(),
                'page'  => 1,
                'list'  => $list->toArray(),
                'pages' => 1,
                'limit' => $list->count()
            ]
        );
    }

    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleted(Request $request)
    {
        $id = $request->input('id',0);
        if (empty($id) && !is_numeric($id)) {
            return $this->fail(ReturnCode::PARAM_ILLEGAL);
        }
        AddressService::getInstance()->delete($this->user()->id,$id);
        return $this->success();
    }
}
