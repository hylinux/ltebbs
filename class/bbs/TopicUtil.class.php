<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/bbs/TopicUtil.class.php
 *
 * 此类是用于操作主题和回复的工具类
 *
 * PHP Version 5
 *
 * @package:   class.bbs
 * @author:    Mike.G 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: www.5anet.com
 * @version:   $Id: TopicUtil.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @Date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'user/UserUtil.class.php';
include_once FUNCTION_PATH.'ConvertString.fun.php';
include_once FUNCTION_PATH.'set_locale_time.fun.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/TopicUtil.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/TopicUtil.lang.php';
}


class TopicUtil {
   /**
    * 判断有无新回复
    * @param:  &$db, 
    * @param:  $id
    * @return: boolan
    * @access: public
    * @static
    */
   public static function haveNewReply(&$db, $id, $user_id) {/*{{{*/
      //求用户最后访问的时间
      //$user_name = UserUtil::getUserNameById($db, $user_id);
      $last_time = UserUtil::getUserLastLogoutTime($db, $user_id);
      

      $sql = 'select last_access_date from bbs_subject where id=?';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($id));
      $rows = $res->FetchRow();
      $temp_time = $rows['last_access_date'];

      if ( $temp_time >= $last_time ) {
         return TRUE;
      } else {
         return FALSE;
      }

   }/*}}}*/

   /**
    * 判断帖子是否存在
    * @param:  &$db,
    * @param:  $id
    * @return: boolean
    * @access: public
    * @static
    */
   public static function isExists(&$db, $id) {/*{{{*/
      $sql = 'select count(*) as num from bbs_subject where id=?';
      $stmt = $db->Prepare($sql);
      $res = $db->Execute($stmt, array($id));
      $rows = $res->FetchRow();

      $num = $rows['num'];
      
      if ( $num ) {
         return TRUE;
      } else {
         return FALSE;
      }
   }/*}}}*/
   
   /**
    * 返回帖子的版块id
    * @param:  &$db
    * @param:  $id
    * $return: $layout_id integer
    * @access: public
    * @static
    */
   public static function getLayoutId(&$db, $id) {/*{{{*/
      $sql = 'select layout_id from bbs_subject where id=?';
      $stmt = $db->Prepare($sql);
      $res = $db->Execute($stmt, array($id));
      $rows = $res->FetchRow();

      return $rows['layout_id'];

   }/*}}}*/
   
   /**
    * 返回帖子的状态
    * @param:  &$db,
    * @param:  $id
    * @return: $topic_status integer
    * @access: public
    * @static
    */
   public static function getTopicStatus(&$db, $id) {/*{{{*/
      $sql = 'select subject_status from bbs_subject where id=?';
      $stmt = $db->Prepare($sql);
      $res = $db->Execute($stmt, array($id));
      $rows = $res->FetchRow();

      /**
       * 为0,则开放
       * 为1,则需要验证
       * 为2,则关闭
       */
      return $rows['subject_status'];

   }/*}}}*/

   /**
    * 求主题加上回复一共有的记录数
    * @param:  &$db
    * @param:  $id
    * @return: $recoreds integer
    * @access: public
    * @static
    */
   public static function getTopicNumber(&$db, $id) {/*{{{*/
      $sql = 'select count(*) as num from bbs_reply where subject_id=?';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($id));
      $rows = $res->FetchRow();

      return $rows['num']+1;
   }/*}}}*/

   /**
    * 求主题加上回复一共有的页数
    * @param:  &$db
    * @param:  $id
    * @return: $total_page
    * @access: public
    * @static
    */
   public static function getTotalPage(&$db, $id, $pre_page = 15) {/*{{{*/
      $total_number = self::getTopicNumber($db, $id);

      return ceil($total_number / $pre_page);
   }/*}}}*/

   /**
    * 帖子的标题
    * @param:  &$db
    * @param:  $id
    * @return: $title
    * @access: public
    * @static
    */
   public static function getTitle(&$db, $id) {/*{{{*/
      $sql = 'select title from bbs_subject where id=?';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($id));
      $rows = $res->FetchRow();

      return $rows['title'];
   }/*}}}*/

   /**
    * 更新帖子的浏览次数
    * @param;  &$db
    * @param:  $id
    * @return NULL
    * @access: public
    * @static
    */
   public static function updateViewNumber(&$db, $id) {/*{{{*/
      $sql = 'update bbs_subject set click_number=click_number+1 where id=?';
      $sth = $db->Prepare($sql);
      $db->Execute($sth, array($id));
      return 1;
   }/*}}}*/


   /**
    * 取得帖子的信息
    * @param:  &$db
    * @param:  $id
    * @param:  $pre_page
    * @param:  $offset_page
    * @return: $topic_array 
    * @access: public
    * @static
    */
   public static function getTopicInfo(&$db, $id, $pre_page=10, $offset_page = 0 ) {/*{{{*/
      $topic_array = array();

      $topic_status = self::getTopicStatus($db, $id);

      //如果显示第一页，则必须给出主题
      if ( $offset_page == 0 ) {
         $sql = 'select title, express, author, content, post_date, is_edit, '.
           ' edit_user, edit_time, subject_status, is_best, is_top from bbs_subject where id=?';
         $sth = $db->Prepare($sql);
         $res = $db->Execute($sth, array($id));

         $rows = $res->FetchRow();
         $posttime = set_locale_time($rows['post_date']);
         $user_name = $rows['author'];
         $user_id = UserUtil::getUserId($db, $user_name);
         $user_header = UserUtil::getUserHeader($db, $user_id);

         $user_info = UserUtil::getUserInfo($db, $user_id);

         $register_date = $user_info['register_date'];
         $user_level = $user_info['user_level'];
         $user_address = $user_info['user_hometown'];
         $user_topic_number = $user_info['user_topic'];
         $user_sign = ConvertString($user_info['user_sign'], ROOT_URL, IMAGE_URL.'express/');

         $is_edit = 0;
         $edit_user = '';
         $edit_time = '';

         if ( $rows['is_edit'] ) {
            $is_edit = 1;
            $edit_user = $rows['edit_user'];
            $edit_time = $rows['edit_time'];
         }


         $user_online = UserUtil::isOnline($db, $user_id);

         $user_can_be_edit = 0;

         if ( !$_SESSION['user']['name'] ) {
            $user_can_be_edit = 0;
         } else {
            if ( strtolower($_SESSION['user']['name']) == strtolower($user_name) ) {
               $user_can_be_edit = 1;
            } else if ( strtolower($_SESSION['user']['name']) != strtolower($user_name) ) {
               //判断用户是否是这个版块的版主。
               $dep = UserUtil::getUserDep($db, $_SESSION['user']['name']);

               if ( $dep == 1 || $dep == 2 ) {
                  $user_can_be_edit = 1;
               } else if ( $dep == 3 ) {
                  $temp_layout_id = self::getLayoutId($db, $id);
                  $user_can_be_edit = UserUtil::isThisLayoutAdmin($db, $id, $temp_layout_id, $_SESSION['user']['name']);
               }
            }
         }


         //判断是否有附件
         //如果有附件，则使用代码替换
         $content = '';

         if ( $topic_status == 2 ) {
            $content = TU_TOPIC_WAS_LOCKED;
         } else {
            $content = $rows['content'].self::haveAttach($db, $id);

            if ( $is_edit ) {
               $attach_string = TU_SUB_TITLE.$edit_user.TU_FROM.$edit_time.TU_EDIT;
               $content .= "\n\n".$attach_string;
            };
         }

         $title = $rows['title'];

         $title = htmlspecialchars($title);

         if ( $rows['is_best'] ) {
            $title = "<font color=red>[".BEST_LABEL."]</font>".$title;
         }

         if ( $rows['is_top'] ) {
            $title = "<font color=red>[".TOP_LABEL."]</font>".$title;
         }

         $topic_array[] = array(
            'id' => $id,
            'posttime' => $posttime,
            'sort_number' => 1,
            'user_name' => $user_name,
            'user_id' => $user_id,
            'user_header' => $user_header,
            'user_sign'=> $user_sign,
            'register_date' => $register_date,
            'user_level' => $user_level,
            'user_address' => $user_address,
            'user_topic_number' => $user_topic_number,
            'title' => $title,
            'content' => ConvertString($content, ROOT_URL, IMAGE_URL.'express/'),
            'online' => $user_online,
            'can_be_edit' => $user_can_be_edit,
            'is_topic' => 1,
            'express' => $rows['express']
         );

         $pre_page = $pre_page - 1;
      } else if ( $offset_page >= 1 ) {
         $offset_page = $offset_page - 1;
      }


      //再查回复的帖子
      $sql = 'select id, title, express,author, content, post_date, is_edit, edit_user, '.
         ' edit_time, reply_status from bbs_reply where subject_id=? '.
            ' order by id asc';
      $res = $db->SelectLimit($sql, $pre_page, $offset_page, array($id));

      while ( $rows = $res->FetchRow() ) {
         $posttime = set_locale_time($rows['post_date']);
         $sort_number = $sort_begin;
         $user_name = $rows['author'];
         $user_id = UserUtil::getUserId($db, $user_name);
         $user_header = UserUtil::getUserHeader($db, $user_id);

         $user_info = UserUtil::getUserInfo($db, $user_id);

         $register_date = $user_info['register_date'];
         $user_level = $user_info['user_level'];
         $user_address = $user_info['user_hometown'];
         $user_topic_number = $user_info['user_topic'];
         $user_sign = ConvertString($user_info['user_sign'], ROOT_URL, IMAGE_URL.'express/');

         $is_edit = 0;
         $edit_user = '';
         $edit_time = '';

         if ( $rows['is_edit'] ) {
            $is_edit = 1;
            $edit_user = $rows['edit_user'];
            $edit_time = $rows['edit_time'];
         }

         $user_online = UserUtil::isOnline($db, $user_id);

         $user_can_be_edit = 0;

         if ( !$_SESSION['user']['name'] ) {
            $user_can_be_edit = 0;
         } else {
            if ( strtolower($_SESSION['user']['name']) == strtolower($user_name) ) {
               $user_can_be_edit = 1;
            } else if ( strtolower($_SESSION['user']['name']) != strtolower($user_name) ) {
               //判断用户是否是这个版块的版主。
               $dep = UserUtil::getUserDep($db, $_SESSION['user']['name']);

               if ( $dep == 1 || $dep == 2 ) {
                  $user_can_be_edit = 1;
               } else if ( $dep == 3 ) {
                  $temp_layout_id = self::getLayoutId($db, $id);
                  $user_can_be_edit = UserUtil::isThisLayoutAdmin($db, $id, $temp_layout_id, $_SESSION['user']['name']);
               }
            }
         }

         $sort_number = self::getSortNumber($db, $id, $rows['id']);

         $content = '';
         $had_closed = 0;

         if ( $rows['reply_status'] ) {
            $had_closed = 1;
         }

         if ( $rows['reply_status'] || $topic_status == 2 ) {
            //如果回帖状态被设定，则表示改帖被关闭或者屏蔽
            $content = TU_TOPIC_WAS_LOCKED;
         } else {
            $content = $rows['content'].self::haveReplyAttach($db, $rows['id']);

            if ( $is_edit ) {
               $attach_string = TU_SUB_TITLE.$edit_user.TU_FROM.$edit_time.TU_EDIT;
               $content .= "\n\n".$attach_string;
            };
         }


         $title = htmlspecialchars($rows['title']);


         $topic_array[] = array(
            'id' => $rows['id'],
            'posttime' => $posttime,
            'sort_number' => $sort_number,
            'user_name' => $user_name,
            'user_id' => $user_id,
            'user_header' => $user_header,
            'user_sign'=>$user_sign,
            'register_date' => $register_date,
            'user_level' => $user_level,
            'user_address' => $user_address,
            'user_topic_number' => $user_topic_number,
            'title' => $title,
            'content' => ConvertString($content, ROOT_URL, IMAGE_URL.'express/'),
            'online' => $user_online,
            'can_be_edit' => $user_can_be_edit,
            'is_topic' => 0,
            'express' => $rows['express'],
            'had_closed' => $had_closed,
         );
      }


      return $topic_array; 

   }/*}}}*/




   /**
    * 求回复在所有的回复中排在第几个位置
    * @param:  &$db
    * @param:  $id 主题的id
    * @param:  $reply_id 帖子的id
    * @return: $num
    * @access: public
    * @static
    */
   public static function getSortNumber(&$db, $id, $reply_id) {/*{{{*/
      $sql = 'select count(*) as num from bbs_reply where subject_id=? '.
         ' and id<=? ';

      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($id, $reply_id));
      $rows = $res->FetchRow();

      return $rows['num'] + 1;
   }/*}}}*/

   /**
    * 判断用户是否有附件
    * @param:  &$db
    * @param:  $id
    * @return: $add_string
    * @access: public
    * @static
    */
   public static function haveAttach(&$db, $id) {/*{{{*/
      $sql = 'select subject_id, file_type from bbs_subject_attach where subject_id=?';
      $res = $db->SelectLimit($sql, 1, 0, array($id));
      $rows = $res->FetchRow();

      if ( $rows['subject_id'] ) {
         return "\n[img]upload/attach/".$rows['subject_id'].$rows['file_type'].'[/img]';
      } 
   }/*}}}*/

   /**
    * 判断用户是否有附件
    * @param:  &$db
    * @param:  $id
    * @return: $add_string
    * @access: public
    * @static
    */
   public static function haveReplyAttach(&$db, $id) {/*{{{*/
      $sql = 'select reply_id, file_type from bbs_reply_attach where reply_id=?';
      $res = $db->SelectLimit($sql, 1, 0, array($id));
      $rows = $res->FetchRow();

      if ( $rows['reply_id'] ) {
         return "\n[img]upload/attach/reply/".$rows['reply_id'].$rows['file_type'].'[/img]';
      } 
   }/*}}}*/

   /**
    * 判断用户回复是否存在
    * @param:  &$db,
    * @param:  $id
    * @return: boolean
    * @access: public
    * @static
    */
   public static function replyIsExists(&$db, $id) {/*{{{*/
      $sql = 'select count(*) as num from bbs_reply where id=?';
      $stmt = $db->Prepare($sql);
      $res = $db->Execute($stmt, array($id));
      $rows = $res->FetchRow();

      $num = $rows['num'];
      
      if ( $num ) {
         return TRUE;
      } else {
         return FALSE;
      }
   }/*}}}*/
 
   /**
    * 从用户回复中返回的版块id
    * @param:  &$db
    * @param:  $id
    * $return: $layout_id integer
    * @access: public
    * @static
    */
   public static function getLayoutFromReplyId(&$db, $id) {/*{{{*/
      $sql = 'select layout_id from bbs_reply where id=?';
      $stmt = $db->Prepare($sql);
      $res = $db->Execute($stmt, array($id));
      $rows = $res->FetchRow();

      return $rows['layout_id'];

   }/*}}}*/
 

}

?>
