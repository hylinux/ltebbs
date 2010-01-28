<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/ShowEditInfo.class.php
 *
 * 显示编辑用户资料界面
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

include_once LIB_PATH.'fckeditor/fckeditor.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ShowEditInfo.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ShowEditInfo.lang.php';
}


class ShowEditInfo extends BaseAction {

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
    * 显示用户更改基本资料界面
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

      //查询用户现在的邮件
      $sql = 'select user_email, public_user_email from base_user_info where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($user_id));

      $rows = $res->FetchRow();

      $smarty->assign('user_email', $rows['user_email']);
      $smarty->assign('email_public', $rows['public_user_email']);



      //显示用户的头像
      $smarty->assign('user_header', UserUtil::getUserHeader($this->db, $user_id));


      //取得用户的性别
      $sql = 'select user_gender, user_hometown, year(user_birthday) as byear, '.
          'month(user_birthday) as bmonth, day(user_birthday) as bday, '.
          'user_website, public_website,public_birthday,'.
          'user_qq, public_user_qq, user_msn,public_user_msn, '.
          'user_skype, public_user_skype,'.
         'user_sign from base_user_info where id=?';
      $stmt = $this->db->prepare($sql);
      $res = $this->db->Execute($stmt, array($user_id));
      $rows = $res->FetchRow();

      $smarty->assign('user_gender', $rows['user_gender']);
      $smarty->assign('user_website', $rows['user_website']);
      $smarty->assign('public_birthday', $rows['public_birthday']);
      $smarty->assign('public_website', $rows['public_website']);
      $smarty->assign('user_hometown', $rows['user_hometown']);
      $smarty->assign('user_qq', $rows['user_qq']);
      $smarty->assign('public_user_qq', $rows['public_user_qq']);
      $smarty->assign('user_msn', $rows['user_msn']);
      $smarty->assign('public_user_msn', $rows['public_user_msn']);
      $smarty->assign('user_skype', $rows['user_skype']);
      $smarty->assign('public_user_skype', $rows['public_user_skype']);


      //使用fckeditor
      $fck = new FCKeditor("user_sign");
      $fck->BasePath = FCKEDITOR_BASEPATH;
      $fck->ToolbarSet = 'Basic';
      $fck->Height = '200';
      $fck->Value = get_magic_quotes_gpc()?stripslashes($rows['user_sign']):$rows['user_sign'];
      $fck->Width = '98%';
      $smarty->assign('fck', $fck);

      //年月日
      $year_option = '';
      for($i=1959; $i<=1997; $i++ ) {
          $year_option .= "<option value='$i'";
              if ( $rows['byear'] == $i ) {
                  $year_option .= ' selected ';
            }

          $year_option .= " >$i</option>\n";
      }

      $smarty->assign('birthday_year', $year_option);

      $month_option = '';
      for($i=1; $i<=12; $i++ ) {
          $month_option .= "<option value='$i' ";
          if( $rows['bmonth'] == $i ) {
              $month_option .= ' selected ';
          }

          $month_option .= ">$i</option>\n";

      }
      $smarty->assign('birthday_month', $month_option);

      $day_option = '';
      for($i=1; $i<=31; $i++ ) {
          $day_option .= "<option value='$i'";
          if( $rows['bday'] == $i ) {
            $day_option .= ' selected ';
          }

          $day_option .= ">$i</option>\n";

      }
      $smarty->assign('birthday_day', $day_option);


      //取得用户的扩展设置
      $sql = 'select user_lang, user_theme, user_whether_receive_email, receive_system_message '.
          ' from user_setting where user_id=?';
      $stmt = $this->db->prepare($sql);
      $res = $this->db->Execute($stmt, array($user_id));
      $rows = $res->FetchRow();

      $smarty->assign('user_theme', $rows['user_theme']);
      $smarty->assign('receive_system_email', $rows['user_whether_receive_email']);
      $smarty->assign('receive_system_message', $rows['receive_system_message']);


      $smarty->display('showeditinfo.tmpl');



   }


}

?>
