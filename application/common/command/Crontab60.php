<?php

namespace app\common\command;

use app\common\library\Notification;
use app\common\model\Client;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Crontab60 extends Command
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
            sleep(60);
        }
    }

    //回调通知
    private function notification()
    {
        $lists = M('porder')->where(['status' => ['in', '4,5,6'], 'client' => Client::CLIENT_API, 'is_notification' => 0, 'notification_num' => ['elt', 2]])->field("id,status")->select();
        foreach ($lists as $k => $v) {
            if ($v['status'] == 4) {
                Notification::rechargeSus($v['id']);
            } else {
                Notification::rechargeFail($v['id']);
            }
        }
    }


}