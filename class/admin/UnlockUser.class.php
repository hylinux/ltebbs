<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/UnLockUser.class.php
*
*  删除用户
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: UnlockUser.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/UnLockUser.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/UnLockUser.lang.php';
}

class UnLockUser extends AdminBaseAction {

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
      $id = $this->getParameterFromGET('id');

      if ( !$id ) {
         $this->AlertAndBack(USER_IS_EMPTY);
         return;
      }

      //检查用户是否存在
      $sql = 'select count(*) as num from base_user_info where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($id));
      $rows = $res->FetchRow();

      if ( !$rows['num'] ) {
         $this->AlertAndBack(USER_IS_NOT_EXISTS);
         return;
      }


      $sql = 'update base_user_info set status=0 where id=?';
      $sth = $this->db->Prepare($sql);
      $this->db->Execute($sth, array($id));

      //用户已被解锁
      $this->AlertAndBack(USER_HAD_BEEN_UNLOCKED);
    

      return;
   }
}

?>
