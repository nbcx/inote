<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace event;

use nb\event\Swoole;
use utils\PHPMailer;
/**
 *
 * User: Collin
 * QQ: 1169986
 * Date: 17/12/2 下午4:46
 */
class Server extends Swoole {

    public function workerStar(\swoole\Server $server,$worker_id) {
        $conf = Config::$o->swoole;
        if($worker_id >= $conf['worker_num']) {
            swoole_set_process_name("php-{$conf['part']}-task-worker");
        }
        else {
            swoole_set_process_name("php-{$conf['part']}-event-worker");
        }
    }

}