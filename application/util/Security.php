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

use nb\Access;
use nb\Pool;
use nb\Request;

/**
 * 安全选项
 */
class Security extends Access {

    /**
     * @var string
     */
    private $token;

    /**
     * @var boolean
     */
    private $enabled = true;


    public function __construct() {
        $this->token = '123123f';
    }

    /**
     * @return $this
     * @throws \ReflectionException
     */
    public static function ins() {
        return Pool::object(get_called_class());
    }

    /**
     * 获取安全URL
     *
     * @param $path
     * @param string $prefix
     * @return string
     */
    public static function url($path, $prefix='') {
        return self::ins()->safeUrl($path, $prefix);
    }

    /**
     * 保护提交数据
     * 失败将执行回调
     */
    public static function protect(callable $func) {
        self::ins()->protect or $func();
    }

    /**
     * 保护提交数据
     * 失败将返回false，成功返回true
     * @return bool
     */
    public function _protect(){
        $request = Request::driver();
        if ($this->enabled && $request->get['_'] != $this->token($request->referer)) {
            return false;
        }
        return true;
    }

    /**
     * 获取安全URL
     *
     * @param $path
     * @param string $prefix
     * @return string
     */
    public function safeUrl($path, $prefix='') {
        $path = $this->tokenUrl($path);
        $path = (0 === strpos($path, './')) ? substr($path, 2) : $path;
        return rtrim($prefix, '/') . '/' . str_replace('//', '/', ltrim($path, '/'));
    }

    /**
     * @param $enabled
     */
    public function enable($enabled = true) {
        $this->enabled = $enabled;
    }

    /**
     * 获取token
     *
     * @param string $suffix 后缀
     * @return string
     */
    public function token($suffix) {
        return md5($this->token . '&' . $suffix);
    }

    /**
     * 生成带token的路径
     *
     * @param $path
     * @return string
     */
    public function tokenUrl($path) {
        $parts = parse_url($path);
        $params = [];

        if (!empty($parts['query'])) {
            parse_str($parts['query'], $params);
        }
        $params['_'] = $this->token(Request::driver()->url);
        $parts['query'] = http_build_query($params);

        return $this->buildUrl($parts);
    }

    /**
     * 根据parse_url的结果重新组合url
     *
     * @access public
     * @param array $params 解析后的参数
     * @return string
     */
    public static function buildUrl($params) {
        return (isset($params['scheme']) ? $params['scheme'] . '://' : NULL)
            . (isset($params['user']) ? $params['user'] . (isset($params['pass']) ? ':' . $params['pass'] : NULL) . '@' : NULL)
            . (isset($params['host']) ? $params['host'] : NULL)
            . (isset($params['port']) ? ':' . $params['port'] : NULL)
            . (isset($params['path']) ? $params['path'] : NULL)
            . (isset($params['query']) ? '?' . $params['query'] : NULL)
            . (isset($params['fragment']) ? '#' . $params['fragment'] : NULL);
    }

}
 
