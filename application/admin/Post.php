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
 * Post
 *
 * @package controller\admin
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/9
 */
class Post extends Admin {

    public function index($page=1) {

        //获取分类树
        $classDao = new \dao\Classify();
        $classifies = $classDao->tree();
        $this->assign('classifies',$classifies);

        $dao = new \dao\Post();
        $search = $this->formx('post');
        list($total,$posts) = $dao->manage(20,$page,$search);
        $this->assign('total',$total);
        $this->assign('posts',$posts);

        //分页
        $navigator = new \util\PageNavigator($total,$page,20,'?page={page}');
        $this->assign('navigator',$navigator);

        $this->display('post');
    }

    public function write($id=0) {
        //文章分类
        $classDao = new \dao\Classify();
        $classifies = $classDao->tree();
        $this->assign('classifies',$classifies);

        //安全链接和文章详情
        $action = '/admin/post/post';
        $data = [];
        if($id) {
            $dao = new \dao\Post();
            $data = $dao->findId($id);
            $action.='?id='.$id;
        }
        $action = $this->safe($action);

        //文件
        $mDao = new \dao\Media();
        $medias = $mDao->useList('post',$id);
        $this->assign('medias',$medias);

        $this->assign('action',$action);
        $this->assign('post',new Collection($data));
        $this->display('post-write');
    }

    public function post($id=0) {
        $this->protect();
        $data = $this->form('post',[
            'title','slug','text','status','created','cid',
            'allowComment','allowPing','allowFeed'
        ]);

        $dao = new \dao\Post();
        $id = $dao->edit($data,$id);

        $this->alert('文章 '.$data['title'].' 编辑成功！<a href="'.url_post($id).'">前往查看</a>');

        redirect('/admin/post/index');
    }

    public function del($ids=0) {
        if(!$ids) {
            $this->back('你必须选择勾选一个以上要删除的文章才能执行此操作');
        }
        if(!is_array($ids)) {
            $ids = [$ids];
        }
        $dao = new \dao\Post();
        $rows = $dao->driver->in('id',$ids)->delete();
        $this->back('已经成功删除了'.$rows.'篇文章！');
    }

}