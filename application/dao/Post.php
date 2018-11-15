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
 * Post
 *
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/10
 */
class Post extends Dao {

    /**
     * 按照文章新鲜度获取列表
     * @param int $rows
     * @param int $start
     * @return array
     */
    public function freshness($rows=20,$start=0) {
        $this->orderby('created desc');
        return $this->paginate($rows,$start);
    }


    /**
     * 获取最新的10条文章，在后台首页展示
     * @return mixed
     */
    public function recent() {
        $this->orderby('id desc')->limit(10);
        return $this->fetchs();
    }


    /**
     * @param int $rows
     * @param int $start
     * @return array
     */
    public function manage($rows=20,$start=0) {
        $this->orderby('id desc');
        $this->driver->left('classify c','c.id=post.cid');
        $this->field('post.*,c.name cname,c.id cid');
        return $this->paginate($rows,$start);
    }

    public function edit($data,$id=null) {
        $data['created'] = $data['created']?strtotime($data['created']):time();

        //存储钩子
        $data = \nb\Hook::pos('dao\Post')->edit($data);


        $id = $id?$this->alter($id,$data):$this->add($data);

        //归档文件
        $mDao = new Media();
        $mDao->archive('post',$id);

        return $id;
    }

    public function alter($id,$data) {
        $post = $this->findId($id);
        $data['slug'] = $data['slug']?:$id;
        $this->updateId($id,$data);
        if($post['cid'] == $data['cid']) {
            return $id;
        }

        $classDao = new Classify();
        $classDao->updateId($data['cid'],'count=count+1');
        $classDao->updateId($post['cid'],'count=count-1');

        return $id;
    }

    public function add($data) {
        $id = $this->insert($data);
        $data['slug'] or $this->updateId($id,['slug'=>$id]);
        $classDao = new Classify();
        $classDao->updateId($data['cid'],'count=count+1');

        return $id;
    }

    /**
     * 判断slug是否存在
     */
    public function exitslug() {

    }

}