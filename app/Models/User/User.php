<?php

namespace App\Models\User;


// 授权验证
use App\Models\BaseModel;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

// 获取用户的唯一标识符名称、获取用户的唯一标识符、获取用户的密码、获取 "记住我 "会话的token、设置 "记住我 "会话的token、获取 "记住我 "会话的token名称
use Illuminate\Auth\Authenticatable;

// 认证
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

// 确定该实体是否具有给定的能力
use Illuminate\Foundation\Auth\Access\Authorizable;

// jwt
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    JWTSubject
{
    // token相关
    use Authenticatable, Authorizable;

    // 不指定表名将使用类的复数形式「蛇形命名」来作为表名
    protected $table = 'user';

    // 不返回password
    protected $hidden = [
        'password'
    ];

    const CREATED_AT = 'add_time';
    const UPDATED_AT = 'update_time';

    /**
     * 获取主键id 用来做jwt身份识别
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * 加入jwt中的自定义信息
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'iss'    => env('JWT_ISS'),
            'userId' => $this->getKey()
        ];
    }
}
