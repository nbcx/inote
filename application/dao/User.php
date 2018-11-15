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

/**
 * User
 *
 * @package dao
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/11
 */
class User extends Dao {

    public function findToken($token) {
        return $this->find('token=?',$token);
    }

}