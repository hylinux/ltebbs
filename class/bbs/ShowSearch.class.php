<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/bbs/ShowSearch.class.php
 *
 * 显示搜索的界面
 *
 * PHP Version 5
 *
 * @package:   class.bbs
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: ShowSearch.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';



class ShowSearch extends BaseAction {

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


   public function run() {
      $smarty = $this->getSmarty();


      $layout_array = array();
      $i = 0;
      
      LayoutUtil::getAllLayout($this->db, $layout_array, $i);

      $layout_option = '';

      foreach ( $layout_array as $layout ) {
         $layout_option .= "<option value=\"".$layout['id']."\">";
         $layout_option .= $layout['name']."</option>\n";
      }

      $smarty->assign('layout_information', $layout_option);


      $smarty->display('bbssearch.tmpl');

      return;      
   }

}

?>
