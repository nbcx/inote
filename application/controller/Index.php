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

use nb\Cookie;
use util\Controller;
/**
 * Index
 *
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/9
 */
class Index extends Controller {

    public function index($page=1) {
        $dao = new \dao\Post();
        list($total,$posts) = $dao->freshness(10,$page);

        //分页
        $navigator = new \util\PageNavigator($total,$page,20,'?page={page}');
        $this->assign('navigator',$navigator);

        $this->assign('total',$total);
        $this->assign('posts',$posts);
        $this->display('index');
    }

    public function archive($slug,$page=1) {
        $dao = new \dao\Post();
        list($total,$posts) = $dao->freshness(10,$page);

        //分页
        $navigator = new \util\PageNavigator($total,$page,20,'?page={page}');
        $this->assign('navigator',$navigator);

        $this->assign('total',$total);
        $this->assign('posts',$posts);
        $this->display('archive');
    }

    public function classify() {

    }


}