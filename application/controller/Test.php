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

use dao\Classify;
use deploy\Config;
use util\Controller;
use util\Tree;
use util\Ulist;
use util\Validate;

/**
 * Test
 *
 * @package controller
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/18
 */
class Test extends Controller {

    public $_validate = Validate::class;

    public $_rule = [
        'mail' => 'email',
    ];

    public function index() {
        echo Config::$o->option->title;
    }

    public function disk() {
        $df = floor(disk_free_space(__APP__)/disk_total_space(__APP__)*100);
        ed($df);
    }

    public function ulist() {
        $dao = new \dao\Classify();
        $mixTree = $dao->fetchs();

        $r = Tree::makeTree($mixTree,[
            'expanded' => true
        ]);
        ed($r);
    }



    public function index2() {
        $input = $this->input('name','email','mail');
        e($input);
    }

    public function c() {
        $test = new \ol\sblog\editor\Test();
        $test->index();
    }

    public function p() {
        \nb\Hook::pos('test')->index();
    }

    public function write() {
        $this->display('write');
    }

    public function profile() {
        $this->display('profile');
    }

}