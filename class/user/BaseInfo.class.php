<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/BaseInfo.class.php
 *
 * 用户基本信息的控制面板
 *
 * PHP Version 5
 *
 * @package:   class.user
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: BaseInfo.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/BaseInfo.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/BaseInfo.lang.php';
}


class BaseInfo extends BaseAction {

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

      //查询用户的个人信息
      $sql = 'select user_gender, user_birthday, public_birthday, user_website, public_website, '.
         'user_icq, public_user_icq, user_AIM, public_user_AIM, user_msn, public_user_msn, '.
         'user_yahoo, public_user_yahoo, user_skype, public_user_skype, user_qq, public_user_qq, '.
         'user_hometown, user_favor from base_user_info where id=?';

      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($user_id));

      $rows = $res->FetchRow();

      $smarty->assign('user_gender', $rows['user_gender']);
      $smarty->assign('user_birthday', $rows['user_birthday']);
      $smarty->assign('public_birthday', $rows['public_birthday']);
      $smarty->assign('user_website', $rows['user_website']);
      $smarty->assign('public_website', $rows['public_website']);
      $smarty->assign('user_icq', $rows['user_icq']);
      $smarty->assign('public_icq', $rows['public_user_icq']);
      $smarty->assign('user_aim', $rows['user_AIM']);
      $smarty->assign('public_aim', $rows['public_user_AIM']);
      $smarty->assign('user_msn', $rows['user_msn']);
      $smarty->assign('public_msn', $rows['public_user_msn']);
      $smarty->assign('user_yahoo', $rows['user_yahoo']);
      $smarty->assign('public_yahoo', $rows['public_user_yahoo']);
      $smarty->assign('user_skype', $rows['user_skype']);
      $smarty->assign('public_skype', $rows['public_user_skype']);
      $smarty->assign('user_qq', $rows['user_qq']);
      $smarty->assign('public_qq', $rows['public_user_qq']);
      $smarty->assign('user_hometown', $rows['user_hometown']);
      $smarty->assign('user_favor', $rows['user_favor']);





      $smarty->display('baseinfo.tmpl');


   }

}


?>
