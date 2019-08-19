<?php

function empty2null($arr, $keys, $islist = true)
{
    if (is_array($arr) && !empty($arr)) {
        if (!is_array($keys)) $keys = array_map('trim', explode(',', $keys));
        foreach ($arr as $k => $row) {
            if ($islist) {
                if (is_array($row)) {
                    foreach ($row as $key => $item) {
                        if (in_array($key, $keys) && empty($item)) {
                            $arr[$k][$key] = null;
                        }
                    }
                }
            } elseif (in_array($k, $keys)) {
                if (empty($row)) {
                    $arr[$k] = null;
                }
            }
        }
    }
    return $arr;
}
