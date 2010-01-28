<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/UserUtil.class.php
 *
 * 用于查询和做一些用户信息的类
 *
 * PHP Version 5
 * @package:   class.user
 * @author:    Mike.G 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: www.5anet.com
 * @version:   $Id: UserUtil.class.php,v 1.2 2006-09-24 14:38:08 ghw Exp $
 * @Date:      $Date: 2006-09-24 14:38:08 $
 */

include_once CLASS_PATH.'bbs/TopicUtil.class.php';
include_once FUNCTION_PATH.'set_locale_time.fun.php';


class UserUtil {

   /**
    * 查询用户最后一次登录的时间
    * @param:  &$db database references
    * @param:  $user_name  String
    * @return: last_access_time 
    * @access: public
    * @static
    */
   public static function getUserLastAccessTime(&$db, $user_name) {/*{{{*/
      //查询上次登录最后的动作的时间
      $temp_sql = 'select access_time from access_log where user_name=? order by id desc';
      $temp_res = 
         $db->SelectLimit($temp_sql, 1, 5, array(strtolower($user_name)));
      $temp_rows = $temp_res->FetchRow();

      if ( $temp_rows['access_time'] ) {
         return $temp_rows['access_time'];
      } else {
         return time();
      }

   }/*}}}*/

   /**
    * 统计当前在线的用户数
    * @param:  &$db database references
    * @retutn: $online_user_number
    * @access: public
    * @static
    */
   public static function getOnlineUserNumber(&$db) {/*{{{*/
      $sql = 'select count(*) as num from online_user where user_name != session_id';
      $stmt = $db->Prepare($sql);
      $res = $db->Execute($stmt);
      $rows = $res->FetchRow();

      return $rows['num'];
   }/*}}}*/

   /**
    * 统计当前在线的游客数
    * @param:  &$db database references
    * @return: $online_vistor_number
    * @access: public
    * @static
    */
   public static function getVistorNumber(&$db) {/*{{{*/
      $sql = 'select count(*) as num from online_user where user_name = session_id';
      $stmt = $db->Prepare($sql);
      $res = $db->Execute($stmt);
      $rows = $res->FetchRow();

      return $rows['num'];
   }/*}}}*/
   
   /**
    * 统计在线的最高时间和最高人数
    * @param:  &$db database reverences
    * @return: info_array array
    * @access: public
    * @static
    */
   public static function getTheHighAccess(&$db) {/*{{{*/
      $sql = 'select online, online_date from max_online_user where id=1 ';
      $res = $db->SelectLimit($sql, 1, 0);
      $rows = $res->FetchRow();


      return array('num' => $rows['online'],
         'time'=>$rows['online_date']);

   }
/*}}}*/

   /**
    * 查询当前在线用户的信息
    * @param:  &$db database references
    * @return: $user_info
    * @access: public
    * @static
    */
   public static function getOnlineUser(&$db) {/*{{{*/
      $sql = 'select a.user_name, b.id from online_user as a join base_user_info as b '.
         ' on a.user_name = b.user_name where a.user_name != a.session_id';
      $stmt = $db->Prepare($sql);
      $res = $db->Execute($stmt);
      
      $user_info = array();

      while ( $rows = $res->FetchRow() ) {
         $user_info[] = array(
            'id'=>$rows['id'],
            'name' => $rows['user_name']
         );
      }

      return $user_info;
   }/*}}}*/

   /**
    * 查询用户的id
    * @param:  $user_name String
    * @return: $user_id Integer
    * @access: public
    * @static
    */
   public static function getUserId(&$db, $user_name) {/*{{{*/
      $sql = 'select id from base_user_info where user_name=?';
      $stmt = $db->Prepare($sql);
      $res = $db->Execute($stmt, array(strtolower($user_name)));
      $rows = $res->FetchRow();

      return $rows['id'];
   }/*}}}*/
   
   /**
    * 查询版块当前在线的用户
    * @param:  &$db database Connection
    * @param:  $sub_id  论坛及子论坛的id数组
    * @return: $user_number 用户的数目
    * @access: public
    * @static
    */
   public static function getUserInfoArray(&$db, &$sub_id) {/*{{{*/
      $sql = 'select a.user_name, b.id from bbs_layout_online_user as a join base_user_info as b '.
         ' on a.user_name = b.user_name where a.user_name != a.session_id'.
         ' and a.layout_id in ('.implode(',', $sub_id).')';
      $stmt = $db->Prepare($sql);
      $res = $db->Execute($stmt);
      
      $user_info = array();

      while ( $rows = $res->FetchRow() ) {
         $user_info[] = array(
            'id'=>$rows['id'],
            'name' => $rows['user_name']
         );
      }

      return $user_info;
   }/*}}}*/

   /**
    * 根据用户的用户ID，返回用户名
    * @param:  &$db, 
    * @param:  $user_id
    * @return: $user_name
    * @access: public
    * @static
    */
   public static function getUserNameById(&$db, $user_id) {/*{{{*/
      $sql = 'select user_name from base_user_info where id=?';
      $stmt = $db->Prepare($sql);
      $res = $db->Execute($stmt, array($user_id));
      $rows = $res->FetchRow();

      return $rows['user_name'];
   }/*}}}*/
   
   /**
    * 返回用户上次退出论坛的时间
    * @param:  &$db
    * @param:  $user_id
    * @return: $last_time
    * @access: public
    * @static
    */
   public static function getUserLastLogoutTime(&$db, $user_id) {/*{{{*/
      $sql = 'select last_time from user_last_time_logout where user_id=?';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      return $rows['last_time']?$rows['last_time']:0;

   }/*}}}*/

   /**
    * 返回用户的头像
    * @param:  &$db
    * @param:  $user_id
    * @return: $user_header String
    * @access: public
    * @static
    */
   public static function getUserHeader(&$db, $user_id) {/*{{{*/
      $sql = 'select user_header from base_user_info where id=?';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      $user_header = $rows['user_header'];
      $user_header_str = '';

      if ( $rows['user_header'] ) {
         if ( $rows['user_header'] <1 || $rows['user_header'] > 37 ) {
            $user_header = -1;
         }      
      } else {
         $user_header= -1;
      }

      if ( $user_header > 0 ) {
         $user_header_str = IMAGE_URL.'face/'.$user_header.'.jpg';
      } else {
         if ( file_exists(ROOT_PATH.'upload/userhead/'.$user_id.'.png') ) {
            $user_header_str = ROOT_URL.'upload/userhead/'.$user_id.'.png';
         } else if ( file_exists(ROOT_PATH.'upload/userhead/'.$user_id.'.gif') ) {
            $user_header_str = ROOT_URL.'upload/userhead/'.$user_id.'.gif';
         } else if ( file_exists(ROOT_PATH.'upload/userhead/'.$user_id.'.jpg') ) {
            $user_header_str = ROOT_URL.'upload/userhead/'.$user_id.'.jpg';
         } else {
            $user_header_str = IMAGE_URL.'face/1.jpg';
         }
      }

      return $user_header_str;

   }
/*}}}*/

   /**
    * 返回用户的信息
    * @param:  &$db
    * @param:  $user_id
    * @return: $user_info_array;
    * @access: public
    * @static
    */
   public static function &getUserInfo(&$db, $user_id) {/*{{{*/

      $sql = 'select register_date, group_dep, user_hometown,user_sign from base_user_info where id=?';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      $register_date = $rows['register_date'];
      $user_hometown = $rows['user_hometown'];
      $user_dep = $rows['group_dep'];

      $user_level = self::getUserDepName($db, $user_dep);

      //取得用户的发帖量
      $user_topic = self::getUserCreateTopicNumber($db, $user_id);


      $user_info_array = array(
         'register_date' => $register_date,
         'user_hometown' => $user_hometown,
         'user_level' => $user_level,
         'user_topic' => $user_topic,
         'user_sign' => $rows['user_sign']
      );


      return $user_info_array;

   }/*}}}*/


   /**
    * 取得用户所在组的名字
    * @param:  &$db
    * @param:  $user_dep
    * @return: $dep_name
    * @access: public
    * @static
    */
   public static function getUserDepName(&$db, $user_dep) {/*{{{*/
      $sql = 'select group_name from sys_group where id=?';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($user_dep));
      $rows = $res->FetchRow();
      return $rows['group_name'];
   }/*}}}*/
   
   /**
    * 取得用户的组
    * @param:  &$db
    * @param:  $user_id
    * @reutn:  $group_id
    * @access: public
    * @static
    */
   public static function getUserDep(&$db, $user_name) {/*{{{*/
      $sql = 'select group_dep from base_user_info where lower(user_name)=?';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array(strtolower($user_name)));
      $rows = $res->FetchRow();
      return $rows['group_dep'];
   }/*}}}*/


   /**
    * 取得用户的发表主题的数量
    * @param:  &$db
    * @param:  $user_id
    * @return: $topic_number
    * @access: public
    * @static
    */
   public static function getUserCreateTopicNumber(&$db, $user_id) {/*{{{*/
      $sql = 'select count(*) as num from bbs_subject where author=?';
      $user_name = self::getUserNameById($db, $user_id);
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($user_name));
      $rows = $res->FetchRow();
      return $rows['num'];
   }/*}}}*/

   /**
    * 判断用户是否在线
    * @param:  &$db
    * @param:  $user_id
    * @return: $online boolean
    * @access: public
    * @static
    */
   public static function isOnline(&$db, $user_id) {/*{{{*/
      $user_name = self::getUserNameById($db, $user_id);
      $sql = 'select count(*) as num from online_user where lower(user_name)=?';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array(strtolower($user_name)));
      $rows = $res->FetchRow();

      if ( $rows['num'] ) {
         return 1;
      } else {
         return 0;
      } 
   }/*}}}*/

   /**
    * 判断用户是否是本版的版主
    * @param:  &$db, 
    * @param:  $id 帖子的id
    * @param:  $user_name 用户的名字
    * @return: $is_admin boolean
    * @access: pulic
    * @static
    */
   public static function isThisLayoutAdmin(&$db, $id, $layout_id, $user_name) {/*{{{*/
      //取得帖子的版块
      $user_id = self::getUserId($db, $user_name);

      $temp_array = array();
      LayoutUtil::getParentId($db, $layout_id, $temp_array);
      array_push($temp_array, $layout_id);

      $sql = 'select count(*) as num from bbs_layout_manager where user_id=? and '.
            ' layout_id in ('.implode(',', $temp_array).')';
      $sth = $db->prepare($sql);
      $res = $db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      if ( $rows['num'] ) {
         return 1;
      } else {
         return 0;
      }
   }/*}}}*/

   /**
    * 判断用户是否存在
    * @param: &$db
    * @param:  $user_id
    * @return: boolean
    * @access: public
    * @static
    */
   public static function isExists(&$db, $user_id) {
      $sql = 'select count(*) as num from base_user_info where id=?';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      if ( $rows['num'] ) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

}

?>
