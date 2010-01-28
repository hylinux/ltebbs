<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/SaveEditSysUser.class.php
*
*  保存新的系统用户密码
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: SaveEditSysUser.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/SaveEditSysUser.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/SaveEditSysUser.lang.php';
}

class SaveEditSysUser extends AdminBaseAction {

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
      $id = $this->getParameterFromPOST('id');
      $password = $this->getParameterFromPOST('passwd');
      $check_password = $this->getParameterFromPOST('check_passwd');


      if ( strlen($password) < 6 ) {
         $this->AlertAndBack(PASSWORD_IS_TO_SHORT);
         return;
      }


      if ( $password != $check_password ) {
         $this->AlertAndBack(SG_PASSWORD_NOT_CHECK);
         return;
      }

      $password = md5($password);


      $sql = 'update sys_admin set user_passwd=? where id=?';
      $sth = $this->db->Prepare($sql);
      $this->db->Execute($sth, array($password, $id));

    
      $this->forward('index.php?action=system');


      return;
   }
}

?>
