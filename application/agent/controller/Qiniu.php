<?php

namespace app\agent\controller;

use  Qiniu\QiniuStorage;
use Util\Random;

class Qiniu extends Base
{
    public function _dayuanren()
    {
        $this->qiniuconfig = C('QINIU');
        $this->qiniu = new QiNiuStorage($this->qiniuconfig);
    }

    public function get_token()
    {
        $token = $this->qiniu->getToken();
        $filename = I('filename') ? I('filename') : "";
        if ($filename) {
            $filename = time() . Random::alnum(6) . substr($filename, strrpos($filename, '.'));
        }
        return djson(0, 'ok', [
            'token' => $token,
            'domain' => $this->qiniuconfig['domain'],
            'prefix' => $this->qiniuconfig['prefix'],
            'filename' => $filename
        ]);
    }

    //上传单个文件 用uploadify
    public function uploadOne()
    {
        ini_set('memory_limit', '3072M');
        set_time_limit(0);
        $file = $_FILES['file'];
        $upfile = array(
            'name' => 'file',
            'fileName' => time() . Random::alnum(6) . substr($file['name'], strrpos($file['name'], '.')),
            'fileBody' => file_get_contents($file['tmp_name'])
        );
        $config = array('Expires' => 3600, 'saveName' => '', 'custom_fields' => []);
        $result = $this->qiniu->upload($config, $upfile);
        if ($result) {
            $result['domain'] = C('QINIU')['domain'];
            $result['url'] = HTTP_TYPE . C('QINIU')['domain'] . DS . $result['key'];
            $result['size'] = $file['size'] / 1024 / 1024;
            return djson(0, '上传成功', $result);
        } else {
            return djson(1, '上传失败', array(
                'error' => $this->qiniu->error,
                'errorStr' => $this->qiniu->errorStr
            ));
        }
    }

}
