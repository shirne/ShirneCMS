<?php

namespace shirne\common;

use NXP\MathExecutor;

class Notation
{
    /*
    params:
          $exp-普通表达式，例如 a+b*(c+d)
          $exp_values-表达式对应数据内容，例如 ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]
    */
    public static function calculate($exp, $exp_values = [])
    {

        $executor = new MathExecutor();

        foreach ($exp_values as $key => $value) {
            $executor->setVar($key, $value);
        }

        return $executor->execute($exp);
    }
}
