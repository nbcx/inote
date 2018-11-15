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
 * Page
 *
 * @package controller\admin
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/9
 */
class Page extends Admin {

    public function index() {
        $dao = new \dao\Page();
        $pages = $dao->fetchs();
        $this->assign('pages',$pages);
        $this->display('page');
    }

    /**
     * 显示页面编辑
     * @param int $id
     */
    public function write($id=0) {
        $action = '/admin/page/post';
        $data = [];
        if($id) {
            $dao = new \dao\Page();
            $data = $dao->findId($id);
            $action.='?id='.$id;
        }
        $action = $this->safe($action);

        //获取单页可用模版
        $stencils = stencil('page-*');
        $this->assign('stencils',$stencils);

        //文件
        $mDao = new \dao\Media();
        $medias = $mDao->useList('page',$id);
        $this->assign('medias',$medias);

        $this->assign('action',$action);
        $this->assign('page',new Collection($data));
        $this->display('page-write');
    }


    /**
     * 编辑和新增页面
     * @param int $id
     */
    public function post($id=0) {
        $this->protect();
        $data = $this->form('post',[
            'title','slug','text','template','status','sort','created',
            'allowComment','allowPing','allowFeed'
        ]);
        $dao = new \dao\Page();

        $id = $dao->edit($data,$id);

        $this->alert($data['title'].'页面编辑成功！<a href="'.url_page($id).'">前往查看</a>');

        redirect('/admin/page/index');
    }

    /**
     * 删除页面
     * @param $id
     */
    public function del($id) {
        $this->protect();

        $dao = new \dao\Page();

        $page = $dao->findId($id);

        $dao->deleteId($id);

        $this->back("成功删除页面[{$page['title']}]！");
    }
}