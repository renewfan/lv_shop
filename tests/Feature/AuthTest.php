<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

//所有测试类必须基于TestCase
class AuthTest extends TestCase
{
    // 开启数据库事务，提交的数据不会提交进入数据库，但是id会递增
    use DatabaseTransactions;

    // 所有测试方法命名，必须以 test 小写开头命名

    /**
     * 获取验证码 123456
     */
    public function testSms()
    {
        $data = [
            'mobile'=>'13445455454'
        ];
        $response = $this->post('wx/auth/regSms',$data);
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
     * 注册
     */
    public function testRegister()
    {
        $data = [
            'username'=>'a',
            'password'=>'123456',
            'mobile'=>'13445455454',
            'code'=>'123456'
        ];
        $response = $this->post('wx/auth/register',$data);
        //返回的json数据
        // $response->getContent();

        // http状态码 正常为200
        $response->assertStatus(200);
        // 返回的原始数组数据
        $res = $response->getOriginalContent();
        dump($res);
        // 状态码 0 成功
        $this->assertEquals(0,$res['code']);
        // 返回数据data 不为空
        $this->assertNotEmpty($res['data']);
    }

    /**
     * 手机号错误
     */
    public function testRegisterMobile()
    {
        $data = [
            'username'=>'a',
            'password'=>'123456',
            'mobile'=>'134454554541',
            'code'=>'123456'
        ];
        $response = $this->post('wx/auth/register',$data);
        //返回的json数据
        // $response->getContent();

        // http状态码 正常为200
        $response->assertStatus(200);
        // 返回的原始数组数据
        $res = $response->getOriginalContent();
        dump($res);
        // 状态码 0 成功
        $this->assertEquals(707,$res['code']);
    }

    /**
     * 验证码错误
     */
    public function testRegisterErrCode()
    {
        $data = [
            'username'=>'a',
            'password'=>'123456',
            'mobile'=>'13445455454',
            'code'=>'12'
        ];
        $response = $this->post('wx/auth/register',$data);
        $response->assertJson(['code'=>703,'msg'=>'验证码错误']);
    }
}
