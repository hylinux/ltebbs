<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/SaveUserSign.class.php
 *
 * 保存用户的个人签名
 *
 * PHP Version 5
 *
 * @package:   class.user
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: SaveUserSign.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';


//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/SaveUserSign.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/SaveUserSign.lang.php';
}


class SaveUserSign extends BaseAction {

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
    * 保存用户的个人签名
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {
      //求得用户的id
      $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);

      //收集用户输入的个人签名
      $user_sign = $this->getParameterFromPOST('usersign');

      if ( strlen($user_sign) > 250 ) {
         $this->AlertAndBack('SU_USER_SIGN_TOO_LONGER');
         return;
      }

      $sql = 'update base_user_info set user_sign=? where id=?';
      $sth = $this->db->Prepare($sql);
      $this->db->Execute($sth, array($user_sign, $user_id));

      $this->forward('index.php?module=user&action=sign');

   }

}

?>
