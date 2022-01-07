<?php
/**
 * Created by 廖强.
 * TEL:18380807104
 * QQ:1204887277
 * User: admin
 * Date: 2017-06-27
 * Time: 9:28
 * Company:网络科技有限责任公司
 */

namespace Util;


class Applet
{
    const API_URL_PREFIX = 'https://api.weixin.qq.com/cgi-bin';
    const AUTH_URL = '/token?grant_type=client_credential&';
    const SEND_MSG = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=";
    const SESSION_KEY_URL = "https://api.weixin.qq.com/sns/jscode2session?";
    const SUBSCRIBE_MESSAGE_URL = '/message/subscribe/send?';
    const API_WXA_CODE_UNLIMIT = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?';
    private $appid;
    private $token;
    private $appsecret;
    private $_receive;
    private $errCode;
    public $errMsg;
    private $access_token;
    private $msgcontent;
    private $_text_filter = true;

    public function __construct($options)
    {
        $this->token = isset($options['token']) ? $options['token'] : '';
        $this->appid = isset($options['appid']) ? $options['appid'] : '';
        $this->appsecret = isset($options['appsecret']) ? $options['appsecret'] : '';
        $this->checkAuth();
    }

    public function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            echo $_GET['echostr'];
        }
    }

    /**
     *
     * @return $this
     *  初始化微信发送数据,并获取当前类的对象
     */
    public function getRev()
    {
        if ($this->_receive) return $this;
        $jsonstr = file_get_contents("php://input");
        $this->_receive = json_decode($jsonstr, true);
        return $this;
    }

    /**
     * 获取接收消息的类型
     */
    public function getRevType()
    {
        if (isset($this->_receive['MsgType']))
            return $this->_receive['MsgType'];
        else
            return false;
    }

    /**
     * 获取事件类型
     */
    public function getEventType()
    {
        if ($this->_receive['MsgType'] == "event")
            return $this->_receive['Event'];
        else
            return false;
    }

    /**
     * 获取开发者设置的sessionFrom参数
     */
    public function getSessionFrom()
    {
        if ($this->_receive['MsgType'] == "event")
            return $this->_receive['sessionFrom'];
        else
            return false;
    }

    /**
     *
     * @return bool
     * 获取用户openid
     */
    public function getRevFrom()
    {
        if (isset($this->_receive['FromUserName']))
            return $this->_receive['FromUserName'];
        else
            return false;
    }

    /**
     *
     * @param $text
     * @return $this
     * 创建文本消息
     */
    public function text($text)
    {
        $data['touser'] = $this->_receive['FromUserName'];
        $data['msgtype'] = "text";
        $data['text']['content'] = $this->_auto_text_filter($text);
        $this->msgcontent = $data;
        return $this;
    }


    public function reply()
    {
        $result = $this->http_post(self::SEND_MSG . $this->access_token, json_encode($this->msgcontent));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                $this->log_result($result);
                return false;
            }
            return true;
        } else {
            $this->log_result("请求失败");
            return false;
        }
    }

    /**
     * 发送订阅消息
     */
    public function sendSubscribeMessage($data)
    {
        if (!$this->access_token) return rjson(1, 'access_token未生成');
        $result = $this->http_post(self::API_URL_PREFIX . self::SUBSCRIBE_MESSAGE_URL . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                return rjson($json['errcode'], $json['errmsg'], $json);
            }
            return rjson(0, 'ok', $json);
        }
        return rjson(1, '请求接口失败');
    }


    /**
     * 添加订阅消息模板
     * $data= {
     * "tid":"401",
     * "kidList":[1,2],
     * "sceneDesc": "测试数据"
     * }
     */
    public function addSubscribeMessage($data)
    {
        if (!$this->access_token) return rjson(1, 'access_token未生成');
        $result = $this->http_post('https://api.weixin.qq.com/wxaapi/newtmpl/addtemplate?access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                return rjson($json['errcode'], $json['errmsg'], $json);
            }
            return rjson(0, 'ok', $json);
        }
        return rjson(1, '请求接口失败');
    }


    //生成不限制数量的二维码
    public function getWxaCodeUnlimit($scene, $page = '', $path = 'uploads/applet/unlimitqr/', $width = 430)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if (!$this->access_token) return rjson(1, 'access_token未生成');
        $data = ['scene' => $scene, 'page' => $page, 'width' => $width];
        $result = $this->http_post(self::API_WXA_CODE_UNLIMIT . 'access_token=' . $this->access_token, self::json_encode($data));

        if (is_null(json_decode($result))) {
            $filename = $path . time() . md5($page . $scene) . '.png';
            $file = fopen($filename, "w");
            fwrite($file, $result);
            fclose($file);
            return rjson(0, 'ok', $filename);
        } else {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                return rjson($json['errcode'], $json['errmsg'], $json);
            }
            return rjson(1, '其他错误', $json);
        }
    }

    private function _auto_text_filter($text)
    {
        if (!$this->_text_filter) return $text;
        return str_replace("\r\n", "\n", $text);
    }

    /**
     * 生成access_token
     * @param string $appid
     * @param string $appsecret
     * @param string $token 手动指定access_token，非必要情况不建议用
     */
    public function checkAuth()
    {
        $token = DataCachea::get($this->appid);
        if ($token) {
            $this->access_token = $token;
            return $this->access_token;
        }
        $result = $this->http_get(self::API_URL_PREFIX . self::AUTH_URL . 'appid=' . $this->appid . '&secret=' . $this->appsecret);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            DataCachea::set($json['access_token'], $this->appid);
            $this->access_token = $json['access_token'];
            return $this->access_token;
        }
        return false;
    }

    /**
     * GET 请求
     * @param string $url
     */
    private function http_get($url)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @param boolean $post_file 是否文件上传
     * @return string content
     */
    private function http_post($url, $param, $post_file = false)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if (is_string($param) || $post_file) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . urlencode($val);
            }
            $strPOST = join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, ['Content-Type:application/json; charset=utf8']);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }


    public function get_Openid_by_code($code)
    {
        $result = $this->http_get(self::SESSION_KEY_URL . "appid=" . $this->appid . "&secret=" . $this->appsecret . "&js_code=" . $code . "&grant_type=authorization_code");
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            } else {
                return $json;
            }
        } else {
            return false;
        }
    }

    /**
     * 微信api不支持中文转义的json结构
     * @param array $arr
     */
    static function json_encode($arr)
    {
        $parts = array();
        $is_list = false;
        //Find out if the given array is a numerical array
        $keys = array_keys($arr);
        $max_length = count($arr) - 1;
        if (($keys [0] === 0) && ($keys [$max_length] === $max_length)) { //See if the first key is 0 and last key is length - 1
            $is_list = true;
            for ($i = 0; $i < count($keys); $i++) { //See if each key correspondes to its position
                if ($i != $keys [$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ($arr as $key => $value) {
            if (is_array($value)) { //Custom handling for arrays
                if ($is_list)
                    $parts [] = self::json_encode($value); /* :RECURSION: */
                else
                    $parts [] = '"' . $key . '":' . self::json_encode($value); /* :RECURSION: */
            } else {
                $str = '';
                if (!$is_list)
                    $str = '"' . $key . '":';
                //Custom handling for multiple data types
                if (!is_string($value) && is_numeric($value) && $value < 2000000000)
                    $str .= $value; //Numbers
                elseif ($value === false)
                    $str .= 'false'; //The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes($value) . '"'; //All other things
                // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
                $parts [] = $str;
            }
        }
        $json = implode(',', $parts);
        if ($is_list)
            return '[' . $json . ']'; //Return numerical JSON
        return '{' . $json . '}'; //Return associative JSON
    }

    function log_result($word, $file = "applet.txt")
    {
        $fp = fopen($file, "a");
        flock($fp, LOCK_EX);
        fwrite($fp, "执行日期：" . date("Y-m-d H:i", time()) . "\n" . $word . "\n\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }


}


class DataCachea
{
    /**
     * @var string $file 缓存文件地址
     * @access public
     */
    static public $file = ".wechat";

    /**
     * 取缓存内容
     * @param bool 是否直接输出，true直接转到缓存页,false返回缓存内容
     * @return mixed
     */
    static public function get($name = 'token')
    {
        $filename = md5($name) . self::$file;
        if (!is_file($filename)) {
            return false;
        }
        $json = file_get_contents($filename);
        $data = json_decode($json, true);
        //过期了
        if ($data['create_time'] + $data['expire'] < time()) {
            return false;
        }
        return $data['content'];
    }

    /**
     * 设置缓存内容
     */
    static public function set($content, $name = 'token', $expire = 7200)
    {
        $filename = md5($name) . self::$file;
        $fp = fopen($filename, 'w');
        fwrite($fp, json_encode(['create_time' => time(), 'expire' => $expire, 'content' => $content]));
        fclose($fp);
    }
}