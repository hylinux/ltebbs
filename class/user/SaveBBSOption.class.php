<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/SaveBBSOption.class.php
 *
 * 保存用户的论坛选项
 *
 * PHP Version 5
 *
 * @package:   class.user
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: SaveBBSOption.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';


//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/SaveBBSOption.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/SaveBBSOption.lang.php';
}


class SaveBBSOption extends BaseAction {

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

      //取得用户传入的参数
      $user_lang = $this->getParameterFromPOST('user_lang');

      if ( !$user_lang ) {
         $user_lang = 'zh';
      }

      $user_theme = $this->getParameterFromPOST('user_theme');

      if ( !$user_theme ) {
         $user_theme = 'default';
      }


      $user_recieve_email = $this->getParameterFromPOST('receive_email');

      if ( $user_recieve_email != 0 ) {
         $user_recieve_email = 1;
      }

      $user_recieve_message = $this->getParameterFromPOST('receive_message');

      if ( $user_recieve_message != 0 ) {
         $user_recieve_message = 1;
      }


      $sql = 'select count(*) as num from user_setting where user_id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($user_id));
      $rows = $res->FetchRow();

      if ( $rows['num'] ) {
         $sql = 'update user_setting set user_lang=?, user_theme=?, user_whether_receive_email=?, '.
            'receive_system_message=? where user_id=?';

         $sth = $this->db->Prepare($sql);
         $this->db->Execute($sth, array($user_lang, $user_theme, $user_recieve_email,
            $user_recieve_message, $user_id));
      } else {
         $sql = 'insert into user_setting (user_lang, user_theme, user_whether_receive_email, '.
            'receive_system_message, user_id ) values (?, ?, ?, ?, ? ) ';
         $sth = $this->db->Prepare($sql);
         $this->db->Execute($sth, array($user_lang, $user_theme, $user_recieve_email,
            $user_recieve_message, $user_id));

      }

      //更新Session设置
      $_SESSION['user']['lang'] = $user_lang;
      $_SESSION['user']['theme'] = $user_theme;

      $this->forward('index.php?module=user&action=bbsoption');

   }
}

?>
