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
 * Media
 *
 * @package controller\admin
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/9
 */
class Media extends Admin {

    public function index($page=1) {

        $dao = new \dao\Media();
        $search = $this->formx('post');
        list($total,$medias) = $dao->manage(20,$page,$search);
        $this->assign('total',$total);
        $this->assign('medias',$medias);

        //分页
        $navigator = new \util\PageNavigator($total,$page,20,'?page={page}');
        $this->assign('navigator',$navigator);

        $this->display('media');
    }

    public function write($id) {
        $dao = new \dao\Media();
        $media = $dao->findId($id);
        $this->assign('media',$media);
        $this->display('media-write');
    }


    public function del($id) {

    }


}