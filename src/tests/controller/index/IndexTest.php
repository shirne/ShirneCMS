<?php

namespace tests\controller\index;

use tests\TestCase;

class IndexTest extends TestCase
{
    public function testIndex()
    {

        $this->visit('/')->see('bootstrap');
    }
}
