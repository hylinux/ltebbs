<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/SaveMailAndPasswd.class.php
 *
 * 保存用户的新密码和新的邮箱
 *
 * PHP Version 5
 *
 * @package:   class.user
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: SaveMailAndPasswd.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/SaveMailAndPasswd.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/SaveMailAndPasswd.lang.php';
}


class SaveMailAndPasswd extends BaseAction {

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
    * 保存用户的邮件和用户的密码
    * @param:  null
    * @return: null
    * @access: public
    */
   public function run() {
      //求得用户的id
      $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);

      /**
       * 取得各种参数
       */
      
      //用户的email
      $user_email = $this->getParameterFromPOST('user_email');

      if ( !$user_email ) {
         $this->AlertAndBack(SM_EMAIL_IS_NULL);
         return;
      }

      if ( strlen($user_email) > 85 ) {
         $this->AlertAndBack(SM_EMAIL_IS_TOO_LONGER);
         return;
      }

      //查询用户原来的邮件
      $sql = 'select user_email from base_user_info where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      $original_email = $rows['user_email'];

      $is_change_email = 0;
      if ( strtolower($original_email) != strtolower($user_email) ) {
         //更改邮件
         //要查查看看这个邮件是否已经被使用过了。
         $sql = 'select count(*) as num from base_user_info where lower(user_email)=? and id!= ?';
         $sth = $this->db->Prepare($sql);
         $res = $this->db->Execute($sth, array(strtolower($user_email), $user_id));
         $rows = $res->FetchRow();

         if (  $rows['num'] > 0 ) {
            $this->AlertAndBack(SM_EMAIL_IS_EXISTS);
            return;
         } else {
            $is_change_email = 1;
         }
      }

      //收集用户的新密码
      $public_email = $this->getParameterFromPOST('public_email');

      if ( $public_email != 1 and $public_email != 0 ) {
         $public_email = 1;
      }

      $new_passwd = $this->getParameterFromPOST('new_passwd');
      $check_passwd = $this->getParameterFromPOST('check_passwd');

      if ( $new_passwd ) {
         //如果用户填写了这个，则表明需要更改密码
         if ( strlen($new_passwd) < 6 ) {
            $this->AlertAndBack(SM_PASSWD_TOO_SHORT);
            return;
         }

         if ( $new_passwd != $check_passwd ) {
            $this->AlertAndBack(SM_PASSWD_CAN_NOT_PASS);
            return;
         }
      }

      if ( $new_passwd ) {
         $new_passwd = md5($new_passwd);
      }

      $sql = 'update base_user_info set user_email=?, public_user_email=? ';

      if ( $new_passwd ) {
         $sql .= ', user_password=? ';
      }

      $sql .= ' where id=?';

      echo $new_passwd."<br>";
      $sth = $this->db->Prepare($sql);
      if ( $new_passwd ) {
         $this->db->Execute($sth, array(
            $user_email, $public_email, $new_passwd, $user_id));
      } else {
         $this->db->Execute($sth, array(
            $user_email, $public_email, $user_id));
      }



      if ( $this->db->ErrorNo() ) {
         $this->AlertAndBack($this->db->ErrorMsg());
         return;
      }

      $this->forward('index.php?module=user&action=mailandpasswd');
      return;
   }



}

?>
