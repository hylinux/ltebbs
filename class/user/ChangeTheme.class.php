<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/ChangeTheme.class.php
 *
 * 改变用户的风格外观
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
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ChangeTheme.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ChangeTheme.lang.php';
}


class ChangeTheme extends BaseAction {

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
     * 改变外观
     */
   public function run() {
      //求得用户的id
      $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);

      //取得用户传入的参数
      $theme = $this->getParameterFromGET('id');


      if ( $theme != 'default' &&
            $theme != 'new' && 
            $theme != 'newll' ) {
            $theme = 'new';
      }

      $this->db->debug = true;
      $sql = 'select count(*) as num from user_setting where user_id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      if ( $rows['num'] ) {
         $sql = 'update user_setting set user_theme=? '.
            ' where user_id=?';

         $sth = $this->db->Prepare($sql);
         $this->db->Execute($sth, array($theme,
             $user_id));
      } else {
         $sql = 'insert into user_setting (user_theme, '.
            ' user_id ) values (?, ? ) ';
         $sth = $this->db->Prepare($sql);
         $this->db->Execute($sth, array($theme, 
            $user_id));

      }

      //更新Session设置
      $_SESSION['user']['theme'] = $theme;

      //送cookie
      if ( $_COOKIE['user'] ) {
         $str_user_info = serialize($_SESSION['user']);
         setcookie('user', $str_user_info, time() + 60*60*24*365, '/', $global_config_web_domain);
      }
      setcookie('5abb_cookie_theme', $theme, time() + 60*60*24*365, '/', $global_config_web_domain);

      $this->forward('index.php');




   }


}

?>
