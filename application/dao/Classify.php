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
use util\Tree;

/**
 * Classify
 *
 * @package dao
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/10
 */
class Classify extends Dao {

    public function tree($parent=0,$level='') {
        $classify = $this->fetchs('parent=?',$parent);
        $data = [];
        foreach ($classify as $v) {
            $v['name'] = $level.$v['name'];
            $data[] = $v;
            if($v['nchild']) {
                $data = array_merge($data,$this->tree($v['id'],$level.'--'));
            }
        }
        return $data;

    }
    public function tree2($tree=null,$level='') {
        $tree or $tree = $this->xtree();
        $data = [];
        foreach ($tree as $v) {
            $v['name'] = $level.$v['name'];
            if(isset($v['child'])) {
                $child = $v['child'];
                unset($v['child']);
                $data[] = $v;
                $data = array_merge($data,$this->tree($child,$level.'--'));
            }
            else {
                $data[] = $v;
            }
        }
        return $data;
    }

    public function xtree() {
        $this->orderby('parent desc,sort asc');
        $all = $this->fetchs();
        $temp = [];
        $xtree = [];
        foreach ($all as $k => $v) {
            if( isset($temp[$v['id']]) ) {
                $v['child'] = $temp[$v['id']];
                unset($temp[$v['id']]);
            }
            if($v['parent']) {
                $temp[$v['parent']][] = $v;
            }
            else {
                $xtree[] = $v;
            }
            unset($all[$k]);
        }
        return $xtree;
    }

    public static function labels($order='`order` desc') {
        $dao = self::driver();//->where();
        //$dao->orderby($order);
        return Tree::makeTree($dao->fetchAll());
    }


    public function edit($data,$id=null) {
        if($id) {
            $classify = $this->findId($id);
            $data['slug'] = $data['slug']?:$id;
            $this->updateId($id,$data);
            if($classify['parent'] != $data['parent']) {
                $this->updateId($classify['parent'],'nchild=nchild-1');
                $this->updateId($data['parent'],'nchild=nchild+1');
            }
        }
        else {
            $data['id'] = $this->insert($data);
            $data['slug'] or $this->updateId($id,['slug'=>$id]);
            $data['parent'] and $this->updateId($data['parent'],'nchild=nchild+1');
        }
        return $data;
    }

}