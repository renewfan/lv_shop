<?php

namespace Tests\Unit;


use App\Exceptions\BusinessException;
use App\ReturnCode;
use App\Services\User\UserService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * 验证码一天10次验证
     */
    public function testSmsCountCheck()
    {
        $mobile = '13445455454';

        foreach (range(0,9) as $i) {
            $res = UserService::getInstance()->smsCountCheck($mobile);
            $this->assertTrue($res);
        }

        // 11
        $res = UserService::getInstance()->smsCountCheck($mobile);
        $this->assertFalse($res);
    }

    /**
     * 验证码生成+填写验证
     */
    public function testSmsCheck()
    {
        $mobile = '13445455454';

        $code = UserService::getInstance()->setSmsCode($mobile);
        $res = UserService::getInstance()->mobileSmsCheck($mobile,$code);

        $this->assertTrue($res);
    }

    /**
     * 验证码生成+填写验证 异常验证
     */
    public function testSmsCheckException()
    {
        $mobile = '13445455454';

        // 运行前进行异常声明
        //        $this->expectException(BusinessException::class);
        //        $this->expectExceptionCode(123);
        //        $this->expectExceptionCode(703);
        $this->expectExceptionObject(new BusinessException(ReturnCode::AUTH_CAPTCHA_UNMATCH));
        UserService::getInstance()->mobileSmsCheck($mobile,111);
    }
}
