<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/ShowGroup.class.php
*
*  显示现有的模块的管理界面
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: ShowGroup.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/ShowGroup.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/ShowGroup.lang.php';
}

class ShowGroup extends AdminBaseAction {

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

      $sql = 'select * from sys_group order by id asc ';
      $res = $this->db->Execute($sql);

      $temp = array();

      while ( $rows = $res->FetchRow() ) {
         $temp[] = array(
            'id' => $rows['id'],
            'name' => $rows['group_name'],
            'description'=>$rows['description']
         );
      }


      $smarty->assign('sysarray', $temp);

      $smarty->display('admingroup.tmpl');

      return;
   }
}

?>
