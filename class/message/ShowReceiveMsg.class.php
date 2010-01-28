<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/message/ShowReceiveMsg.class.php
 *
 * 显示用户的短消息的内容
 *
 * PHP Version 5
 *
 * @package:   class.message
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: ShowReceiveMsg.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';

include_once FUNCTION_PATH.'ConvertString.fun.php';


//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ShowReceiveMsg.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ShowReceiveMsg.lang.php';
}


class ShowReceiveMsg extends BaseAction {
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
    * 显示短消息的内容
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {
      //读取用户传入的id
      $id = $this->getParameterFromGET('id');

      if ( !$id ) {
         $this->AlertAndBack(SR_ID_IS_EMPTY);
         return;
      }

      //求得用户的id
      $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);

      //验证id是否存在
      $sql = 'select count(*) as num from message_inbox where id=? and user_id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($id, $user_id));
      $rows = $res->FetchRow();

      if ( !$rows['num'] ) {
         $this->AlertAndBack(SR_ID_IS_NOT_EXISTS_OR_NOT_BELONE_USER);
         return;
      }


      $smarty = $this->getSmarty();


      $smarty = $this->getSmarty();
      $user_name = $_SESSION['user']['name'];
      $smarty->assign('view_user_name', $user_name);

      //用户的所在组
      $sql = 'select b.group_name from base_user_info as a join sys_group as b on '.
         ' a.group_dep = b.id where a.id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      $smarty->assign('user_roles', $rows['group_name']);

        //加入统计信息
      $user_header = UserUtil::getUserHeader($this->db, $user_id);
      $smarty->assign('head_url', $user_header);

      //查询新的短消息的数量
      $sql = 'select count(*) as num from message_inbox where user_id=? and is_read = 0 ';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      $smarty->assign('new_message_label', $rows['num']);


      //共有短消息数
      $sql = 'select count(*) as num from message_inbox where user_id=? ';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      $smarty->assign('total_message_number', $rows['num']);

      //取得用户注册时间
      $sql = 'select register_date from base_user_info where id=?';
      $stmt = $this->db->prepare($sql);
      $res  = $this->db->CacheExecute(60*60, $stmt, array($user_id));
      $rows = $res->FetchRow();

      $smarty->assign('user_register_date', $rows['register_date']);


      //取得用户最后的登录时间
      $sql = 'SELECT from_unixtime(last_time) as lastlogout FROM `user_last_time_logout` where user_id=?';
      $stmt = $this->db->prepare($sql);
      $res = $this->db->CacheExecute(60*60, $stmt, array($user_id));
      $rows = $res->FetchRow();

      $smarty->assign('user_last_logout', $rows['lastlogout']);

      //发表的主题数
      $sql = 'select count(*) as num from bbs_subject where author = ?';
      $stmt = $this->db->Prepare($sql);
      $res = $this->db->CacheExecute(10, $stmt, array($user_name));
      $rows = $res->FetchRow();
      $smarty->assign('all_topic_number', $rows['num']);

      //参与的帖子数
      $sql = 'select count(*) as num from bbs_reply where author=?';
      $stmt = $this->db->Prepare($sql);
      $res = $this->db->CacheExecute(10, $stmt, array($user_name));
      $rows = $res->FetchRow();

      $smarty->assign('all_reply_number', $rows['num']);

      ///拥有的短消息的数量
      $sql = 'select count(*) as num from message_inbox where user_id=?';
      $stmt = $this->db->prepare($sql);
      $res = $this->db->CacheExecute(20, $stmt, array($user_id));
      $rows = $res->FetchRow();
      $number_inbox = $rows['num'];

      $sql = 'select count(*) as num from message_outbox where user_id=?';
      $stmt = $this->db->prepare($sql);
      $res = $this->db->CacheExecute(20, $stmt, array($user_id));
      $rows = $res->FetchRow();

      $smarty->assign('message_all_number', $number_inbox + $rows['num']);

      //拥有的收藏数
      $sql = 'select count(*) as num from favor where user_id=?';
      $stmt = $this->db->prepare($sql);
      $res = $this->db->CacheExecute(10, $stmt, array($user_id));
      $rows = $res->FetchRow();

      $smarty->assign('favor_amount', $rows['num']);





      //使得短消息成为已读
      $sql = 'update message_inbox set is_read = 1 where id=?';
      $sth = $this->db->Prepare($sql);
      $this->db->Execute($sth, array($id));

      //查询短消息的内容
      $sql = 'select a.title, a.send_user_id, a.receive_time, a.content, '.
         ' b.user_name from message_inbox a '.
         ' left join base_user_info b on a.send_user_id = b.id where a.id=?';

      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($id));
      $rows = $res->FetchRow();

      if ( $rows['send_user_id'] == 0 ) {
         $sender = 'system';
      } else {
         $sender = $rows['user_name'];
      }

      $smarty->assign('title', $rows['title']);
      $smarty->assign('sender', $rows['user_name']);
      $smarty->assign('send_time', $rows['receive_time']);
      $smarty->assign('content', ConvertString($rows['content'], ROOT_URL, IMAGE_URL.'express/'));


      $smarty->display('showmsg.tmpl');

   }

}


?>
