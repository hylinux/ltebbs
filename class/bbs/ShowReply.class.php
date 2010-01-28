<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/bbs/ShowReply.class.php
 *
 * 这个类是用来向用户显示回复帖子的界面的
 * 在回复帖子的时候我们需要考虑到几个因素
 * 1、用户是必须登录的。
 * 2、在Session里保留一些需要保存的数据
 * 3、附件的处理
 *
 * PHP Version 5
 *
 * @package:   class.bbs
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: ShowReply.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
//包含必要的类
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';
include_once CLASS_PATH.'bbs/TopicUtil.class.php';

include_once LIB_PATH.'fckeditor/fckeditor.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ShowReply.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ShowReply.lang.php';
}

class ShowReply extends BaseAction {

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
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {/*{{{*/
      $smarty = $this->getSmarty();

      $smarty->assign('clone_title', CREATE_NEW_REPLY);
      
      //取得主题的id
      $topic_id = $this->getParameterFromGET('id');
      
      //验证帖子的是否存在
      if ( !TopicUtil::isExists($this->db, $topic_id) ) {
         $this->AlertAndBack(TOPIC_IS_NOT_EXISTS);
         return;
      }

      //验证帖子的状态
      $status = TopicUtil::getTopicStatus($this->db, $topic_id);

      //注意状态为3,则为帖子被锁定，不能回复了。
      if ( $status == 2 ) {
         $this->AlertAndBack(TOPIC_HAD_BE_CLOSED);
         return;
      } else if ( $status == 3 ) {
         $this->AlertAndBack(TOPIC_HAD_BE_LOCK);
         return;
      }

      //取得该帖子所在的版块
      $layout_id = TopicUtil::getLayoutId($this->db, $topic_id);

      if ( !LayoutUtil::isExists($this->db, $layout_id)) {
         //论坛不存在，则转向首页
         $this->forward('index.php');
      }

      //更新用户在本版的信息
      LayoutUtil::updateOnlineUser($this->db, $layout_id);

      $bbs_status = LayoutUtil::getLayoutStatus($this->db, $layout_id);
      if ( $bbs_status == 1 && !isset($_SESSION['user']) ) {
         $this->AlertAndForward(SRY_NEED_LOGIN, 'index.php?module=user&action=showlogin');
         return;
      } else if ( $bbs_status == 2 ) {
         $this->AlertAndForward(SRY_LAYOUT_WAS_CLOSED, 'index.php');
         return;
      } else if ( $bbs_status == 3 ) {
         //等于三不允许发帖
         $this->AlertAndBack(SRY_NOW_ALLOW_NEW_TOPIC);
         return;
      } else if ( LayoutUtil::isClosedByParent($this->db, $bbs_id) ) {
         $this->AlertAndForward(SRY_LAYOUT_WAS_CLOSED, 'index.php');
         return;
      }


      //返回论坛上面的导行栏。
      $nav_array = LayoutUtil::getParentLayoutInfo($this->db, $layout_id);
      //导航栏
      $smarty->assign('nav_array', $nav_array);


      //先删除已经不存在的用户
      LayoutUtil::delNotExistsUser($this->db);


      //从Session里读出数据
      $temp_title = $_SESSION['temp_title'];
      if ( strlen($temp_title) <= 0 ) {
         $smarty->assign('temp_title', $temp_title);
      } 

      //看看是否是引用
      $quote = $this->getParameterFromGET('quote');
      $reply_id = $this->getParameterFromGET('replyid');

      $temp_content = $_SESSION['temp_content'];

      $fck = new FCKeditor("content");
      $fck->BasePath = FCKEDITOR_BASEPATH;


      if ( $temp_content ) {
         if ( get_magic_quotes_gpc() ) {
             $fck->Value = stripslashes($temp_content);
         } else {
             $fck->Value = $temp_content;
         }
      } else {
         if ( $quote == 1 ) {
            if ( $reply_id == 0 ) {
               $sql = 'select content from bbs_subject where id=?';
               $sth = $this->db->Prepare($sql);
               $res = $this->db->Execute($sth, array($topic_id));
               $rows = $res->FetchRow();
                 if ( get_magic_quotes_gpc() ) {
                     $temp_content1 = stripslashes($rows['content']);
                 } else {
                     $temp_content1 = $rows['content'];
                 }
               $fck->Value = '[quote]'.$temp_content1.'[/quote]';
            } else {
               $sql = 'select content from bbs_reply where id=?';
               $sth = $this->db->Prepare($sql);
               $res = $this->db->Execute($sth, array($reply_id));
               $rows = $res->FetchRow();

                 if ( get_magic_quotes_gpc() ) {
                     $temp_content1 = stripslashes($rows['content']);
                 } else {
                     $temp_content1 = $rows['content'];
                 }
               $fck->Value = '[quote]'.$temp_content1.'[/quote]';

            }
         }
      }


      $smarty->assign('fck', $fck);

      $temp_express = $_SESSION['temp_express'];
      $smarty->assign('temp_express', $temp_express);


      $smarty->assign('is_new_topic', 0);
      $smarty->assign('is_new_reply', 1);

      $smarty->assign('bbsid', $topic_id);

      $smarty->display('topic.tmpl');
   }
/*}}}*/


}


?>
