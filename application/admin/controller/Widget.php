<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-14
 * Time: 9:50
 */

namespace app\admin\controller;


use app\admin\model\SystemAttachment;
use app\admin\model\SystemAttachmentCategory;
use Qiniu\QiniuStorage;
use Util\Random;

/**
 
 **/
class Widget extends Base
{
    //图片首页
    public function images()
    {
        $this->assign('pid', 0);
        return view();
    }

    //获取图片列表
    public function get_image_list()
    {
        $limit = I('?limit') ? I('limit') : 18;
        $attach = new SystemAttachment();
        $map = [];
        I('?pid') && I('pid') && $map['pid'] = I('pid');
        $lists = $attach->where($map)->order('att_id desc')->paginate($limit);
        return djson(0, 'ok', $lists);
    }

    //获取分类
    public function get_image_cate()
    {
        $cate = new SystemAttachmentCategory();
        $map = [];
        if (I('?key') && I('key')) {
            $map['name'] = ['like', '%' . I('key') . '%'];
        } else {
            $map['pid'] = 0;
        }
        return djson(0, 'ok', $cate->where($map)->select());
    }

    /**
     * 图片管理上传图片
     */
    public function upload()
    {
        $pid = I('pid', session('pid'));
        $style = intval(C('UPLOAD_STYLE'));
        switch ($style) {
            case 1:
                //本地
                $upret = $this->uploadLocal();
                break;
            case 2:
                //七牛
                $upret = $this->uploadQiniu();
                break;
            default:
                return djson(1, '暂时不支持的上传方式');
        }
        if ($upret['errno'] != 0) {
            return djson(1, $upret['errmsg']);
        }
        $updata = $upret['data'];
        if (SystemAttachment::attachmentAdd($updata['name'], $updata['size'], $updata['att_type'], $updata['url'], $updata['url'], $pid, $style, time())) {
            return djson(0, '上传成功');
        }
        return djson(1, '上传失败');
    }

    //上传本地
    private function uploadLocal()
    {
        //获取表单上传文件
        $file = request()->file('file');
        if (empty($file)) {
            return rjson(1, '请选择上传文件');
        }
        $file_info = $file->getInfo();
        //移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['size' => C('DOWNLOAD_UPLOAD.maxSize'), 'ext' => C('DOWNLOAD_UPLOAD.exts')])->move(C('DOWNLOAD_UPLOAD.movePath'));
        if ($info) {
            $result['name'] = $file_info['name'];
            $result['domain'] = $_SERVER['HTTP_HOST'];
            $result['url'] = HTTP_TYPE . $_SERVER['HTTP_HOST'] . C('DOWNLOAD_UPLOAD.rootPath') . $info->getSaveName();
            $result['size'] = $file_info['size'];
            $result['att_type'] = $file_info['type'];
            return rjson(0, '上传成功', $result);
        } else {
            //上传失败获取错误信息
            return rjson(1, $file->getError());
        }
    }


    //上传单个文件 用uploadify
    private function uploadQiniu()
    {
        ini_set('memory_limit', '3072M');
        set_time_limit(0);
        $file = $_FILES['file'];
        if (empty($file)) {
            return rjson(1, '请选择上传文件');
        }
        $upfile = array(
            'name' => 'file',
            'fileName' => time() . Random::alnum(6) . substr($file['name'], strrpos($file['name'], '.')),
            'fileBody' => file_get_contents($file['tmp_name'])
        );
        $this->qiniu = new QiniuStorage(C('QINIU'));
        $config = array('Expires' => 3600, 'saveName' => '', 'custom_fields' => []);
        $result = $this->qiniu->upload($config, $upfile);
        if ($result) {
            $result['name'] = $file['name'];
            $result['domain'] = C('QINIU')['domain'];
            $result['url'] = C('QINIU')['prefix'] . '://' . C('QINIU')['domain'] . DS . $result['key'];
            $result['size'] = $file['size'];
            $result['att_type'] = $file['type'];
            return rjson(0, '上传成功', $result);
        } else {
            return rjson(1, '上传失败', array(
                'error' => $this->qiniu->error,
                'errorStr' => $this->qiniu->errorStr
            ));
        }
    }


    /**
     * ajax 提交删除
     */
    public function delete()
    {
        $imageid = I('imageid/a');
        if (empty($imageid))
            return djson(1, '还没选择要删除的图片呢?');
        foreach ($imageid as $v) {
            if ($v) self::deleteimganddata($v);
        }
        return djson(0, '删除成功');
    }

    /**删除图片和数据记录
     * @param $att_id
     */
    public function deleteimganddata($att_id)
    {
        $attinfo = SystemAttachment::get($att_id);
        if ($attinfo) {
            SystemAttachment::where('att_id', $att_id)->delete();
        }
    }

    /**
     * 移动图片分类显示
     */
    public function moveimg($imgaes)
    {
        $cate = new SystemAttachmentCategory();
        $list = $cate->select();
        $list = collection($list)->toArray();
        $list_all = $cate->toFormatTree($list);
        $this->assign('list', $list_all);
        $this->assign('imgaes', $imgaes);
        return view();
    }

    /**
     * 移动图片分类操作
     */
    public function moveImgCecate()
    {
        $data['pid'] = I('pid');
        $data['imgaes'] = I('imgaes');
        if ($data['imgaes'] == '') return djson(1, '请选择图片');
        if (!$data['pid']) return djson(1, '请选择分类');
        $res = SystemAttachment::where('att_id', 'in', $data['imgaes'])->update(['pid' => $data['pid']]);
        if ($res)
            return djson(0, '移动成功');
        else
            return djson(1, '移动失败');
    }

    /**
     * ajax 添加分类
     */
    public function addcate($id = 0)
    {
        $id = I('id');
        $pid = I('pid');

        //查找信息
        $info = SystemAttachmentCategory::get($id);

        $cate = new SystemAttachmentCategory();
        $list = $cate->where(['pid' => 0])->select();
        $list = collection($list)->toArray();
        $list_all = $cate->toFormatTree($list);

        if ($info && count($info['child']) > 0) {
            $list_all = [];
        }

        $this->assign('list', $list_all);
        $this->assign('info', $info);
        return view();
    }

    /**
     * 添加分类
     */
    public function saveCate()
    {
        $data['pid'] = I('pid');
        $data['name'] = I('name');
        if (empty($data['name']))
            return djson(1, '分类名称不能为空!');

        if (I('id')) {
            $res = SystemAttachmentCategory::where(['id' => I('id')])->update($data);
            if ($res)
                return djson(0, '更新成功!');
            else
                return djson(1, '更新失败!');
        } else {
            $res = SystemAttachmentCategory::create($data);
            if ($res)
                return djson(0, '添加成功!');
            else
                return djson(1, '添加失败!');
        }

    }

    /**
     * 编辑分类
     */
    public function editcate($id)
    {
        $Category = Category::get($id);
        if (!$Category) return Json::fail('数据不存在!');
        $formbuider = [];
        $formbuider[] = Form::hidden('id', $id);
        $formbuider[] = Form::select('pid', '上级分类', (string)$Category->getData('pid'))->setOptions(function () use ($id) {
            $list = Category::getCateList();
            $options = [['value' => 0, 'label' => '所有分类']];
            foreach ($list as $id => $cateName) {
                $options[] = ['label' => $cateName['html'] . $cateName['name'], 'value' => $cateName['id']];
            }
            return $options;
        })->filterable(1);
        $formbuider[] = Form::input('name', '分类名称', $Category->getData('name'));
        $jsContent = <<<SCRIPT
parent.SuccessCateg();
parent.layer.close(parent.layer.getFrameIndex(window.name));
SCRIPT;
        $form = Form::make_post_form('编辑分类', $formbuider, Url::buildUrl('updateCate'), $jsContent);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 更新分类
     * @param $id
     */
    public function updateCate($id)
    {
        $data = Util::postMore([
            'pid',
            'name'
        ]);
        if ($data['pid'] == '') return Json::fail('请选择父类');
        if (!$data['name']) return Json::fail('请输入分类名称');
        Category::edit($data, $id);
        return Json::successful('分类编辑成功!');
    }

    /**
     * 删除分类
     */
    public function deletecate($id)
    {
        $chdcount = SystemAttachmentCategory::where('pid', $id)->count();
        if ($chdcount) return djson(1, '有子栏目不能删除');
        $chdcount = SystemAttachment::where('pid', $id)->count();
        if ($chdcount) return djson(1, '栏目内有图片不能删除');
        if (SystemAttachmentCategory::where(['id' => $id])->delete())
            return djson(0, '删除成功!');
        else
            return djson(1, '删除失败');
    }
}