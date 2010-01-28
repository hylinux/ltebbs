<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/ShowBBSOption.class.php
 *
 * 显示用户的个人论坛选项
 *
 * PHP Version 5
 *
 * @package:   class.user
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: ShowBBSOption.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ShowBBSOption.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ShowBBSOption.lang.php';
}


class ShowBBSOption extends BaseAction {

   /**
    * 数据库的连接
    */
   public $db;

   /**
    * 系统支持的语言
    */
   private $system_lang = array (
      'zh');

   /**
    * 系统支持的theme
    */
   private $system_theme = array (
       'default'=>BBSO_THEME_DEFAULT,
       'new'=>BBSO_THEME_BLUE,
       'newll'=>BBSO_THEME_RED,
   );

   /**
    * 系统支持的时区
    */
   private $system_time = array(
      'zh'
   );



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
    * 显示用户更改邮件和密码的界面
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {
      //求得用户的id
      $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);

      $smarty = $this->getSmarty();
      $user_name = $_SESSION['user']['name'];
      $smarty->assign('view_user_name', $user_name);

      //用户的所在组
      $sql = 'select b.group_name from base_user_info as a join sys_group as b on '.
         ' a.group_dep = b.id where a.id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      $smarty->assign('user_roles', $rows['group_name']);

      //查询用户现在的情况
      $sql = 'select user_lang, user_theme, user_whether_receive_email, receive_system_message '.
         ' from user_setting where user_id=?';

      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      $user_lang = 'zh';
      $user_theme = 'default';
      $user_receive_email = 1;
      $user_receive_message = 1;

      if ( $rows['user_lang'] ) {
            $user_lang = $rows['user_lang'];
      }

      if ( $rows['user_theme'] ) {
            $user_theme = $rows['user_theme'];
      }

      if ( $rows['user_whether_receive_email'] ) {
            $user_receive_email = $rows['user_whether_receive_email'];
      } else {
            $user_receive_email = 0;
      }

      if ( $rows['receive_system_message'] ) {
            $user_receive_message = $rows['receive_system_message'];
      } else {
            $user_receive_message = 0;
      }


      $smarty->assign('user_email_1', $user_receive_email);
      $smarty->assign('user_message_1', $user_receive_message);



      $user_lang_option = '';

      foreach ( $this->system_lang as $lang ) {
         $user_lang_option .= "<option value=\"".$lang."\"";

         if ( $user_lang == $lang ) {
            $user_lang_option .= " selected ";
         }

         $user_lang_option .= ">$lang</option>\n";
      }

      $smarty->assign('user_lang_option', $user_lang_option);


      $user_theme_option = '';

      foreach ( $this->system_theme as $theme_key => $theme_name ) {
         $user_theme_option .= "<option value=\"".$theme_key."\"";

         if ( $user_theme == $theme_key ) {
            $user_theme_option .= " selected ";
         }

         $user_theme_option .= ">$theme_name</option>\n";
      }

      $smarty->assign('user_theme_option', $user_theme_option);


         





      $smarty->display('bbsoption.tmpl');

   }

}

?>
