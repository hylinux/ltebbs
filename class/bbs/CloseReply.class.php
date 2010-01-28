<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/bbs/CloseReply.class.php
 *
 * 锁定一个用户回复
 * 在操作之前之前要判断用户的身份。
 * 登录不需判断。
 * 因为已经判断过了。
 * 但是需要判断用户是否是这个版的版主，或者用户是管理员。
 * 还需对传入的版块做出判断
 *
 * PHP Version 5
 *
 * @package:   class.bbs
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: CloseReply.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'bbs/TopicUtil.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/CloseReply.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/CloseReply.lang.php';
}

class CloseReply extends BaseAction {

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
    * 关闭这个主题
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {
      
      //取回用户需要设置帖子id
      $id = $this->getParameterFromGET('id');
      
      if ( !$id ) {
         $this->AlertAndBack(CR_ID_IS_EMPTY);
         return;
      }

      //验证主题是否存在
      if ( !TopicUtil::replyIsExists($this->db, $id) ) {
         $this->AlertAndBack(CR_ID_IS_NOT_EXISTS);
         return;
      }


      //验证用户的身份
      $sql = 'select id, group_dep from base_user_info where lower(user_name) =?';
      $sth = $this->db->prepare($sql);
      $res = $this->db->Execute($sth, array(
         strtolower($_SESSION['user']['name'])));
      $rows = $res->FetchRow();

      $user_id = $rows['id'];
      $user_group = $rows['group_dep'];

      if ( $user_group != 1 && $user_group != 2 && $user_group != 3 ) {
         //用户没有权限锁定回复
         $this->AlertAndBack(CR_USER_HAVE_NO_PRIVILEGES);
         return;
      }

      if ( $user_group == 3 ) {
         //如果用户是版主
         //则查看用户是否是本版的版主
         $layout_id = TopicUtil::getLayoutFromReplyId($this->db, $id);

         $temp_array = array();
         LayoutUtil::getParentId($this->db, $layout_id, $temp_array);
         array_push($temp_array, $layout_id);

         $sql = 'select count(*) as num from bbs_layout_manager where user_id=? and '.
            ' layout_id in ('.implode(',', $temp_array).')';
         $sth = $this->db->prepare($sql);
         $res = $this->db->Execute($sth, array($user_id));
         $rows = $res->FetchRow();


         if ( !$rows['num'] ) {
            $this->AlertAndBack(CR_USER_HAVE_NO_PRIVILEGES);
            return;
         }
      }


      //其他的情况中用户是可以解锁这个回复的
      //用户是这个版块的版主
      //用户是超级版主
      //用户是系统管理员

      $sql = 'update bbs_reply set reply_status=1 where id=?';
      $sth = $this->db->Prepare($sql);
      $this->db->Execute($sth, array($id));

      //成功后，则转向
      //求这个回帖的位置所在的位置

      $sql = 'select subject_id from bbs_reply where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($id));
      $rows = $res->FetchRow();
      $topic_id = $rows['subject_id'];

      $sort_number = TopicUtil::getSortNumber($this->db, $topic_id, $id);

      $page = ceil ( $sort_number / 10 );

      //这里还有很多的工作需要做
      $this->forward('index.php?module=bbs&action=viewtopic&id='.$topic_id.'&page='.$page.
         '#topic'.$sort_number);


   }


}

?>
