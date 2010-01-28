<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/user/Login.class.php
*
*  accept user's login request
*
*  this module will do these things:
*  1. accept user's login request.
*  2. redirect the user's request to the index.
*
*  PHP Version 5
*  
*  @package:   class.user
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: Login.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/BaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/Login.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/Login.lang.php';
}

class Login extends BaseAction {
   /**
   *  accept user's login request
   *  @param:  NULL
   *  @return: NULL
   *  @access; public
   */
    public function run() {
      global $global_config_web_domain;
       //if user had logined the system
      if ( isset($_SESSION['user']) ) {
         $this->forward('index.php');
         return;
      }

      //if user require remeber user's login info
      $is_remeber = $this->getParameterFromPost('is_remeber');


      //get the value that user input by input box
      $username = $this->getParameterFromPost('username');
      if ( !$username ) {
         $this->AlertAndBack(LOGIN_USER_NAME_IS_EMPTY);
         return;
      }

      $password = $this->getParameterFromPost('password');
      if ( !$password ) {
         $this->AlertAndBack(LOGIN_USER_PASS_IS_EMPTY);
         return;
      }

      // check whether user is exits.
      $db = $this->getDB();

      $sql = "select count(*) as num from base_user_info where lower(user_name)=?";
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array(strtolower($username)));
      $rows = $res->FetchRow();
      
      if ( !$rows['num'] ) {
         $this->AlertAndBack(LOGIN_USER_IS_NOT_EXISTS);
         return;
      }

      //判断用户是否被锁定
      $sql = 'select status from base_user_info where lower(user_name)=?';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array(strtolower($username)));
      $rows = $res->FetchRow();

      if ( $rows['status'] ) {
         $this->AlertAndBack(USER_HAD_BEEN_LOCKED);
         return;
      }



      // if user is exists. so, we must select his password.
      $sql = "select id, user_password, user_header from base_user_info where lower(user_name)=?";
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array(strtolower($username)));
      $rows = $res->FetchRow();

      $select_password = $rows['user_password'];
      $userhead = $rows['user_header'];
      $user_id = $rows['id'];

      // do a query for user's lang and theme
      $sql = 'select user_lang, user_theme, user_local_time from user_setting where '
         .' user_id = ? ';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, $user_id);
      $rows = $res->FetchRow();

      $userlang = $rows['user_lang'];
      $usertheme = $rows['user_theme'];
      $usertime = $rows['user_local_time'];



      $password = md5($password);
      
      if ( $password != $select_password) {
         //the password is not correct
         $this->AlertAndBack(LOGIN_PASSWD_IS_NOT_CORRECT);
         return;
      }

      // the password is corrent
      $user_info = array(
         'name' => $username,
         'userhead'=>$userhead,
         'lang'   => $userlang,
         'theme'  => $usertheme,
         'localtime' => $usertime,
         'check'=>md5($password),
     );


      $_SESSION['user'] = $user_info;
      $cookie_time = 0;
      if ( $is_remeber == '1y'  ) {
        $cookie_time = time() + 60*60*24*365;
      } elseif($is_remeber == '1m' ) {
        $cookie_time = time() + 60*60*24*31;
      } elseif ( $is_remeber == '1d' ) {
        $cookie_time = time() + 60*60*24;
      } elseif ( $is_remeber == '1h' ) {
        $cookie_time = time() + 60*60;
      } else {
        $cookie_time = 0;
      }

      if ( $cookie_time ) {
          //发送cookie
          $str_user_info = serialize($user_info);
         setcookie('user', $str_user_info, $cookie_time, '/', $global_config_web_domain);
      }

      //更新在线用户的资料
      $sql = 'update online_user set user_name = ? where session_id=?';
      $stmt = $db->Prepare($sql);
      $db->Execute($stmt, array($username, session_id()));

      $this->forward('index.php');
   }
}

?>
