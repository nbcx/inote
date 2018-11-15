<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * functions
 *
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/11
 */
/**
 * 获取文章的url地址
 * @param $id
 * @return mixed
 */
function url_post($id) {
    return url('post',['id'=>$id]);
}

function url_page($id) {
    return url('page',['id'=>$id]);
}

function url_classify($id) {
    return url('classify',['slug'=>$id]);
}
/**
 * 生成安全连接
 * @param $path
 * @param string $prefix
 * @return mixed
 */
function safe($path, $prefix='') {
    return \util\Security::url($path, $prefix);
}

/**
 * 自闭合html修复函数
 * 使用方法:
 * <code>
 * $input = '这是一段被截断的html文本<a href="#"';
 * echo Typecho_Common::fixHtml($input);
 * //output: 这是一段被截断的html文本
 * </code>
 *
 * @access public
 * @param string $string 需要修复处理的字符串
 * @return string
 */
function fixHtml($string) {
    //关闭自闭合标签
    $startPos = strrpos($string, "<");

    if (false == $startPos) {
        return $string;
    }

    $trimString = substr($string, $startPos);

    if (false === strpos($trimString, ">")) {
        $string = substr($string, 0, $startPos);
    }

    //非自闭合html标签列表
    preg_match_all("/<([_0-9a-zA-Z-\:]+)\s*([^>]*)>/is", $string, $startTags);
    preg_match_all("/<\/([_0-9a-zA-Z-\:]+)>/is", $string, $closeTags);

    if (!empty($startTags[1]) && is_array($startTags[1])) {
        krsort($startTags[1]);
        $closeTagsIsArray = is_array($closeTags[1]);
        foreach ($startTags[1] as $key => $tag) {
            $attrLength = strlen($startTags[2][$key]);
            if ($attrLength > 0 && "/" == trim($startTags[2][$key][$attrLength - 1])) {
                continue;
            }
            if (!empty($closeTags[1]) && $closeTagsIsArray) {
                if (false !== ($index = array_search($tag, $closeTags[1]))) {
                    unset($closeTags[1][$index]);
                    continue;
                }
            }
            $string .= "</{$tag}>";
        }
    }

    return preg_replace("/\<br\s*\/\>\s*\<\/p\>/is", '</p>', $string);
}

//生成友好时间形式
function dateword($from) {
    static $now = NULL;
    $now == NULL && $now = time();
    !is_numeric($from) && $from = strtotime($from);
    $seconds = $now - $from;
    $minutes = floor($seconds / 60);
    $hours = floor($seconds / 3600);
    $day = round((strtotime(date('Y-m-d', $now)) - strtotime(date('Y-m-d', $from))) / 86400);
    if ($seconds == 0) {
        return '刚刚';
    }
    if (($seconds >= 0) && ($seconds <= 60)) {
        return "{$seconds}秒前";
    }
    if (($minutes >= 0) && ($minutes <= 60)) {
        return "{$minutes}分钟前";
    }
    if (($hours >= 0) && ($hours <= 24)) {
        return "{$hours}小时前";
    }
    if ((date('Y') - date('Y', $from)) > 0) {
        return date('Y-m-d', $from);
    }

    switch ($day) {
        case 0:
            return date('今天H:i', $from);
            break;

        case 1:
            return date('昨天H:i', $from);
            break;

        default:
            //$day += 1;
            return "{$day} 天前";
            break;
    }
}

function stencil($filter) {
    $files = __APP__.'theme'.DS.\nb\Config::$o->theme.DS.$filter;
    //获取主题
    $files = glob($files);

    $themes = [];
    foreach ($files as $v) {
        $temp = explode('/',$v);
        $temp = end($temp);
        $temp = explode('.',$temp);
        $themes[] = $temp[0];
    }
    return $themes;
}

/**
 * 将markdown转为html
 * @param $text
 * @return mixed
 */
function markdown($text) {
    $parser = \nb\Pool::value('util\HyperDown',function (){
        $parser = new \util\HyperDown();
        $parser->hook('afterParseCode', function ($html) {
            return preg_replace("/<code class=\"([_a-z0-9-]+)\">/i", "<code class=\"lang-\\1\">", $html);
        });
        $parser->hook('beforeParseInline', function ($html) use ($parser) {
            return preg_replace_callback("/^\s*<!\-\-\s*more\s*\-\->\s*$/s", function ($matches) use ($parser) {
                return $parser->makeHolder('<!--more-->');
            }, $html);
        });
        $parser->enableHtml(true);
        $parser->_commonWhiteList .= '|img|cite|embed|iframe';
        $parser->_specialWhiteList = array_merge($parser->_specialWhiteList, [
            'ol' => 'ol|li',
            'ul' => 'ul|li',
            'blockquote' => 'blockquote',
            'pre' => 'pre|code'
        ]);
        return $parser;
    });
    return $parser->makeHtml($text);
}

/**
 * 获取文章内容摘要
 *
 * @access public
 * @param mixed $more 文章截取后缀
 */
function excerpt(&$text) {
    $excerpt = content($text);

    if(false === strpos($excerpt, '<!--more-->')) {
        return $excerpt;
    }
    //$excerpt = explode('<!--more-->', $excerpt);
    list($excerpt) = explode('<!--more-->', $excerpt);

    return fixHtml($excerpt);

}

/**
 * 对文章的简短纯文本描述
 *
 * @access protected
 * @return string
 */
function description(&$text) {
    $excerpt = excerpt($text);
    $plainTxt = str_replace("\n", '', trim(strip_tags($excerpt)));
    //$plainTxt = $plainTxt ? $plainTxt : $this->title;
    return xsubStr($plainTxt, 0, 100, '...');
}

/**
 * 获取文章内容
 *
 * @access protected
 * @return string
 */
function content(&$text) {
    //return $text;
    return markdown($text);
}

/**
 * 输出文章的第一行作为摘要
 *
 * @return string
 */
function summary(&$text) {
    $content = content($text);//$this->content;
    $parts = preg_split("/(<\/\s*(?:p|blockquote|q|pre|table)\s*>)/i", $content, 2, PREG_SPLIT_DELIM_CAPTURE);
    if (!empty($parts)) {
        $content = $parts[0] . $parts[1];
    }
    return $content;
}


/**
 * 宽字符串截字函数
 *
 * @access public
 * @param string $str 需要截取的字符串
 * @param integer $start 开始截取的位置
 * @param integer $length 需要截取的长度
 * @param string $trim 截取后的截断标示符
 * @return string
 */
function xsubStr($str, $start, $length, $trim = "...") {
    if (!strlen($str)) {
        return '';
    }

    $iLength = strLen($str) - $start;
    $tLength = $length < $iLength ? ($length - strLen($trim)) : $length;

    if (function_exists('mb_get_info') && function_exists('mb_regex_encoding')) {
        $str = mb_substr($str, $start, $tLength, 'UTF-8');
    }
    else if (preg_match_all("/./u", $str, $matches)) {
        $str = implode('', array_slice($matches[0], $start, $tLength));
    }
    else {
        $str = xsubStr($str, $start, $tLength);
    }

    return $length < $iLength ? ($str . $trim) : $str;
}

/**
 * 获取gravatar头像地址
 *
 * @param string $mail
 * @param int $size
 * @param string $rating
 * @param string $default
 * @param bool $isSecure
 * @return string
 */
function gravatar($mail, $size=64) {
    $rating = "PG";
    $default = null;
    $isSecure = false;
    $url = $isSecure ? 'https://secure.gravatar.com' : 'http://www.gravatar.com';
    $url .= '/avatar/';

    if (!empty($mail)) {
        $url .= md5(strtolower(trim($mail)));
    }

    $url .= '?s=' . $size;
    $url .= '&amp;r=' . $rating;
    $url .= '&amp;d=' . $default;

    return $url;
}