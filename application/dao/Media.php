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
 * Media
 *
 * @package dao
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/14
 */
class Media extends Dao {

    /**
     * 获取文件管理列表
     * @param int $rows
     * @param int $start
     * @return array
     */
    public function manage($rows=20,$start=0,$search=null) {
        $this->left('post','post.id=media.parent');
        $this->field('media.*,post.title ptitle,post.id pid');
        $this->orderby('modified desc');

        if($search->title) {
            $this->driver->like('media.title',$search->title,'a');
        }

        return $this->paginate($rows,$start);
    }

    /**
     * 父类编辑页面使用
     */
    public function useList($type,$parent) {
        $dao = self::driver()->orderby('sort desc');
        $dao->where('(type=? and parent=?) or parent=0', [$type,$parent]);
        return $dao->fetchAll();
    }

    /**
     * 模版标签使用
     * @param $type
     * @param $id
     * @param string $status
     * @param string $order
     * @return mixed
     */
    public static function labels($type,$id,$status='publish',$order='sort desc') {
        $dao = self::driver()->orderby($order);
        $dao->where('status=? and type=? and parent=?', [$status,$type,$id]);
        return $dao->fetchAll();
    }


    /**
     * 将未归档文件分配到指定类型下
     *
     * @param $type
     * @param $id
     * @return int
     */
    public function archive($type,$id) {
        $this->where('parent=0');
        $rows = $this->update([
            'type'=>$type,
            'parent'=>$id
        ]);
        return $rows;
    }

}