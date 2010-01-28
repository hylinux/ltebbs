<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet.com
*  File Name   :  class/user/Logout.class.php
*
*  user logout system
*
*  PHP Version 5
*
*  @package:   class.default
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: Logout.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      @Date:$
*/

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/Logout.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/Logout.lang.php';
}

class Logout extends BaseAction {
   /**
   *  run this action
   *  @param:  NULL
   *  @return: NULL
   *  @access: public
   */
   public function run() {
        global $global_config_web_domain;

      $db = $this->getDB();

      if ( !isset($_SESSION['user']) ) {
         $this->forward('index.php');
      }

      setcookie('user', '', time()-3600, '/', $global_config_web_domain);

      $user_name = $_SESSION['user']['name'];

      //user logout
      unset($_SESSION['user']);

      //记录用户的登出动作
      $user_id = UserUtil::getUserId($db, $user_name);

      $sql = 'select count(*) as num from user_last_time_logout where user_id=?';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($user_id));

      $rows = $res->FetchRow();

      $now = time();
      if ( $rows['num'] ) {
            $update_sql = 'update user_last_time_logout set last_time=? where user_id=?';
            $update_sth = $db->Prepare($update_sql);
            $db->Execute($update_sth, array($now, $user_id));
      } else {
            $insert_sql = 'insert into user_last_time_logout (user_id, '.
               ' last_time) values (?, ?)';
            $insert_sth = $db->Prepare($insert_sql);
            $db->Execute($insert_sth, array($user_id, $now));
      }
      
      $session_id = session_id();
      $sql = 'update online_user set user_name = ? where session_id=?';
      $stmt = $db->Prepare($sql);
      $db->Execute($stmt, array($session_id, $session_id));

      $this->forward('index.php');
      return;
   }
}

?>
