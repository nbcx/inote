<?php

/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace util;
use dao\Classify;
use nb\Collection;

/**
 * Tree
 *
 * @package util
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/25
 */
class Ulist extends Collection {

    private $tree;

    private $config = [
        'wrapTag' => 'ul',
        'wrapClass' => '',
        'itemTag' => 'li',
        'itemClass' => '',
        'showCount' => false,
        'showFeed' => false,
        'countTemplate' => '(%d)',
        'feedTemplate' => '<a href="%s">RSS</a>'
    ];

    public function __construct($data=[],$config=[]) {
        $nodes = [];
        foreach($data as $v){
            if($v['parent']==0){
                $nodes[0][]=$v;
            }
            else{
                $nodes[$v['parent']][]=$v;
            }
        }
        $this->tree = $nodes;
        $this->tmp = array_merge($this->config,$config);
    }

    public static function show($data,$option=[]) {
        $tree = new self($data,$option);
        $tree->display();
    }

    protected function _wrapStart() {
        return '<' . $this->wrapTag . (empty($this->wrapClass) ? '' : ' class="' . $this->wrapClass . '"') . '>';
    }

    protected function _wrapEnd() {
        return '</' . $this->wrapTag . '>';
    }

    protected function _itemStart() {
        return '<' . $this->itemTag . (empty($this->itemClass) ? '' : ' class="' . $this->itemClass . '"') . '>';
    }

    protected function _itemEnd() {
        return '</' . $this->itemTag . '>';
    }

    protected function _childWrapStart() {

    }

    protected function _childItemStart() {

    }



    public function display() {
        $treeDatas = $this->tree;
        echo $this->wrapStart;
        foreach($treeDatas[0] as $nodeDatas){
            echo $this->itemStart;
            echo '<a href="#">'.$nodeDatas['name'].'</a>';
            $this->traverSesing($nodeDatas, $treeDatas);
            echo $this->itemEnd;
        }
        echo $this->wrapEnd;
    }

    /*
     * 遍历打印其父结点及其子结点
     */
    function traverSesing($nodeDatas,$treeDatas){
        if(!empty($treeDatas[$nodeDatas['id']])&& is_array($treeDatas[$nodeDatas['id']])) {
            echo $this->wrapStart;
            foreach($treeDatas[$nodeDatas['id']] as $childNodeDatas){
                echo $this->itemStart;
                echo '<a href="#">'.$nodeDatas['name'].'</a>';
                $this->traverSesing($childNodeDatas, $treeDatas);
                echo $this->itemEnd;
            }
            echo $this->wrapEnd;
        }

    }

}