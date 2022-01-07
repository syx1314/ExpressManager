<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-13
 * Time: 17:30
 */

namespace app\api\controller;


use app\common\model\Client;


class Base extends \app\common\controller\Base
{

    public function _commonbase()
    {
        //你的初始化
        $host_name = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "*";
        header("Access-Control-Allow-Origin: $host_name");
        header("Access-Control-Allow-Credentials:true");
        header("Access-Control-Max-Age:120");
        header("Access-Control-Allow-Methods: PUT,POST,GET,DELETE,OPTIONS");//Access-Control-Allow-Methods
        header("Access-Control-Allow-Headers: Origin,X-Requested-With,Content-Type,Accept,Authorization,Content-Language,Client,Appid");

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit;
        }
        $this->header = getHeaders();

        if (isset($this->header['Appid']) && $wxconfig = M('weixin')->where(['appid' => $this->header['Appid']])->find()) {
            if (!$wxconfig) {
                djson(1, "appid错误")->send();
                exit;
            }
            $this->wxconfig = $wxconfig;
        } else {
            $this->wxconfig = M('weixin')->find();
        }

        $this->client = isset($this->header['Client']) && $this->header['Client'] ? intval($this->header['Client']) : Client::CLIENT_WX;
        //调用二级初始化函数
        if (method_exists($this, '_initbasechild')) {
            $this->_initbasechild();
        }
    }

    /**
     
     * @return \think\response\View
     * 找不到方法
     */
    public function _empty()
    {
        return djson(1, '页面找到不到了！');
    }
}