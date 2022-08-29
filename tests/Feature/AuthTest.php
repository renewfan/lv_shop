<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function testRegister()
    {
        // 开启数据库事务，提交的数据不会提交进入数据库，但是id会递增
        //use DatabaseTransactions;
        $data = [
            'username'=>'a',
            'password'=>'123456',
            'mobile'=>'13445455454',
            'code'=>'123456',
        ];
        $response = $this->post('wx/auth/register',$data);
        echo $response->getContent();
        // http状态码 正常为200
        $response->assertStatus(200);
        // 返回的json数据
        $res = $response->getOriginalContent();
        // 状态码 0 成功
        $this->assertEquals(0,$res['code']);
        // 返回数据data 不为空
        $this->assertEmpty($res['data']);
    }
}
