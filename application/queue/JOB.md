# think-queue使用
think-queue是Thinkphp官方团队开发的一个专门支持队列服务的扩展包，使用composer管理，使用起来非常方便。

## 特点
1.Queue内置了 Redis，Database，Topthink ，Sync这四种驱动。
2.Queue消息消息可进行发布，获取，执行，删除，重发，失败处理，延迟执行，超时控制等操作

## 官方地址
- [https://github.com/top-think/think-queue](https://github.com/top-think/think-queue)

- [优秀的使用笔记](https://github.com/coolseven/notes/blob/master/thinkphp-queue/README.md)

## 安装与配置
1.下载
```
//我们框架5.0的安装这个版本，最新版本不支持
composer require topthink/think-queue 1.1.4
```
2.配置
```
配置文件位于 application/extra/queue.php
```

3.数据库表添加（如果用Database引擎）
```
DROP TABLE IF EXISTS `dyr_jobs`;
CREATE TABLE `dyr_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
```

## 使用
1.发起任务
```
//在任意代码中加入
queue('app\queue\job\Work@fire', $data);
```

2.处理任务
```
处理文件位于 application/queue/job/Work.php
异常处理文件 application/common/exception/QueueHandler.php(先到application/tags.php 添加queue_failed项目)
```

3.监听执行
```
//调试的时候可以用listen比较方便，下面有启动模式介绍，两种模式各有优势。
//启动前去除函数禁用 proc_open
//php 配置文件去掉 always_populate_raw_post_data 注释
php think queue:listen
```

4.启动命令
- Work 模式
```
php think queue:work \
--daemon            //是否循环执行，如果不加该参数，则该命令处理完下一个消息就退出
--queue  helloJobQueue  //要处理的队列的名称
--delay  0 \        //如果本次任务执行抛出异常且任务未被删除时，设置其下次执行前延迟多少秒,默认为0
--force  \          //系统处于维护状态时是否仍然处理任务，并未找到相关说明
--memory 128 \      //该进程允许使用的内存上限，以 M 为单位
--sleep  3 \        //如果队列中无任务，则sleep多少秒后重新检查(work+daemon模式)或者退出(listen或非daemon模式)
--tries  2          //如果任务已经超过尝试次数上限，则触发‘任务尝试次数超限’事件，默认为0
```
- Listen 模式
```
php think queue:listen \
--queue  helloJobQueue \   //监听的队列的名称
--delay  0 \         //如果本次任务执行抛出异常且任务未被删除时，设置其下次执行前延迟多少秒,默认为0
--memory 128 \       //该进程允许使用的内存上限，以 M 为单位
--sleep  3 \         //如果队列中无任务，则多长时间后重新检查
--tries  0 \         //如果任务已经超过重发次数上限，则进入失败处理逻辑，默认为0
--timeout 60         // work 进程允许执行的最长时间，以秒为单位
```
- Work Listen 各自使用场景
```

work 命令的适用场景是：
任务数量较多
性能要求较高
任务的执行时间较短
消费者类中不存在死循环，sleep() ，exit() ,die() 等容易导致bug的逻辑
--------------------------
listen 命令的适用场景是：
任务数量较少
任务的执行时间较长(如生成大型的excel报表等)，
任务的执行时间需要有严格限制
```

5.常驻内存

supervisor是用Python开发的一个client/server服务，是Linux/Unix系统下的一个进程管理工具。可以很方便的监听、启动、停止、重启一个或多个进程。用supervisor管理的进程，当一个进程意外被杀死，supervisor监听到进程死后，会自动将它重启，很方便的做到进程自动恢复的功能，不再需要自己写shell脚本来控制。

(1)方案1（宝塔适用）

找到宝塔面板软件商店安装 “Supervisor管理器” 最新版
添加守护进程
名称随便写;启动用户：默认root;运行目录选择:站点目录/public;启动命令：如下；进程数量：1
```
php think queue:work --daemon  --tries  3
```

(2)方案2（非宝塔用户）

安装
```
yum install epel-release
yum install supervisor
//设置开机自动启动
systemctl enable supervisord
```

修改配置文件：/etc/supervisord.conf
```
...
...
...
[include]
files = relative/directory/*.ini
###找到上面这行，如果有;就去掉
```

目录/etc/supervisord.d下添加文件queue.ini
```
[program:queue]
###生产模式用work比较好
command=php /www/wwwroot/dthink/think queue:work --daemon  --tries  3
###开发模式用listen比较好
###command=php /www/wwwroot/dthink/think queue:listen  --tries  3
autorestart=true
###注意上面是自己网站的目录，要记得修改
```

常用的几个supervisor命令
```
启动
supervisord -c /etc/supervisord.conf        //开启服务
supervisorctl -c /etc/supervisord.conf      //进入控制台
supervisorctl status                        //查看所有进程的状态
supervisorctl stop program_name             //停止某进程
supervisorctl start program_name            //启动进程
supervisorctl restart                       //重启
supervisorctl update                        //配置文件修改后使用该命令加载新的配置
supervisorctl reload                        //重新启动配置中的所有程序
supervisorctl stop all                      //停止全部进程
```