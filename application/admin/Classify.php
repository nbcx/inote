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
 * Classify
 *
 * @package controller\admin
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/9
 */
class Classify extends Admin {

    /**
     * 显示分类管理列表
     * @param int $parent
     */
    public function index($parent=0) {
        $classifies = new \dao\Classify();
        $classifies = $classifies->fetchs('parent=?',$parent);
        $this->assign('classifies',$classifies);
        $this->assign('parent',$parent);
        $this->display('classify');
    }

    /**
     * 显示分类新增和修改页面
     * @param int $parent
     * @param int $id
     */
    public function write($parent=0,$id=0) {
        $dao = new \dao\Classify();
        //ed($dao->xtree2());
        $classifies = $dao->tree();

        //安全链接和修改分类详细信息
        $action = '/admin/classify/post';
        $classify = [];
        if($id) {
            $action.'?id='.$id;
            $classify = $dao->findId($id);
        }
        $classify = new Collection($classify);

        //父分类
        $parent = $dao->findId($parent?:$classify->parent);

        $this->assign('action', $this->safe($action));
        $this->assign('parent', $parent);
        $this->assign('classify',$classify);
        $this->assign('classifies',$classifies);
        $this->display('classify-write');
    }

    /**
     * 处理分类修改和新增请求
     * @param int $id
     */
    public function post($id=0) {

        $this->protect();

        $data = $this->form('post',[
            'name','slug','parent','description'
        ]);
        $dao = new \dao\Classify();

        if($dao->exists('slug=?',$data['slug'])) {
            //ed('缩略名已经存在');
            $this->back('缩略名已经存在');
        }
        $classify = $dao->edit($data,$id);
        $preUrl = url_classify($classify['slug']);

        $this->look($preUrl,$data['name']);
        redirect('/admin/classify/index?parent='.$classify['parent']);
    }

    /**
     * 处理删除分类请求
     * @param $id
     */
    public function del($id) {
        $this->protect();

        $dao = new \dao\Classify();
        $classify = $dao->findId($id);

        $dao->deleteId($id);

        $classify['parent'] and $dao->updateId($classify['parent'],'lowernum=lowernum-1');

        $this->back("已经删除[{$classify['name']}]分类！");
    }
}