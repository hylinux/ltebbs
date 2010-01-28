<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/ShowAddManager.class.php
*
*  显示新增版主的界面
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: ShowAddManager.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';


class ShowAddManager extends AdminBaseAction {
   
   /**
   *  run this action
   *  @param:  NULL
   *  @return: NULL
   *  @access: public
   */
   public function run() {

      //only show the home page
      $smarty = $this->getSmarty();

      $layout_id = $this->getParameterFromGET('id');

      $smarty->assign('id', $layout_id);

      $db = $this->getDB();

      $sql = 'select parent_id from bbs_layout where id=?';
      $sth = $db->Prepare($sql);
      $res = $db->Execute($sth, array($layout_id));
      $rows = $res->FetchRow();

      $parent_id = $rows['parent_id'];
      $smarty->assign('parent_id', $parent_id);


      $smarty->display('adminaddmanager.tmpl');
      return;
   }
}

?>
