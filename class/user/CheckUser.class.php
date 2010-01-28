<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/CheckUser.class.php
 *
 * 检查用户是否存在
 *
 * PHP Version 5
 *
 * @package:   class.user
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: $
 * @date:      $Date:$
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';


//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/CheckUser.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/CheckUser.lang.php';
}


class CheckUser extends BaseAction {

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
     * 检查用户是否存在
     */
   public function run() {
       $user_name = $this->getParameterFromGET('username');
       if ( strlen($user_name) <= 0 ) {
            echo "<font color=red>".CK_USER_CAN_NOT_BE_EMPTY."</font>";
            return;
       }

       if ( strlen($user_name) > 40 ) {
           echo "<font color=red>".CK_USER_LENGTH_MUST_LESS_THEN_40."</font>";
           return;
       }

       $sql = 'select count(*) as num from base_user_info where user_name=?';
       $stmt = $this->db->prepare($sql);
       $res = $this->db->Execute($stmt, array($user_name) );

       $rows = $res->FetchRow();

       if ( $rows['num'] ) {
            echo "<font color=red>".CK_USER_HAD_BEEN_EXISTS."</font>";
            return;
       } else {
           echo "<font color=green>".CK_USER_NOT_EXISTS."</font>";
           return;
       }
   }
}

?>
