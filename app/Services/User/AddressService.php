<?php


namespace App\Services\User;


use App\Exceptions\BusinessException;
use App\Models\User\Address;
use App\ReturnCode;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Model;

class AddressService extends BaseService
{
    /**
     * 地址列表
     * @param int $user_id
     * @return []|Collection
     */
    public function getListByUserId(int $user_id)
    {
        return Address::query()
            ->where('user_id', $user_id)
            ->where('deleted', 0)
            ->get();
    }

    public function getAddressByUserIdId(int $user_id, int $id)
    {
        return Address::query()
            ->where('user_id', $user_id)
            ->where('id', $id)
            ->where('deleted', 0)
            ->first();
    }

    /**
     * 地址详情
     * @param int $id
     * @return []|Collection
     */
    public function getById(int $id)
    {
        return Address::query()
            ->where('id', $id)
            ->where('deleted', 0)
            ->first();
    }

    /**
     *  创建、编辑地址
     * @param $input
     * @param $user_id
     * @return Address|\Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public function saveAddress($input, $user_id)
    {
        if (!is_null($input('id'))) {
            $address = AddressService::getInstance()->getAddressByUserIdId($user_id, $input('id'));
        } else {
            $address          = new Address();
            $address->user_id = $user_id;
        }

        if ($input['is_default']) {
            $this->resetDefault($user_id);
        }

        $address->address_detail = $input['address_detail'];
        $address->area_code      = $input['area_code'];
        $address->city           = $input['city'];
        $address->county         = $input['county'];
        $address->is_default     = $input['is_default'];
        $address->name           = $input['name'];
        $address->postal_code    = $input['postal_code'];
        $address->province       = $input['province'];
        $address->tel            = $input['tel'];
        $address->save();
        return $address;
    }

    // 将其他默认地址改为非默认
    public function resetDefault($userId)
    {
        return Address::query()
            ->where('user_id', $userId)
            ->where('is_default', 1)
            ->update(['is_default' => 0]);
    }

    /**
     * 删除
     * @param int $user_id
     * @param int $id
     * @return bool|mixed|null
     * @throws BusinessException
     */
    public function delete(int $user_id, int $id)
    {
        $address = $this->getAddressByUserIdId($user_id, $id);
        if (is_null($address)) {
            $this->throwBusinessException(ReturnCode::PARAM_ILLEGAL);
        }
        return $address->delete();
    }
}
