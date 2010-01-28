<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/SaveManager.class.php
*
*  保存新增加的版主
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: SaveManager.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/SaveManager.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/SaveManager.lang.php';
}

class SaveManager extends AdminBaseAction {

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
      $name = $this->getParameterFromPOST('username');
      $layout_id = $this->getParameterFromPOSt('id');


      if ( !$name ) {
         $this->AlertAndBack(USER_IS_EMPTY);
         return;
      }

      if ( !$layout_id ) {
         $this->AlertAndBack(LAYOUT_IS_EMPTY);
         return;
      }



      //检查用户名是否存在
      $sql = 'select count(*) as num from base_user_info where lower(user_name)=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array(strtolower($name)));
      $rows = $res->FetchRow();

      if ( !$rows['num'] ) {
         $this->AlertAndBack(USER_IS_NOT_EXISTS);
         return;
      }

      $user_id = UserUtil::getUserId($this->db, $name);

      //检查版块时候存在
      $sql = 'select count(*) as num from bbs_layout where id=?';
      $sth = $this->db->prepare($sql);
      $res = $this->db->Execute($sth, array($layout_id));
      $rows = $res->FetchRow();

      if ( !$rows['num'] ) {
         $this->AlertAndBack(LAYOUT_IS_NOT_EXISTS);
         return;
      }

      //求该版块的parent id
      $sql = 'select parent_id from bbs_layout where id=?';
      $sth = $this->db->prepare($sql);
      $res = $this->db->Execute($sth, array($layout_id));
      $rows = $res->FetchRow();
      $parent_id = $rows['parent_id'];



      //检查用户是否已经是版主
      $sql = 'select count(*) as num from bbs_layout_manager where user_id=? and layout_id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($user_id, $layout_id));
      $rows = $res->FetchRow();

      if ( $rows['num'] ) {
         $this->AlertAndBack(USER_HAD_BEEN_ADDED_AS_MANAGER);
         return;
      }


      $sql = 'insert into bbs_layout_manager(user_id, layout_id) values (?, ?)';
      $sth = $this->db->Prepare($sql);
      $this->db->Execute($sth, array($user_id, $layout_id));

    
      $this->forward('index.php?action=layout&parent='.$parent_id);

      return;
   }
}

?>
