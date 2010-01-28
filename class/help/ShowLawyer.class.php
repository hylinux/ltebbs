<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/message/ShowLawyer.class.php
 *
 * 显示论坛的帮助
 *
 * PHP Version 5
 *
 * @package:   class.help
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: ShowHelp.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';


class ShowLawyer extends BaseAction {
   
   /**
    * 显示帮助
    */
   public function run() {
      $this->forward('index.php?module=help#lawyer');
   }

}

?>
