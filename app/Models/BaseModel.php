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
        $items  = parent::toArray();
        $keys   = array_keys($items);
        $values = array_values($items);
        $keys   = array_map(function ($key) {
            return lcfirst(Str::studly($key));
        }, $keys);
        return array_combine($keys, $values);
    }
}
