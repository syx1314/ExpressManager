<?php

namespace app\api\controller;


class Address extends Home
{

    /**
     * @return \think\response\Json
     * 所有收货地址
     */
    public function all_address()
    {
        $list = M('address')->where(['customer_id' => $this->customer['id']])->order("isdefault desc,create_time desc")->select();
        if ($list) {
            return djson(0, 'ok', $list);
        } else {
            return djson(1, '您还没有收货地址');
        }
    }

    //收货地址
    public function my_address()
    {
        if (I("address_id")) {
            $address_map['id'] = I("address_id");
            $address_map['customer_id'] = $this->customer['id'];
            $address = M('address')->where($address_map)->find();
        } else {
            $address_map['isdefault'] = 1;
            $address_map['customer_id'] = $this->customer['id'];
            $address = M('address')->where($address_map)->find();
            if (!$address) {
                $address_maps['customer_id'] = $this->customer['id'];
                $address = M('address')->where($address_maps)->find();
            }
        }
        if ($address) {
            return djson(0, 'ok', $address);
        } else {
            return djson(1, '您还没有收货地址，马上去添加');
        }
    }

    /**
     * @return \think\response\Json
     * 地址信息
     */
    public function address_info()
    {
        $list = M('address')->where(['customer_id' => $this->customer['id'], 'id' => I('id')])->find();
        if ($list) {
            return djson(0, 'ok', $list);
        } else {
            return djson(1, '未找到该地址');
        }
    }

    public function save_info()
    {
        if (I('id')) {
            $uid = M('address')->where(['customer_id' => $this->customer['id'], 'id' => I('id')])->setField([
                'name' => I('name'),
                'mobile' => I('mobile'),
                'city' => I('city'),
                'province' => I('province'),
                'county' => I('county'),
                'street' => I('street'),
                'isdefault' => I('isdefault'),
                'type' => I('type')
            ]);
            if ($uid) {
                return djson(0, '修改成功');
            } else {
                return djson(1, '修改失败');
            }
        } else {
            $aid = M('address')->insertGetId([
                'name' => I('name'),
                'mobile' => I('mobile'),
                'city' => I('city'),
                'province' => I('province'),
                'county' => I('county'),
                'street' => I('street'),
                'isdefault' => I('isdefault'),
                'type' => I('type'),
                'customer_id' => $this->customer['id'],
                'create_time' => time()
            ]);
            if ($aid) {
                return djson(0, '添加成功');
            } else {
                return djson(1, '添加失败');
            }
        }
    }

    /**
     * @return \think\response\Json
     * 删除地址
     */
    public function del_address()
    {
        $did = M('address')->where(['customer_id' => $this->customer['id'], 'id' => I('id')])->delete();
        if ($did) {
            return djson(0, 'ok');
        } else {
            return djson(1, '未找到收货地址');
        }
    }


}
