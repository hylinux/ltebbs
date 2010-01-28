<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet.com
*  File Name   :  class/admin/Logout.class.php
*
*  user logout system admin
*
*  PHP Version 5
*
*  @package:   class.default
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: Logout.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';

class Logout extends AdminBaseAction {
   /**
   *  run this action
   *  @param:  NULL
   *  @return: NULL
   *  @access: public
   */
   public function run() {

      $db = $this->getDB();

      if ( !isset($_SESSION['adminuser']) ) {
         $this->forward('index.php');
      }

      //user logout
      unset($_SESSION['adminuser']);

      $this->forward('index.php');
      return;
   }
}

?>
