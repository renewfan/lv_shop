<?php

namespace Tests\Feature;

use App\Services\User\UserService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BasicTestCase;

//所有测试类必须基于TestCase
class AuthTest extends BasicTestCase
{
    // 开启数据库事务，提交的数据不会提交进入数据库，但是id会递增
    use DatabaseTransactions;

    // 所有测试方法命名，必须以 test 小写开头命名

    /**
     * 获取验证码 123456
     */
    public function testSms()
    {
        $data     = [
            'mobile' => '13445455454'
        ];
        $response = $this->post('wx/auth/regSms', $data);
        //返回的json数据
        // $response->getContent();

        // http状态码 正常为200
        $response->assertStatus(200);
        $response->assertJson(['code' => 0, 'msg' => '成功']);
        // 返回的原始数组数据
        $res = $response->getOriginalContent();
        dump($res);
    }

    /**
     * 注册
     */
//    public function testRegister()
//    {
//        $data     = [
//            'username' => 'a',
//            'password' => '123456',
//            'mobile'   => '13445455454',
//            'code'     => '123456'
//        ];
//        $response = $this->post('wx/auth/register', $data);
//        //返回的json数据
//        // $response->getContent();
//
//        // http状态码 正常为200
//        $response->assertStatus(200);
//        // 返回的原始数组数据
//        $res = $response->getOriginalContent();
//        dump($res);
//        // 状态码 0 成功
//        $this->assertEquals(0, $res['code']);
//        // 返回数据data 不为空
//        $this->assertNotEmpty($res['data']);
//    }

    /**
     * 手机号错误
     */
    public function testRegisterMobile()
    {
        $data     = [
            'username' => 'a',
            'password' => '123456',
            'mobile'   => '134454554541',
            'code'     => '123456'
        ];
        $response = $this->post('wx/auth/register', $data);
        //返回的json数据
        // $response->getContent();

        // http状态码 正常为200
        $response->assertStatus(200);
        // 返回的原始数组数据
        $res = $response->getOriginalContent();
        dump($res);
        // 状态码 0 成功
        $this->assertEquals(707, $res['code']);
    }

    /**
     * 验证码错误
     */
    public function testRegisterErrCode()
    {
        $data     = [
            'username' => 'a',
            'password' => '123456',
            'mobile'   => '13445455454',
            'code'     => '12'
        ];
        $response = $this->post('wx/auth/register', $data);
        $response->assertJson(['code' => 703, 'msg' => '验证码错误']);
    }

    /**
     * 登录
     */
    public function testLogin()
    {
        $data = [
            'username' => 'root',
            'password' => 'root'
        ];

        $response = $this->post('wx/auth/login', $data);

        echo $response->getOriginalContent()['data']['token'] ?? '';
        $this->assertNotEmpty($response->getOriginalContent()['data']['token'] ?? '');
    }

    /**
     * token获取用户信息
     */
    public function testInfo()
    {
        $response2    = $this->get('wx/auth/info', $this->getAuthHeader());
        $user         = UserService::getInstance()->getByUsername('root');
        $response2->assertJson([
            'data' => [
                'nickName' => $user->nickname,
                'avatar'   => $user->avatar,
                'mobile'   => $user->mobile
            ]
        ]);
    }

    /**
     * 退出
     */
    public function testLogout()
    {
        $response2    = $this->get('wx/auth/info', $this->getAuthHeader());
        $user         = UserService::getInstance()->getByUsername('user123');
        $response2->assertJson([
            'data' => [
                'nickName' => $user->nickname,
                'avatar'   => $user->avatar,
                'mobile'   => $user->mobile
            ]
        ]);

        $response3      = $this->post('wx/auth/logout', [], $this->getAuthHeader());
        $response3->assertJson(['errno' => 0]);


        $response4 = $this->get('wx/auth/info', $this->getAuthHeader());
        $response4->assertJson(['errno' => 501]);
    }

    /**
     * 重置密码
     */
    public function testRest()
    {
        $mobile = '15100000000';
        $code   = UserService::getInstance()->setSmsCode($mobile);

        $reset_data = [
            'mobile'   => $mobile,
            'password' => 'user1234',
            'code'     => $code
        ];
        $response   = $this->post('wx/auth/reset', $reset_data);
        $response->assertJson(['errno' => 0]);

        $user   = UserServices::getInstance()->getByMobile($mobile);
        $isPass = Hash::check('user1234', $user->password);
        $this->assertTrue($isPass);
    }

    /**
     * 修改个人信息
     */
    public function testProfile()
    {
        $profile_data    = [
            'avatar'   => '',
            'gender'   => 1,
            'nickname' => 'user1234'
        ];

        $response        = $this->post('wx/auth/profile', $profile_data, $this->getAuthHeader());
        $response->assertJson(['errno' => 0]);

        $user = UserServices::getInstance()->getByUsername('user123');
        $this->assertEquals('user1234', $user->nickname);
        $this->assertEquals(1, $user->gender);
    }
}
