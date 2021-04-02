<?php

namespace addon\base;


class AuthedController extends BaseController
{
    public function initialize()
    {
        $this->checkLogin();
    }

}