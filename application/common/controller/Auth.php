<?php
class  Auth {
    function __construct(){
    }
    public function auth($host) {

        $isAuth = false;

        $data= [ "http://m8.b306.com"];

        var_dump(stripos($data[$i],$host));
        for ($i =0 ; $i < count($data);$i++) {
            if (stripos($data[$i],$host)!==false) {
                $isAuth =true;
                break;
            }
            if (!stripos($data[$i],$host)) {
                $isAuth =false;
            }
        }
        if ($isAuth) {
            echo '{
                "errno":0,
                "errmsg":"正版授权",
                "data":{
                "code":0,
                "msg":"正版授权",
                "data":""
                }
                }';
        }else {
            echo '{
                "errno":1,
                "errmsg":"系统未授权，请联系客服官方正版授权2b",
                "data":{
                "code":1,
                "msg":"系统未授权，请联系客服官方正版授权2b",
                "data":""
                }
                }';
        }
    }


    function getAuthList($host) {
        $data= [ "m8.b306.com"];

        for ($i =0 ; $i < count($data);$i++) {
            if ($data[$i]) {
                return true;
            }
        }
        return  false;
    }
}
