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

use nb\Config;
use nb\view\tag\Driver;

class Label extends Driver {

    /**
     * 定义标签列表
     */
    protected $tags = [
        //独立页面
        'pages'         => ['attr' => 'do,sort,return', 'close' => 1],
        'page'          => ['attr' => 'do,num,page,sort,return', 'close' => 1],

        //独立页面
        'classifyx'         => ['attr' => 'do,sort,return', 'close' => 1],
        'classify'          => ['attr' => 'do,num,page,sort,return', 'close' => 1],

        //文件
        'medias'         => ['attr' => 'do,sort,return', 'close' => 1],
        'media'          => ['attr' => 'do,num,page,sort,return', 'close' => 1],

        'plugins'      => ['attr' => 'name,func,args,id', 'close' => 1],
        'plugin'       => ['attr' => 'name,func,args,return', 'close' => 1],

        //评论
        'comments'     => ['attr' => 'do,num,page,sort,return,tid', 'close' => 1],
        'comment'      => ['attr' => 'do,num,page,sort,return,tid', 'close' => 1],

        //业务标签
        'navigation'   => ['attr' => 'name,type', 'close' => 1],

        //评论
        'tree'     => ['attr' => 'tt', 'close' => 1],
        'twig'      => ['attr' => 'tt', 'close' => 1],
    ];

    public function tagTree($tag, $content) {
        $method = $this->_media($tag);
        return $this->_($tag,$content,$method);
    }

    public function tagTwig($tag, $content) {
        $method = $this->_media($tag);
        return $this->_s($tag,$content,$method);
    }


    public function tagMedia($tag, $content) {
        $method = $this->_media($tag);
        return $this->_($tag,$content,$method);
    }

    public function tagMediax($tag, $content) {
        $method = $this->_media($tag);
        return $this->_s($tag,$content,$method);
    }

    /**
     * do     执行的动作
     * num    返回的数量
     * page   第几页
     * sort   排序
     * return 接受返回值的变量名字
     * @param $tag
     * @return string
     * @throws \Exception
     */
    public function _media($tag) {
        $do = isset($tag['do'])?$tag['do']:'id';
        switch ($do) {
            case 'id':
                $id = $tag['id'];
                $method = "\\dao\\Media::findId({$id})";
                break;
            case 'list':
                $num = isset($tag['num'])?$tag['num']:'10';
                $page = isset($tag['page'])?$tag['page']:'1';//id desc
                $sort = isset($tag['sort'])?$tag['sort']:'order desc';
                $type = isset($tag['type'])?$tag['type']:'post';
                $parent = $tag['parent'];

                $method = "\\dao\\Media::labels({$type},{parent},{$num},{$page},'{$sort}')";
                break;
        }
        return $method;
    }


    public function tagPage($tag, $content) {
        $method = $this->_page($tag);
        return $this->_($tag,$content,$method);
    }

    public function tagPages($tag, $content) {
        $method = $this->_page($tag);
        return $this->_s($tag,$content,$method);
    }

    /**
     * do     执行的动作
     * num    返回的数量
     * page   第几页
     * sort   排序
     * return 接受返回值的变量名字
     * @param $tag
     * @return string
     * @throws \Exception
     */
    public function _page($tag) {
        $do = isset($tag['do'])?$tag['do']:'id';
        switch ($do) {
            case 'id':
                $id = $tag['id'];
                $method = "\\dao\\Page::findId({$id})";
                break;
            case 'list':
                $num = isset($tag['num'])?$tag['num']:'10';
                $page = isset($tag['page'])?$tag['page']:'1';//id desc
                $sort = isset($tag['sort'])?$tag['sort']:'order desc';
                $method = "\\dao\\Page::labels({$num},{$page},'{$sort}')";
                break;
        }
        return $method;
    }



    public function tagClassify($tag, $content) {
        $method = $this->_classify($tag);
        return $this->_($tag,$content,$method);
    }

    public function tagClassifyx($tag, $content) {
        $method = $this->_classify($tag);
        return $this->_s($tag,$content,$method);
    }

    /**
     * do     执行的动作
     * num    返回的数量
     * page   第几页
     * sort   排序
     * return 接受返回值的变量名字
     * @param $tag
     * @return string
     * @throws \Exception
     */
    public function _classify($tag) {
        $do = isset($tag['do'])?$tag['do']:'id';
        switch ($do) {
            case 'id':
                $id = $tag['id'];
                $method = "\\dao\\Page::findId({$id})";
                break;
            case 'list':
                $num = isset($tag['num'])?$tag['num']:'10';
                $page = isset($tag['page'])?$tag['page']:'1';//id desc
                $sort = isset($tag['sort'])?$tag['sort']:'order desc';
                $method = "\\dao\\Classify::labels({$num},{$page},'{$sort}')";
                break;
        }
        return $method;
    }

    /**
     * 执行插件里定义的方法
     * @param $tag
     *          name 插件名字
     *          func 插件方法
     *          args 插件方法参数
     *          return 接受返回值的变量名字
     * @param $content
     * @return string
     */
    public function tagPlugin($tag, $content) {
        if(isset($tag['name'])) {
            $name = $tag['name'];
        }
        else {
            throw new \Exception('tag plugins must have attr name');
        }
        $func = isset($tag['func'])?$tag['func']:'func';
        $args = isset($tag['args'])?$tag['args']:'';
        $return = isset($tag['return'])?$tag['return']:'data';
        $label = Plugin::parseInfo($name)->label;
        if(!$label) {
            return '';
        }
        if($label) {
            $method = "\\{$label}::{$func}({$args})";
        }
        else {
            $method = "\\plugins\\{$name}\\Label::{$func}({$args})";
        }
        $parse = '<?php ';
        $parse .= '$'.$return.' = '.$method.';';
        $parse .= ' ?>';
        $parse .= $content;
        return $parse;
    }

    /**
     * 执行插件里定义的方法，并对结果做volist处理
     * @param $tag
     *          name 插件名字
     *          func 插件方法
     *          args 插件方法参数
     *          id   作为volist的id参数
     * @param $content
     * @return string
     */
    public function tagPlugins($tag, $content) {
        if(isset($tag['name'])) {
            $name = $tag['name'];
        }
        else {
            throw new \Exception('tag plugins must have attr name');
        }
        $func = isset($tag['func'])?$tag['func']:'func';
        $args = isset($tag['args'])?$tag['args']:'';
        $id   = isset($tag['id'])?$tag['id']:'v';
        $label = Plugin::parseInfo($name)->label;
        if(!$label) {
            return '';
        }
        if($label) {
            $method = "\\{$label}::{$func}({$args})";
        }
        else {
            $method = "\\plugins\\{$name}\\Label::{$func}({$args})";
        }
        $parse = '<?php ';
        $parse .= '$__LIST__ = '.$method.';';
        $parse .= ' ?>';
        $parse .= '<volist name="__LIST__" id="' . $id . '">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }


    /**
     * empty标签解析
     * 如果某个变量为empty 则输出内容
     * 格式： {empty name="" }content{/empty}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagNavigation($tag, $content) {
        $name = $tag['name'];
        $name = $this->autoBuildVar($name);

        $parseStr = '<?php $nav = entity\Navigation::Lists(); ?>';
        $parseStr .= $content;
        return $parseStr;
    }


    /**
     * 树
     */
    public function tagTrees($tag, $content) {
        $data = $tag['name'];
        $wrapTag = empty($tag['wrapTag'])?'ul':$tag['data'];
        $wrapClass = empty($tag['wrapClass'])?'':$tag['wrapClass'];
        $firstClass = empty($tag['firstClass'])?'':$tag['firstClass'];
        $itemTag = empty($tag['itemTag'])?'li':$tag['itemTag'];
        $itemClass = empty($tag['itemClass'])?'':$tag['itemClass'];
        $childName = empty($tag['childName'])?'children':$tag['childName'];
        $hrefName = empty($tag['hrefName'])?'permalink':$tag['hrefName'];
        $showName = empty($tag['showName'])?'title':$tag['showName'];

        //viewTree($data,$wrapTag='ul',$wrapClass='',$firstClass='',$itemTag='li',$itemClass='',$childName='children',$hrefName='permalink',$showName='title')
        $parse = '<?php ';
        $parse .= "viewTree($data,'{$wrapTag}','{$wrapClass}','{$firstClass}','{$itemTag}','{$itemClass}','{$childName}','{$hrefName}','{$showName}');";
        $parse .= ' ?>';
        return $parse;
    }


    /**
     * 拼接语句
     * @param $tag
     * @param $content
     * @param $method
     * @return string
     */
    public function _($tag,$content,$method) {
        $return = isset($tag['return'])?$tag['return']:'data';
        $parse = '<?php ';
        $parse .= '$'.$return.' = '.$method.';';
        $parse .= ' ?>';
        $parse .= $content;
        return $parse;
    }

    /**
     * 拼接语句，并加上volist
     * @param $tag
     * @param $content
     * @param $method
     * @return string
     */
    public function _s($tag,$content,$method) {
        $return = isset($tag['return'])?$tag['return']:'data';
        $parse = '<?php ';
        $parse .= '$__LIST__ = '.$method.';';
        $parse .= ' ?>';
        $parse .= '<volist name="__LIST__" id="' . $return . '">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }
}