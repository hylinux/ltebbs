<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/email/SendEmail.class.php
 *
 * 发送邮件
 * 我们需要做一些判断
 * 1. 用户是否存在
 * 2. 用户是否愿意公开自己的邮件
 *
 *
 * PHP Version 5
 *
 * @package:   class.email
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: SendEmail.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/SendEmail.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/SendEmail.lang.php';
}


class SendEmail extends BaseAction {

   /**
    * 数据库的连接
    */
   public $db;


   /**
    * 构造函数
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function __construct() {
      $this->db = $this->getDB();
   }




   /**
    * 显示用户发送短信的界面
    */
   public function run() {
      $id = $this->getParameterFromPOST('id');

      if ( !$id ) {
         $this->AlertAndBack(SSE_USER_ID_IS_EMPTY);
         return;
      }

      //验证用户是否存在
      if ( !UserUtil::isExists($this->db, $id) ) {
         $this->AlertAndBack(SSE_USER_IS_NOT_EXISTS);
         return;
      }

      //查看用户是否公开有邮件。
      //如果没有，则不能向这个用户发送邮件
      $sql = 'select public_user_email from base_user_info where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($id));
      $rows = $res->FetchRow();

      if ( ! $rows['public_user_email'] ) {
         $this->AlertAndBack(SSE_USER_EMAIL_IS_NOT_PUBLIC);
         return;
      }

      //标题
      $title = $this->getParameterFromPOST('title');

      if ( !$title ) {
         $title = SSE_NO_TITLE;
      }

      $content = $this->getParameterFromPOST('content');

      //查询邮件
      $sql = 'select user_email from base_user_info where user_name=?';
      $sth = $this->db->prepare($sql);
      $res = $this->db->Execute($sth, array($_SESSION['user']['name']));
      $rows = $res->FetchRow();

      $from_address = $rows['user_email'];

      $sql = 'select user_email from base_user_info where id=?';
      $sth = $this->db->prepare($sql);
      $res = $this->db->Execute($sth, $id);
      $rows = $res->FetchRow();

      $to_address = $rows['user_email'];

      $headers = "To:".$to_address."\r\n";
      $headers .= "From:".$from_address."\r\n";


      mail($to_address, $title, $content, $headers);

      $back_url = $this->getParameterFromPOST('backurl');

      if ( $back_url ) {
         $back_url = base64_decode($back_url);
         $this->TipsAndForward(
             SE_SEND_EMAIL_SUCCESS
             ,
             
             $back_url);
      } else {
          $this->TipsAndForward(
                SE_SEND_EMAIL_SUCCESS,
              'index.php?module=user');
      }

   }

}

?>
