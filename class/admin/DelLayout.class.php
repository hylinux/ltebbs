<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/DelLayout.class.php
*
*  删除版块
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: DelLayout.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/DelLayout.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/DelLayout.lang.php';
}

class DelLayout extends AdminBaseAction {

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
         return;
      }
      //取得所有的子版块
      $sql = 'select parent_id from bbs_layout where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($id));
      $rows = $res->FetchRow();
      $parent_id = $rows['parent_id'];
      

      $all_id = array();
      LayoutUtil::getChildId($this->db, $id, $all_id);
      array_push($all_id, $id);

      $sql = 'delete from bbs_layout where id in ('.implode(',', $all_id).')';
      $this->db->Execute($sql);

      //删除所有的帖子
      $sql = 'delete from bbs_subject where layout_id in ('.implode(',', $all_id).')';
      $this->db->Execute($sql);

      //删除所有的回复
      $sql = 'delete from bbs_reply where layout_id in ('.implode(',', $all_id).')';
      $this->db->Execute($sql);

      //求父版块

      $this->forward('index.php?action=layout&parent='.$parent_id);


      return;
   }
}

?>
