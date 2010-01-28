<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/SetGroup.class.php
*
*  显示现有得模块，让用户进行选择。
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: SetGroup.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/SetGroup.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/SetGroup.lang.php';
}

class SetGroup extends AdminBaseAction {

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
         $this->AlertAndBack(USER_IS_EMPTY);
         return;
      };

      $sql = 'select count(*) as num from base_user_info where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($id));
      $rows = $res->FetchRow();

      if ( !$rows['num'] ) {
         $this->AlertAndBack(USER_IS_NOT_EXISTS);
         return;
      }

      $sql = 'select group_dep from base_user_info where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($id));
      $rows = $res->FetchRow();




      $smarty = $this->getSmarty();
      $smarty->assign('id', $id);

      $smarty->assign('group_id', $rows['group_dep']);


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

      $layout_array = array();
      $i = 0;
      
      LayoutUtil::getAllLayout($this->db, $layout_array, $i);

      $layout_option = '';

      foreach ( $layout_array as $layout ) {
         $layout_option .= "<option value=\"".$layout['id']."\">";
         $layout_option .= $layout['name']."</option>\n";
      }


      $smarty->assign('layout_string', $layout_option);
      $smarty->assign('sysarray', $temp);
      $smarty->assign('page', $this->getParameterFromGET('page'));
      $smarty->assign('t', $this->getParameterFromGET('t'));
      $smarty->assign('m', $this->getParameterFromGET('m'));


      $smarty->display('adminsetgroup.tmpl');

      return;
   }
}

?>
