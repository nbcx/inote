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
use util\Sys;

/**
 * Index
 *
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/9
 */
class Index extends Admin {

    public function index() {
        $postDao = new \dao\Post();
        $posts = $postDao->recent();
        $this->assign('posts',$posts);

        $cDao = new \dao\Comment();
        $comments = $cDao->recent();
        $this->assign('comments',$comments);


        $this->assign('sys',new Sys());
        $this->display('index');
    }

    public function test() {
        $this->display('test');
    }

}