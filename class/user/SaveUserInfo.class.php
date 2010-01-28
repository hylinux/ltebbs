<?php
//vim:set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/SaveUserInfo.class.php
 *
 * 保存用户编辑后的资料
 *
 * PHP Version 5
 *
 * @package:   class.user
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: $
 * @date:      $Date: $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';


//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/SaveUserInfo.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/SaveUserInfo.lang.php';
}


class SaveUserInfo extends BaseAction {

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
    * 保存用户的编辑后的资料
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {
      //求得用户的id
      $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);

      //取得用户传入的参数
      $user_lang = $this->getParameterFromPOST('user_lang');

      if ( !$user_lang ) {
         $user_lang = 'zh';
      }

      $user_theme = $this->getParameterFromPOST('user_theme');

      if ( !$user_theme ) {
         $user_theme = 'new';
      }


      $user_recieve_email = $this->getParameterFromPOST('receive_email');

      if ( $user_recieve_email != 0 ) {
         $user_recieve_email = 1;
      }

      $user_recieve_message = $this->getParameterFromPOST('receive_message');

      if ( $user_recieve_message != 0 ) {
         $user_recieve_message = 1;
      }


      $sql = 'select count(*) as num from user_setting where user_id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      if ( $rows['num'] ) {
         $sql = 'update user_setting set user_lang=?, user_theme=?, user_whether_receive_email=?, '.
            'receive_system_message=? where user_id=?';

         $sth = $this->db->Prepare($sql);
         $this->db->Execute($sth, array($user_lang, $user_theme, $user_recieve_email,
            $user_recieve_message, $user_id));
      } else {
         $sql = 'insert into user_setting (user_lang, user_theme, user_whether_receive_email, '.
            'receive_system_message, user_id ) values (?, ?, ?, ?, ? ) ';
         $sth = $this->db->Prepare($sql);
         $this->db->Execute($sth, array($user_lang, $user_theme, $user_recieve_email,
            $user_recieve_message, $user_id));

      }

      //更新Session设置
      $_SESSION['user']['lang'] = $user_lang;
      $_SESSION['user']['theme'] = $user_theme;


      //取得原始密码
      $old_password = $this->getParameterFromPOST('olduserpass');
      $old_password = md5($old_password);
      $sql = 'select count(*) as num from base_user_info where id=? and '.
          'user_password = ? ';
      $stmt = $this->db->prepare($sql);
      $res = $this->db->Execute($stmt, array($user_id, $old_password));
      $rows = $res->FetchRow();

      if ( $rows['num'] ) {
          $new_password = $this->getParameterFromPOST('userpass');
          $re_password = $this->getParameterFromPOST('userpasscheck');

          if ( strlen($new_password) > 6 && strlen($new_password) < 16 &&
                 $new_password == $re_password
          ) {
              $update_sql = 'update base_user_info set user_password=? where id=?';
              $stmt = $this->db->prepare($update_sql);
              $this->db->Execute($stmt, array(md5($new_password), $user_id));
          }
      }


      //取得用户其他的参数
      //是否公开邮件
      $public_email = $this->getParameterFromPOST('public_email');
    
      if ( $public_email ) {
          $public_email = 1;
      } else {
          $public_email = 0;
      }

      //性别
      $user_gender = $this->getParameterFromPOST('register_gender');
      //家乡
      $user_hometown = $this->getParameterFromPOST('user_hometown');
      
      //生日
      $birthday_year = $this->getParameterFromPost('birthday_year');
      $birthday_month = $this->getParameterFromPost('birthday_month');
      $birthday_day = $this->getParameterFromPost('birthday_day');
      //检查日期的合法性
      $check_time = mktime(0, 0, 0, $birthday_month, $birthday_day, $birthday_year);

      if ( !$check_time || $check_time == -1 ) {
        $this->AlertAndForward(RE_CHECK_BIRTHDAY_NOT_VALIDATE,
            'index.php?module=user&action=register');
        return;
      }

      $birthday_date = $birthday_year.'-'.$birthday_month.'-'.$birthday_day;

      //是否公开生日
      $public_birthday = $this->getParameterFromPOST('public_birthday');
      if ( $public_birthday ) {
          $public_birthday = 1;
      } else {
          $public_birthday = 0;
      }

      //QQ
      $user_qq = $this->getParameterFromPOST('user_qq');
      //是否公开qq
      $public_user_qq = $this->getParameterFromPOST('public_user_qq');
      if ( $public_user_qq ) {
          $public_user_qq = 1;
      } else {
          $public_user_qq = 0;
      }

    //MSN
    $user_msn = $this->getParameterFromPOST('user_msn');
    $public_user_msn = $this->getParameterFromPOST('public_user_msn');
    if ( $public_user_msn ) {
          $public_user_msn = 1;
      } else {
          $public_user_msn = 0;
      }

    //skype
    $user_skype = $this->getParameterFromPOST('user_skype');
    $public_user_skype = $this->getParameterFromPOST('public_user_skype');
    if ( $public_user_skype ) {
          $public_user_msn = 1;
      } else {
          $public_user_skype = 0;
      }
    
    //个人网站
    $user_website = $this->getParameterFromPOST('user_website');
    $public_website = $this->getParameterFromPOST('public_website');
    if ( $public_website ) {
          $public_website = 1;
      } else {
          $public_website = 0;
      }

    //签名
    $user_sign = $this->getParameterFromPOST('user_sign');

    $update_sql = 'update base_user_info set public_user_email=?, user_gender=?, user_hometown=?, '.
        'user_birthday=?, public_birthday=?, user_qq=?, public_user_qq=?, user_msn=?, '.
        'public_user_msn=?, user_skype=?, public_user_skype=?, user_website=?, public_website=?, '.
        'user_sign=? where id=?';
    $stmt = $this->db->prepare($update_sql);
    $this->db->Execute(
        $stmt,
        array(
            $public_email, 
            $user_gender,
            $user_hometown,
            $birthday_date, 
            $public_birthday, 
            $user_qq,
            $public_user_qq,
            $user_msn,
            $public_user_msn,
            $user_skype,
            $public_user_skype,
            $user_website,
            $public_website,
            $user_sign,
            $user_id
            )
        );


    if ( $this->db->ErrorNo() ) {
        $this->AlertAndBack(SUI_SAVE_INFO_WARNING);
        return;
    }

    //更新成功
    $this->TipsAndForward(SUI_UPDATE_SUCCESS,
        'index.php?module=user&action=editinfo');

    return;

   }
}

?>
