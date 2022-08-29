<?php


namespace App\Http\Controllers\Wx\Goods;

use App\Http\Controllers\Wx\WxController;
use App\ReturnCode;
use App\Services\Goods\CatalogService;
use Illuminate\Http\Request;

class CatalogController extends WxController
{
    // 中间件使用范围
    protected $only = [];

    /**
     * 全部分类
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $id      = $request->input('id', 0);
        // 所有一级
        $l1_list = CatalogService::getInstance()->getL1List();

        // 选中的一级，默认第一个
        if (empty($id)) {
            $current = $l1_list->first();
        } else {
            $current = $l1_list->where('id', $id)->first();
        }

        // 当前一级下二级
        $l2_list = null;
        if (!is_null($current)) {
            $l2_list = CatalogService::getInstance()->getL2ListByPid($current->id);
        }

        return $this->success(
            [
                'categoryList'       => $l1_list->toArray(),
                'currentCategory'    => $current,
                'currentSubCategory' => $l2_list->toArray()
            ]
        );
    }

    /**
     * 当前分类
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function current(Request $request)
    {
        $id = $request->input('id', 0);

        if (empty($id)) {
            return $this->codeReturn(ReturnCode::PARAM_ILLEGAL);
        }

        // 当前一级
        $current = CatalogService::getInstance()->getL1ListByid($id);

        if (is_null($current)) {
            return $this->codeReturn(ReturnCode::PARAM_VALUE_ILLEGAL);
        }

        // 当前一级下二级
        $l2_list = CatalogService::getInstance()->getL2ListByPid($current->id);
        return $this->success(
            [
                'currentCategory'    => $current,
                'currentSubCategory' => $l2_list->toArray()
            ]
        );
    }
}
