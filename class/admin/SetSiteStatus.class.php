<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/SetSiteStatus.class.php
*
*  设置模块的状态
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: SetSiteStatus.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/SetSiteStatus.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/SetSiteStatus.lang.php';
}

class SetSiteStatus extends AdminBaseAction {

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
      //取得参数
      $id = $this->getParameter('id');
      $status = $this->getParameter('status');

      if ( $status != 0 && $status != 1 && $status != 2 && $status != 3 ) {
         $status = 0;
      }

      $sql = 'update sys_modules set status=? where id=?';
      $sth = $this->db->Prepare($sql);
      $this->db->Execute($sth, array($status, $id));

      $this->forward('index.php?action=site');


      return;
   }
}

?>
