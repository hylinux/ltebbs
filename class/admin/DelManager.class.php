<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/DelManger.class.php
*
*  删除版主
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: DelManager.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/DelManager.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/DelManager.lang.php';
}

class DelManager extends AdminBaseAction {

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
      $layout_id = $this->getParameterFromPOST('id');

      //版主列表
      $manager_array = $this->getParameterFromPOST('user_id');

      if ( !is_array($manager_array) ) {
         $this->AlertAndBack(SYS_ERROR);
         return;
      }

      //检查版块时候存在
      $sql = 'select count(*) as num from bbs_layout where id=?';
      $sth = $this->db->prepare($sql);
      $res = $this->db->Execute($sth, array($layout_id));
      $rows = $res->FetchRow();

      if ( !$rows['num'] ) {
         $this->AlertAndBack(LAYOUT_IS_NOT_EXISTS);
         return;
      }

      $sql = 'delete from bbs_layout_manager where user_id=? and layout_id=?';
      $sth = $this->db->Prepare($sql);
      //开始删除
      foreach ( $manager_array as $manager ) {
         $this->db->Execute($sth, array($manager, $layout_id));
      }

      //取得parent_id
      $sql = 'select parent_id from bbs_layout where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($layout_id));
      $rows = $res->FetchRow();

      $parent_id = $rows['parent_id'];

      $this->forward('index.php?action=layout&parent='.$parent_id);


      return;
   }
}

?>
