<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/SavePost.class.php
*
*  保存新的公告
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: SavePost.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/SavePost.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/SavePost.lang.php';
}

class SavePost extends AdminBaseAction {

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
      $title = $this->getParameterFromPOST('title');
      $content = $this->getParameterFromPOST('content');
      $end_date_t = $this->getParameterFromPOST('end_date');

      if ( !$title ) {
         $this->AlertAndBack(TITLE_IS_EMPTY);
         return;
      }

      if ( strlen($title) > 50 ) {
         $this->AlertAndBack(TITLE_IS_TOO_LONGER);
         return;
      }

      if ( !$content ) {
         $this->AlertAndBack(CONTENT_IS_EMPTY);
         return;
      }

      $begin_date = time();

      if ( !$end_date_t ) {
         $end_date_t = 3;
      }

      $end_date = $begin_date + 60*60*24*$end_date_t;

      $sql = 'insert into site_post ( title, content, begin_date, expires ) values (?, ?, ?, ?)';
      $sth = $this->db->Prepare($sql);
      $this->db->Execute($sth, array($title, $content, $begin_date, $end_date));
    
      $this->forward('index.php?action=post');
      return;
   }
}

?>
