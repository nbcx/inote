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

use dao\Setting;
use nb\Collection;
use nb\Config;

/**
 * Controller
 *
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/9
 */
class Controller extends \nb\Controller {

    public function __before() {
        $conf = Config::$o;
        //设置使用主题
        $this->view->config([
            'view_path' =>__APP__ . "theme/{$conf->theme}/",
            'tpl_replace_string' => [
                '_pub_' =>'/public/',
                '_theme_'=>"/theme/{$conf->theme}/"
            ],
        ]);
        $this->assign('auth',Auth::init());
        $this->assign('conf',$conf);

        //用户配置
        $option = new Setting();
        $this->assign('setting',new Collection($option->all()));

        return true;
    }

    //获取安全地址
    protected function safe($path, $prefix='') {
        return Security::url($path, $prefix);
    }

    //验证地址是否安全
    protected function protect($func=null) {
        $func or $func = function (){
            tips('非法请求');
        };
        Security::protect($func);
    }

}