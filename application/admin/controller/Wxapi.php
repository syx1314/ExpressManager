<?php
// +----------------------------------------------------------------------
//微信接口
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\library\Createlog;
use app\common\library\Email;
use app\common\library\Userlogin;
use app\common\model\Client;

/**
 * @author 廖强
 */
class Wxapi extends Base
{
    private $wechat;
    private $type;
    private $openid;

    public function _dayuanren()
    {
        $this->wxconfig = M('weixin')->where(['appid' => I('id')])->find();
        $this->wechat = new \Util\Wechat($this->wxconfig);
    }

    /**
     * 一般情况勿动此方法
     * 入口
     * @author liaoqiang
     */
    public function index()
    {
        //判断是否开启验证
        $this->wechat->valid();
        $this->type = $this->wechat->getRev()->getRevType();
        $this->openid = $this->wechat->getRevFrom();
        $this->type && $this->add_weixin_log($this->openid, $this->type, json_encode($this->wechat->getRevEvent()), json_encode($this->wechat->getRevData()));
        switch ($this->type) {
            case 'text':
                $this->textReply();
                break;
            case 'event':
                $this->eventReply();
                break;
            case 'image':
                $pic = $this->wechat->getRevPic();
                break;
            case 'voice':
                $voice = $this->wechat->getRevVoice();
                break;
            case  'video':
            case  'shortvideo':
                $video = $this->wechat->getRevVideo();
                break;
            case  'location':
                $loc = $this->wechat->getRevGeo();
                break;
            case  'link':
                $link = $this->wechat->getRevLink();
                break;
            default:

        }

    }

    /**
     * 一般情况勿动此方法
     
     * 文字消息处理
     */
    private function textReply()
    {
        $content = $this->wechat->getRevContent();
        $map['keywords'] = ['like', '%' . $content . '%'];
        $map['type'] = 1;
        $map['status'] = 1;
        $map['weixin_appid'] = $this->wxconfig['appid'];
        $reply = M('weixin_reply')->where($map)->find();
        if (!$reply) {
            $map['keywords'] = "*";
            $reply = M('weixin_reply')->where($map)->find();
        }
        //自动回复
        $this->auto_reply($reply);
    }

    // 事件处理

    /**
     * 一般情况勿动此方法
     
     */
    private function eventReply()
    {
        $event = $this->wechat->getRevEvent();

        $map['event'] = $event['event'];
        $map['type'] = 2;
        $map['status'] = 1;
        $map['weixin_appid'] = $this->wxconfig['appid'];
        //自动定义事件处理方法
        switch (strtolower($event['event'])) {
            case 'unsubscribe'://取消关注

                break;
            case 'subscribe': //关注

                $res = $this->wechat->getUserInfo($this->openid);
                if ($res['errno'] != 0) return;
                $userinfo = $res['data'];
                break;
            case 'scan': //扫码
                if ($key = $this->wechat->getRevSceneId()) {
                    $map['eventkey'] = $key;
                }
                break;
            case 'click'://自定义菜单点击事件
                if (strpos($event['key'], 'wechat_menu') !== false) {
                    $evarr = explode('#', $event['key']);
                    $menu = M('weixin_menu')->where(['id' => $evarr[2]])->find();
                    if ($menu['type'] == 'text') {
                        $this->wechat->text($menu['content'])->reply();
                    }
                    if ($menu['type'] == 'keys') {
                        $event['key'] = $menu['content'];
                    }
                }
                $map['eventkey'] = $event['key'];
                break;
            case 'location'://上报地理位置
                $location = $this->wechat->getRevEventGeo();
                break;
            case 'scancode_push'://扫码推送
                $scan = $this->wechat->getRevScanInfo();
                break;
            case 'scancode_waitmsg'://扫码推送等待
                $scan = $this->wechat->getRevScanInfo();
                $this->wechat->text("你扫码我知道了")->reply();
                break;
            case 'pic_sysphoto'://弹出系统拍照发图点击按钮
                $pic = $this->wechat->getRevSendPicsInfo();
                break;
            case 'pic_photo_or_album'://弹出拍照或者相册发图点击按钮
                $pic = $this->wechat->getRevSendPicsInfo();
                break;
            case 'pic_weixin'://弹出微信相册发图器点击按钮
                $pic = $this->wechat->getRevSendPicsInfo();
                break;
            case 'location_select'://弹出地理位置选择器点击按钮
                $loc = $this->wechat->getRevSendGeoInfo();
                break;
            default:
        }
        $reply = M('weixin_reply')->where($map)->find();
        //自动回复
        $this->auto_reply($reply);
        //调用自定义的方法
        $this->eventDeal($event);
    }

    /**
     * 一般情况勿动此方法
     
     * @param $reply
     * 自动回复模板调用
     */
    private function auto_reply($reply)
    {
        if ($reply) {
            switch (intval($reply['reply_style'])) {
                case 1://文本
                    $this->wechat->text(htmlspecialchars_decode($reply['text']))->reply();
                    break;
                case 2://图文
                    $msg[0]['Title'] = htmlspecialchars_decode($reply['title']);
                    $msg[0]['Description'] = htmlspecialchars_decode($reply['description']);
                    $msg[0]['PicUrl'] = $reply['picurl'];
                    $msg[0]['Url'] = $reply['url'];
                    $this->wechat->news($msg)->reply();
                    break;
                case 3://图片
                    $this->wechat->image($reply['media_id'])->reply();
                    break;
                case 4://语音
                    $this->wechat->voice($reply['media_id'])->reply();
                    break;
                case 5://视频
                    $this->wechat->video($reply['media_id'], htmlspecialchars_decode($reply['title']), htmlspecialchars_decode($reply['description']))->reply();
                    break;
                case 6://音乐
                    $this->wechat->music(htmlspecialchars_decode($reply['title']), htmlspecialchars_decode($reply['description']), $reply['musicurl'], $reply['musicurl'], $reply['media_id'])->reply();
                    break;
                case 7://转客服
                    $this->wechat->transfer_customer_service()->reply();
                    break;
                default:
            }
        }
    }

    /**
     * 可根据自己的需要修改此方法
     
     * 自定义事件处理，其他操作
     */
    private function eventDeal($event)
    {
        //自定义事件处理方法
        switch (strtolower($event['event'])) {
            case 'unsubscribe': //取消关注
                if ($user = M('customer')->where(["wx_openid" => $this->openid, 'weixin_appid' => $this->wxconfig['appid'], 'is_del' => 0])->find()) {
                    M('customer')->where(["id" => $user['id']])->setField(['is_subscribe' => 0]);
                    Createlog::customerLog($user['id'], '用户关注公众号', '系统');
                }
                break;
            case 'subscribe': //关注，如果用户还未关注公众号，则用户可以关注公众号，关注后微信会将带场景值关注事件推送给开发者。
                if ($user = M('customer')->where(["wx_openid" => $this->openid, 'weixin_appid' => $this->wxconfig['appid'], 'is_del' => 0])->find()) {
                    M('customer')->where(["id" => $user['id']])->setField(['is_subscribe' => 1]);
                    Createlog::customerLog($user['id'], '用户关注公众号', '系统');
                } else {
                    $key = $this->wechat->getRevSceneId();
                    $res = $this->wechat->getUserInfo($this->openid);
                    if ($res['errno'] != 0) return;
                    $userinfo = $res['data'];
                    $res = Userlogin::wxh5_user_reg($userinfo, $key, $this->wxconfig['appid'], Client::CLIENT_WX);
                    $res['errno'] == 0 && M('customer')->where(["id" => $res['data']['id']])->setField(['is_subscribe' => 1]);
                }
                break;
            case 'scan': //扫码 如果用户已经关注公众号，则微信会将带场景值扫描事件推送给开发者。
                if ($key = $this->wechat->getRevSceneId()) {

                }
                break;
            case 'click': //菜单点击事件 $event['key']

                break;
            case 'view': //点击菜单打开链接

                break;
            case 'location'://上报地理位置
                $location = $this->wechat->getRevEventGeo();
                break;
            case 'scancode_push'://扫码推送
                $scan = $this->wechat->getRevScanInfo();
                break;
            case 'scancode_waitmsg'://扫码推送等待
                $scan = $this->wechat->getRevScanInfo();
                break;
            case 'pic_sysphoto'://弹出系统拍照发图点击按钮
                $pic = $this->wechat->getRevSendPicsInfo();
                break;
            case 'pic_photo_or_album'://弹出拍照或者相册发图点击按钮
                $pic = $this->wechat->getRevSendPicsInfo();
                break;
            case 'pic_weixin'://弹出微信相册发图器点击按钮
                $pic = $this->wechat->getRevSendPicsInfo();
                break;
            case 'location_select'://弹出地理位置选择器点击按钮
                $loc = $this->wechat->getRevSendGeoInfo();
                break;
            default:
        }
    }

    /**
     
     * @param $openid
     * @param $type
     * @param $text
     * 写入日志
     */
    private function add_weixin_log($openid, $msg_type, $event, $text)
    {
        M('weixin_log')->insertGetId([
            'create_time' => time(),
            'openid' => $openid,
            'msg_type' => $msg_type,
            'event' => $event,
            'text' => $text,
            'weixin_appid' => $this->wxconfig['appid']
        ]);
    }

    /**
     * 写日志
     */
    private function writelog($text)
    {
        $myfile = fopen("wxapi.txt", "a") or die("Unable to open file!");
        fwrite($myfile, '***' . time_format(time()) . "***\r\n" . '---------start---------' . "\r\n" . $text . "\r\n---------end---------\r\n\r\n");
        fclose($myfile);
    }
}
