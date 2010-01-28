<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/ShowLogin.class.php
*
*  Show the Login interface
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: ShowLogin.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/ShowLogin.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/ShowLogin.lang.php';
}

class ShowLogin extends AdminBaseAction {
   
   /**
   *  run this action
   *  @param:  NULL
   *  @return: NULL
   *  @access: public
   */
   public function run() {
      // if user had loginned.
      // so we send the request to the other url
      if ( isset($_SESSION['adminuser']) ) {
         $this->forward('index.php');
      }

      // only show the login interface
      $this->show('adminlogin.tmpl');

      return;
   }
}

?>
