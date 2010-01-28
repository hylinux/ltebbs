<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/bbs/SaveEdit.class.php
 *
 * 保存编辑后的帖子或者是主题
 *
 * PHP Version 5
 *
 * @package:   class.bbs
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: SaveEdit.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';
include_once CLASS_PATH.'bbs/TopicUtil.class.php';

include_once FUNCTION_PATH.'getCurrentDate.fun.php';



//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/SaveEdit.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/SaveEdit.lang.php';
}

class SaveEdit extends BaseAction {
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
    * 保存编辑后的帖子
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {
      //收集变量
      $topic = $this->getParameterFromPOST('topic');
      $id = $this->getParameterFromPOST('id');
      $title = $this->getParameterFromPOST('title');
      $content = $this->getParameterFromPOST('content');
      $express = $this->getParameterFromPOST('express');
      $delattach = $this->getParameterFromPOST('delattach');


      if ( !$title || strlen($title)<=0 ) {
         $this->AlertAndBack(SE_TITLE_IS_EMPTY);
         return;
      }

      /*
      if ( strlen($title) > 140 ) {
         $this->AlertAndBack(SE_TITLE_TOO_LONG);
         return;
      }*/



      if ( !$content || strlen($content) <= 0 ) {
         $this->AlertAndBack(SE_CONTENT_IS_EMPTY);
         return;
      }


      //做出基本的判断/*{{{*/
      if ( !$id ) {
         $this->AlertandBack(SE_NO_TOPIC_ID);
         return;
      }

      //找出这个帖子所在的版块的id, 作者。
      $layout_id = 0;
      $author = '';

      if ( $topic ) {
         //如果是主题
         $sql = 'select layout_id, author from bbs_subject where id=?';
         $sth = $this->db->prepare($sql);
         $res = $this->db->execute($sth, array($id));
         $rows = $res->FetchRow();

         $layout_id = $rows['layout_id'];
         $author = $rows['author'];
      } else {
         $sql = 'select layout_id, author from bbs_reply where id=?';
         $sth = $this->db->prepare($sql);
         $res = $this->db->execute($sth, array($id));
         $rows = $res->FetchRow();

         $layout_id = $rows['layout_id'];
         $author = $rows['author'];
      }

      if ( !$layout_id ) {
         //保存的帖子根本不存在。
         $this->AlertAndBlack(SE_TOPIC_IS_NOT_EXISTS);
         return;
      }

      //判断用户是否可以编辑
       if ( $topic ) {
         //如果等于1,则为主题
            //如果存在，则判断用户是否有权利修改
            $sql = 'select author, layout_id from bbs_subject where id=?';
            $sth = $this->db->Prepare($sql);
            $res = $this->db->Execute($sth, array($id));
            $rows = $res->FetchRow();
            $user_name = $rows['author'];

            $bbs_id = $rows['layout_id'];

            $user_can_be_edit = 0;
            if ( strtolower($_SESSION['user']['name']) == strtolower($user_name) ) {
               $user_can_be_edit = 1;
            } else if ( strtolower($_SESSION['user']['name']) != strtolower($user_name) ) {
               //判断用户是否是这个版块的版主。
               $dep = UserUtil::getUserDep($db, $_SESSION['user']['name']);

               if ( $dep == 1 || $dep == 2 ) {
                  $user_can_be_edit = 1;
               } else if ( $dep == 3 ) {
                  $user_can_be_edit = UserUtil::isThisLayoutAdmin($db, $id, $_SESSION['user']['name']);
               }
            }

            if ( !$user_can_be_edit ) {
               $this->AlertAndBack(SE_YOU_HAVE_NO_PRIVIATE);
               return;
            }
      } else {
         //$topic 为其他值，那么就是回帖，而不是主题
         $sql = 'select author, subject_id, layout_id from bbs_reply where id=?';
         $sth = $this->db->Prepare($sql);
         $res = $this->db->Execute($sth, array($id));
         $rows = $res->FetchRow();

         if ( !$rows['author'] ) {
            $this->AlertAndBack(SE_TOPIC_ID_IS_NOT_EXISTS);
            return;
         }

         //如果存在，
         //则判断用户是否有权限
         $user_name = $rows['author'];
         $subject_id = $rows['subject_id'];
         $bbs_id = $rows['layout_id'];

         $user_can_be_edit = 0;
         if ( strtolower($_SESSION['user']['name']) == strtolower($user_name) ) {
            $user_can_be_edit = 1;
         } else if ( strtolower($_SESSION['user']['name']) != strtolower($user_name) ) {
            //判断用户是否是这个版块的版主。
            $dep = UserUtil::getUserDep($db, $_SESSION['user']['name']);

            if ( $dep == 1 || $dep == 2 ) {
               $user_can_be_edit = 1;
            } else if ( $dep == 3 ) {
               $user_can_be_edit = UserUtil::isThisLayoutAdmin($db, $subject_id, $_SESSION['user']['name']);
            }
         }

         if ( !$user_can_be_edit ) {
            $this->AlertAndBack(SE_YOU_HAVE_NO_PRIVIATE);
            return;
         }
      }
    /*}}}*/



      //判断做完了，则可以开始进行更新了。
      
      //求现在的时间
      $now = getNoFormateCurrentDate();


      if ( $topic ) {
         $user_name = $_SESSION['user']['name'];

         $sql = 'update bbs_subject set title=?, content=?, express=?, is_edit=1, '.
           ' edit_user=?, edit_time=? where id=?';
         $sth = $this->db->prepare($sql);
         $this->db->execute($sth, 
            array($title, $content, $express, $user_name, $now, $id));

         if ( $this->db->ErrorNo() ) {
            $this->AlertAndBack($this->db->ErrorMsg());
            return;
         }

         if ( $delattach ) {
            //删除这个附件
            $sql = 'select file_type from bbs_subject_attach where subject_id=?';
            $sth = $this->db->prepare($sql);
            $res = $this->db->execute($sth, array($id));
            $rows = $res->FetchRow();
            $file_type = $rows['file_type'];

            $del_sql = 'delete from bbs_subject_attach where subject_id=?';
            $sth = $this->db->prepare($del_sql);
            $this->db->execute($sth, array($id));

            //删除文件。
            $filename = ROOT_PATH.'upload/attach/'.$id.$file_type;
            unlink($filename);
         }
      } else {
         $user_name = $_SESSION['user']['name'];

         $sql = 'update bbs_reply set title=?, content=?, express=?, is_edit=1, '.
           ' edit_user=?, edit_time=? where id=?';
         $sth = $this->db->prepare($sql);
         $this->db->execute($sth, 
            array($title, $content, $express, $user_name, $now, $id));

         if ( $this->db->ErrorNo() ) {
            $this->AlertAndBack($this->db->ErrorMsg());
            return;
         }


         if ( $delattach ) {
            //删除这个附件
            $sql = 'select file_type from bbs_reply_attach where reply_id=?';
            $sth = $this->db->prepare($sql);
            $res = $this->db->execute($sth, array($id));
            $rows = $res->FetchRow();
            $file_type = $rows['file_type'];

            $del_sql = 'delete from bbs_reply_attach where reply_id=?';
            $sth = $this->db->prepare($del_sql);
            $this->db->execute($sth, array($id));

            //删除文件。
            $filename = ROOT_PATH.'upload/attach/reply/'.$id.$file_type;
            unlink($filename);
         }

      }


      //编辑成功后，返回当时的页面
      if ( $topic ) {
         //如果是主页
         //则返回第一页
          $this->TipsAndForward(
              SE_SAVE_EDIT_SUCCESS,
              'index.php?module=bbs&action=viewtopic&id='.$id);

         return;
      } else {
         //不是主题
         //则是回复
         //求这个回帖的位置所在的位置
         $sql = 'select subject_id from bbs_reply where id=?';
         $sth = $this->db->prepare($sql);
         $res = $this->db->Execute($sth, array($id));
         $rows = $res->FetchRow();
         
         $sort_number = TopicUtil::getSortNumber($this->db, $rows['subject_id'], $id);

         $page = ceil ( $sort_number / 10 );

         //这里还有很多的工作需要做
         $this->TipsAndForward(
             SE_SAVE_EDIT_SUCCESS,
             'index.php?module=bbs&action=viewtopic&id='.$rows['subject_id'].'&page='.$page.
             '#topic'.$sort_number);

      }

   }

}
