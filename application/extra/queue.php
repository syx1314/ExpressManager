<?php

return [
    /**
     * 取消消息队列
     */
//    'connector' => 'Sync',//取消消息队列，同步执行

    /**
     * 数据库驱动【不建议】
     */
    'connector' => 'Database', //数据库驱动
    'expire' => null,//任务过期时间
    'default' => 'default',//默认队列名称
    'table' => 'jobs',//存储消息的表名，不带前缀
    'dns' => [],

    /**
     * redis驱动【建议】
     */
    'connector'  => 'Redis',		// Redis 驱动
    'expire'     => 60,		// 任务的过期时间，默认为60秒; 若要禁用，则设置为 null
    'default'    => 'default',		// 默认的队列名称
    'host'       => '127.0.0.1',	// redis 主机ip
    'port'       => 6379,		// redis 端口
    'password'   => '',		// redis 密码
    'select'     => 0,		// 使用哪一个 db，默认为 db0
    'timeout'    => 0,		// redis连接的超时时间
    'persistent' => false,		// 是否是长连接
];
