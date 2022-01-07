<?php

namespace Emails;
/**
 * Created by PhpStorm.
 * User: 13788
 * Date: 2017/8/17
 * Time: 9:09
 *调用
 *  vendor("PHPMailer.Phpmailers");
 * $email = new \Phpmailers();
 * $ret = $email->sendMail("1378841192@qq.com", "title", 'content');
 */
include_once("class.phpmailer.php");
include_once("class.smtp.php");


class Phpmailers
{
    private $MAIL_HOST = '';//smtp服务器的名称
    private $MAIL_USERNAME = '';//你的邮箱名
    private $MAIL_FROM = '';
    private $MAIL_FROMNAME = '';
    private $MAIL_PASSWORD = '';
    private $MAIL_CHARSET = 'utf-8';
    private $PORT = 465;
    private $DEBUG = 0;
    public $errmsg;

    public function __construct($options = [])
    {
        $this->MAIL_HOST = isset($options['mail_smtp_host']) ? $options['mail_smtp_host'] : '';
        $this->MAIL_USERNAME = isset($options['mail_smtp_user']) ? $options['mail_smtp_user'] : '';
        $this->MAIL_PASSWORD = isset($options['mail_smtp_pass']) ? $options['mail_smtp_pass'] : '';
        $this->MAIL_FROM = isset($options['mail_smtp_user']) ? $options['mail_smtp_user'] : '';
        $this->MAIL_FROMNAME = isset($options['mail_smtp_name']) ? $options['mail_smtp_name'] : '';
        $this->DEBUG = isset($options['debug']) ? $options['debug'] : 0;
        $this->PORT = isset($options['mail_smtp_port']) ? $options['mail_smtp_port'] : 465;
    }

    /**
     * @param $to
     * @param $title
     * @param $content
     * 邮箱发送
     */
    function sendMail($to, $title, $content)
    {
        //实例化PHPMailer核心类
        $mail = new \PHPMailer();
        //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
        $mail->SMTPDebug = $this->DEBUG;
        //使用smtp鉴权方式发送邮件
        $mail->isSMTP();
        //smtp需要鉴权 这个必须是true
        $mail->SMTPAuth = true;
        //链接qq域名邮箱的服务器地址
        $mail->Host = $this->MAIL_HOST;
        //设置使用ssl加密方式登录鉴权
        $mail->SMTPSecure = 'ssl';
        //设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
        $mail->Port = $this->PORT;
        $mail->CharSet = $this->MAIL_CHARSET;
        //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $mail->FromName = $this->MAIL_FROMNAME;
        //smtp登录的账号 这里填入字符串格式的qq号即可
        $mail->Username = $this->MAIL_USERNAME;
        //smtp登录的密码 使用生成的授权码（就刚才叫你保存的最新的授权码）
        $mail->Password = $this->MAIL_PASSWORD;
        //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
        $mail->From = $this->MAIL_FROM;
        //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
        $mail->isHTML(true);
        //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
        $mail->addAddress($to, 'dashan');
        $mail->Subject = $title;
        $mail->Body = $content;
        $status = $mail->send();
        $this->errmsg = $mail->ErrorInfo;
        //简单的判断与提示信息
        if ($status) {
            return true;
        } else {
            return false;
        }
    }
}