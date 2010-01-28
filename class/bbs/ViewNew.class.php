<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/bbs/ViewNew.class.php
 *
 * 查看最新发表的帖子
 *
 * PHP Version 5
 *
 * @package:   class.bbs
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: ViewNew.class.php,v 1.2 2006-09-24 14:38:08 ghw Exp $
 * @date:      $Date: 2006-09-24 14:38:08 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';

//包含需要用到的函数
include_once FUNCTION_PATH.'utf8_substr.fun.php';
include_once FUNCTION_PATH.'set_locale_time.fun.php';

//包含需要用到的类
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';


class ViewNew extends BaseAction {
   
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
      $this->db = $this->getCacheDB();
   }

   
   /**
    * 显示版面的情况
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {
      //取得用户的id
      $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);


      $smarty = $this->getSmarty();

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

      //公告显示结束

      $q = $this->getParameterFromGET('q');
      $encode_q = $q;
      //取得查询字符串
      if ( !$q ) {
         //取得用户最后一次的动作时间
         $last_time = UserUtil::getUserLastLogoutTime($this->db, $user_id);

         //生成一个where语句
         $q = " where last_access_date >='".$last_time."'";

         $encode_q = base64_encode($q);

      } else {
         $q = base64_decode($q);
      }


      $smarty->assign('encode_q', $encode_q);

      //生成所有的记录数
      $sql = 'select count(*) as num from bbs_subject '.$q;
      $res = $this->db->Execute($sql);
      $rows = $res->FetchRow();

      $total_number = $rows['num'];

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
         
         //显示搜索结果
          //求出偏移
         $offset_number = ( $page - 1 ) * $this->page_number;

         $subject_array = LayoutUtil::getCacheSubjectInfo($this->db, $this->page_number, 
            $offset_number, $q);

         if ( $total_page > 0 ) {
            $smarty->assign('subject', $subject_array);
            $smarty->assign('have_subject', 1);
         }

      $smarty->display('viewnew.tmpl');


   }
}

?>
