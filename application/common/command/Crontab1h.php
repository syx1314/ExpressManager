<?php

namespace app\common\command;

use app\common\library\Notification;
use app\common\library\RedisPackage;
use app\common\model\Client;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\model\Expressorder as ExorderModel;

class Crontab1h extends Command
{
    protected function configure()
    {
        $this->setName('Crontab1h')->setDescription('1h定时器');
    }

    protected function execute(Input $input, Output $output)
    {
        while (1) {
            self::fetchRemoteOrder();
            echo "执行完成！" . date("Y-m-d H:i:s", time()) . PHP_EOL;
            sleep(60);
        }
    }

    //回调通知
    public static function fetchRemoteOrder()
    {
       // 先从redis 拿到需要更新得任务队列
        echo "执行队列" ;
        queue('app\queue\job\Work@contab1hFetchExpress');
    }
}
