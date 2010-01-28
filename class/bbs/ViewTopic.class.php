<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/bbs/ViewTopic.class.php
 *
 * 查看论坛的帖子，但是再查看帖子的时候需要注意到好几个问题。
 * 1、首先要反查到这个帖子所在的论坛版块。
 * 2、根据版块的状态确定用户是否可以查看这个主题。
 *    主要的版块状态有： 0 则为无限制的开发
 *                       1 则需要用户登录
 *                       2 则为论坛关闭
 * 3、在用户查看了这个论坛之后，如果该用户已经登录，则
 *    记录用户已经阅读过这个帖子。
 * 4、增加帖子的点击数。
 * （另外需要注意的是要验证帖子是否存在。)
 *
 * PHP Version 5
 *
 * @package:   class.bbs
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: ViewTopic.class.php,v 1.2 2006-09-24 14:38:08 ghw Exp $
 * @date:      $Date: 2006-09-24 14:38:08 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';
include_once CLASS_PATH.'bbs/TopicUtil.class.php';


//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ViewTopic.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ViewTopic.lang.php';
}

class ViewTopic extends BaseAction {

   /**
    * 数据库的连接
    */
   public $db;

   /**
    * 每一页显示记录的条数
    */
   private $pre_page = 10;

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
    * 查看帖子
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {

      //取得帖子的id
      $topic_id = $this->getParameterFromGET('id');
      $topic_id = (int)$topic_id;

      if ( !$topic_id || $topic_id < 1 ) {
         $this->AlertAndBack(VT_TOPIC_ID_IS_NOT_VALID);
         return;
      }

      //验证帖子的id是否存在
      if ( !TopicUtil::isExists($this->db, $topic_id) ) {
         $this->AlertAndBack(VT_TOPIC_ID_IS_NOT_EXISTS);
         return;
      }

      //查询帖子的版块id
      $layout_id = TopicUtil::getLayoutId($this->db, $topic_id);

      //验证论坛的版块
      if ( !LayoutUtil::isExists($this->db, $layout_id) ) {
         $this->AlertAndBack(VT_LAYOUT_IS_NOT_EXISTS);
         return;
      } 

      //得出论坛版块的状态
      $layout_status = LayoutUtil::getLayoutStatus($this->db, $layout_id);

      //状态为0则为全部开放
      //状态为1则为需要验证
      //状态为2则为关闭

      //如果为2
      if ( $layout_status == 2 ) {
         $this->AlertAndBack(VT_LAYOUT_IS_CLOSED);
         return;
      } else if ( $layout_status == 1 ) {
         if ( ! $_SESSION['user']['name'] ) {
            $this->AlertAndBack(VT_LAYOUT_NEED_AUTHOR);
            return;
         } 
      }

      //取得帖子的状态
      $topic_status = TopicUtil::getTopicStatus($this->db, $topic_id);

      /**
       * 为0, 则开放
       * 为1, 则需要认证
       * 为2,则关闭
       */
      if ( $topic_status == 1 ) {
         if ( ! $_SESSION['user']['name'] ) {
            $this->AlertAndBack(VT_TOPIC_NEED_AUTHOR);
            return;
         } 
      }


      //增加帖子的浏览次数
      TopicUtil::updateViewNumber($this->db, $topic_id);


      //取得Smarty的对象
      $smarty = $this->getSmarty();


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

      $smarty->assign('have_system_post', $is_have_post);
      $smarty->assign('post_str', $post_str);


      //求帖子的访问的导航菜单
      $nav_array = LayoutUtil::getParentLayoutInfo($this->db, $layout_id);
      //导航栏
      $smarty->assign('nav_array', $nav_array);
      //求帖子的标题
      $title = TopicUtil::getTitle($this->db, $topic_id);
      $smarty->assign('topic_title', $title);
      $smarty->assign('clone_title', ' -> '.$title);





      //取得页面
      $page = $this->getParameterFromGET('page');

      if ( !$page || $page <= 0 ) {
         $page = 1;
      }

      //求总的页面
      $total_page = TopicUtil::getTotalPage($this->db, $topic_id, $this->pre_page);

      if ( $page > $total_page && $total_page > 0 ) {
         $page = $total_page;
      }

      //帖子的id和版块的id
      $smarty->assign('topic_id', $topic_id);
      $smarty->assign('bbs_id', $layout_id);
      //页面
      $smarty->assign('now_page', $page);
      $smarty->assign('total_page', $total_page);

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

      $offset_page = ( $page - 1 ) * $this->pre_page;

      $topic_array = TopicUtil::getTopicInfo($this->db, $topic_id, $this->pre_page, $offset_page);


      $smarty->assign('topic', $topic_array);


      //取得当前用户的身份
      $user_name = $_SESSION['user']['name'];
      $user_id = UserUtil::getUserId($this->db, $user_name);

      if ( strlen($user_name) ) {

         //验证用户的身份
         $sql = 'select group_dep from base_user_info where lower(user_name) =?';
         $sth = $this->db->prepare($sql);
         $res = $this->db->Execute($sth, array(
            strtolower($user_name)));
         $rows = $res->FetchRow();

         $user_group = $rows['group_dep'];


         if ( $user_group == 1 || $user_group == 2 ) {
            $smarty->assign('can_be_close', 1);
         } else if ( $user_group == 3 ) {
            $layout_id = TopicUtil::getLayoutId($this->db, $topic_id);

            $sql = 'select count(*) as num from bbs_layout_manager where user_id=? and '.
               ' layout_id=?';
            $sth = $this->db->prepare($sql);
            $res = $this->db->Execute($sth, array($user_id, $layout_id));
            $rows = $res->FetchRow();

            if ( !$rows['num'] ) {
               $smarty->assign('can_be_close', 0);
            } else {
               $smarty->assign('can_be_close', 1);
            }
         }
      } else {
         $smarty->assign('can_be_close', 0);
      }


      //加密一个返回的url
      $backurl = 'index.php?module=bbs&action=viewtopic&id='.$topic_id.'&page='.$page;
      $backurl = base64_encode($backurl);
      $smarty->assign('backurl', $backurl);

      $smarty->display('viewtopic.tmpl');

   }




}

?>
