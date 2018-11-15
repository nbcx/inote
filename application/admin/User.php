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
use util\Auth;

/**
 * User
 *
 * @package controller\admin
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/26
 */
class User extends Admin {

    public function index() {
        $this->assign('action',$this->safe('/admin/user/post'));
        $this->assign('updatepass',$this->safe('/admin/user/updatepass'));
        $this->display('profile');
    }

    /**
     * 修改个人信息
     */
    public function post($mail,$screenName=null) {
        $this->protect();
        $auth = Auth::init();
        $screenName or $screenName = $auth->user;
        $dao = new \dao\User();
        $dao->updateId($auth->id,[
            'screenName'=>$screenName,
            'mail'=>$mail
        ]);

        $this->back('个人信息修改成功！');
    }

    /**
     * 修改密码
     */
    public function updatepass($password,$confirm) {
        $this->protect();
        if($password != $confirm) {
            $this->back('两次密码不一致');
        }

        $dao = new \dao\User();
        $dao->updateId(Auth::init()->id,[
            'password'=>password_hash($password, PASSWORD_DEFAULT)
        ]);

        $this->back('密码修改成功');
    }

}