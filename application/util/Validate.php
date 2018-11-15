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
 * 通用验证器
 *
 * @package util
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/18
 */
class Validate extends \nb\Validate {

    protected $rule = [
        'names'  =>  'checkName:nbframework',
        'email' =>  'email',
    ];

    protected $message = [
        'names'  =>  '用户名必须',
        'email' =>  '邮箱格式错误',
    ];

    // 自定义验证规则
    protected function checkName($value,$rule,$data=[]) {
        return $rule == $value ? true : '名称错误';
    }

}