<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/CheckCode.class.php
 *
 * 检查验证码是否正确
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
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/CheckCode.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/CheckCode.lang.php';
}


class CheckCode extends BaseAction {


    /**
     * 检查验证码是否正确
     */
   public function run() {
       $checkcode = $this->getParameterFromGET('code');
       if ( strlen($checkcode) <= 0 ) {
            echo "<font color=red>".CK_CHECKCODE_IS_EMPTY."</font>";
            return;
       }

       $register_check_code = strtolower($_SESSION['register_check_code']);

       $checkcode = strtolower($checkcode);

       if ( $checkcode == $register_check_code ) {
            echo "<font color=green>".CK_CHECKCODE_IS_RIGHT."</font>";
            return;
       } else {
           echo "<font color=red>".CK_CHECKCODE_IS_WRONG."</font>";
           return;
       }
   }
}

?>
