<?php


namespace App\Services;


use App\Exceptions\BussinessException;
use App\Models\User;
use App\Notifications\VerificationCode;
use App\ReturnCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Leonis\Notifications\EasySms\Channels\EasySmsChannel;
use Overtrue\EasySms\PhoneNumber;

class UserService extends BaseService
{
    /**
     * 用户名是否被使用
     * @param $username
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getByUsername($username){
        return User::query()->where('username',$username)->where('deleted',0)->first();
    }

    /**
     * 手机号是否被使用
     * @param $mobile
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getByUsermobile($mobile){
        return User::query()->where('mobile',$mobile)->where('deleted',0)->first();
    }

    /**
     * 验证码 1天10次
     * @param $mobile
     * @return bool
     */
    public function smsCountCheck($mobile){
        // 1天10次
        $code_count_key = 'reg_sms_count_' . $mobile;
        if (Cache::has($code_count_key)) {
            $code_count = Cache::increment($code_count_key);
            if ($code_count > 10) {
                return false;
            }
        } else {
            // 当前时间到第二天0点间隔
            Cache::put($code_count_key, 1, Carbon::tomorrow()->diff(now()));
        }
        return true;
    }

    /**
     * 验证码设置
     * @param $mobile
     * @return string
     * @throws \Exception
     */
    public function setSmsCode($mobile){
        // 验证码+手机关系存储
        // 生成验证码
        $key = 'reg_sms_' . $mobile;
        $get_code = random_int(100000, 999999);
        $get_code = strval($get_code); // 转字符串类型
        Cache::put($key, $get_code, 600);
        return $get_code;
    }

    /**
     * 验证码发送
     * @param $mobile
     * @param $get_code
     */
    public function smsSend($mobile, $get_code){
        if (app()->environment('testing')) {
            return;
        }

        Notification::route(
            EasySmsChannel::class,
            new PhoneNumber($mobile, 86)
        )->notify(new VerificationCode($get_code));
    }

    /**
     * 验证码验证
     * @param $mobile
     * @param $get_code
     * @return bool
     * @throws BussinessException
     */
    public function mobileSmsCheck($mobile, $get_code){
        $key = 'reg_sms_' . $mobile;
        $mobile_code = Cache::get($key);
        $res = $mobile_code===$get_code;
        if ($res) {
            Cache::forget($key);
            return true;
        } else {
            throw new BussinessException(ReturnCode::AUTH_CAPTCHA_UNMATCH);
        }
    }
}
