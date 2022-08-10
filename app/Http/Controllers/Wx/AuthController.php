<?php


namespace App\Http\Controllers\Wx;


use App\Models\User;
use App\ReturnCode;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends WxController
{
    /*
     * 注册
     * */
    public function register(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        $mobile   = $request->input('mobile');
        $code     = $request->input('code');

        if (empty($username) || empty($password) || empty($mobile) || empty($code)) {
           return $this->Fail(ReturnCode::PARAM_ILLEGAL);
        }

        //用户名是否被注册
        $user = UserService::getInstance()->getByUsername($username);
        if (!is_null($user)) {
            return $this->Fail(ReturnCode::PARAM_VALUE_ILLEGAL);
        }

        // 手机号格式
        $vaildator = Validator::make(['mobile' => $mobile], ['mobile' => 'regex:/^1[0-9]{10}$/']);
        if ($vaildator->fails()) {
            return $this->Fail(ReturnCode::AUTH_INVALID_MOBILE);

        }
        // 手机号是否被注册
        $user = UserService::getInstance()->getByUsermobile($mobile);
        if (!is_null($user)) {
            return $this->Fail(ReturnCode::AUTH_MOBILE_REGISTERED);
        }

        // 验证码是否正确
        UserService::getInstance()->mobileSmsCheck($mobile, $code);

        $new_user                  = new User();
        $new_user->username        = $username;
        $new_user->password        = Hash::make($password);
        $new_user->mobile          = $mobile;
        $new_user->avatar          = 'xxx';
        $new_user->nickname        = $username;
        $new_user->last_login_time = Carbon::now()->toDateString();
        $new_user->last_login_ip   = $request->getClientIp();
        $new_user->save();

        return $this->Success([
            'token' => '',
            'info'  => [
                'username' => $username,
                'mobile'   => $mobile
            ]
        ]);
    }

    /*
     * 验证码
     * */
    public function regSms(Request $request)
    {
        $mobile = $request->input('mobile');
        if (empty($mobile)) {
            return $this->Fail(ReturnCode::PARAM_ILLEGAL);
        }

        // 手机号格式
        $vaildator = Validator::make(['mobile' => $mobile], ['mobile' => 'regex:/^1[0-9]{10}$/']);
        if ($vaildator->fails()) {
            return $this->Fail(ReturnCode::AUTH_INVALID_MOBILE);
        }

        // 手机号是否被注册
        $user = UserService::getInstance()->getByUsermobile($mobile);
        if (!is_null($user)) {
            return $this->Fail(ReturnCode::AUTH_MOBILE_REGISTERED);
        }

        // 防止一直请求
        // 1min一次
        $code_lock = Cache::add('reg_sms_lock_' . $mobile, 1);
        if (!$code_lock) {
            return $this->Fail(ReturnCode::AUTH_CAPTCHA_FREQUENCY);
        }

        $res = UserService::getInstance()->smsCountCheck($mobile);
        if (!$res) {
            return $this->Fail(ReturnCode::AUTH_CAPTCHA_FREQUENCY, '验证码1天10次');
        }

        // 验证码+手机关系存储
        // 生成验证码
        $get_code = UserService::getInstance()->setSmsCode($mobile);

        // 发送 -- https://github.com/overtrue/easy-sms
        // https://github.com/yl/easysms-notification-channel
        //UserService::getInstance()->smsSend($mobile, $get_code);

        return $this->Success();
    }

    /*
     * 登录
     * */
    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if (empty($username) || empty($password)) {
            return $this->Fail(ReturnCode::PARAM_ILLEGAL);
        }

        // 用户是否存在
        $user = UserService::getInstance()->getByUsername($username);
        if (!is_null($user)) {
            return $this->Fail(ReturnCode::AUTH_INVALID_ACCOUNT);
        }

        // 密码是否正确
        $password_pass = Hash::check($password,$user->getAuthPassword());
        if (!$password_pass) {
            return $this->Fail(ReturnCode::AUTH_INVALID_ACCOUNT,'密码不正确');
        }

        // 登录数据
        $user->last_login_time = now()->toDateTimeString();
        $user->last_login_ip   = $request->getClientIp();
        $update_res = $user->save();
        if (!$update_res) {
            return $this->Fail(ReturnCode::UPDATED_FAIL);
        }

        // 获取token
        $token = '';

        return $this->Success([
            'token' => $token,
            'info'  => [
                'username' => $username
            ]
        ]);
    }
}
