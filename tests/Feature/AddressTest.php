<?php

namespace Tests\Feature;

use App\Models\User\Address;
use App\Services\User\AddressService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Tests\BasicTestCase;

class AddressTest extends BasicTestCase
{
    use DatabaseTransactions;

    /**
     * 列表
     */
    public function testList()
    {
        $response = $this->get('wx/address/list',$this->getAuthHeader());

        //返回的json数据
        // $response->getContent();

        // http状态码 正常为200
        $response->assertStatus(200);
        $response->assertJson(['code'=>0,'msg'=>'成功']);
        // 返回的原始数组数据
        $res = $response->getOriginalContent();
        dump($res);
    }

    /**
     * 删除
     */
    public function testDelete()
    {
        // 创建
        $data = [
            "name" => "1",
            "tel" => "15158040000",
            "province" => "北京市",
            "city" => "市辖区",
            "county" => "东城区",
            "areaCode" => "110101",
            "postalCode" => "",
            "addressDetail" => "1",
            "isDefault" => false
        ];
        $response = $this->post('wx/address/save', $data, $this->getAuthHeader());
        $response->assertJson(['code' => 0]);
        $id = $response->getOriginalContent()['data'] ?? 0;

        $response1 = $this->post('wx/address/delete', ['id' =>$id], $this->getAuthHeader());
        $response1->assertJson(['code' => 0]);

        $address = Address::query()->find($id);
        $this->assertEmpty($address);
    }

    /**
     * 创建、编辑
     */
    public function testSave()
    {
        // 创建
        $data = [
            "name" => "1",
            "tel" => "15158040000",
            "province" => "北京市",
            "city" => "市辖区",
            "county" => "东城区",
            "areaCode" => "110101",
            "postalCode" => "",
            "addressDetail" => "1",
            "isDefault" => false
        ];
        $response = $this->post('wx/address/save', $data, $this->getAuthHeader());
        $response->assertJson(['code' => 0]);

        // 编辑
        $id = $response->getOriginalContent()['data'] ?? 0;
        $data = [
            "id" => $id,
            "name" => "2",
            "tel" => "15158040001",
            "province" => "北京市",
            "city" => "市辖区",
            "county" => "东城区",
            "areaCode" => "110102",
            "postalCode" => "",
            "addressDetail" => "3",
            "isDefault" => true
        ];
        $response1 = $this->post('wx/address/save', $data, $this->getAuthHeader());
        $response1->assertJson(['code' => 0]);

        // 用户id、id查询地址数据
        $address = AddressService::getInstance()->getAddressByUserIdId($this->user->id, $id);
        // 从给定数组中获取项的子集
        $this->assertEquals($data, Arr::only($address->toArray(), array_keys($data)));
    }
}
