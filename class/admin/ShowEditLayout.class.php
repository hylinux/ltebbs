<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/ShowAddLayout.class.php
*
*  显示新增版块的界面
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: ShowEditLayout.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';



class ShowEditLayout extends AdminBaseAction {

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
      $id = $this->getParameterFromGET('id');

      if ( !$id ) { 
         return;
      }

      $sql = 'select title, description from bbs_layout where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($id));
      $rows = $res->FetchRow();



      
      //only show the home page
      $smarty = $this->getSmarty();

      $smarty->assign('layout_name', $rows['title']);
      $smarty->assign('layout_desc', $rows['description']);
      $smarty->assign('id', $id);

      $sql = 'select parent_id from bbs_layout where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($id));
      $rows = $res->FetchRow();

      $parent_id = $rows['parent_id'];
      $smarty->assign('parent_id', $parent_id);


      $smarty->display('admineditlayout.tmpl');

      return;
   }
}

?>
