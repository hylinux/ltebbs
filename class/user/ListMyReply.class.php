<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/ListMyReply.class.php
 *
 * 分页列出用户参与发表的主题
 *
 * PHP Version 5
 *
 * @package:   class.user
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: $
 * @date:      $Date:$
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';


//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ListMyReply.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ListMyReply.lang.php';
}


class ListMyReply extends BaseAction {

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
    * 显示用户的控制面板
    */
   public function run() {

      //求得用户的id
      $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);

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


      //取得导航栏菜单
      //开始检查帖子。
    $total_number = LayoutUtil::getTotalNumberReplyByUser($this->db, $user_name);

     //求总公的页面
     $total_page = ceil($total_number / $this->page_number );
  
     //取得当前的页面
     $page = $this->getParameter('page');

     if ( !$page || $page < 0 ) {
        $page = 1;
     }

     if ( $page > $total_page && $total_page > 0 ) {
        $page = $total_page;
     }

     $begin_page = 1;
     $end_page = $total_page;

     if ( $page <= 10 && $total_page >=10  ) {
        $end_page = 10; 
     } else if ( $page > 10  ) {
        if ( $page % 10 == 0 ) {
           //向前翻
           $end_page = $page; 

           $begin_page = $end_page - 9;

        } else if ( $page % 10 == 1 ) {

           //向后翻
           //确定开始的页数
           $begin_page = $page; 

           if ( $begin_page > $total_page ) {
              $begin_page = $page - 9;
           }

           if ( ( $begin_page + 9 ) > $total_page ) {
              $end_page = $total_page;
           } else {
              $end_page = $begin_page + 9;
           } 

        } else {
           $num = $page % 10;
           $pre_num = floor($page / 10 );
           $begin_page = $pre_num * 10 + 1;
           $end_page = $begin_page + 9;
        }
     }

     if ( $end_page > $total_page ) {
        $end_page = $total_page;
     }

     $nav_page_array = array();
     for( $i = $begin_page; $i<=$end_page; $i++ ) {
        array_push($nav_page_array, $i);
     }
     //帖子导航栏
     $smarty->assign('nav_page', $nav_page_array);
     //当前的页面
     $smarty->assign('now_page', $page);
     //共有的页面
     $smarty->assign('total_page', $total_page);



     $subject_array = LayoutUtil::getReplyInfoByUser($this->db, $user_name, 
     $this->page_number, ($page -1 ) * $this->page_number, $show_best);
     $smarty->assign('subject', $subject_array);




      $smarty->display('listmyreply.tmpl');




   }





   //缺省每页显示多少条记录
   private $page_number = 15;


}

?>