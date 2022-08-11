<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
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
            'iss'=>env('JWT_ISS'),
            'userId'=>$this->getKey()
        ];
    }
}
