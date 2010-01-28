<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/ResetPasswd.class.php
*
*  重置用户密码并给用户发送邮件
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: ResetPasswd.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/ResetPasswd.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/ResetPasswd.lang.php';
}

class ResetPasswd extends AdminBaseAction {

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
   *  run this action
   *  @param:  NULL
   *  @return: NULL
   *  @access: public
   */
   public function run() {
      //取得用户的id
      $id = $this->getParameterFromGET('id');

      //没有指定用户
      if ( !$id ) {
         $this->AlertAndBack(USER_IS_EMPTY);
         return;
      }

      //验证用户是否存在
      $sql = 'select count(*) as num from base_user_info where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($id));

      $rows = $res->FetchRow();

      if ( !$rows['num'] ) {
         //用户不存在
         $this->AlertAndBack(USER_IS_NOT_EXISTS);
         return;
      }

      //随机生一个六位数的密码

      /**
      *  定义可用的字符集
      */
      $check_char = array (
         0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9,
         10=>'a', 11=>'b', 12=>'c', 13=>'d', 14=>'e', 15=>'f', 16=>'g',
         17=>'h', 18=>'i', 19=>'j', 20=>'k', 21=>'L', 22=>'m', 23=>'n',
         24=>'o', 25=>'p', 26=>'q', 27=>'r', 28=>'s', 29=>'t', 30=>'w',
         31=>'v', 32=>'y', 33=>'x', 34=>'z'
      );

      $check_stirng = "";

      for($i=1; $i<=6; $i++ ) {
         $j = rand(1, 34);
         $check_string .= $check_char[$j];
      } 

      $new_passwd = md5($check_string);

      //更新用户的密码
      $sql = 'update base_user_info set user_password=? where id=?';
      $sth = $this->db->Prepare($sql);
      $this->db->Execute($sth, array($new_passwd, $id));

      //取得用户邮件
      $sql = 'select user_name, user_email from base_user_info where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($id));
      $rows = $res->FetchRow();
      $user_email = $rows['user_email'];
      $user_name = $rows['user_name'];

      $content = PASSWD_HAD_BEEN_RESET."\n";
      $content .= YOU_LOGIN_NAME.$user_name."\n";
      $content .= YOU_LOGIN_PASSWD.$check_string."\n";

      $headers = "To:".$to_address."\r\n";
      $headers .= "From:".WEBSITE_EMAIL."\r\n";

      @mail($user_email, EMAIL_TITLE, $content, $headers);

      $this->AlertAndBack(PASSWD_RESET);


      return;
   }
}

?>
