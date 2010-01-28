<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/message/DelSendMsg.class.php
 *
 * 删除用户的收件箱里的短消息
 *
 * PHP Version 5
 *
 * @package:   class.message
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: DelSendMsg.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';

include_once FUNCTION_PATH.'utf8_substr.fun.php';


//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/DelSendMsg.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/DelSendMsg.lang.php';
}


class DelSendMsg extends BaseAction {

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
    * 删除用户选定的短信
    */
   public function run() {

      //得到用户的id
      $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);

      //得到用户请求的id
      $id_array = $this->getParameterFromPOST('id');

      if ( !is_array($id_array) ) {
         $this->AlertAndBack(DM_SYSTEM_ERROR);
         return;
      }

      //进行身份的判断
      $sql = 'select count(*) as num from message_outbox where user_id=? and id=?';
      $sth = $this->db->Prepare($sql);

      foreach ( $id_array as $id ) {
         $res = $this->db->Execute($sth, array($user_id, $id));
         $rows = $res->FetchRow();

         if ( !$rows['num'] ) {
            $this->AlertAndBack(DM_ID_IS_NOT_YOUR);
            return;
         }
      }


      //身份判断通过。
      //开始删除
      reset($id_array);

      $sql = 'delete from message_outbox where id in ('.implode(',', $id_array).')';
      $this->db->Execute($sql);

      $this->forward('index.php?module=message&action=send');

   }

}

?>
