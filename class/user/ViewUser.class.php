<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目： 5anet(BBS)
 * 文件： class/user/ViewUser.class.php
 *
 * 查看用户的个人信息
 *
 * PHP Version 5
 *
 * @package:   class.user
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: ViewUser.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';
include_once FUNCTION_PATH.'ConvertString.fun.php';



//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ViewUser.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ViewUser.lang.php';
}

class ViewUser extends BaseAction {

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
    * 查看用户的个人信息
    * @param:  NULL
    * @return: NULL
    * @access: public
    */

   public function run() {
      //取得用户的id
      $user_id = $this->getParameterFromGET('id');


      

      if ( !$user_id && $user_id != 0  ) {
         $this->AlertAndBack(VU_USER_ID_IS_EMPTY);
         return;
      }

      if ( $user_id == 0 ) {
         $this->AlertAndBack(VU_USER_IS_SYSTEM);
         return;
      }


      if ( !UserUtil::isExists($this->db, $user_id) ) {
         $this->AlertAndBack(VU_USER_IS_NOT_EXISTS);
         return;
      }


      $smarty = $this->getSmarty();

      //back url
      $back_url = 'index.php?module=user&action=view&id='.$user_id;
      $back_url = base64_encode($back_url);
      $smarty->assign('backurl', $back_url);


      //assign user id
      $smarty->assign('user_id', $user_id);

      //用户名
      $user_name = UserUtil::getUserNameById($this->db, $user_id);
      $smarty->assign('view_user_name', $user_name);

      //用户所在的组
      $sql = 'select b.group_name from base_user_info as a join sys_group as b on '.
         ' a.group_dep = b.id where a.id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      $smarty->assign('user_roles', $rows['group_name']);

      //判断用户是否在线
      $sql = 'select count(*) as num from online_user where user_name =? ';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($user_name));
      $rows= $res->FetchRow();

      if ( $rows['num'] ) {
         $smarty->assign('user_is_online', 1);
      } else {
         $smarty->assign('user_is_online', 0);
      }


      //求用户的头像
      $user_header = UserUtil::getUserHeader($this->db, $user_id);
      $smarty->assign('head_url', $user_header);

      $sql = 'select user_gender,user_birthday, public_birthday, user_email, public_user_email, '.
         'user_website, public_website, register_date, user_icq, public_user_icq, user_AIM, '.
         'public_user_AIM, user_msn, public_user_msn, user_yahoo, public_user_yahoo,user_skype, '.
         ' public_user_skype, user_qq, public_user_qq, user_hometown, user_favor, user_sign '.
         ' from base_user_info where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      //性别
      $smarty->assign('user_sex', $rows['user_gender']);

      //生日
      if ( $rows['public_birthday'] ) {
         $smarty->assign('user_birthday', $rows['user_birthday']);
      } else {
         $smarty->assign('user_birthday', VU_NOT_PUBLIC);
      }

      //电子邮件
      if ( $rows['public_user_email'] ) {
         $smarty->assign('user_email', $rows['user_email']);
      } else {
         $smarty->assign('user_email', VU_NOT_PUBLIC);
      }

      //个人网站
      if ( $rows['public_website'] ) {
         $smarty->assign('user_website', $rows['user_website']);
      } else {
         $smarty->assign('user_website', VU_NOT_PUBLIC);
      }

      //注册日期
      $smarty->assign('user_register_date', $rows['register_date']);

      //ICQ
      if ( $rows['public_user_icq'] ) {
         $smarty->assign('user_icq', $rows['user_icq']);
      } else {
         $smarty->assign('user_icq', VU_NOT_PUBLIC);
      }

      //AIM
      if ( $rows['public_user_AIM'] ) {
         $smarty->assign('user_aim', $rows['user_AIM']);
      } else {
         $smarty->assign('user_aim', VU_NOT_PUBLIC);
      }

      //MSN
      if ( $rows['public_user_msn'] ) {
         $smarty->assign('user_msn', $rows['user_msn']);
      } else {
         $smarty->assign('user_msn', VU_NOT_PUBLIC);
      }

      //Yahoo
      if ( $rows['public_user_yahoo'] ) {
         $smarty->assign('user_yahoo', $rows['user_yahoo']);
      } else {
         $smarty->assign('user_yahoo', VU_NOT_PUBLIC);
      }

      //skype
      if ( $rows['public_user_skype'] ) {
         $smarty->assign('user_skype', $rows['user_skype']);
      } else {
         $smarty->assign('user_skype', VU_NOT_PUBLIC);
      }

      //QQ
      if ( $rows['public_user_qq'] ) {
         $smarty->assign('user_qq', $rows['user_qq']);
      }

      //hometown
      $smarty->assign('user_home_town', $rows['user_hometown']);
      //user favor
      $smarty->assign('user_favor', $rows['user_favor']);
      //user sign
      $smarty->assign('user_sign', ConvertString($rows['user_sign'], ROOT_URL, IMAGE_URL.'express/'));


      //用户的发帖数
      $topic_number = UserUtil::getUserCreateTopicNumber($this->db, $user_id);
      $smarty->assign('user_topic_number', $topic_number);


      $smarty->display('viewuser.tmpl');

   }



}

?>
