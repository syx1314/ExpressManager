<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-13
 * Time: 10:47
 */
return [
// 视图输出字符串内容替换
    'view_replace_str' => [
        '__IMG__' => ROOT . 'public' . DS . 'agent' . DS . 'img',
        '__CSS__' => ROOT . 'public' . DS . 'agent' . DS . 'css',
        '__JS__' => ROOT . 'public' . DS . 'agent' . DS . 'js',
        '__PG__' => ROOT . 'public' . DS . 'agent' . DS . 'plugins',
        '__ADMIN__' => ROOT . 'public' . DS . 'agent',
    ],
    //默认控制器
    'default_controller' => 'Admin',
    // 默认操作名
    'default_action' => 'index',

];