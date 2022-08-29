<?php


namespace App\Services\User;


use App\Exceptions\BusinessException;
use App\Models\User\Address;
use App\ReturnCode;
use App\Services\BaseService;

class AddressService extends BaseService
{
    /**
     * 地址列表
     * @param int $user_id
     * @return []|Collection
     */
    public function getListByUserId(int $user_id){
        return Address::query()
            ->where('user_id',$user_id)
            ->where('deleted',0)
            ->get();
    }

    public function getAddressByUserIdId(int $user_id,int $id){
        return Address::query()
            ->where('user_id',$user_id)
            ->where('id',$id)
            ->where('deleted',0)
            ->first();
    }

    /**
     * 地址详情
     * @param int $id
     * @return []|Collection
     */
    public function getById(int $id){
        return Address::query()
            ->where('id',$id)
            ->where('deleted',0)
            ->first();
    }

    /**
     * 删除
     * @param int $user_id
     * @param int $id
     * @return bool|mixed|null
     * @throws BusinessException
     */
    public function delete(int $user_id,int $id){
        $address = $this->getAddressByUserIdId($user_id,$id);
        if (is_null($address)) {
            $this->throwBusinessException(ReturnCode::PARAM_ILLEGAL);
        }
        return $address->delete();
    }
}
