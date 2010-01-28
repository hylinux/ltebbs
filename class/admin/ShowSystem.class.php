<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/ShowSystem.class.php
*
*  显示管理系统用户的界面
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: ShowSystem.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/ShowSystem.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/ShowSystem.lang.php';
}

class ShowSystem extends AdminBaseAction {

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

      $sql = 'select id, user_name from sys_admin';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth);
      $rows= $res->GetArray();


      $smarty->assign('sysuser', $rows);

      $smarty->display('adminsysuser.tmpl');

      return;
   }
}

?>
