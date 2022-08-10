<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    // 不指定表名将使用类的复数形式「蛇形命名」来作为表名
    protected $table = 'user';

    const CREATED_AT = 'add_time';
    const UPDATED_AT = 'update_time';
}
