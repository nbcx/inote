<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace dao;

use nb\Dao;

/**
 * Comment
 *
 * @package dao
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/14
 */
class Comment extends Dao {

    public function manage($rows=20,$start=0,$search=null) {
        //$this->left('post','post.id=media.parent');
        //$this->field('media.*,post.title ptitle,post.id pid');
        $this->orderby('created desc');

        if($search->title) {
            $this->driver->like('media.title',$search->title,'a');
        }

        return $this->paginate($rows,$start);
    }

    /**
     * 获取最新的10条评论，在后台首页展示
     * @return mixed
     */
    public function recent() {
        $this->orderby('id desc')->limit(10);
        return $this->fetchs();
    }

    /**
     * 获取指定类型的评论,以树形式返回，包括总条数
     * @param $type
     * @param $parent
     * @param int $rows
     * @param int $start
     * @return [n,tree]
     */
    public function tree($type,$parent,$rows = 0, $start = 0) {
        $this->where('type=? and parent=? and reply=0',[$type,$parent]);
        list($n,$tree) = $this->paginate($rows,$start);
        foreach ($tree as &$v) {
            $v['count'] and  $v['child'] = $this->reply($v['id']);
        }
        return [$n,$tree];
    }

    public function reply($reply) {
        $this->where('reply=?',$reply);
    }

    public static function labels($order='`order` desc') {
        $dao = self::driver();//->where();
        //$dao->orderby($order);
        return Tree::makeTree($dao->fetchAll());
    }

}