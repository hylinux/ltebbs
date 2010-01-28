<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/bbs/ShowIndex.class.php
 *
 * 显示BBS的首页
 *
 * PHP Version 5
 * @package:   class.bbs
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: ShowBBSIndex.class.php,v 1.2 2006-09-24 14:38:08 ghw Exp $
 * @date:      $Date: 2006-09-24 14:38:08 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';

//包含需要用到的函数
include_once FUNCTION_PATH.'utf8_substr.fun.php';
include_once FUNCTION_PATH.'set_locale_time.fun.php';

//包含需要用到的类
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';

include_once FUNCTION_PATH.'ConvertString.fun.php';


//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ShowBBSIndex.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ShowBBSIndex.lang.php';
}

class ShowBBSIndex extends BaseAction {
   /**
    * 数据库的连接
    */
   public $db = null;

   /**
    * 构造函数
    * 用户生成这个数据库的连接
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function __construct() {
      $this->db = $this->getDB();
   }


   /**
    * 显示BBS的首页
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {
      //先删除已经不存在的用户
      LayoutUtil::delNotExistsUser($this->db);

      //取得站点的公告，并显示在页面上
      $is_have_post = false;
      $post_str = '';
      if ( PostUtil::haveNotExpirePost($this->getDB()) ) {
         $is_have_post = true;
         $post_array = PostUtil::getPost($this->getDB(), 3);

         foreach ( $post_array as $post_rows ) {
            $post_str .= '<a href=\'index.php?module=post&action=view&id='.
               $post_rows['id'].'\' title=\''.$post_rows['title'].'\'>'.
               utf8_substr($post_rows['title'], 0, 35).'</a>'.
               '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
         }
      }



      //取得论坛的统计信息
      $online_user_number = UserUtil::getOnlineUserNumber($this->db);
      $online_vistor_number = UserUtil::getVistorNumber($this->db);
      //在线的最高时间
      $high_access = UserUtil::getTheHighAccess($this->db);
      $high_number = $high_access['num'];
      $high_time = $high_access['time'];


      //在线用户列表
      $online_user_array = UserUtil::getOnLineUser($this->db);

      //取得站点的版面信息
      $bbs_layout = $this->getBBSLayout();

      $smarty = $this->getSmarty();


      //统计当前在线人数和发表的主题数，帖子数。
      //总共有的会员数
      $dbh = $this->getDB();
      $sql = "select count(*) as num from online_user";
      $stmt = $dbh->prepare($sql);
      $res = $dbh->Execute($stmt);
      $rows = $res->FetchRow();
      $smarty->assign('all_page_online_user', $rows['num']);

      $sql = "select count(*) as num from bbs_subject ";
      $stmt = $dbh->prepare($sql);
      $res = $dbh->CacheExecute(40, $stmt );
      $rows = $res->FetchRow();
      $smarty->assign('all_page_topic_number', $rows['num']);

      $sql = "select count(*) as num from bbs_reply ";
      $stmt = $dbh->prepare($sql);
      $res = $dbh->CacheExecute(60, $stmt );
      $rows = $res->FetchRow();
      $smarty->assign('all_page_reply_number', $rows['num']);


      $sql = "select count(*) as num from base_user_info ";
      $stmt = $dbh->prepare($sql);
      $res = $dbh->Execute($stmt );
      $rows = $res->FetchRow();
      $smarty->assign('all_page_user_number', $rows['num']);


      //查找最近新登录的三位新会员
      $sql = "select id, user_name from base_user_info order by id desc limit 3";
      $stmt =  $dbh->prepare($sql);
      $res  = $dbh->CacheExecute(100, $stmt);
      $rows = $res->GetArray();

      $smarty->assign('newuser', $rows);

 

      $smarty->assign('have_system_post', $is_have_post);
      $smarty->assign('post_str', $post_str);
      $smarty->assign('online_user_number', $online_user_number);
      $smarty->assign('online_vistor_number', $online_vistor_number);
      $smarty->assign('high_number', $high_number);
      $smarty->assign('high_time', $high_time);
      $smarty->assign('user_info', $online_user_array);
      $smarty->assign('info', $bbs_layout);
      $smarty->display('showbbsindex.tmpl');


   }


   /**
    * 取得论坛板块的信息
    * @param:  NULL
    * @return: array
    * @access: private
    */
   private function &getBBSLayout() {
      $bbs_layout = array();

      $bbs_layout_id = LayoutUtil::getLayoutInfoByParentId($this->db);


      foreach ( $bbs_layout_id as $rows ) {
         $bbs_id = $rows['id'];
         $bbs_title = $rows['title'];

         //查询二级子论坛
         $sub_bbs_layout_id = LayoutUtil::getLayoutInfoByParentId($this->db, $bbs_id);
         $sub_array = array();
         
         foreach ( $sub_bbs_layout_id as $sub_rows ) {

            $sub_id = $sub_rows['id'];
            //注意：$sub_bbs_id是一个数组
            $sub_bbs_id = array();
            LayoutUtil::getChildId($this->db, $sub_id, $sub_bbs_id);
            array_push($sub_bbs_id, $sub_id);


            /**
             * 如果已经将论坛锁住，则不判断是否有新帖
             */
            /**
             *  $layout_status == 0 || $layout_status is null 则为开放
             *  $layout_status == 1 则需要验证
             *  $layout_status == 2 则为关闭
             */
            $layout_status = LayoutUtil::getLayoutStatus($this->db, $sub_id);


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

               if ( LayoutUtil::isClosedbyParent($this->db, $sub_id) ) {
                  $image = 'lock.gif';
               } else {

               if ( isset($_SESSION['user'] ) ) {
                  if ( LayoutUtil::haveNewTopic($this->db, $_SESSION['user']['name'], $sub_bbs_id) ) {
                  /**
                   * 求出最后时间后，需要我们找出当前子论坛下各个子论坛的id
                   */
                     $image = 'havenewtopic.gif';
                  }
               }
               }
            }

            //求出论坛及子论坛下面的查看的人数
            $view_number = LayoutUtil::getViewNumber($this->db, $sub_bbs_id);

            //求出论坛和子论坛下的所有的主题
            $topic_number = LayoutUtil::getTopicNumber($this->db, $sub_bbs_id);

            //求出论坛下所有的回复数
            $reply_number = LayoutUtil::getReplyNumber($this->db, $sub_bbs_id);


            //求论坛里最后发表的帖子
            $temp_rows = LayoutUtil::getLastPostTopic($this->db, $sub_id);

            $last_id = $temp_rows['id'];
            $last_title = $temp_rows['title'];
            $last_time = $temp_rows['last_access_date'];
            $last_time = set_locale_time($last_time);
            $short_title = utf8_substr($last_title, 0, 10);


            //求版主列表的字符串
            $manager_list_array = LayoutUtil::getManagerList($this->db, $sub_id);

            $manager_str = "";

            foreach ( $manager_list_array as $temp_rows ) {
               $manager_str .= "<option value=".$temp_rows['user_id'].">";
               $manager_str .= $temp_rows['user_name']."</option>\n";
            }


            $sub_array[] = array(
               'id' => $sub_rows['id'],
               'title' => $sub_rows['title'],
               'content' => ConvertString(stripslashes($sub_rows['description']), ROOT_URL, IMAGE_URL.'express/'),
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

         $bbs_layout[] = array (
            'id' => $bbs_id,
            'title'=> $bbs_title,
            'subbbs'=>$sub_array
         );
      }

      return $bbs_layout;
   }






}

?>
