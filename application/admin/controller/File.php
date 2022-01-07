<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-14
 * Time: 14:41
 */

namespace app\admin\controller;

/**
 
 **/
class File extends Base
{
    /**
     * 单文件上传
     */
    public function upload()
    {
        //获取表单上传文件
        $file = request()->file('file');
        if (empty($file)) {
          return $this->error('请选择上传文件');
        }
        //移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['size' => C('DOWNLOAD_UPLOAD.maxSize'), 'ext' => C('DOWNLOAD_UPLOAD.exts')])->move(C('DOWNLOAD_UPLOAD.movePath'));
        if ($info) {
            return djson(0, '上传成功', C('DOWNLOAD_UPLOAD.rootPath') . $info->getSaveName());
        } else {
            //上传失败获取错误信息
            return djson(1, $file->getError());
        }
    }

    /**
     * 多文件上传
     */
    public function uploads()
    {
        // 获取表单上传文件
        $files = request()->file('files');
        $imginfo = array();
        foreach ($files as $file) {
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->validate(['size' => C('DOWNLOAD_UPLOAD.maxSize'), 'ext' => C('DOWNLOAD_UPLOAD.exts')])->move(C('DOWNLOAD_UPLOAD.movePath'));
            if ($info) {
                // 成功上传后 获取上传信息
                array_push($imginfo, C('DOWNLOAD_UPLOAD.rootPath') . $info->getSaveName());
            } else {
                // 上传失败获取错误信息
                return djson(0, $file->getError());
            }
        }
        return djson(0, '文件上传成功', $imginfo);
    }
}