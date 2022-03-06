<?php

namespace app\common\command;

use app\common\library\Notification;
use app\common\model\Client;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\model\Expressorder as ExorderModel;

class Crontab1h extends Command
{
    protected function configure()
    {
        $this->setName('Crontab60')->setDescription('60秒定时器');
    }

    protected function execute(Input $input, Output $output)
    {
        while (1) {
            $this->notification();
            echo "执行完成！" . date("Y-m-d H:i:s", time()) . PHP_EOL;
            sleep(60*60);
        }
    }

    //回调通知
    private function fetchRemoteOrder()
    {
       // 先从redis 拿到需要更新得任务队列

    }
}
