<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace util;

use dao\User;
use nb\Collection;
use nb\Pool;

/**
 * 登陆用户信息
 *
 * @package util
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/11
 */
class Auth extends Collection {

    public static function init() {
        if($user = Pool::get(get_class())) {
            return $user;
        }
        $token = \nb\Cookie::get('_s');//session('_user');
        $user = [];

        if($token) {
            $dao = new User();
            $user = $dao->findToken($token);
            $user or \nb\Cookie::delete('_s');
        }

        $user = new self($user);
        return Pool::set(get_class(),$user);
    }

    /**
     * 是否登陆
     * @return bool
     */
    public function _islogin() {
        return (boolean)$this->id;
    }

    /**
     * 是否未登陆
     * @return bool
     */
    public function _islogout() {
        return !$this->id;
    }

}