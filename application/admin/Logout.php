<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace controller\admin;

use util\Admin;

/**
 * Logout
 *
 * @package controller\admin
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/11
 */
class Logout extends Admin {

    public function post() {
        $pass = password_hash('123456', PASSWORD_DEFAULT);
        e($pass);

        $result = password_verify('123456', $pass);
        if($result) {
            ed('yes');
        }
        ed('no');
    }

}