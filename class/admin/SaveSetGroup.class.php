<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/SaveSetGroup.class.php
*
*  保存用户的组设置
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: SaveSetGroup.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/SaveSetGroup.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/SaveSetGroup.lang.php';
}

class SaveSetGroup extends AdminBaseAction {

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
      $page = $this->getParameterFromPOST('page');
      $t = $this->getParameterFromPOST('t');
      $m = $this->getParameterFromPOST('m');
      $group = $this->getParameterFromPOST('group');
      $user_id = $this->getParameterFromPOST('id');
      $layout = $this->getParameterFromPOST('layout');


      //检测用户的id
      if ( !$user_id ) {
         $this->AlertAndBack(USER_IS_EMPTY);
         return;
      };

      $sql = 'select count(*) as num from base_user_info where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      if ( !$rows['num'] ) {
         $this->AlertAndBack(USER_IS_NOT_EXISTS);
         return;
      }

      //检测组是否存在
      $sql = 'select count(*) as num from sys_group where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($group));
      $rows = $res->FetchRow();
      
      if ( !$rows['num'] ) {
         $this->AlertAndBack(GROUP_IS_NOT_EXISTS);
         return;
      }

      if ( $group == 3 ) {
         //如果选择了版主，则必须考虑到要选择版块
         $sql = 'select count(*) as num from bbs_layout where id=?';
         $sth = $this->db->Prepare($sql);
         $res = $this->db->Execute($sth, array($id));
         $rows = $res->FetchRow();

         if ( !$rows ) {
            $this->AlertAndBack(LAYOUT_IS_NOT_EXISTS);
            return;
         }
      }

      $sql = 'update base_user_info set group_dep=? where id=?';
      $sth = $this->db->Prepare($sql);
      $this->db->Execute($sth, array($group, $user_id));

      if ( $group == 3 ) {
         $sql = 'insert into bbs_layout_manager(user_id, layout_id) values (?, ?)';
         $sth = $this->db->Prepare($sql);
         $this->db->Execute($sth, array($user_id, $group));
      }

      
      $this->forward('index.php?action=user&page='.$page.'&t='.$t.'&m='.$m);

      return;
   }
}

?>
