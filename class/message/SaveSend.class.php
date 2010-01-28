<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/message/SaveSend.class.php
 *
 * 保存用户发送的短消息
 *
 * PHP Version 5
 *
 * @package:   class.message
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: SaveSend.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';

include_once FUNCTION_PATH.'getCurrentDate.fun.php';


//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/SaveSend.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/SaveSend.lang.php';
}


class SaveSend extends BaseAction {
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
    * 保存用户发送的短消息
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {
      //求得用户的id
      $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);

      //收集变量，并对每个变量进行一定的判断
      //接收用户
      $receive_user = $this->getParameterFromPOST('username');

      if ( !$receive_user ) {
         $this->AlertAndBack(RECEIVE_USER_IS_NULL);
         return;
      }

      //短消息标题
      $title = $this->getParameterFromPOST('title');

      if ( !$title ) {
         $this->AlertAndBack(TITLE_IS_NULL);
         return;
      }

      if ( strlen($title) > 150 ) {
         $this->AlertAndBack(TITLE_LENGTH_IS_TO_LONGER);
         return;
      }


      //短消息的内容
      $content = $this->getParameterFromPOST('content');

      if ( !$content ) {
         $this->AlertAndBack(CONTENT_IS_NULL);
         return;
      }



      $user_array = preg_split('/,/', $receive_user);
        
      $faild_array = array();

      foreach ( $user_array as $user_item ) {
              $sql = 'select count(*) as num from base_user_info where user_name=?';
          $sth = $this->db->Prepare($sql);
          $res = $this->db->Execute($sth, array(strtolower($user_item)));
          $rows = $res->FetchRow();

          if ( $rows['num'] ) {

              $receive_user_id = UserUtil::getUserId($this->db, $user_item);


              $now_time = getNoFormateCurrentDate();

              //开始发送短消息
              $sql = 'insert into message_inbox (user_id, send_user_id, title, receive_time, '.
                 'content) values (?, ?, ?, ?, ?)';

              $sth = $this->db->Prepare($sql);
              $this->db->Execute($sth, array(
                 $receive_user_id, $user_id,  $title, $now_time, $content));

              //开始向用户自己的发件箱插入一条记录。
              $sql = 'insert into message_outbox ( user_id, receive_user_id, title, send_time, '.
                 ' content ) values ( ?, ?, ?, ?, ?)';

              $sth = $this->db->Prepare($sql);
              $this->db->Execute($sth, array(
                  $user_id, $receive_user_id, $title, $now_time, $content));

          } else {
            $faild_array[] = $user_item;
          }
     }

      //接收回转的URL
      $back_url = $this->getParameterFromPOST('backurl');
      $user_failed_string = '';
      $show_message = SE_SEND_SUCCESS;
      if ( !empty($faild_array) ) {
          $user_failed_string = implode(',', $faild_array);
          $show_message .= "<br><br>".HAVE_THOSE_FAILED.":<br>".$user_failed_string."<br>";
      }

      if ( !$back_url ) {
          $this->TipsAndForward($show_message,
              'index.php?module=message&action=send');
      } else {
          $back_url = base64_decode($back_url);

          $this->TipsAndForward($show_message,
              $back_url);
      }

      return;

   }

}



?>
