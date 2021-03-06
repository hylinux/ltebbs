<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/admin/SaveIpBlackList.class.php
*
*  保存新的IP黑名单
*  
*  PHP Version 5
*  
*  @package:   class.admin
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: SaveIpBlackList.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/AdminBaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/admin/SaveIpBlackList.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/admin/SaveIpBlackList.lang.php';
}

class SaveIpBlackList extends AdminBaseAction {

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
      $name = $this->getParameterFromPOST('ip');

      if ( !$name ) {
         $this->AlertAndBack(IP_IS_EMPTY);
         return;
      }


      //检查IP是否已经在黑名单中
      $sql = 'select count(*) as num from black_list_by_ip where ip=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array(strtolower($name)));
      $rows = $res->FetchRow();

      if ( $rows['num'] ) {
         $this->AlertAndBack(IP_HAD_BEEN_ADDED);
         return;
      }


      $sql = 'insert into black_list_by_ip (ip) values (?)';
      $sth = $this->db->Prepare($sql);
      $this->db->Execute($sth, array($name));

    
      $this->forward('index.php?action=ipblacklist');

      return;
   }
}

?>
