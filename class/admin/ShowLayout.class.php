<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/ShowSite.class.php
*
*  显示现有的模块的管理界面
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: ShowLayout.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';


//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/ShowLayout.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/ShowLayout.lang.php';
}

class ShowLayout extends AdminBaseAction {

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
   *  run this action
   *  @param:  NULL
   *  @return: NULL
   *  @access: public
   */
   public function run() {

      $parent_id = $this->getParameter('parent');

      if ( ! $parent_id ) {
         $parent_id = 0;
      }


      $smarty = $this->getSmarty();

      $sql = 'select id, title, description, status from bbs_layout where parent_id=? order by id asc';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($parent_id));

      $temp = array();

      while ( $rows = $res->FetchRow() ) {

         $status = SL_OPEN_STATUS;

         if ( $rows['status'] == 0 ) {
            $status = SL_OPEN_STATUS;
         } else if ( $rows['status'] == 1 ) {
            $status = SL_NEED_LOGIN;
         } else if ( $rows['status'] == 2 ) {
            $status = SL_CLOSE;
         } else if ( $rows['status'] == 3 ) {
            $status = SL_ONLY_SPLIT_CATEGORY;
         } else {
            $status = SL_OPEN_STATUS;
         }

         //求现有的版主列表
         $manager_list_array = LayoutUtil::getManagerList($this->db, $rows['id']);

         $manager_str = "";

         foreach ( $manager_list_array as $temp_rows ) {
            $manager_str .= "<option value=".$temp_rows['user_id'].">";
            $manager_str .= $temp_rows['user_name']."</option>\n";
         }


         
         $temp[] = array(
            'name' => $rows['title'],
            'desc' => $rows['description'],
            'status' => $status,
            'id'=>$rows['id'],
            'ma'=>$manager_str,
         );
      }

      //返回论坛上面的导行栏。
      $nav_array = LayoutUtil::getParentLayoutInfo($this->db, $parent_id);
      //导航栏
      $smarty->assign('menu', $nav_array);

      
      $smarty->assign('parent_id', $parent_id);

      $smarty->assign('layout', $temp);

      $smarty->display('adminlayout.tmpl');

      return;
   }
}

?>
