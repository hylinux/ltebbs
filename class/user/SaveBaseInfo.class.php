<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/SaveBaseInfo.class.php
 *
 * 保存用户的基本信息
 *
 * PHP Version 5
 *
 * @package:   class.user
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: SaveBaseInfo.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/SaveBaseInfo.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/SaveBaseInfo.lang.php';
}


class SaveBaseInfo extends BaseAction {

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
    * 显示用户的基本信息
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {
      //求得用户的id
      $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);

      //取得各种参数
      //然后在对各个参数进行判断。
      //性别
      $user_gender = $this->getParameterFromPOST('gender');

      if ( !$user_gender )  {
         $user_gender = 'male';
      }

      if ( $user_gender != 'male' and $user_gender != 'female' ) {
         $user_gender = 'male';
      }

      //用户的生日
      $user_birthday = $this->getParameterFromPOST('user_birthday');
      $public_birthday = $this->getParameterFromPOST('public_birthday');

      if ( $public_birthday != 1 and $public_birthday != 0 ) {
         $public_birthday = 1;
      }
      //这里对于日期的判断需要做严格一些，但是我现在真的没有那么多的时间。
      //以后再补回来好了。
      
      $user_website = $this->getParameterFromPOST('user_website');
      $public_website = $this->getParameterFromPOST('public_website');

      if ( $public_website != 1 and $public_website != 0 ) {
         $public_website = 1;
      }


      $user_icq = $this->getParameterFromPOST('user_icq');
      $public_icq = $this->getParameterFromPOST('public_icq');

      if ( $public_icq != 1 and $public_icq != 0 ) {
         $public_icq = 1;
      }

      $user_aim = $this->getParameterFromPOST('user_aim');
      $public_aim = $this->getParameterFromPOST('public_aim');

      if ( $public_aim != 1 and $public_aim != 0 ) {
         $public_aim = 1;
      }

      $user_msn = $this->getParameterFromPOST('user_msn');
      $public_msn = $this->getParameterFromPOST('public_msn');

      if ( $public_msn != 1 and $public_msn != 0 ) {
         $public_msn = 1;
      }

      $user_yahoo = $this->getParameterFromPOST('user_yahoo');
      $public_yahoo = $this->getParameterFromPOST('public_yahoo');

      if ( $public_yahoo != 1 and $public_yahoo != 0 ) {
         $public_yahoo = 1;
      }

      $user_skype = $this->getParameterFromPOST('user_skype');
      $public_skype = $this->getParameterFromPOST('public_skype');

      if ( $public_skype != 1 and $public_skype != 0 ) {
         $public_skype = 1;
      }

      $user_qq = $this->getParameterFromPOST('user_qq');
      $public_qq = $this->getParameterFromPOST('public_qq');

      if ( $public_qq != 1 and $public_qq != 0 ) {
         $public_qq = 1;
      }

      $user_hometown = $this->getParameterFromPOST('user_hometown');

      if ( strlen($user_hometown) > 80 ) {
         $this->AlertAndBack(SB_HOMETOWN_TOO_LONGER);
         return;
      }

      //爱好
      $user_favor = $this->getParameterFromPOST('favor');

      if ( strlen($user_favor) > 150 ) {
         $this->AlertAndBack(SB_USER_FAVOR_TOO_LONGER);
         return;
      }


      //更新用户的基本资料
      $update_sql = 'update base_user_info set user_gender=?,user_birthday=?, '.
         'public_birthday=?, user_website=?, public_website=?, user_icq=?, '.
         'public_user_icq=?, user_AIM=?, public_user_AIM=?, user_msn=?, public_user_msn=?,'.
         'user_yahoo=?, public_user_yahoo=?, user_skype=?, public_user_skype=?, user_qq=?,'.
         'public_user_qq=?, user_hometown=?, user_favor=? where id=?';

      $this->db->debug = 1;
      $sth = $this->db->Prepare($update_sql);
      $this->db->Execute($sth, array(
         $user_gender, $user_birthday, $public_birthday, $user_website, $public_website, 
         $user_icq, $public_icq, $user_aim, $public_aim, $user_msn, $public_msn, $user_yahoo,
         $public_yahoo, $user_skype, $public_skype, $user_qq, $public_qq, $user_hometown, 
         $user_favor, $user_id
      ));

      if ( $this->db->ErrorNo() ) {
         $this->AlertAndBack($this->db->ErrorMsg());
         return;
      }

      $this->forward('index.php?module=user&action=baseinfo');

   }
}

?>
