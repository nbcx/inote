<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace controller;

use util\Controller;
use util\Security;

/**
 * Post
 *
 * @package controller
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/10
 */
class Post extends Controller {

    public function index($id=176) {
        $psot = new \dao\Post();
        $post = $psot->findId($id);
        $this->assign('post',$post);

        $this->assign('actionComment',$this->safe('/comment/post?type=post&parent='.$id));
        $this->display('post');
    }

    public function read() {
        $a = Security::url('comment');
        //$s = new Security();
        //$a = $s->url('comment');
        echo "<a href='{$a}'>{$a}</a>";

        //$this->display('post');
    }

}