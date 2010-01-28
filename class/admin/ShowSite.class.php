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
*  @version:   $Id: ShowSite.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/ShowSite.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/ShowSite.lang.php';
}

class ShowSite extends AdminBaseAction {

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

      $smarty = $this->getSmarty();

      $sql = 'select * from sys_modules order by id asc ';
      $res = $this->db->Execute($sql);
      $temp = array();

      while ( $rows = $res->FetchRow() ) {
         
         $status = OPEN_STATUS;

         if ( $rows['status'] == 0 ) {
            $status = OPEN_STATUS;
         } else if ( $rows['status'] == 1 ) {
            $status = AUTHOR_STATUS;
         } else if ( $rows['status'] == 2 ) {
            $status = TEMP_CLOSE_STATUS;
         } else if ( $rows['status'] == 3 ) {
            $status = CLOSE_FOR_EVER_STATUS;
         }

         $temp[] = array (
            'name' => $rows['module_name'],
            'author' => $rows['author'],
            'version' => $rows['version'],
            'id' => $rows['id'],
            'description' => $rows['description'],
            'status' => $status,
         );
      }


      $smarty->assign('module', $temp);

      $smarty->display('adminsite.tmpl');

      return;
   }
}

?>
