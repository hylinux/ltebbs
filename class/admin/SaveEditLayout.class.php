<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/SaveEditLayout.class.php
*
*  保存编辑后的版块
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: SaveEditLayout.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/SaveEditLayout.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/SaveEditLayout.lang.php';
}

class SaveEditLayout extends AdminBaseAction {

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
      $id   = $this->getParameterFromPOST('id');
      $name = $this->getParameterFromPOST('name');
      $desc = $this->getParameterFromPOST('desc');

      if ( !$name ) {
         $this->AlertAndBack(SG_NAME_IS_EMPYT);
         return;
      }

      if ( strlen($name) > 50 ) {
         $this->AlertAndBack(SG_NAME_IS_TOO_LONGER);
         return;
      }

      if ( strlen($desc) > 200 ) {
         $this->AlertAndBack(SG_DESC_IS_TOO_LONGER);
         return;
      }

      $sql = 'update bbs_layout set title=?, description=? where id=?';
      $sth = $this->db->Prepare($sql);
      $this->db->Execute($sth, array($name, $desc, $id));

      $sql = 'select parent_id from bbs_layout where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($id));
      $rows = $res->FetchRow();

      $parent_id = $rows['parent_id'];

      $this->forward('index.php?action=layout&parent='.$parent_id);

      return;
   }
}

?>
