<?php


namespace App\Services\Goods;


use App\Models\Goods\Catalog;
use App\Services\BaseService;

class CatalogService extends BaseService
{
    /**
     * 一级分类
     * @return []|Collection
     */
    public function getL1List(){
        return Catalog::query()
            ->where('level','L1')
            ->where('deleted',0)
            ->get();
    }

    /**
     * 根据一级分类id获取所属二级分类
     * @return []|Collection
     */
    public function getL2ListByPid(int $pid){
        return Catalog::query()
            ->where('level','L2')
            ->where('pid',$pid)
            ->where('deleted',0)
            ->get();
    }

    /**
     * 一级分类
     * @return []|Collection
     */
    public function getL1ListByid(int $id){
        return Catalog::query()
            ->where('level','L1')
            ->where('id',$id)
            ->where('deleted',0)
            ->first();
    }
}
