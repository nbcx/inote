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
 * Comment
 *
 * @package controller\admin
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/9
 */
class Comment extends Admin {

    public function index($page=1) {
        $dao = new \dao\Comment();
        $search = $this->formx('post');
        list($total,$comments) = $dao->manage(20,$page,$search);
        $this->assign('total',$total);
        $this->assign('comments',$comments);

        //分页
        $navigator = new \util\PageNavigator($total,$page,20,'?page={page}');
        $this->assign('navigator',$navigator);

        $this->display('comment');
    }

    public function write($id) {
        $dao = new \dao\Comment();
        $comment = $dao->findId($id);

        $this->assign('comment',$comment);
        $this->display('comment-write');
    }

    public function reply($id) {
        $dao = new \dao\Comment();
        $comment = $dao->findId($id);

        $this->assign('comment',$comment);
        $this->display('comment-reply');
    }

    public function post($id=0,$parent=0) {

    }

    public function action() {

    }

}