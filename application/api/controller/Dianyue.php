<?php


namespace app\api\controller;

use think\Log;

/**
 * 接口异常监控
 **/
class Dianyue
{
    
    public function _inithomechild()
    {

    }
    public function send() {
        $data= [
           'w'=> I('account'),
            'type'=>I('area')
        ];
        return $this->http_post('http://www.591mf.top/home/Electric', $data);
    }


    /**
     * post请求
     */
    private function http_post($url, $param)
    {
        $oCurl = curl_init();
        if (is_string($param)) {
            $strPOST = $param;
        } else {
            $strPOST = http_build_query($param);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 30);
        curl_setopt($oCurl, CURLOPT_HEADER, 0);
          $hd =[
            'C0ntent-Length'=>'131413',
            'X-Requested-With'=>'XMLHttpRequest',
            'Content-Type'=>'application/x-www-form-urlencoded; charset=UTF-8',
            'Origin'=>'http://www.591mf.top',

        ];
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $hd);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        echo  '返回 状态'.http_build_query($aStatus);
        echo  '返回'.$sContent;
    }
}