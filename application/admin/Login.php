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

use dao\User;
use nb\Config;
use nb\Cookie;
use util\Admin;
use util\Controller;
use util\Security;

/**
 * Login
 *
 * @package controller\admin
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/11
 */
class Login extends Admin {

    public function __before() {
        $this->assign('conf',Config::$o);
        return true;
    }

    /**
     * 显示登录页面
     */
    public function index() {
        $this->assign('action',$this->safe('/admin/login/post'));
        $this->display('login');
    }

    /**
     * 处理登录请求
     */
    public function post() {
        list($name,$password,$remember) = $this->input('post',[
            'name','password','remember'
        ]);

        $dao = new User();
        $user = $dao->find('name=?',$name);
        if(!$user) {
            $this->back('用户名不存在！');
        }

        if(!password_verify($password, $user['password'])) {
            $this->back('密码错误');
        }

        //生成登陆后的token
        $taken = md5(time().$password);

        //如果选自动登陆，则设置永不过期
        //否则随浏览器关闭而过期
        Cookie::set('_s',$taken);

        $dao->updateId($user['id'],['token'=>$taken]);
        redirect('/admin');
    }

}