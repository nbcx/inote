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
/**
 * PageNavigator
 *
 * @package util
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/14
 */
class PageNavigator {

    /**
     * 记录总数
     *
     * @access protected
     * @var integer
     */
    protected $_total;

    /**
     * 页面总数
     *
     * @access protected
     * @var integer
     */
    protected $_totalPage;

    /**
     * 当前页面
     *
     * @access protected
     * @var integer
     */
    protected $_currentPage;

    /**
     * 每页内容数
     *
     * @access protected
     * @var integer
     */
    protected $_pageSize;

    /**
     * 页面链接模板
     *
     * @access protected
     * @var string
     */
    protected $_pageTemplate;

    /**
     * 链接锚点
     *
     * @access protected
     * @var string
     */
    protected $_anchor;

    /**
     * 页面占位符
     *
     * @access protected
     * @var mixed
     */
    protected $_pageHolder = ['{page}', '%7Bpage%7D'];

    protected $isrender = true;

    /**
     * 构造函数,初始化页面基本信息
     *
     * @access public
     * @param integer $total 记录总数
     * @param integer $page 当前页面
     * @param integer $pageSize 每页记录数
     * @param string $pageTemplate 页面链接模板
     * @return void
     */
    public function __construct($total, $currentPage, $pageSize, $pageTemplate) {
        $this->_total = $total;
        $this->_totalPage = ceil($total / $pageSize);
        $this->_currentPage = $currentPage;
        $this->_pageSize = $pageSize;
        $this->_pageTemplate = $pageTemplate;

        if (($currentPage > $this->_totalPage || $currentPage < 1) && $total > 0) {
            //throw new \Exception('Page Not Exists', 404);
            $this->isrender = false;
        }
    }

    /**
     * 设置页面占位符
     *
     * @access protected
     * @param string $holder 页面占位符
     * @return void
     */
    public function setPageHolder($holder) {
        $this->_pageHolder = [
            '{' . $holder . '}',
            str_replace(['{', '}'], ['%7B', '%7D'], $holder)
        ];
    }

    /**
     * 设置锚点
     *
     * @access public
     * @param string $anchor 锚点
     * @return void
     */
    public function setAnchor($anchor) {
        $this->_anchor = '#' . $anchor;
    }

    /**
     * 输出盒装样式分页栏
     *
     * @access public
     * @param string $prevWord 上一页文字
     * @param string $nextWord 下一页文字
     * @param int $splitPage 分割范围
     * @param string $splitWord 分割字符
     * @param string $currentClass 当前激活元素class
     * @return void
     */
    public function render($prevWord = 'PREV', $nextWord = 'NEXT', $splitPage = 3, $splitWord = '...', array $template = []) {
        if($this->isrender === false) {
            return;
        }

        if ($this->_total < 1) {
            return;
        }


        $default = [
            'itemTag' => 'li',
            'textTag' => 'span',
            'currentClass' => 'active',
            'prevClass' => 'prev',
            'nextClass' => 'next'
        ];

        $template = array_merge($default, $template);
        extract($template);

        // 定义item
        $itemBegin = empty($itemTag) ? '' : ('<' . $itemTag . '>');
        $itemCurrentBegin = empty($itemTag) ? '' : ('<' . $itemTag
            . (empty($currentClass) ? '' : ' class="' . $currentClass . '"') . '>');
        $itemPrevBegin = empty($itemTag) ? '' : ('<' . $itemTag
            . (empty($prevClass) ? '' : ' class="' . $prevClass . '"') . '>');
        $itemNextBegin = empty($itemTag) ? '' : ('<' . $itemTag
            . (empty($nextClass) ? '' : ' class="' . $nextClass . '"') . '>');
        $itemEnd = empty($itemTag) ? '' : ('</' . $itemTag . '>');
        $textBegin = empty($textTag) ? '' : ('<' . $textTag . '>');
        $textEnd = empty($textTag) ? '' : ('</' . $textTag . '>');
        $linkBegin = '<a href="%s">';
        $linkCurrentBegin = empty($itemTag) ? ('<a href="%s"'
            . (empty($currentClass) ? '' : ' class="' . $currentClass . '"') . '>')
            : $linkBegin;
        $linkPrevBegin = empty($itemTag) ? ('<a href="%s"'
            . (empty($prevClass) ? '' : ' class="' . $prevClass . '"') . '>')
            : $linkBegin;
        $linkNextBegin = empty($itemTag) ? ('<a href="%s"'
            . (empty($nextClass) ? '' : ' class="' . $nextClass . '"') . '>')
            : $linkBegin;
        $linkEnd = '</a>';

        $from = max(1, $this->_currentPage - $splitPage);
        $to = min($this->_totalPage, $this->_currentPage + $splitPage);

        //输出上一页
        if ($this->_currentPage > 1) {
            echo $itemPrevBegin . sprintf($linkPrevBegin,
                    str_replace($this->_pageHolder, $this->_currentPage - 1, $this->_pageTemplate) . $this->_anchor)
                . $prevWord . $linkEnd . $itemEnd;
        }

        //输出第一页
        if ($from > 1) {
            echo $itemBegin . sprintf($linkBegin, str_replace($this->_pageHolder, 1, $this->_pageTemplate) . $this->_anchor)
                . '1' . $linkEnd . $itemEnd;

            if ($from > 2) {
                //输出省略号
                echo $itemBegin . $textBegin . $splitWord . $textEnd . $itemEnd;
            }
        }

        //输出中间页
        for ($i = $from; $i <= $to; $i++) {
            $current = ($i == $this->_currentPage);

            echo ($current ? $itemCurrentBegin : $itemBegin) . sprintf(($current ? $linkCurrentBegin : $linkBegin),
                    str_replace($this->_pageHolder, $i, $this->_pageTemplate) . $this->_anchor)
                . $i . $linkEnd . $itemEnd;
        }

        //输出最后页
        if ($to < $this->_totalPage) {
            if ($to < $this->_totalPage - 1) {
                echo $itemBegin . $textBegin . $splitWord . $textEnd . $itemEnd;
            }

            echo $itemBegin . sprintf($linkBegin, str_replace($this->_pageHolder, $this->_totalPage, $this->_pageTemplate) . $this->_anchor)
                . $this->_totalPage . $linkEnd . $itemEnd;
        }

        //输出下一页
        if ($this->_currentPage < $this->_totalPage) {
            echo $itemNextBegin . sprintf($linkNextBegin,
                    str_replace($this->_pageHolder, $this->_currentPage + 1, $this->_pageTemplate) . $this->_anchor)
                . $nextWord . $linkEnd . $itemEnd;
        }
    }

}