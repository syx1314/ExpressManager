<?php


namespace app\common\exception;

use app\common\library\Email;
use think\Log;

/**
 * 当设置了 --tries  n 参数，一个任务执行了N次以后还在运行，就会抛出此异常
 
 **/
class QueueHandler
{
    const should_run_hook_callback = true;

    /**
     * @param $jobObject   \think\queue\Job   //任务对象，保存了该任务的执行情况和业务数据
     * @return bool     true                  //是否需要删除任务并触发其failed() 方法
     * return 返回 true 时，系统会自动删除该任务，并且自动调用消费者类中的 failed() 方法
     * return 返回 false 时，系统不会自动删除该任务，也不会自动调用消费者类中的 failed() 方法，需要开发者另行处理失败任务的删除和通知。
     */
    public function logAllFailedQueues(&$jobObject)
    {
        $failedJobLog = [
            'jobHandlerClassName' => $jobObject->getName(), // 'application\index\job\Hello'
            'queueName' => $jobObject->getQueue(),               // 'helloJobQueue'
            'jobData' => $jobObject->getRawBody(),  // '{'a': 1 }'
            'attempts' => $jobObject->attempts(),            // 3
        ];
        $title = 'queue异常:目录' . __DIR__ . "消息队列" . $jobObject->getName() . '已经运行了' . $jobObject->attempts() . '次了，依然未完成。';
        Log::error($title . json_encode($failedJobLog, true));
        Email::sendMail($title, json_encode($failedJobLog, true));

        // $jobObject->release();     //重发任务
        //$jobObject->delete();         //删除任务
        //$jobObject->failed();	  //通知消费者类任务执行失败

        return self::should_run_hook_callback;
    }
}