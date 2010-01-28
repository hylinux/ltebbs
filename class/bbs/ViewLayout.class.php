<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/bbs/ViewLayout.class.php
 *
 * 查看论坛。
 * 在用户查看论坛的时候，我们需要做几件事：
 * 1、检查论坛的状态，然后根据论坛的状态进行
 * 2、根据论坛的状态检查用户的状态
 * 3、显示子论坛
 * 4、显示置顶贴
 * 5、显示普通贴
 *
 * PHP Version 5
 *
 * @package:   class.bbs
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: ViewLayout.class.php,v 1.2 2006-09-24 14:38:08 ghw Exp $
 * @date:      $Date: 2006-09-24 14:38:08 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';

//包含需要用到的函数
include_once FUNCTION_PATH.'utf8_substr.fun.php';
include_once FUNCTION_PATH.'set_locale_time.fun.php';

//包含需要用到的类
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ViewLayout.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ViewLayout.lang.php';
}

class ViewLayout extends BaseAction {
   
   /**
    * 数据库的连接
    */
   public $db;


   /**
    * 每一页显示帖子数
    */
   private $page_number = 25;


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
    * 显示版面的情况
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {
      //收集论坛的ID
      $bbs_id = $this->getParameter("id");

      if ( !$bbs_id ) {
         $this->forward('index.php');
      }

      //验证论坛是否存在
      if ( !LayoutUtil::isExists($this->db, $bbs_id)) {
         //论坛不存在，则转向首页
         $this->forward('index.php');
      }

      //更新用户在本版的信息
      LayoutUtil::updateOnlineUser($this->db, $bbs_id);


      //如果论坛存在，则返回论坛的状态
      $bbs_status = LayoutUtil::getLayoutStatus($this->db, $bbs_id);
      if ( $bbs_status == 1 && !isset($_SESSION['user']) ) {
         $this->AlertAndForward(VL_NEED_LOGIN, 'index.php?module=user&action=showlogin');
         return;
      } else if ( $bbs_status == 2 ) {
         $this->AlertAndForward(VL_LAYOUT_WAS_CLOSED, 'index.php');
         return;
      } else if ( LayoutUtil::isClosedByParent($this->db, $bbs_id) ) {
         $this->AlertAndForward(VL_LAYOUT_WAS_CLOSED, 'index.php');
         return;
      }

      //取回smarty的实例
      $smarty = $this->getSmarty();

      //返回论坛上面的导行栏。
      $nav_array = LayoutUtil::getParentLayoutInfo($this->db, $bbs_id);
      //导航栏
      $smarty->assign('nav_array', $nav_array);



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




      //状态确认了。开始检查论坛是否有子论坛
      $bbs_title = LayoutUtil::getTitle($this->db, $bbs_id);
      $bbs_sub_info = LayoutUtil::getSubBBS($this->db, $bbs_id);

      //论坛的ID
      $smarty->assign('bbs_id', $bbs_id);
      //子论坛的信息
      $smarty->assign('bbs_title', $bbs_title);
      $smarty->assign('have_sub_bbs', isset($bbs_sub_info[0])?1:0 );
      $smarty->assign('info', $bbs_sub_info);

      //如果状态为3，则不允许发帖，就不显示帖子
      $smarty->assign('not_allow_new_topic', $bbs_status == 3 ? 1 : 0 );

      //如果状态为3，则不允许发帖，
      //如果不为3，则表示可以发帖子，
      //就应该显示帖子的数据
      if ( $bbs_status != 3 ) {

         //开始检查帖子。
         $total_number = LayoutUtil::getTotalNumberTopicByParentId($this->db, $bbs_id);

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


         //如果是$page =1 就显示置顶贴，要不然不显示。
         //呵呵。
         //先看看是否要显示精华
         $show_best = $this->getParameterFromGET('showbest');
         $top_number = $this->page_number;
         if ( $page == 1 ) {
            $top_subject_array = LayoutUtil::getTopicSubjectInfo($this->db, $bbs_id, 
               $this->page_number, $show_best);
            $temp_number = count($top_subject_array);
            $top_number = $this->page_number - $temp_number;
            if ( $temp_number >= 1 ) {
               $smarty->assign('have_top_subject', 1);
               $smarty->assign('top_subject', $top_subject_array);
            }
            
         }

         //求出偏移
         $offset_number = ( $page - 1 ) * $top_number;

         $subject_array = LayoutUtil::getSubjectInfo($this->db, $bbs_id, $this->page_number, 
            $top_number, $offset_number, $show_best);

         if ( count($subject_array) >= 1 ) {
            $smarty->assign('subject', $subject_array);
            $smarty->assign('have_subject', 1);
         }

         $smarty->assign('show_best', $show_best);
      }

      //记录用户到本版中
      //还是应该记录一下用户在本版待的时间 
      //然后需要计算一下在本版，和本版的子版中正在浏览的人数
      //删除超时用户
      
      //取回本版和子版的ID的数组
      $sub_id_array = array();
      LayoutUtil::getChildId($this->db, $bbs_id, $sub_id_array);
      array_push($sub_id_array, $bbs_id);

      LayoutUtil::delExpiresUser($this->db, $sub_id_array);
      //统计在本版浏览的用户数
      $online_user = LayoutUtil::getViewNumber($this->db, $sub_id_array);
      //返回本版浏览的用户的信息
      $user_info = UserUtil::getUserInfoArray($this->db, $sub_id_array);

      $online_user_number = count($user_info);
      $vistor_number = $online_user - $online_user_number;

      $smarty->assign('online_user_number', $online_user_number);
      $smarty->assign('online_vistor_number', $vistor_number);
      
      $smarty->assign('user_info', $user_info);

      
      $smarty->display('viewlayout.tmpl');

   }






}


?>
