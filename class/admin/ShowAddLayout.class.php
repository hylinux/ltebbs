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
*  @version:   $Id: ShowAddLayout.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';
include_once LIB_PATH.'fckeditor/fckeditor.php';
include_once FUNCTION_PATH.'utf8_substr.fun.php';



class ShowAddLayout extends AdminBaseAction {
   
   /**
   *  run this action
   *  @param:  NULL
   *  @return: NULL
   *  @access: public
   */
   public function run() {
      $parent_id = $this->getParameterFromGET('parent');

      if ( !$parent_id ) {
         $parent_id = 0;
      }


      //only show the home page
      $smarty = $this->getSmarty();
      $smarty->assign('parent_id', $parent_id);


      $fck = new FCKeditor("desc");
      $fck->BasePath = FCKEDITOR_BASEPATH;
      $fck->ToolbarSet = 'Basic';
      $smarty->assign('fck', $fck);

      $smarty->display('adminaddlayout.tmpl');
      return;
   }
}

?>
