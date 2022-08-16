<?php


namespace App\Services;


use App\Exceptions\BusinessException;
use App\Models\Address;
use App\ReturnCode;

class AddressService extends BaseService
{
    /**
     * 地址列表
     * @param int $user_id
     * @return []|Collection
     */
    public function getListByUserId(int $user_id){
        return Address::query()->where('user_id',$user_id)->where('deleted',0)->get();
    }

    public function getAddressByUserIdId(int $user_id,int $id){
        return Address::query()->where('user_id',$user_id)->where('id',$id)->where('deleted',0)->first();
    }

    public function delete(int $user_id,int $id){
        $address = $this->getAddressByUserIdId($user_id,$id);
        if (is_null($address)) {
            $this->throwBusinessException(ReturnCode::PARAM_ILLEGAL);
        }
        return $address->delete();
    }
}
