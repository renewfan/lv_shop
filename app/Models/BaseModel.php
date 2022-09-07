<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BaseModel extends Model
{
    /**
     * key转换为驼峰形式
     * @return array|false
     */
    public function toArray()
    {
        // 获取数据
        $items  = parent::toArray();
        $keys   = array_keys($items);
        $values = array_values($items);
        // key 转换
        $keys = array_map(function ($key) {
            // key转驼峰，再首字母小写
            // is_default --> IsDefault --> isDefault
            return lcfirst(Str::studly($key));
        }, $keys);
        // key-value 重组
        return array_combine($keys, $values);
    }
}
