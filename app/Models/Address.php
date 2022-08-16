<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    // 不指定表名将使用类的复数形式「蛇形命名」来作为表名
    protected $table = 'address';

    const CREATED_AT = 'add_time';
    const UPDATED_AT = 'update_time';

    // 属性类型转换
    protected $casts = [
        'deleted'=>'boolean',
        'is_default'=>'boolean'
    ];
}
