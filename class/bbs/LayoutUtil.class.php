<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/bbs/LayoutUtil.class.php
 *
 * 此类是用于操作板块的工具类，也可以说是板块的模拟类。
 *
 * PHP Version 5
 *
 * @package:   class.bbs
 * @author:    Mike.G 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: www.5anet.com
 * @version:   $Id: LayoutUtil.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @Date:      $Date: 2006-08-28 13:09:20 $
 */

//包含需要用到的工具类
include_once CLASS_PATH.'user/UserUtil.class.php';
include_once FUNCTION_PATH.'utf8_substr.fun.php';
include_once FUNCTION_PATH.'set_locale_time.fun.php';
include_once CLASS_PATH.'bbs/TopicUtil.class.php';

include_once FUNCTION_PATH.'ConvertString.fun.php';

//包含语言文件
//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/LayoutUtil.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/LayoutUtil.lang.php';
}



class LayoutUtil {
   /**
    * 根据给出的父论坛的id
    * 取回所有的子论坛板块的id
    * 并返回一个数组
    * 
    * @param:  $id integer
    * @return: $chilc_id array
    * @access: private
    * @static
    */
   public static  function getChildId(&$db, $id, &$child_array) {/*{{{*/

      $sub_id = $id;
      $sql = 'select id from bbs_layout where parent_id=?';
      $stmt = $db->Prepare($sql);

      $res = $db->Execute($stmt, array($sub_id));

      while ( $rows = $res->FetchRow() ) {
         array_push($child_array, $rows['id']);
         self::getChildId($db, $rows['id'], $child_array);
      }

   }/*}}}*/
   
   /**
    * 根据给出的论坛的ID，
    * 取回所有的父论坛的id
    * 并返回一个数组
    * @param:  &$db database connection
    * @param:  $parent_array
    * @param: $id integer
    * @return: null
    * @access: public
    * @static
    */
   public static function getParentId(&$db, $id, &$parent_array) {/*{{{*/

      $sub_id = $id;
      $sql = 'select parent_id from bbs_layout where id=?';
      $stmt = $db->Prepare($sql);

      $res = $db->Execute($stmt, array($sub_id));

      while ( $rows = $res->FetchRow() ) {

         if ( $rows['parent_id'] == 0 ) {
            return;
         } else {
            array_push($parent_array, $rows['parent_id']);
            self::getParentId($db, $rows['parent_id'], &$parent_array);
         }
      }
   }/*}}}*/


   /**
    * 从板块里删除那些已经从系统外登录出的用户
    * @param:  &$db Database reference
    * @return: NULL
    * @access: public
    * @static
    */
   public static  function delNotExistsUser(&$db) {/*{{{*/
      $sql = 'select session_id from online_user';
      $sth = $db->prepare($sql);
      $res = $db->Execute($sth);

      $temp_array = array();

      while ( $rows = $res->FetchRow() ) {
         array_push($temp_array, $db->qstr($rows['session_id']));
      }

      $sql = 'delete from bbs_layout_online_user where session_id not in ('.
         implode(',', $temp_array).')';
      $db->Execute($sql);

   }/*}}}*/

   /**
    * 根据给出的板块的id，返回论坛的状态
    * @param:  &$db Database Reference
    * @param:  $id integer layout id
    * @return: $status integer
    * @access: public
    * @static
    */
   public static function getLayoutStatus(&$db, $id) {/*{{{*/
      $temp_sql = 'select  status from  bbs_layout where id=?';
      $temp_stmt = $db->Prepare($temp_sql);
      $temp_res = $db->Execute($temp_stmt, array($id));
      $temp_rows = $temp_res->FetchRow();

      /**
       *  $layout_status == 0 || $layout_status is null 则为开放
       *  $layout_status == 1 则需要验证
       *  $layout_status == 2 则为关闭
       */
      $layout_status = $temp_rows['status'];

      return $layout_status;
   }/*}}}*/


   /**
    * 返回所有一级的版面的基本信息
    * @param:  &$db database references
    * @return: $bbs_array_id 
    * @access: public
    * @static
    */
   public static function &getLayoutInfoByParentId(&$db, $parent_id = 0) {/*{{{*/

      $bbs_array_id = array();
      $sql = "select id, title, description  from bbs_layout where parent_id=$parent_id ".
        " order by id asc ";
      $res = $db->Execute($sql);

      while ( $rows = $res->FetchRow() ) {
         $id = $rows['id'];
         $title = $rows['title'];
         $description = $rows['description'];
         
         $bbs_array_id[] = array (
            'id' => $id,
            'title' => $title,
            'description' => $description
         );
      }

      return $bbs_array_id;
   }/*}}}*/
   
   /**
    * 判断论坛是否有新帖
    * @param:  &$db database references
    * @param:  $user_name
    * @param:  $sub_bbs_id 板块及子板块的ID数组
    * @return: boolean
    * @access: public
    * @static
    */
   public static function haveNewTopic(&$db, $user_name, &$sub_bbs_id) {/*{{{*/

      /**
       * 求出最后时间后，需要我们找出当前子论坛下各个子论坛的id
       */
      $user_id = UserUtil::getUserId($db, $user_name);
      $last_time = UserUtil::getUserLastLogoutTime($db, $user_id);

      $sql = 'select count(*) as num from bbs_subject where layout_id in ('.
         implode(',', $sub_bbs_id).') and last_access_date >= ? ';

      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($last_time));
      $rows = $res->FetchRow();

      if ( $rows['num'] ) {
         return TRUE;
      } else {
         return FALSE;
      }
   }/*}}}*/
   
   /**
    * 返回论坛及子论坛里正在查看的人数
    * @param:  &$db 
    * @param:  $sub_bbs_id
    * @return: $view_number integer
    * @access: public
    * @static
    */
   public static function getViewNumber(&$db, &$sub_bbs_id) {/*{{{*/
   
      $temp_sql = 'select count(*) as num from bbs_layout_online_user '.
               ' where layout_id in ('.implode(',', $sub_bbs_id).')';

      $temp_res = $db->Execute($temp_sql);
      $temp_rows = $temp_res->FetchRow();
      $view_number = $temp_rows['num'];

      return $view_number;
   }/*}}}*/
   
   
   /**
    * 返回论坛及子论坛的主题数
    * @param:  &$db
    * @param:  $sub_bbs_id
    * @return: $topic_number
    * @access: public
    * @static
    */
   public static function getTopicNumber(&$db, &$sub_bbs_id) {/*{{{*/

      $temp_sql = 'select count(*) as num from bbs_subject where '.
               ' layout_id in ('.implode(',', $sub_bbs_id).')';
      $temp_res = $db->Execute($temp_sql);
      $temp_rows = $temp_res->FetchRow();
      $topic_number = $temp_rows['num'];
      return $topic_number;
   }/*}}}*/


   /**
    * 返回论坛及自论坛的回复数
    * @param:  &$db
    * @param:  $sub_bbs_id
    * @return: $reply_number
    * @access: public
    * @static
    */
   public static function getReplyNumber(&$db, &$sub_bbs_id) {/*{{{*/
      $temp_sql = 'select count(*) as num from bbs_reply where '.
               ' layout_id in ('.implode(',', $sub_bbs_id).')';
      $temp_res = $db->Execute($temp_sql);
      $temp_rows = $temp_res->FetchRow();
      $reply_number = $temp_rows['num'];

      return $reply_number;
   }/*}}}*/
   
   /**
    * 返回论坛最后发表的帖子
    * @param:  &$db
    * @param:  $sub_id 
    * @return: $subject_array array
    * @access: public
    * @static
    */
   public static function getLastPostTopic(&$db, $sub_id) {/*{{{*/

       $sub_id_array = array();
       LayoutUtil::getChildId($db, $sub_id, $sub_id_array);
       array_push($sub_id_array, $sub_id);

       $temp_sql = 'select id, title, last_access_date from bbs_subject where layout_id in ('.
          implode(', ', $sub_id_array).') '.
          ' order by last_access_date desc ';

       $temp_res = $db->SelectLimit($temp_sql, 1, 0);

       return $temp_res->FetchRow();

   }/*}}}*/

   
   /**
    * 返回给定的版主列表
    * @param:  &$db
    * @param:  $sub_id
    * @return: $array
    * @access: public
    * @static
    */
   public static function &getManagerList(&$db, $sub_id) {/*{{{*/
      $user_list = array();

      $temp_sql = 'select a.user_id, b.user_name from bbs_layout_manager as a join '.
               ' base_user_info as b on a.user_id = b.id where a.layout_id=? ';
      $temp_stmt = $db->Prepare($temp_sql);
      $temp_res = $db->Execute($temp_sql, array($sub_id));

      while ( $temp_rows = $temp_res->FetchRow() ) {
         $temp = array(
            'user_id' => $temp_rows['user_id'],
            'user_name' => $temp_rows['user_name'],
         );
         
         $user_list[] = $temp;
      }

      return $user_list;
   }/*}}}*/
   
   /**
    * 判断论坛是否存在
    * @param:  $bbs_id
    * @return: boolean
    * @access: public
    * @static
    */
   public static function isExists(&$db, $bbs_id) {/*{{{*/
      $sql = 'select count(*) as num from bbs_layout where id=?';
      $stmt = $db->Prepare($sql);
      $res = $db->Execute($stmt, array($bbs_id));
      $rows = $res->FetchRow();

      if ( $rows['num'] ) {
         return 1;
      } else {
         return 0;
      }

   }/*}}}*/

   /**
    * 返回子论坛的信息
    * @param:  $id, 论坛ID
    * @param:  $db, 数据库的连接
    * @reurn:  Array
    * @access; public
    */
   public static function &getSubBBS(&$db, $id) {/*{{{*/
      //查询所有下级子论坛
      $sub_bbs_layout_id = LayoutUtil::getLayoutInfoByParentId($db, $id);
      $sub_array = array();
         
      foreach ( $sub_bbs_layout_id as $sub_rows ) {

         $sub_id = $sub_rows['id'];
         //注意：$sub_bbs_id是一个数组
         $sub_bbs_id = array();
         LayoutUtil::getChildId($db, $sub_id, $sub_bbs_id);
         array_push($sub_bbs_id, $sub_id);


         /**
          * 如果已经将论坛锁住，则不判断是否有新帖
          */
         /**
          *  $layout_status == 0 || $layout_status is null 则为开放
          *  $layout_status == 1 则需要验证
          *  $layout_status == 2 则为关闭
          */
         $layout_status = LayoutUtil::getLayoutStatus($db, $sub_id);


         /**
           * 判断是否有新帖子
           * 判断有新帖子的流程是：
           * 如果用户已经登录，则找出用户的作后动作的时间
           * 如果用户没有登录，则显示没有新帖子
           */

         $image = 'nonewtopic.gif';

         if ( $layout_status == 2 ) {
            $image = 'lock.gif';
         } else {

            if ( isset($_SESSION['user'] ) ) {
               if ( LayoutUtil::haveNewTopic($db, $_SESSION['user']['name'], $sub_bbs_id) ) {
                  /**
                   * 求出最后时间后，需要我们找出当前子论坛下各个子论坛的id
                   */
                  $image = 'havenewtopic.gif';
               }
            }
         }

         //求出论坛及子论坛下面的查看的人数
         $view_number = LayoutUtil::getViewNumber($db, $sub_bbs_id);

         //求出论坛和子论坛下的所有的主题
         $topic_number = LayoutUtil::getTopicNumber($db, $sub_bbs_id);

         //求出论坛下所有的回复数
         $reply_number = LayoutUtil::getReplyNumber($db, $sub_bbs_id);


         //求论坛里最后发表的帖子
         $temp_rows = LayoutUtil::getLastPostTopic($db, $sub_id);

         $last_id = $temp_rows['id'];
         $last_title = $temp_rows['title'];
         $last_time = $temp_rows['last_access_date'];
         $last_time = set_locale_time($last_time);
         $short_title = utf8_substr($last_title, 0, 10);


         //求版主列表的字符串
         $manager_list_array = LayoutUtil::getManagerList($db, $sub_id);

         $manager_str = "";

         foreach ( $manager_list_array as $temp_rows ) {
            $manager_str .= "<option value=".$temp_rows['user_id'].">";
            $manager_str .= $temp_rows['user_name']."</option>\n";
         }


         $sub_array[] = array(
               'id' => $sub_rows['id'],
               'title' => $sub_rows['title'],
               'content' =>ConvertString(stripslashes($sub_rows['description']), ROOT_URL, IMAGE_URL.'express/'),
               'image' => $image,
               'viewnumber' => $view_number,
               'topic_number' => $topic_number,
               'reply_number' => $reply_number,
               'topicid' => $last_id,
               'topic_title' => $last_title,
               'short_title' => $short_title,
               'last_time' => $last_time,
               'managerlist' =>$manager_str
         );
     }

     return $sub_array;

   }
/*}}}*/
   

   /**
    * 返回论坛的标题
    * @param:  $id 论坛的ID
    * @param:  &$db
    * @return: String
    * @access: public
    * @static
    */
   public static function getTitle(&$db, $id) {/*{{{*/
      $sql = 'select title from bbs_layout where id=?';
      $stmt = $db->Prepare($sql);
      $res = $db->Execute($stmt, array($id));
      $rows = $res->FetchRow();

      return $rows['title'];
   }/*}}}*/

   /**
    * 判断版面的父版是否被关掉
    * 如果关掉，则子论坛也是应该拒绝访问
    * @param:  &$db
    * @param:  $id
    * @return: boolean
    * @access: public
    * @static
    */
   public static function isClosedByParent(&$db, $id) {/*{{{*/
      $sql = 'select parent_id from bbs_layout where id=?';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($id));
      $rows = $res->FetchRow();
      $parent_id = $rows['parent_id'];

      if ( $parent_id == 0 ) {
         return false;
      }

      $sql = 'select status from bbs_layout where id=? ';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sql, array($parent_id));
      $rows = $res->FetchRow();

      $status = $rows['status'];

      if ( $status == 2 ) {
         return true;
      } else {
         if (  self::isClosedbyParent($db, $parent_id) ) {
            return true;
         } else {
            return false;
         }
      }

   }
/*}}}*/


   /**
    * 返回本版及父版的数组信息。
    * @param:  $db
    * @param:  $id
    * @return: array
    * @access; public
    * @static
    */
   public static function &getParentLayoutInfo(&$db, $id) {/*{{{*/
      $parent_array = array();
      self::getParentId($db, $id, $parent_array);
      array_unshift($parent_array, $id);

      $info_array = array();

      $sql = 'select id, title, parent_id, status from bbs_layout where id=?';
      $sth = $db->Prepare($sql);

      for ( $i=count($parent_array)-1; $i>=0; $i-- ) {
         $temp_id = $parent_array[$i];
         $res = $db->Execute($sth, array($temp_id));
         $rows = $res->FetchRow();

         $temp_array = array(
            'id'=>$rows['id'],
            'title'=>$rows['title'],
            'parent_id'=>$rows['parent_id'],
            'status'=>$rows['status']
         );

         $info_array[count($parent_array) - ( $i+1)] = $temp_array;

      }

      return $info_array;
   }/*}}}*/


   /**
    * 取得固定的板块的帖子总数
    * @param:  &$db
    * @param:  $id
    * @return: integer
    * @access: public
    * @static
    */
   public static function getTotalNumberTopicByParentId(&$db, $id) {/*{{{*/
      $sql = 'select count(*) as num from bbs_subject where layout_id=?';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($id));
      $rows = $res->FetchRow();

      return $rows['num'];
   }/*}}}*/
   

   /**
    * 根据用户名取得用户的主题总数
    * @param:   &$db
    * @param:   $username
    * @return:  integer
    * @access:  public
    * @static
    */
   public static function getTotalNumberTopicByUser(&$db, $user_name, $show_best=0) {/*{{{*/

       $sql = 'select count(*) as num from bbs_subject where author=?';
       if ( $show_best == 1 ) {
            $sql.= ' and is_best = 1 ';
       }
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($user_name));
      $rows = $res->FetchRow();

      return $rows['num'];
   }/*}}}*/


   /**
    * 根据用户名取得用户回复帖子数
    * @param:   &$db
    * @param:   $username
    * @return:  integer
    * @access:  public
    * @static
    */
   public static function getTotalNumberReplyByUser(&$db, $user_name) {/*{{{*/

       $sql = 'select count(*) as num from bbs_reply where author=?';
       if ( $show_best == 1 ) {
            $sql.= ' and is_best = 1 ';
       }
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($user_name));
      $rows = $res->FetchRow();

      return $rows['num'];
   }/*}}}*/



   /**
    * 取得一个论坛的置顶帖子的情况
    * @param:  &$db Database Connection
    * @param:  $id 论坛板块的id
    * @return: array 
    * @access: public
    * @static
    */
   public static function &getTopicSubjectInfo(&$db, $id, $pre_page, $show_best=0) {/*{{{*/
      if ( $show_best == 1 ) {
         $sql = 'select id, title, author, express,subject_status, is_best, click_number, reply_number,'.
            ' last_access_date from bbs_subject where layout_id=? and is_top = 1 and '.
            ' is_best=1 order by last_access_date desc, id desc';
      } else {
         $sql = 'select id, title, author, express,subject_status, is_best, click_number, reply_number,'.
            ' last_access_date from bbs_subject where layout_id=? and is_top = 1 order by last_access_date desc, '.
            ' id desc';
      }

      $info_array = array();

      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($id));

      $now_user_id = UserUtil::getUserId($db, $_SESSION['user']['name']);

      while ( $rows = $res->FetchRow() ) {
         $user_id = UserUtil::getUserId($db, $rows['author']);

         //看状态。
         //==0, 开放
         //==1, 需要验证
         //==2, 帖子被关闭
         //如果被关闭，则不需看是否有新帖
         $status_image = 'no_topic.gif';
         if ( $rows['subject_status'] == 2 ) {
            $status_image = 'topic_lock.gif';
         } else {
            if ( !isset($_SESSION['user']) ) {
               $status_iamge = 'no_topic.gif';
            } else {
               if ( TopicUtil::haveNewReply($db, $rows['id'], $now_user_id) ) {
                  $status_image = 'new_topic.gif';
               } else {
                  $status_image = 'no_topic.gif';
               }
            }

         }


         $last_user_name = '';
         $last_user_id = '';
         $total_page = 0;
         $find_number = 0;
         if ( $rows['reply_number'] > 0 ) {
            $temp_sql = 'select author from bbs_reply where subject_id=? order by id desc ';
            $temp_res = $db->SelectLimit($temp_sql, 1, 0, array($rows['id']));
            $temp_rows = $temp_res->FetchRow();
            $last_user_name = $temp_rows['author'];
            $last_user_id = UserUtil::getUserId($db, $last_user_name);

            $temp_sql = 'select count(*) as num from bbs_reply where subject_id=?';
            $temp_sth = $db->Prepare($temp_sql);
            $temp_res = $db->Execute($temp_sth, array($rows['id']));
            $temp_rows = $temp_res->FetchRow();

            $total_number = $temp_rows['num'];

            $total_page = ceil( ($total_number+1) / $pre_page );

            if ( $total_page > $pre_page ) {
               $find_number = ($total_number+1) % $pre_page;
            } else {
               $find_number = $total_number + 1;
            }
         }



         $temp_sql = 'select count(*) as num from bbs_subject_attach where subject_id=?';
         $temp_sth = $db->Prepare($temp_sql);
         $temp_res = $db->Execute($temp_sth, array($rows['id']));
         $temp_rows = $temp_res->FetchRow();
         $is_have_attach = $temp_rows['num'];

         $title = "<font color=red>[".LU_IS_TOP."]</font>";

         if( $rows['is_best'] ) {
            $title .= "&nbsp;<font color=red>[".LU_IS_BEST."]</font>";
         }

         $title .= $rows['title'];
         
         
         $info_array[] = array(
            'image' => $status_image,
            'id'=>$rows['id'],
            'title'=>$title,
            'have_new_reply' => ( $status_image == 'new_topic.gif'?1:0),
            'userid'=>$user_id,
            'username'=>$rows['author'],
            'clicks_number'=>$rows['click_number'],
            'reply_number'=>$rows['reply_number'],
            'last_time'=>set_locale_time($rows['last_access_date']),
            'last_username'=>$last_user_name,
            'last_userid'=>$last_user_id,
            'last_page'=>$total_page,
            'last_number'=>$find_number,
            'have_attach'=>$is_have_attach,
            'express' => $rows['express']
            );
      }

     return $info_array; 
   }
/*}}}*/
   
   /**
    * 取得论坛普通帖子的情况
    * @param:  &$id Database Connection
    * @param:  $id 论坛板块的id
    * @return; array
    * @acess:  public
    * @static
    */
   public static function &getSubjectInfo(&$db, $id, $pre_page, $show_page, $offset_page, $show_best=0 ) {/*{{{*/
      if ( $show_best == 1 ) {
         $sql = 'select id, title, author, subject_status, express, is_best, click_number, reply_number,'.
            ' last_access_date from bbs_subject where layout_id=? and is_top = 0 and is_best=1 '.
            ' order by last_access_date desc, id desc';
      } else {
         $sql = 'select id, title, author, subject_status, express, is_best, click_number, reply_number,'.
            ' last_access_date from bbs_subject where layout_id=? and is_top = 0 order by last_access_date desc, '.
            ' id desc';
      }

      $info_array = array();

      $res = $db->SelectLimit($sql, $show_page, $offset_page, array($id));

      $now_user_id = UserUtil::getUserId($db, $_SESSION['user']['name']);

      while ( $rows = $res->FetchRow() ) {
         $user_id = UserUtil::getUserId($db, $rows['author']);

         //看状态。
         //==0, 开放
         //==1, 需要验证
         //==2, 帖子被关闭
         //如果被关闭，则不需看是否有新帖
         $status_image = 'no_topic.gif';
         if ( $rows['subject_status'] == 2 ) {
            $status_image = 'topic_lock.gif';
         } else {
            if ( !isset($_SESSION['user']) ) {
               $status_iamge = 'no_topic.gif';
            } else {
               if ( TopicUtil::haveNewReply($db, $rows['id'], $now_user_id) ) {
                  $status_image = 'new_topic.gif';
               } else {
                  $status_image = 'no_topic.gif';
               }
            }

         }


         $last_user_name = '';
         $last_user_id = '';
         $total_page = 0;
         $find_number = 0;
         if ( $rows['reply_number'] > 0 ) {
            $temp_sql = 'select author from bbs_reply where subject_id=? order by id desc ';
            $temp_res = $db->SelectLimit($temp_sql, 1, 0, array($rows['id']));
            $temp_rows = $temp_res->FetchRow();
            $last_user_name = $temp_rows['author'];
            $last_user_id = UserUtil::getUserId($db, $last_user_name);

            $temp_sql = 'select count(*) as num from bbs_reply where subject_id=?';
            $temp_sth = $db->Prepare($temp_sql);
            $temp_res = $db->Execute($temp_sth, array($rows['id']));
            $temp_rows = $temp_res->FetchRow();

            $total_number = $temp_rows['num'];

            $total_page = ceil( ($total_number+1) / 10 );

            if ( $total_page > 10 ) {
               $find_number = ($total_number+1) % 10;
            } else {
               $find_number = $total_number + 1;
            }
         }


         $temp_sql = 'select count(*) as num from bbs_subject_attach where subject_id=?';
         $temp_sth = $db->Prepare($temp_sql);
         $temp_res = $db->Execute($temp_sth, array($rows['id']));
         $temp_rows = $temp_res->FetchRow();
         $is_have_attach = $temp_rows['num'];


         $title = "";
         if( $rows['is_best'] ) {
            $title .= "&nbsp;<font color=red>[".LU_IS_BEST."]</font>";
         }

         $title .= $rows['title'];

         
         $info_array[] = array(
            'image' => $status_image,
            'id'=>$rows['id'],
            'title'=>$title,
            'have_new_reply' => ( $status_image == 'new_topic.gif'?1:0),
            'userid'=>$user_id,
            'username'=>$rows['author'],
            'clicks_number'=>$rows['click_number'],
            'reply_number'=>$rows['reply_number'],
            'last_time'=>set_locale_time($rows['last_access_date']),
            'last_username'=>$last_user_name,
            'last_userid'=>$last_user_id,
            'last_page'=>$total_page,
            'last_number'=>$find_number,
            'have_attach'=>$is_have_attach,
            'express'=>$rows['express']
            );
      }

     return $info_array; 






   }/*}}}*/
   
   /**
    * 根据用户取得用户发表的主题
    * @param:  &$id Database Connection
    * @param:  $id 论坛板块的id
    * @return; array
    * @acess:  public
    * @static
    */
   public static function &getSubjectInfoByUser(&$db, $user_name, $get_number=5, $from_n=0,$show_best=0 ) {/*{{{*/
      if ( $show_best == 1 ) {
          $sql = 'select a.id, a.title, a.author, a.subject_status, a.express, a.is_best, a.click_number,'.
             'a. reply_number,'.
             ' a.last_access_date, b.title as btitle, b.id as bid from bbs_subject as a, bbs_layout as b '.
            '  where a.author=? and a.is_best=1 and a.layout_id = b.id '.
            ' order by a.last_access_date desc, a.id desc';
      } else {
          $sql = 'select a.id, a.title, a.author, a.subject_status, a.express, a.is_best, a.click_number,'.
             ' a.reply_number,'.
             ' a.last_access_date, b.title as btitle, b.id as bid from bbs_subject as a, bbs_layout as b '.
            ' where a.author=?  and a.layout_id = b.id order by a.last_access_date desc,a.id desc';
      }

      $info_array = array();
      $res = $db->SelectLimit($sql, $get_number, $from_n, array($user_name));

      $now_user_id = UserUtil::getUserId($db, $_SESSION['user']['name']);

      while ( $rows = $res->FetchRow() ) {
         $user_id = UserUtil::getUserId($db, $rows['author']);

         //看状态。
         //==0, 开放
         //==1, 需要验证
         //==2, 帖子被关闭
         //如果被关闭，则不需看是否有新帖
         $status_image = 'no_topic.gif';
         if ( $rows['subject_status'] == 2 ) {
            $status_image = 'topic_lock.gif';
         } else {
            if ( !isset($_SESSION['user']) ) {
               $status_iamge = 'no_topic.gif';
            } else {
               if ( TopicUtil::haveNewReply($db, $rows['id'], $now_user_id) ) {
                  $status_image = 'new_topic.gif';
               } else {
                  $status_image = 'no_topic.gif';
               }
            }

         }


         $last_user_name = '';
         $last_user_id = '';
         $total_page = 0;
         $find_number = 0;
         if ( $rows['reply_number'] > 0 ) {
            $temp_sql = 'select author from bbs_reply where subject_id=? order by id desc ';
            $temp_res = $db->SelectLimit($temp_sql, 1, 0, array($rows['id']));
            $temp_rows = $temp_res->FetchRow();
            $last_user_name = $temp_rows['author'];
            $last_user_id = UserUtil::getUserId($db, $last_user_name);

            $temp_sql = 'select count(*) as num from bbs_reply where subject_id=?';
            $temp_sth = $db->Prepare($temp_sql);
            $temp_res = $db->Execute($temp_sth, array($rows['id']));
            $temp_rows = $temp_res->FetchRow();

            $total_number = $temp_rows['num'];

            $total_page = ceil( ($total_number+1) / 10 );

            if ( $total_page > 10 ) {
               $find_number = ($total_number+1) % 10;
            } else {
               $find_number = $total_number + 1;
            }
         }


         $temp_sql = 'select count(*) as num from bbs_subject_attach where subject_id=?';
         $temp_sth = $db->Prepare($temp_sql);
         $temp_res = $db->Execute($temp_sth, array($rows['id']));
         $temp_rows = $temp_res->FetchRow();
         $is_have_attach = $temp_rows['num'];


         $title = "";
         if( $rows['is_best'] ) {
            $title .= "&nbsp;<font color=red>[".LU_IS_BEST."]</font>";
         }

         $title .= $rows['title'];

         
         $info_array[] = array(
            'image' => $status_image,
            'id'=>$rows['id'],
            'title'=>$title,
            'have_new_reply' => ( $status_image == 'new_topic.gif'?1:0),
            'userid'=>$user_id,
            'username'=>$rows['author'],
            'clicks_number'=>$rows['click_number'],
            'reply_number'=>$rows['reply_number'],
            'last_time'=>set_locale_time($rows['last_access_date']),
            'last_username'=>$last_user_name,
            'last_userid'=>$last_user_id,
            'last_page'=>$total_page,
            'last_number'=>$find_number,
            'have_attach'=>$is_have_attach,
            'express'=>$rows['express'],
            'btitle'=>$rows['btitle'],
            'bid'=>$rows['bid'],
            );
      }

     return $info_array; 

   }/*}}}*/

   /**
    * 根据用户取得用户收藏发表的主题
    * @param:  &$id Database Connection
    * @param:  $id 论坛板块的id
    * @return; array
    * @acess:  public
    * @static
    */
   public static function &getSubjectInfoByFavor(&$db, $user_name, $dir=0, $get_number=5, $from_n=0) {/*{{{*/
      if ( $dir ) {
          $sql = 'select a.id as fid,b.id, b.title, b.author, b.subject_status, b.express, b.is_best,'.
              ' b.click_number, b.reply_number, b.last_access_date, c.title as btitle, '.
              ' c.id as bid, d.dir_name from favor as a, bbs_subject as b, bbs_layout as c, '.
              ' favor_dir as d  where a.favor_id = b.id and b.layout_id = c.id and a.dir_id = d.id and a.user_id =?'.
              ' and a.dir_id = ? order by a.id desc ';
      } else {
          $sql = 'select a.id as fid,b.id, b.title, b.author, b.subject_status, b.express, b.is_best,'.
              ' b.click_number, b.reply_number, b.last_access_date, c.title as btitle, '.
              ' c.id as bid, d.dir_name from favor as a, bbs_subject as b, bbs_layout as c, '.
              ' favor_dir as d  where a.favor_id = b.id and b.layout_id = c.id and a.dir_id = d.id and a.user_id =?'.
              ' order by a.id desc ';
      }

      $info_array = array();
      if ( $dir ) {
          $res = $db->SelectLimit($sql, $get_number, $from_n, array($user_name, $dir));
      } else {
          $res = $db->SelectLimit($sql, $get_number, $from_n, array($user_name));
      }

      $now_user_id = UserUtil::getUserId($db, $_SESSION['user']['name']);

      while ( $rows = $res->FetchRow() ) {
         $user_id = UserUtil::getUserId($db, $rows['author']);

         //看状态。
         //==0, 开放
         //==1, 需要验证
         //==2, 帖子被关闭
         //如果被关闭，则不需看是否有新帖
         $status_image = 'no_topic.gif';
         if ( $rows['subject_status'] == 2 ) {
            $status_image = 'topic_lock.gif';
         } else {
            if ( !isset($_SESSION['user']) ) {
               $status_iamge = 'no_topic.gif';
            } else {
               if ( TopicUtil::haveNewReply($db, $rows['id'], $now_user_id) ) {
                  $status_image = 'new_topic.gif';
               } else {
                  $status_image = 'no_topic.gif';
               }
            }

         }


         $last_user_name = '';
         $last_user_id = '';
         $total_page = 0;
         $find_number = 0;
         if ( $rows['reply_number'] > 0 ) {
            $temp_sql = 'select author from bbs_reply where subject_id=? order by id desc ';
            $temp_res = $db->SelectLimit($temp_sql, 1, 0, array($rows['id']));
            $temp_rows = $temp_res->FetchRow();
            $last_user_name = $temp_rows['author'];
            $last_user_id = UserUtil::getUserId($db, $last_user_name);

            $temp_sql = 'select count(*) as num from bbs_reply where subject_id=?';
            $temp_sth = $db->Prepare($temp_sql);
            $temp_res = $db->Execute($temp_sth, array($rows['id']));
            $temp_rows = $temp_res->FetchRow();

            $total_number = $temp_rows['num'];

            $total_page = ceil( ($total_number+1) / 10 );

            if ( $total_page > 10 ) {
               $find_number = ($total_number+1) % 10;
            } else {
               $find_number = $total_number + 1;
            }
         }


         $temp_sql = 'select count(*) as num from bbs_subject_attach where subject_id=?';
         $temp_sth = $db->Prepare($temp_sql);
         $temp_res = $db->Execute($temp_sth, array($rows['id']));
         $temp_rows = $temp_res->FetchRow();
         $is_have_attach = $temp_rows['num'];


         $title = "";
         if( $rows['is_best'] ) {
            $title .= "&nbsp;<font color=red>[".LU_IS_BEST."]</font>";
         }

         $title .= $rows['title'];

         
         $info_array[] = array(
            'image' => $status_image,
            'id'=>$rows['id'],
            'title'=>$title,
            'have_new_reply' => ( $status_image == 'new_topic.gif'?1:0),
            'userid'=>$user_id,
            'username'=>$rows['author'],
            'clicks_number'=>$rows['click_number'],
            'reply_number'=>$rows['reply_number'],
            'last_time'=>set_locale_time($rows['last_access_date']),
            'last_username'=>$last_user_name,
            'last_userid'=>$last_user_id,
            'last_page'=>$total_page,
            'last_number'=>$find_number,
            'have_attach'=>$is_have_attach,
            'express'=>$rows['express'],
            'btitle'=>$rows['btitle'],
            'bid'=>$rows['bid'],
            'dir_name'=>$rows['dir_name'],
            'fid'=>$rows['fid'],
            );
      }

     return $info_array; 

   }/*}}}*/
   
 


   /**
    * 根据用户取得用户参与的主题
    * @param:  &$id Database Connection
    * @param:  $id 论坛板块的id
    * @return; array
    * @acess:  public
    * @static
    */
   public static function &getReplyInfoByUser(&$db, $user_name, $get_number=5, $from_n=0,$show_best=0 ) {/*{{{*/
      if ( $show_best == 1 ) {
          $sql = 'select distinct a.id, a.title, a.author, a.subject_status, a.express, a.is_best, a.click_number,'.
             ' a.reply_number,'.
             ' a.last_access_date, c.title as btitle, c.id as bid '.
             ' from bbs_subject as a, bbs_reply as b, bbs_layout as c  where b.author=? and a.is_best=1 '.
             ' and a.id = b.subject_id and a.layout_id = c.id '.
            ' order by a.last_access_date desc, a.id desc';
      } else {
          $sql = 'select distinct a.id, a.title, a.author, a.subject_status, a.express, a.is_best, a.click_number,'.
             'a.reply_number, c.title as btitle, c.id as bid,'.
             ' a.last_access_date from bbs_subject as a, bbs_reply as b, bbs_layout as c  where b.author=? '.
            ' and a.id = b.subject_id and a.layout_id = c.id  order by a.last_access_date desc, '.
            ' a.id desc';
      }

      $info_array = array();

      $res = $db->SelectLimit($sql, $get_number, $from_n, array($user_name));

      $now_user_id = UserUtil::getUserId($db, $_SESSION['user']['name']);

      while ( $rows = $res->FetchRow() ) {
         $user_id = UserUtil::getUserId($db, $rows['author']);

         //看状态。
         //==0, 开放
         //==1, 需要验证
         //==2, 帖子被关闭
         //如果被关闭，则不需看是否有新帖
         $status_image = 'no_topic.gif';
         if ( $rows['subject_status'] == 2 ) {
            $status_image = 'topic_lock.gif';
         } else {
            if ( !isset($_SESSION['user']) ) {
               $status_iamge = 'no_topic.gif';
            } else {
               if ( TopicUtil::haveNewReply($db, $rows['id'], $now_user_id) ) {
                  $status_image = 'new_topic.gif';
               } else {
                  $status_image = 'no_topic.gif';
               }
            }

         }


         $last_user_name = '';
         $last_user_id = '';
         $total_page = 0;
         $find_number = 0;
         if ( $rows['reply_number'] > 0 ) {
            $temp_sql = 'select author from bbs_reply where subject_id=? order by id desc ';
            $temp_res = $db->SelectLimit($temp_sql, 1, 0, array($rows['id']));
            $temp_rows = $temp_res->FetchRow();
            $last_user_name = $temp_rows['author'];
            $last_user_id = UserUtil::getUserId($db, $last_user_name);

            $temp_sql = 'select count(*) as num from bbs_reply where subject_id=?';
            $temp_sth = $db->Prepare($temp_sql);
            $temp_res = $db->Execute($temp_sth, array($rows['id']));
            $temp_rows = $temp_res->FetchRow();

            $total_number = $temp_rows['num'];

            $total_page = ceil( ($total_number+1) / 10 );

            if ( $total_page > 10 ) {
               $find_number = ($total_number+1) % 10;
            } else {
               $find_number = $total_number + 1;
            }
         }


         $temp_sql = 'select count(*) as num from bbs_subject_attach where subject_id=?';
         $temp_sth = $db->Prepare($temp_sql);
         $temp_res = $db->Execute($temp_sth, array($rows['id']));
         $temp_rows = $temp_res->FetchRow();
         $is_have_attach = $temp_rows['num'];


         $title = "";
         if( $rows['is_best'] ) {
            $title .= "&nbsp;<font color=red>[".LU_IS_BEST."]</font>";
         }

         $title .= $rows['title'];

         
         $info_array[] = array(
            'image' => $status_image,
            'id'=>$rows['id'],
            'title'=>$title,
            'have_new_reply' => ( $status_image == 'new_topic.gif'?1:0),
            'userid'=>$user_id,
            'username'=>$rows['author'],
            'clicks_number'=>$rows['click_number'],
            'reply_number'=>$rows['reply_number'],
            'last_time'=>set_locale_time($rows['last_access_date']),
            'last_username'=>$last_user_name,
            'last_userid'=>$last_user_id,
            'last_page'=>$total_page,
            'last_number'=>$find_number,
            'have_attach'=>$is_have_attach,
            'express'=>$rows['express'],
            'bid'=>$rows['bid'],
            'btitle'=>$rows['btitle'],
            );
      }

     return $info_array; 

   }/*}}}*/
   



   /**
    * 取得查询后的帖子的情况
    * @param:  &$id Database Connection
    * @param:  $id 论坛板块的id
    * @return; array
    * @acess:  public
    * @static
    */
   public static function &getCacheSubjectInfo(&$db, $pre_page, $offset_page, $q ) {/*{{{*/

      $sql = 'select id, title, author, subject_status, express, is_best, click_number, reply_number,'.
            ' last_access_date from bbs_subject '.$q.
            ' order by last_access_date desc';

      $info_array = array();


      $res = $db->CacheSelectLimit(1800, $sql, $pre_page, $offset_page);

      $now_user_id = UserUtil::getUserId($db, $_SESSION['user']['name']);

      while ( $rows = $res->FetchRow() ) {
         $user_id = UserUtil::getUserId($db, $rows['author']);

         //看状态。
         //==0, 开放
         //==1, 需要验证
         //==2, 帖子被关闭
         //如果被关闭，则不需看是否有新帖
         $status_image = 'no_topic.gif';
         if ( $rows['subject_status'] == 2 ) {
            $status_image = 'topic_lock.gif';
         } else {
            if ( !isset($_SESSION['user']) ) {
               $status_iamge = 'no_topic.gif';
            } else {
               if ( TopicUtil::haveNewReply($db, $rows['id'], $now_user_id) ) {
                  $status_image = 'new_topic.gif';
               } else {
                  $status_image = 'no_topic.gif';
               }
            }

         }


         $last_user_name = '';
         $last_user_id = '';
         $total_page = 0;
         $find_number = 0;
         if ( $rows['reply_number'] > 0 ) {
            $temp_sql = 'select author from bbs_reply where subject_id=? order by id desc ';
            $temp_res = $db->SelectLimit($temp_sql, 1, 0, array($rows['id']));
            $temp_rows = $temp_res->FetchRow();
            $last_user_name = $temp_rows['author'];
            $last_user_id = UserUtil::getUserId($db, $last_user_name);

            $temp_sql = 'select count(*) as num from bbs_reply where subject_id=?';
            $temp_sth = $db->Prepare($temp_sql);
            $temp_res = $db->Execute($temp_sth, array($rows['id']));
            $temp_rows = $temp_res->FetchRow();

            $total_number = $temp_rows['num'];

            $total_page = ceil( ($total_number+1) / 10 );

            if ( $total_page > 10 ) {
               $find_number = ($total_number+1) % 10;
            } else {
               $find_number = $total_number + 1;
            }
         }


         $temp_sql = 'select count(*) as num from bbs_subject_attach where subject_id=?';
         $temp_sth = $db->Prepare($temp_sql);
         $temp_res = $db->Execute($temp_sth, array($rows['id']));
         $temp_rows = $temp_res->FetchRow();
         $is_have_attach = $temp_rows['num'];


         $title = "";
         if( $rows['is_best'] ) {
            $title .= "&nbsp;<font color=red>[".LU_IS_BEST."]</font>";
         }

         $title .= $rows['title'];

         
         $info_array[] = array(
            'image' => $status_image,
            'id'=>$rows['id'],
            'title'=>$title,
            'have_new_reply' => ( $status_image == 'new_topic.gif'?1:0),
            'userid'=>$user_id,
            'username'=>$rows['author'],
            'clicks_number'=>$rows['click_number'],
            'reply_number'=>$rows['reply_number'],
            'last_time'=>set_locale_time($rows['last_access_date']),
            'last_username'=>$last_user_name,
            'last_userid'=>$last_user_id,
            'last_page'=>$total_page,
            'last_number'=>$find_number,
            'have_attach'=>$is_have_attach,
            'express'=>$rows['express']
            );
      }

     return $info_array; 

   }/*}}}*/
 

   /**
    * 删除在版块超时的用户
    * @param:  &$db Database Connection
    * @param:  $id   Layout id
    * @return: NULL
    * @access: public
    * @static
    */
   public static function delExpiresUser(&$db, &$sub_id_array) {/*{{{*/

      $sql = 'delete from bbs_layout_online_user where (access_time + 3000) < ? '.
         ' and layout_id in ('.implode(',', $sub_id_array).')';
      $sth = $db->Prepare($sql);
      $db->Execute($sth, array(time()));

      return true;
   }/*}}}*/

   /**
    * 更新本版的在线用户的访问信息
    * @param:  &$db
    * @return: null
    * @access: public
    * @static
    */
   public static function updateOnlineUser(&$db, $bbs_id) {/*{{{*/
      $session_id = session_id();

      $user_name = '';

      if ( isset($_SESSION['user']) ) {
         $user_name = $_SESSION['user']['name'];
      }

      if ( strlen($user_name) <= 0 ) {
         $user_name = $session_id;
      }

      $sql = 'select count(*) as num from bbs_layout_online_user where session_id=?';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($session_id));
      $rows = $res->FetchRow();

      if ( $rows['num'] ) {
         $update_sql = 'update bbs_layout_online_user set access_time=?, '.
            ' user_name=?, layout_id=? where session_id=? ';
         $sth = $db->Prepare($update_sql);
         $db->Execute($sth, array(time(), $user_name, $bbs_id, $session_id));

      } else {
         $insert_sql = 'insert into bbs_layout_online_user ( layout_id, user_name, '.
            ' session_id, access_time ) values (?, ?, ?, ?)';
         $sth = $db->Prepare($insert_sql);
         $db->Execute($sth, array($bbs_id, $user_name, $session_id, time()));
      }

   }/*}}}*/

   /**
    * 求所有版块的数组返回
    * @param:  &$db
    * @param:  &$array
    * @return: NULL
    * @access: public
    * @static
    */
   
   public static function getAllLayout(&$db, &$array, &$i, $parent_id = 0) {/*{{{*/
      $res = $db->Execute("select id,title from bbs_layout  where parent_id='".$parent_id."'");

      while ( $rows = $res->FetchRow() ) {
         if ( $rows['id'] ) {
            $prefix = str_repeat("&nbsp;", $i*12);
            array_push($array, array(
               'id'=>$rows['id'],
               'name'=>$prefix.$rows['title']));

            $i = $i + 1;
            self::getAllLayout($db, $array, $i, $rows['id']);
         }
      }
      $i = $i - 1;
   }/*}}}*/


}

?>
