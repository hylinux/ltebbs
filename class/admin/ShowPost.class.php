<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/ShowPost.class.php
*
*  显示系统公告
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: ShowPost.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/ShowPost.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/ShowPost.lang.php';
}

class ShowPost extends AdminBaseAction {

   /**
    * 数据库的连接
    */
   public $db;

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
   *  run this action
   *  @param:  NULL
   *  @return: NULL
   *  @access: public
   */
   public function run() {

      $smarty = $this->getSmarty();

      //求总的数量
      $sql = 'select count(*) as num from site_post';
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




      //求用户的情况
      $sql = 'select id, title, begin_date, expires, case when expires > unix_timestamp() then 0 '.
            ' else 1 end as expire from site_post';

      $res = $this->db->SelectLimit($sql, $this->page_number, $offset_number);
      
      $temp_array = array();

      while ( $rows = $res->FetchRow() ) {
         $temp_array[] = $rows;
      }


      $smarty->assign('post', $temp_array);





      $smarty->display('adminpost.tmpl');

      return;
   }
}

?>
