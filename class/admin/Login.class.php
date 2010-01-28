<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/Login.class.php
*
*  accept user's login request
*
*  this module will do these things:
*  1. accept user's login request.
*  2. redirect the user's request to the index.
*
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: Login.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/Login.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/Login.lang.php';
}

class Login extends AdminBaseAction {
   /**
   *  accept user's login request
   *  @param:  NULL
   *  @return: NULL
   *  @access; public
   */
   public function run() {
      //if user had logined the system
      if ( isset($_SESSION['adminuser']) ) {
         $this->forward('index.php');
         return;
      }

      //get the value that user input by input box
      $username = $this->getParameterFromPost('username');
      if ( !$username ) {
         $this->AlertAndBack(LOGIN_USER_NAME_IS_EMPTY);
         return;
      }

      $password = $this->getParameterFromPost('userpass');
      if ( !$password ) {
         $this->AlertAndBack(LOGIN_USER_PASS_IS_EMPTY);
         return;
      }

      // check whether user is exits.
      $db = $this->getDB();

      $sql = "select count(*) as num from sys_admin where lower(user_name)=?";
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array(strtolower($username)));
      $rows = $res->FetchRow();
      
      if ( !$rows['num'] ) {
         $this->AlertAndBack(LOGIN_USER_IS_NOT_EXISTS);
         return;
      }

      // if user is exists. so, we must select his password.
      $sql = "select user_passwd from sys_admin where lower(user_name)=?";
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array(strtolower($username)));
      $rows = $res->FetchRow();

      $select_password = $rows['user_passwd'];

      $password = md5($password);
      
      if ( $password != $select_password) {
         //the password is not correct
         $this->AlertAndBack(LOGIN_PASSWD_IS_NOT_CORRECT);
         return;
      }


      $_SESSION['adminuser'] = $username;

      $this->forward('index.php');
   }
}

?>
