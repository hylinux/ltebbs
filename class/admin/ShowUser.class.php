<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/bbs/ShowUser.class.php
 *
 * 列出用户
 *
 * PHP Version 5
 *
 * @package:   class.user
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: ShowUser.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/AdminBaseAction.class.php';
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ShowUser.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ShowUser.lang.php';
}



class ShowUser extends AdminBaseAction {

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


   public function run() {
      $smarty = $this->getSmarty();

      //取得查询用户的条件
      $t = $this->getParameterFromGET('t');

      $where_sql = '';
      if ( $t ) {
         $where_sql = " and lower(substring(user_name, 1, 1)) = '".strtolower(substr($t, 0, 1))."'";
      }

      $smarty->assign('t_sort', $t);

      $m = $this->getParameterFromGET('m');

      $smarty->assign('m', $m);

      if ( $m ) {
         $where_sql .= ' and ( group_dep = 1 or group_dep = 2 or group_dep = 3 ) ';
      }

      if ( strlen($where_sql) > 0 ) {
         $where_sql = ' where 1 '.$where_sql;
      }

      if ( $m ) {
         $smarty->assign('user_list_label', LU_MANAGER_LABEL);
      } else {
         $smarty->assign('user_list_label', LU_USER_LIST);
      }


      //求总的数量
      $sql = 'select count(*) as num from base_user_info '.$where_sql;
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
         $sql = 'select a.id, a.user_name, a.register_date, b.group_name, a.status from '.
            '  base_user_info a join '.
         ' sys_group b on a.group_dep = b.id '.$where_sql.' order by a.user_name asc ';

      $res = $this->db->SelectLimit($sql, $this->page_number, $offset_number);
      
      $temp_array = array();

      while ( $rows = $res->FetchRow() ) {
         $temp_sql = 'select count(*) as num from bbs_subject where author=?';
         $temp_sth = $this->db->Prepare($temp_sql);
         $temp_res = $this->db->Execute($temp_sth, array($rows['user_name']));
         $temp_rows = $temp_res->FetchRow();

         $topic_number = $temp_rows['num'];

         
         $temp_sql = 'select last_time from  user_last_time_logout where user_id=?  order by id desc';
         $temp_sth = $this->db->Prepare($temp_sql);
         $temp_res = $this->db->Execute($temp_sth, array($rows['id']));
         $temp_rows = $temp_res->FetchRow();

         $last_access_time = $temp_rows['last_time'];

         $temp_sql = 'select count(*) as num from black_list_by_user where user_name=?';
         $temp_sth = $this->db->Prepare($temp_sql);
         $temp_res = $this->db->Execute($temp_sth, array($rows['user_name']));
         $temp_rows = $temp_res->FetchRow();

         $was_added = $temp_rows['num'];

         //取得头像
         $user_header = UserUtil::getUserHeader($this->db, $rows['id']);

         $temp_array[] = array(
            'id'=>$rows['id'],
            'name' => $rows['user_name'],
            'group' => $rows['group_name'],
            'register_date' => $rows['register_date'],
            'topic_number' => $topic_number,
            'last_access_time' => $last_access_time,
            'header' => $user_header,
            'status'=>$rows['status'],
            'was_add'=>$was_added
         );

      }


      $smarty->assign('myuser', $temp_array);


      $smarty->display('adminshowuser.tmpl');

      return;      
   }

}

?>
