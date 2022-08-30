<?php


namespace App\Http\Controllers\Wx\User;


use App\Exceptions\BusinessException;
use App\Http\Controllers\Wx\WxController;
use App\Models\User\User;
use App\ReturnCode;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends WxController
{
    // 中间件使用范围
    protected $only = ['info', 'profile'];

    /**
     * 注册
     * @param Request $request
     * @return JsonResponse
     * @throws BusinessException
     */
    public function register(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        $mobile   = $request->input('mobile');
        $code     = $request->input('code');

        if (empty($username) || empty($password) || empty($mobile) || empty($code)) {
            return $this->fail(ReturnCode::PARAM_ILLEGAL);
        }

        //用户名是否被注册
        $user = UserService::getInstance()->getByUsername($username);
        if (!is_null($user)) {
            return $this->fail(ReturnCode::AUTH_NAME_REGISTERED);
        }

        // 手机号格式
        $vaildator = Validator::make(['mobile' => $mobile], ['mobile' => 'regex:/^1[0-9]{10}$/']);
        if ($vaildator->fails()) {
            return $this->fail(ReturnCode::AUTH_INVALID_MOBILE);

        }
        // 手机号是否被注册
        $user = UserService::getInstance()->getByUsermobile($mobile);
        if (!is_null($user)) {
            return $this->fail(ReturnCode::AUTH_MOBILE_REGISTERED);
        }

        // 验证码是否正确
        UserService::getInstance()->mobileSmsCheck($mobile, $code);

        $new_user                  = new User();
        $new_user->username        = $username;
        $new_user->password        = Hash::make($password);
        $new_user->mobile          = $mobile;
        $new_user->nickname        = $username;
        $new_user->last_login_time = Carbon::now()->toDateString();
        $new_user->last_login_ip   = $request->getClientIp();
        $new_user->save();

        // 使用wx保护器生成token
        $token = Auth::guard('wx')->login($new_user);

        return $this->success([
            'token' => $token,
            'info'  => [
                'nickName' => $new_user->nickname,
                'avatarUrl' => $new_user->avatar
            ]
        ]);
    }

    /**
     * 短信
     * @param Request $request
     * @return JsonResponse
     */
    public function regSms(Request $request)
    {
        $mobile = $request->input('mobile');
        if (empty($mobile)) {
            return $this->fail(ReturnCode::PARAM_ILLEGAL);
        }

        // 手机号格式
        $vaildator = Validator::make(['mobile' => $mobile], ['mobile' => 'regex:/^1[0-9]{10}$/']);
        if ($vaildator->fails()) {
            return $this->fail(ReturnCode::AUTH_INVALID_MOBILE);
        }

        // 手机号是否被注册
        $user = UserService::getInstance()->getByUsermobile($mobile);
        if (!is_null($user)) {
            return $this->fail(ReturnCode::AUTH_MOBILE_REGISTERED);
        }

        // 防止一直请求
        // 1min一次
        // add 只存储缓存中不存在的数据。如果存储成功，将返回 true ，否则返回 false
        $code_lock = Cache::add('reg_sms_lock_' . $mobile, 1, 60);
        if (!$code_lock) {
            return $this->fail(ReturnCode::AUTH_CAPTCHA_FREQUENCY);
        }

        $res = UserService::getInstance()->smsCountCheck($mobile);
        if (!$res) {
            return $this->fail(ReturnCode::AUTH_CAPTCHA_FREQUENCY, '验证码1天10次');
        }

        // 验证码+手机关系存储
        // 生成验证码
        $get_code = UserService::getInstance()->setSmsCode($mobile);

        // 发送 -- https://github.com/overtrue/easy-sms
        if (!app()->environment('testing')) {
            // https://github.com/yl/easysms-notification-channel
            UserService::getInstance()->smsSend($mobile, $get_code);
        }

        return $this->success();
    }

    /**
     * 登录
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if (empty($username) || empty($password)) {
            return $this->fail(ReturnCode::PARAM_ILLEGAL);
        }

        // 用户是否存在
        $user = UserService::getInstance()->getByUsername($username);
        if (is_null($user)) {
            return $this->fail(ReturnCode::AUTH_INVALID_ACCOUNT);
        }

        // 密码是否正确
        $password_pass = Hash::check($password, $user->getAuthPassword());
        if (!$password_pass) {
            return $this->fail(ReturnCode::AUTH_INVALID_ACCOUNT, '密码不正确');
        }

        // 登录数据
        $user->last_login_time = now()->toDateTimeString();
        $user->last_login_ip   = $request->getClientIp();
        $update_res            = $user->save();
        if (!$update_res) {
            return $this->fail(ReturnCode::UPDATED_FAIL);
        }

        // 使用wx保护器生成token
        $token = Auth::guard('wx')->login($user);

        return $this->success([
            'token' => $token,
            'info'  => [
                'nickName' => $user->nickname,
                'avatarUrl' => $user->avatar
            ]
        ]);
    }

    /**
     * token获取用户信息
     * @return JsonResponse
     */
    public function info()
    {
        $user_info = Auth::guard()->user();
        return $this->success([
            'nickName' => $user_info->nickname,
            'avatar'   => $user_info->avatar,
            'gender'   => $user_info->gender,
            'mobile'   => $user_info->mobile
        ]);
    }

    /**
     * 登出
     * @return JsonResponse
     */
    public function logout()
    {
        Auth::guard()->logout();
        return $this->success();
    }

    /**
     * 重置密码
     * @param Request $request
     * @return JsonResponse
     * @throws BusinessException
     */
    public function reset(Request $request)
    {
        $password = $request->input('password');
        $mobile   = $request->input('mobile');
        $code     = $request->input('code');

        if (empty($password) || empty($mobile) || empty($code)) {
            return $this->fail(ReturnCode::PARAM_ILLEGAL);
        }

        // 手机号格式
        $vaildator = Validator::make(['mobile' => $mobile], ['mobile' => 'regex:/^1[0-9]{10}$/']);
        if ($vaildator->fails()) {
            return $this->fail(ReturnCode::AUTH_INVALID_MOBILE);

        }

        // 验证码是否正确
        UserService::getInstance()->mobileSmsCheck($mobile, $code);

        // 手机号是否未注册
        $user = UserService::getInstance()->getByUsermobile($mobile);
        if (is_null($user)) {
            return $this->fail(ReturnCode::AUTH_MOBILE_UNREGISTERED);
        }

        // 注册过的手机修改密码
        $user->password = Hash::make($password);
        $res            = $user->save();

        return $this->failOrsuccess($res, ReturnCode::UPDATED_FAIL);
    }

    /**
     * 修改个人信息
     * @param Request $request
     * @return JsonResponse
     */
    public function profile(Request $request)
    {
        $user_info = Auth::guard()->user();
        $nickname  = $request->input('nickname');
        $gender    = $request->input('gender');
        $avatar    = $request->input('avatar');

        if (!empty($nickname)) {
            $user_info->nickname = $nickname;
        }
        if (!empty($gender)) {
            $user_info->gender = $gender;
        }
        if (!empty($avatar)) {
            $user_info->avatar = $avatar;
        }
        $res = $user_info->save();
        return $this->failOrSuccess($res, ReturnCode::UPDATED_FAIL);
    }
}
