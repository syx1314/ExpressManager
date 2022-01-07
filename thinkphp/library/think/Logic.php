<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/12
 * Time: 18:45
 */

namespace think;

/**
 * 参数验证控制器
 * liaoqiang
 * Class Logic
 * @package think
 * 在tp5 的 validate 基础上增加的方法，主要是为了简单化，规范化我们的验证。不用像TP5那样每次都要new对象
 */
class Logic
{
    protected $allowMethods = '';
    protected $rules = [];
    protected $message = [];

    public function validate()
    {
        $data = [];
        if ($this->allowMethods) {
            switch (strtolower($this->allowMethods)) {
                case 'post':
                    if (!request()->isPost()) {
                        djson(11, 'Please use post')->send();
                        exit;
                    } else {
                        $data = request()->post();
                    }
                    break;
                case 'get':
                    if (!request()->isGet()) {
                        djson(11, 'Please use get')->send();
                        exit;
                    } else {
                        $data = request()->param();
                    }
                    break;
                default:
                    djson(11, 'allowMethods param error')->send();
                    exit;
            }
        } else {
            $data = request()->param();
        }
        $v = new \think\Validate($this->rules, $this->message);
        $ret = $v->check($data);
        if (!$ret) {
            djson(11, $v->getError())->send();
            exit;
        }

    }
}