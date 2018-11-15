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

/**
 * Sys
 *
 * @package util
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/26
 */
class Sys extends Access {

    //服务器硬盘容量
    public function _disk_total_space() {
        return disk_total_space(__APP__);
    }

    //服务器剩余容量
    public function _disk_free_space() {
        return disk_free_space(__APP__);
    }

    //获取硬盘使用百分比
    public function _disk_use_percent() {
        return floor($this->disk_free_space/$this->disk_total_space*100);
    }

    //获取php版本信息
    public function _phpversion() {
        return PHP_VERSION;
    }

    //ZEND版本
    public function _zendversion() {
        return zend_version();
    }

    public function _os() {
        return PHP_OS;
    }

    //获取服务器软件信息
    public function _server() {
        return $_SERVER ['SERVER_SOFTWARE'];
    }

    //最大上传限制
    public function _upload_max_filesize() {
        return get_cfg_var ("upload_max_filesize")?get_cfg_var ("upload_max_filesize"):"0";
    }

    //最大执行时间,单位S
    public function _max_execution_time() {
        return get_cfg_var("max_execution_time");
    }

    //脚本运行占用最大内存
    public function _memory_limit() {
        return get_cfg_var ("memory_limit")?get_cfg_var("memory_limit"):"0";
    }

    //获取使用数据库信息
    public function _database_info() {
        return 'Mysql 8.0';

    }
}