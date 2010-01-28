<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/CheckPassword.class.php
 *
 * 检查用户密码是否正确
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
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/CheckPassword.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/CheckPassword.lang.php';
}


class CheckPassword extends BaseAction {

    private $db;


    public function __construct() {
        $this->db = $this->getDB();
    }


    /**
     * 验证密码是否正确
     */
   public function run() {
       $check_password = $this->getParameterFromGET('password');
       if ( strlen($check_password) <= 0 ) {
            echo "<font color=red>".CK_CHECKPASSWORD_IS_EMPTY."</font>";
            return;
       }

       $crypt_password = md5($check_password);

       $sql = 'select user_password from base_user_info where user_name=?';
       $stmt = $this->db->prepare($sql);
       $res = $this->db->Execute($stmt, array($_SESSION['user']['name']));
       $rows = $res->FetchRow();

       $password = $rows['user_password'];



       if ( $password == $crypt_password ) {
            echo "<font color=green>".CK_CHECKPASSWORD_IS_RIGHT."</font>";
            return;
       } else {
           echo "<font color=red>".CK_CHECKPASSWORD_IS_WRONG."</font>";
           return;
       }
   }
}

?>
