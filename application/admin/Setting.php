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
use nb\Collection;
use util\Admin;

/**
 * Setting
 *
 * @package controller\admin
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/26
 */
class Setting extends Admin {

    public function index() {
        //用户配置
        $dao =  new \dao\Setting();
        $this->assign('setting',new Collection($dao->all()));

        $this->assign('action',$this->safe('/admin/setting/post'));
        $this->display('website');
    }


    public function post() {

    }
}