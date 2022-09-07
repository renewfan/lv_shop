<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 全部分类
     */
    public function testIndex()
    {
        $response = $this->get('wx/catalog/index');
        //返回的json数据
        // $response->getContent();

        // http状态码 正常为200
        $response->assertStatus(200);
        // 返回的原始数组数据
        $res = $response->getOriginalContent();
        dump($res);
        dump('--------------------------------------');

        $id = 1005000;
        $response1 = $this->get('wx/catalog/index?id='.$id);

        //返回的json数据
        // $response1->getContent();

        // http状态码 正常为200
        $response1->assertStatus(200);
        // 返回的原始数组数据
        $res1 = $response1->getOriginalContent();
        dump($res1);
    }

    /**
     * 当前分类
     */
    public function testCurrent()
    {
        $id = 1005000;
        $response1 = $this->get('wx/catalog/current?id='.$id);

        //返回的json数据
        // $response1->getContent();

        // http状态码 正常为200
        $response1->assertStatus(200);
        // 返回的原始数组数据
        $res1 = $response1->getOriginalContent();
        dump($res1);
    }
}
