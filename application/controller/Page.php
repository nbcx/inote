<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace controller;

use dao\Media;
use util\Controller;

/**
 * Page
 *
 * @package controller
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/10
 */
class Page extends Controller {

    public function index($id) {
        $dao = new \dao\Page();
        $page = $dao->findId($id);
        $this->assign('page',$page);

        $this->assign('actionComment',$this->safe('/comment/post?type=page&parent='.$id));

        $this->display('page');
    }

}