<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/ShowDelManager.class.php
*
*  显示删除版主的界面
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: ShowDelManager.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/ShowDelManager.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/ShowDelManager.lang.php';
}

class ShowDelManager extends AdminBaseAction {

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

      //取得传入的版块的id
      $layout_id = $this->getParameterFromGET('id');
      
      $smarty->assign('id', $layout_id);

      //检查版块时候存在
      $sql = 'select count(*) as num from bbs_layout where id=?';
      $sth = $this->db->prepare($sql);
      $res = $this->db->Execute($sth, array($layout_id));
      $rows = $res->FetchRow();

      if ( !$rows['num'] ) {
         $this->AlertAndBack(LAYOUT_IS_NOT_EXISTS);
         return;
      }

      $sql = 'select parent_id from bbs_layout where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($layout_id));
      $rows = $res->FetchRow();

      $parent_id = $rows['parent_id'];
      $smarty->assign('parent_id', $parent_id);



      //求现有的版主列表
      $manager_list_array = LayoutUtil::getManagerList($this->db, $layout_id);

      $manager_str = "";

      foreach ( $manager_list_array as $temp_rows ) {
         $manager_str .= "<input type=\"checkbox\" name=\"user_id[]\" value=".$temp_rows['user_id'].">";
         $manager_str .= $temp_rows['user_name']."<br>\n";
      }


      $smarty->assign('manager_list', $manager_str);

      $smarty->display('adminshowdelmanager.tmpl');

      return;
   }
}

?>
