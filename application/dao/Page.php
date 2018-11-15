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
 * Page
 *
 * @package dao
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/12
 */
class Page extends Dao {

    public static function labels($order='`order` desc') {
        $dao = self::driver()->where('status=?', 'publish');
        $dao->orderby($order);
        return $dao->fetchAll();
    }

    public function edit($data,$id=null) {
        $data['created'] = $data['created']?strtotime($data['created']):time();

        //存储钩子
        $data = \nb\Hook::pos('dao\Page')->edit($data);

        if($id) {
            $data['slug'] = $data['slug']?:$id;
            $this->updateId($id,$data);
        }
        else {
            $id = $this->insert($data);
            $data['slug'] or $this->updateId($id,['slug'=>$id]);
        }

        //归档文件
        $mDao = new Media();
        $mDao->archive('page',$id);

        return $id;
    }

}