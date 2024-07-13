<?php

namespace tests\validate\admin;

use app\admin\validate\CategoryValidate;
use tests\TestCase;

class CategoryTest extends TestCase
{
    public function testIndex()
    {

        $validate = new CategoryValidate();
        $validate->setId(0);
        $this->assertFalse($validate->check([
            'title' => '新闻中心',
            'short' => '新闻',
        ]), 'name 必填');
        $this->assertTrue($validate->check([
            'title' => '新闻中心',
            'short' => '新闻',
            'name' => 'news1'
        ]), 'name 字母开头+数字');
        $this->assertFalse($validate->check([
            'title' => '新闻中心',
            'short' => '新闻',
            'name' => '5news'
        ]), 'name 不能以数字开头');
        $this->assertFalse($validate->check([
            'title' => '新闻中心',
            'short' => '新闻',
            'name' => '新news'
        ]), 'name 不允许中文');
    }
}
