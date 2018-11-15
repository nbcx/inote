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

use nb\Request;
use util\Controller;
use util\Security;

/**
 * Comment
 *
 * @package controller\admin
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/9
 */
class Comment extends Controller {

    public function post($type,$parent,$reply=0) {
        $data = $this->form('post',['author','mail','url','text']);

        $request = Request::driver();
        $data['type'] = $type;
        $data['parent'] = $parent;
        $data['agent'] =$request->agent;
        $data['ip'] =$request->ip;
        $data['created'] =time();
        $data['reply'] = $reply;

        $dao =new \dao\Comment();
        $dao->insert($data);

        $url = 'url_'.$type;
        redirect($url($parent));
    }

    /**
     * 评论的评论
     * @param $id
     */
    public function reply($id) {
        //检查要评论对象的状态
        $data = $this->form('post',['author','mail','url','text']);

        $dao =new \dao\Comment();
        $parent = $dao->findId($id);

        $request = Request::driver();
        $data['type'] = $parent['type'];
        $data['parent'] = $parent['id'];
        $data['agent'] =$request->agent;
        $data['ip'] =$request->ip;
        $data['created'] =time();
        $data['reply'] = $id;

        $id = $dao->insert($data);
    }

}