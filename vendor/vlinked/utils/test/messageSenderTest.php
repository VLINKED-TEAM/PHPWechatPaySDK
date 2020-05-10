<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/31
 * Time: 15:54
 */

require "../vendor/autoload.php";

use VlinkedUtils\Message\Mail\Mailer;
use VlinkedUtils\Env;
use VlinkedUtils\Message\Mail\MailConfig;
use VlinkedUtils\Message\Mail\MailMessage;


$mailConfig = new MailConfig("smtp.126.com", Env::get("mail.username"), Env::get("mail.password"), 465, 'ssl');
try{
    $mailMessage = new MailMessage("1123", "测试",["735825608@qq.com","1589772615@qq.com"]);
}catch (Exception $get){
   echo  $get->getMessage();
}

try{
Mailer::sendMail($mailConfig, $mailMessage,true);
}catch (Exception $get){
    echo  $get->getMessage();
}