<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/main/Application.class.php
*  
*  The Control define File
*
*  This class is a control class. and in this class, we will parse the user's
*  Action and recored user's action, and receive user's request, and resend the 
*  user's request to a action component,and response user's request
*
*  PHP Version 5
*  
*  @package:   class/main
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: Application.class.php,v 1.3 2006-09-24 14:38:08 ghw Exp $
*  @date:      $Date: 2006-09-24 14:38:08 $
*/

//include the action map file
if ( file_exists(CONFIG_PATH.'action_map.inc.php') ) {
   include_once CONFIG_PATH.'action_map.inc.php';
} else {
   header("Content-type:text/html;charset=UTF-8");
   die("Can't include the action map file\n");
}

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/Application.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/Application.lang.php';
}


// include the database 
include_once CLASS_PATH.'main/DB.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';
include_once CLASS_PATH.'main/BaseAction.class.php';

include_once FUNCTION_PATH.'getCurrentDate.fun.php';



class Application extends BaseAction {
   /**
   *  The instantce refernce.
   *  @var: ref
   */
   private static $instance;

   /**
   *  The action map
   *  @var: ref
   */
   private $action_map;


   /**
   *  The construct
   *  In this, we only need a define a construct method
   *  to there is only one instance in this case
   */
   private function __construct() {/*{{{*/
      global $action_map;
      $this->action_map = $action_map;


   }/*}}}*/

   /**
   *  return the once Application instance
   *  @param:  NULL
   *  @return: ref
   *  @access: public
   *  @static
   */
   public static function getInstance() {/*{{{*/
      if ( !isset(self::$instance ) ) {
         $c = __CLASS__;
         self::$instance = new $c;
      }

      return self::$instance;
   }/*}}}*/

   /**
   *  run this Application
   *  @param:  NULL
   *  @return: NULL
   *  @access: public
   */
   public function run() {

      if ( $this->checkBlackList() ) {
         header("Content-type:text/html;charset=UTF-8");
         die(APP_USER_IN_BLACK_LIST);
         exit(0);
      }

      if ( $this->checkLocked() ) {
         header("Content-type:text/html;charset=UTF-8");
         die(APP_USER_HAD_BEEN_LOCKED);
         exit(0);
      }


      //get user's request module
      $module = $this->getModule();

      //get user's action 
      $action = $this->getAction($module);

      //Save the module name and action name into 
      //Session
      $_SESSION['module'] = $module;
      $_SESSION['action'] = $action;

      /**
      *  if the action need privileges. so
      *  we must double check the action
      */
      if ( $this->needPrivilege($module, $action) ) {
         if ( !$this->checkUserPrivilege($module, $action) ) {
            $this->ShowNoPrivilege();
            return;
         } /* else {
            // record user's action
            //$this->recordUserAction($module, $action);
         } */
      }


      if ( !$action ) {
         header("Content-type:text/html;charset=UTF-8");
         die(APP_ACTION_IS_NOT_EXISTS);
      }

      
      // include user's Action Complement
      // and run this complement
      if ( file_exists(CLASS_PATH.$module.'/'.
         $this->action_map[$module][$action]['class'].'.class.php') ) {
         include_once CLASS_PATH.$module.'/'.
            $this->action_map[$module][$action]['class'].'.class.php';
      } else {
         header("Content-type:text/html;charset=UTF-8");
         die("Can't find the module: $module, class: ".$this->action_map[$module][$action]['class'].
            " defined file.");
      }
      
      //get the action complement and new one instance 
      // and run this instance
      $class_type = $this->action_map[$module][$action]['class'];
      // get one class instantce
      //$this->getClassInstance($module, $class_type);
      $class = new $class_type;
      $class->run();

   }

   /**
    * get the method instantce
    * @param:  $class_type;
    * @return: $object 
    * @access: private
    */
   private function getClassInstance($module, $class_type) {

      if ( file_exists(OBJECT_CACHE_PATH.$module.'_'.$class_type) ) {
         $temp_str = file_get_contents(OBJECT_CACHE_PATH.$module.'_'.$class_type);
         $object = unserialize($temp_str);
      } else {
         $object = new $class_type;
         $temp_str = serialize($object);
         file_put_contents(OBJECT_CACHE_PATH.$module.'_'.$class_type, $temp_str);
      }
      return $object;
   } 


   /**
   *  get user's request module
   *  @param:  NULL
   *  @return: String
   *  @access: private
   */
   private function getModule() {
      $module = $_SERVER['REQUEST_METHOD'] == 'GET'?$_GET['module']:$_POST['module'];
      $module = strtolower($module);

      if ( !$module ) {
         $module = 'bbs';
      }

      if ( !array_key_exists($module, $this->action_map) ) {
         header("Content-type:text/html;charset=UTF-8");
         die(APP_MODULE_IS_NOT_EXISTS);
      }


      // Check the module status
      $db = DB::getConnection();

      $sql = 'select status from sys_modules where lower(module_name) =? ';
      $stmt = $db->prepare($sql);
      $res = $db->Execute($stmt, array(strtolower($module)));

      $rows = $res->FetchRow();

      $status = $rows['status'];

      /**
       *  $status == NULL or $status == 0 no limited
       *  $status == 1    need user had login in
       *  $status == 2    tempporary closed
       *  $status == 3    closed long time.
       */

      if ( !$status || $status == 0 ) {
         return $module;
      } else if ( $status == 1 ) {
         if ( $_SESSION['user'] ) {
            return $module;
         } else {
            $this->ShowLogin();
            return;
         }
      } else if ( $status == 2 ) {
         header("Content-type:text/html;charset=UTF-8");
         die(APP_SITE_TEMPORARY_CLOSED);
      } else if ( $status == 3 ) {
         header("Content-type:text/html;charset=UTF-8");
         die(APP_SITE_CLOSED);
      } else {
         header("Content-type:text/html;charset=UTF-8");
         die(APP_SITE_CLOSED);
      }
   }


   /**
   *  get user's request action
   *  @param:  $module String
   *  @return: $action String
   *  @access: private
   */
   private function getAction($module='default') {/*{{{*/
      $action = $_SERVER['REQUEST_METHOD'] == 'GET'?$_GET['action']:$_POST['action'];

      if ( !$action ) {
         $action = 'default';
      }

      // record user's action
      $this->recordUserAction($module, $action);

      // get the action descript
      $action_description = $this->action_map[$module][$action]['description'];

      //更新用户的状态
      $db = DB::getConnection();
      $update_sql = 'update online_user set current_status=? where session_id=?';
      $stmt = $db->prepare($update_sql);
      $db->Execute($stmt, 
         array($action_description,
         session_id()));


      if ( array_key_exists($action, $this->action_map["$module"]) ) {
         return $action;
      } else {
         return null;
      }
   }/*}}}*/
   
   /**
   *  Show the Login Interface
   *  @param:  NULL
   *  @return: NULL
   *  @access: private
   */
   private function ShowLogin() {/*{{{*/
      if ( file_exists(CLASS_PATH.'user/ShowLogin.class.php') ) {
         include_once CLASS_PATH.'user/ShowLogin.class.php';
      } else {
         header("Content-type:text/html;charset=UTF-8");
         die("Can't include the ShowLogin Class defined file");
      }

      $show_app = new ShowLogin();
      $show_app->run();
      exit;
   }/*}}}*/
   
   /**
   *  user's access log
   *  @param:  $module access module
   *  @param:  $actoin access action
   *  @return: NULL
   *  @access; private
   */
   private function recordUserAction($module='bbs', $action='default') {
      $ip = getenv('REMOTE_ADDR');

      
      if ( !$ip ) {
         $ip = 'not get';
      }

      $username = $_SESSION['user']['name'];

      if ( !$username ) {
         $username = $ip;
      }

      $now = time();

      $db = DB::getConnection();


      if ( !$db ) {
         header("Content-type:text/html;charset=UTF-8");
         die("Can't not connection the database");
      }

      // update online user status
      // after 45 minitues, if user have not any action, we must 
      // beleive this guys had gnone aways.
      
      //记录访问计数器
      $sql = 'select count(*) as num from online_user where session_id=?';
      $sth = $db->prepare($sql);
      $res = $db->Execute($sth, array( session_id() ) );
      $rows = $res->FetchRow();

      if ( !$rows['num'] ) {
         //如果没有记录则我们需要记录该用户的反问计数器
         $count = $rows['num'];

         $sql = 'update total_count set total_count=total_count + 1 where id=1';
         $db->execute($sql);

         //记录当天的访问数
         $sql = 'select count(*) as num from web_count where count_date=?';
         $date_array = getdate();
         $now_date = $date_array['year'].'-'.$date_array['mon'].'-'.$date_array['mday'];
         $sth = $db->prepare($sql);
         $res = $db->Execute($sth, array($now_date));
         $rows = $res->FetchRow();

         if ( !$rows['num'] ) {
            $sql = 'insert into web_count (count_date, access_number ) values (?, ? ) ';
            $sth = $db->prepare($sql);
            $db->execute($sth, array(
               $now_date, 1));
         } else {
            $sql = 'update web_count set access_number = access_number + 1 where '.
               ' count_date=? ';
            $sth = $db->prepare($sql);
            $db->execute($sth, array(
               $now_date));
         }

      }




      $session_id = session_id();
      $sql = 'select count(*) as num from online_user where lower(user_name)=?';
      $stmt = $db->prepare($sql);
      $res = $db->Execute($stmt, 
         array( isset($_SESSION['user'])?strtolower($_SESSION['user']['name']):strtolower($session_id)));
      $rows = $res->FetchRow();

      if ( $rows['num'] ) {
         $update_sql = 'update online_user set access_time=? where session_id=?';
         $update_stmt = $db->prepare($update_sql);
         $db->Execute($update_stmt, array(time(), $session_id));
      } else {
         $user_name = $session_id;
         if ( isset($_SESSION['user']) ) {
            $user_name = $_SESSION['user']['name'];
         }

         $ip = getenv('REMOTE_ADDR');
            
         $insert_sql = 'insert into online_user (user_name, user_ip, connect_time, 
            access_time, session_id) values (?, ?, ?, ?, ? )';
         $insert_stmt = $db->prepare($insert_sql);
         $db->Execute($insert_sql, array(
            $user_name,
            $ip, 
            time(),
            time(),
            $session_id
         ));
      }

      // recored these user for logout
      $now = time();
      $sql = 'select user_name from online_user where access_time + 2700 < ? ';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($now));

      while ( $rows = $res->FetchRow() ) {
         $user_id = UserUtil::getUserId($db, $rows['user_name']);
         $temp_sql = 'select count(*) as num from user_last_time_logout where user_id=?';
         $temp_sth = $db->Prepare($temp_sql);
         $temp_res = $db->Execute($temp_sth, array($user_id));
         $temp_rows = $temp_res->FetchRow();

         if ( $temp_rows['num'] ) {
            $update_sql = 'update user_last_time_logout set last_time=? where user_id=?';
            $update_sth = $db->Prepare($update_sql);
            $db->Execute($update_sth, array($now, $user_id));
         } else {
            $insert_sql = 'insert into user_last_time_logout (user_id, '.
               ' last_time) values (?, ?)';
            $insert_sth = $db->Prepare($insert_sql);
            $db->Execute($insert_sth, array($user_id, $now));
         }
      }

      // delte all user that had gnone away.
      
      
      $sql = 'delete from online_user where access_time + 2700 < ? ';
      $stmt = $db->prepare($sql);
      $db->Execute($stmt, array($now));


      //记录最大同时在线的人数
      $sql = 'select count(*) as num from online_user ';
      $res = $db->Execute($sql);
      $rows = $res->FetchRow();
      $online_user_number = $rows['num'];

      //看看目前最大的用户同时在线数
      $sql = 'select online from max_online_user where id=1';
      $res = $db->Execute($sql);
      $rows = $res->FetchRow();

      if ( !$rows['online'] ) {
         $sql = 'insert into max_online_user (id, online, online_date ) values (?, ?, ?)';
         $sth = $db->prepare($sql);
         $db->Execute($sth, 
            array(
               1,
               $online_user_number,
               getNoFormateCurrentDate(),
            ));
      } else if ( $rows['online'] < $online_user_number ) {
         $sql = 'update max_online_user set online=?, online_date=? where id=?';
         $sth = $db->prepare($sql);
         $db->Execute($sth, array(
            $online_user_number,
            getNoFormateCurrentDate(),
            1
         ));
      }

      



      return;
   }


   /**
   *  Check user's privileges
   *  @param:  $module  access module
   *  @param:  $action  access action
   *  @return: boolean
   *  @access: private
   */
   private function checkUserPrivilege($module, $action) {/*{{{*/
      if ( !isset($_SESSION['user']) ) {
         return false;
      }
      
      if ( !$module || !$action ) {
         return false;
      }

      //如果是要求检查登录，则在此检查
      if ( $this->action_map[$module][$action]['onlychecklogin']  ) {
         if ( isset($_SESSION['user']) ) {
            return true;
         } else {
            return false;
         }
      }

      //check whether the module is exists
      $db = DB::getConnection();

      $sql = "select count(*) as num from sys_modules where module_name = ?";
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array(
         $module)
      );

      $rows = $res->FetchRow();

      $num = $rows['num'];

      if ( !$num ) {
         return false;
      }

      // find out the module id
      // Select module and action, user, group id
      $sql = "select id from sys_modules where module_name =? ";
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($module));
      $rows = $res->FetchRow();
      $module_id = $rows['id'];


      //check whether the action is exists.
      $sql = "select count(*) as num from sys_actions where module_id =? and action_name = ? ";
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sql, array(
         $module_id, $action )
      );

      $rows = $res->FetchRow();

      $num = $rows['num'];

      if ( !$num ) {
         return false;
      }


      // action id
      $sql = "select id from sys_actions where module_id=? and action_name =? ";
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($module_id, $action));
      $rows = $res->FetchRow();
      $action_id = $rows['id'];

      //user id
      $sql = "select id, group_dep from base_user_info where user_name = ? ";
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array(strtolower($_SESSION['user']['name'])));
      $rows = $res->FetchRow();
      $user_id = $rows['id'];
      $group_id = $rows['group_dep'];

      //Check whether user have this privileges
      // At the first we must check the user's privileges tables;
      $sql = "select count(*) as num from sys_user_privileges where userid=? and actionid=? ";
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array(
         $user_id,
         $action_id));
      $rows = $res->FetchRow();

      if ( $rows['num'] == 1 ) {
         return true;
      }

      // if user's privileges table did not included the privileges.
      // so we must check the user's group privileges tables
      $sql = "select count(*) as num from sys_group_privileges where groupid=? and ".
      " actionid = ? ";
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array(
         $group_id,
         $action_id));
      $rows = $res->FetchRow();

      $is_have = true;
      if ( $rows['num'] == 1 ) {
         $is_have = true;
      } else {
         $is_have = false;
      }


      return $is_have; 
   }/*}}}*/
   
   /**
   *  Show a error message to tell user not login
   *
   *  @param:  NULL
   *  @return: NULL
   *  @access: private
   */
   private function ShowNoPrivilege() {/*{{{*/
      $this->AlertAndForward(APP_YOU_HAVE_NO_PRIVILEGES, 
         'index.php');

      return;
      /*
      header("Content-type:text/html;charset=UTF-8");
      echo "<script language=javascript>\n";
      echo "alert('".APP_YOU_HAVE_NO_PRIVILEGES."')\n";
      echo "document.location.href='index.php';\n";
      echo "</script>\n";
      return;
       */

   }/*}}}*/


   /**
   *  double check the action whether it was need to 
   *  access privileges
   *  @param:  Strig    $module  user require module
   *  @param:  String   $action  user's Action
   *  @return: boolean
   *  @access: private
   */
   private function needPrivilege($module='default', $action='default') {/*{{{*/
      if ( $this->action_map[$module][$action]['validate'] ) {
         return 1;
      } else {
         return 0;
      }
   }/*}}}*/
   
   /**
    * 检查用户是否被列在黑名单中
    * @param:  NULL
    * @return: Boolean
    * @access: private
    */
   private function checkBlackList() {/*{{{*/
      $ip = getenv('REMOTE_ADDR');

      #echo $ip."<br>";

      $user_name = NULL;
      if ( isset($_SESSION['user'])) {
         $user_name = $_SESSION['user']['name'];
      }

      $dbh = DB::getConnection();

      if ( $user_name ) {
         $sql = 'select count(*) as num from black_list_by_user where lower(user_name)=?';

         $stmt = $dbh->prepare($sql);
         $res = $dbh->Execute($stmt, array(strtolower($user_name)));
         $rows = $res->FetchRow();

         if ( $rows['num'] ) {
            return TRUE;
         }
      }

      $ip_array = array();
      $ip_array = split('\.', $ip);

      #print_r($ip_array);

      $sql = 'select count(*) as num from black_list_by_ip where ip = ? or ip = ? or '
            .' ip = ? or ip = ? ';

      $stmt = $dbh->prepare($sql);
      $res = $dbh->Execute($sql, array(
               $ip_array[0].'.*',
               $ip_array[0].'.'.$ip_array[1].'.*',
               $ip_array[0].'.'.$ip_array[1].'.'.$ip_array[2].'.*',
               $ip));
      $rows = $res->FetchRow();

      if ( $rows['num'] ) {
         return TRUE;
      }

      return FALSE;
   }/*}}}*/

   /**
    * 检查用户是否被锁定了
    * @param:  NULL
    * @return: Boolean
    * @access: private
    */
   private function checkLocked() {
      if ( !$_SESSION['user']['name'] ) {
         return FALSE;
      }

      $dbh = DB::getConnection();
      $sql = 'select status from base_user_info where lower(user_name)=?';
      $sth = $dbh->Prepare($sql);
      $res = $dbh->Execute($sth, array(strtolower($_SESSION['user']['name'])));
      $rows = $res->FetchRow();

      if ( $rows['status'] ) {
         return TRUE;
      } else {
         return FALSE;
      }

   }



}

?>
