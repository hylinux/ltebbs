<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/bbs/ShowEdit.class.php
 *
 * 显示用户编辑帖子的界面
 * 注意这里需要权限的判断。
 *
 *
 * PHP Version 5
 *
 * @package:   class.bbs
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: ShowEdit.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';

//包含必要的类
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';
include_once CLASS_PATH.'bbs/TopicUtil.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';

include_once LIB_PATH.'fckeditor/fckeditor.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ShowEdit.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ShowEdit.lang.php';
}

class ShowEdit extends BaseAction {


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
    * 运行本类
    */
   public function run() {/*{{{*/
      $id = $this->getParameterFromGET('id');
      $is_topic = $this->getParameterFromGET('topic');
      $bbs_id = 0;

      //判断$id是否存在。
      if ( $is_topic == 1 ) {
         //如果等于1,则为主题
         if ( !TopicUtil::isExists($this->db, $id) ) {
            $this->AlertAndBack(SE_TOPIC_ID_IS_NOT_EXISTS);
            return;
         } else {
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
               $dep = UserUtil::getUserDep($this->db, $_SESSION['user']['name']);

               if ( $dep == 1 || $dep == 2 ) {
                  $user_can_be_edit = 1;
               } else if ( $dep == 3 ) {
                  $user_can_be_edit = UserUtil::isThisLayoutAdmin($this->db, $id, $bbs_id, $_SESSION['user']['name']);
               }
            }

            if ( !$user_can_be_edit ) {
               $this->AlertAndBack(SE_YOU_HAVE_NO_PRIVIATE);
               return;
            }
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
            $dep = UserUtil::getUserDep($this->db, $_SESSION['user']['name']);

            if ( $dep == 1 || $dep == 2 ) {
               $user_can_be_edit = 1;
            } else if ( $dep == 3 ) {
               $user_can_be_edit = UserUtil::isThisLayoutAdmin($this->db, $subject_id, $bbs_id, $_SESSION['user']['name']);
            }
         }

         if ( !$user_can_be_edit ) {
            $this->AlertAndBack(SE_YOU_HAVE_NO_PRIVIATE);
            return;
         }
      }


      $smarty = $this->getSmarty();

      if ( $is_topic ) {
         $smarty->assign('clone_title', SE_EDIT_TOPIC);
      } else {
         $smarty->assign('clone_title', SE_EDIT_REPLY);
      }


      //更新用户在本版的信息
      LayoutUtil::updateOnlineUser($this->db, $bbs_id);
      //返回论坛上面的导行栏。
      $nav_array = LayoutUtil::getParentLayoutInfo($this->db, $bbs_id);
      //导航栏
      $smarty->assign('nav_array', $nav_array);


      //先删除已经不存在的用户
      LayoutUtil::delNotExistsUser($this->db);


      //用户有权限了。
      //则可以开始显示用户帖子的内容
      $smarty->assign('id_edit', 1);
      $smarty->assign('is_topic', $is_topic);
      $smarty->assign('is_edit', 1);
      $smarty->assign('bbsid', $id);

      if ( $is_topic == 1 ) {
         //如果$is_topic 等于1, 则为主题
         $sql = 'select title, content, express from bbs_subject where id=?';
         $sth = $this->db->Prepare($sql);
         $res = $this->db->Execute($sql, array($id));
         $rows = $res->FetchRow();

         $smarty->assign('temp_title', $rows['title']);

         $fck = new FCKeditor("content");
         $fck->BasePath = FCKEDITOR_BASEPATH;
         if ( get_magic_quotes_gpc() ) {
             $fck->Value = stripslashes($rows['content']);
         } else {
             $fck->Value = $rows['content'];
         }

         $smarty->assign('fck', $fck);

         $smarty->assign('temp_express', $rows['express']);

         //查询是否有附件
         $sql = 'select subject_id, file_type from bbs_subject_attach where subject_id=?';
         $sth = $this->db->Prepare($sql);
         $res = $this->db->Execute($sth, array($id));
         $rows = $res->FetchRow();

         if ( $rows['subject_id'] ) {
            $filename = ROOT_URL.'upload/attach/'.$rows['subject_id'].$rows['file_type'];
            $smarty->assign('image_name', $filename);
         }

      } else {
         $sql = 'select title, content, express from bbs_reply where id=?';

         $sth = $this->db->Prepare($sql);
         $res = $this->db->Execute($sql, array($id));
         $rows = $res->FetchRow();

         $smarty->assign('temp_title', $rows['title']);
         $fck = new FCKeditor("content");
         $fck->BasePath = FCKEDITOR_BASEPATH;
//         $fck->Value = $rows['content'];

         if ( get_magic_quotes_gpc() ) {
             $fck->Value = stripslashes($rows['content']);
         } else {
             $fck->Value = $rows['content'];
         }

         $smarty->assign('fck', $fck);

         $smarty->assign('temp_express', $rows['express']);

         //查询是否有附件
         $sql = 'select reply_id, file_type from  bbs_reply_attach where reply_id=?';
         $sth = $this->db->Prepare($sql);
         $res = $this->db->Execute($sth, array($id));
         $rows = $res->FetchRow();

         if ( $rows['reply_id'] ) {
            $filename = ROOT_URL.'upload/attach/reply/'.$rows['reply_id'].$rows['file_type'];
            $smarty->assign('image_name', $filename);
         }

      }



      $smarty->display('topic.tmpl');

      return;      
   }/*}}}*/

}

?>
