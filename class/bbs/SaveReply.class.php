<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/bbs/SaveReply.class.php
 *
 * 保存新的回复
 *
 * PHP Version 5
 *
 * @package:   class.bbs
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: SaveReply.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';


//包含需要用到的函数
include_once FUNCTION_PATH.'getIp.fun.php';
include_once FUNCTION_PATH.'getCurrentDate.fun.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/SaveReply.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/SaveReply.lang.php';
}

class SaveReply extends BaseAction {
   /**
    * 数据库的连接
    */
   public $db;

   /**
    * 每页显示的记录数
    */
   private $pre_page = 10;

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
    * 保存新回复
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {
      //取得主题的id
      $topic_id = $this->getParameterFromPOST('id');
      
      //验证帖子的是否存在
      if ( !TopicUtil::isExists($this->db, $topic_id) ) {
         $this->AlertAndBack(TOPIC_IS_NOT_EXISTS);
         return;
      }

      //验证帖子的状态
      $status = TopicUtil::getTopicStatus($this->db, $topic_id);

      //注意状态为3,则为帖子被锁定，不能回复了。
      if ( $status == 2 ) {
         $this->AlertAndBack(TOPIC_HAD_BE_CLOSED);
         return;
      } else if ( $status == 3 ) {
         $this->AlertAndBack(TOPIC_HAD_BE_LOCK);
         return;
      }



        //取得版块的id
      $bbs_id = TopicUtil::getLayoutId($this->db, $topic_id);

      if ( !$bbs_id ) {
         $this->forward('index.php');
      }

      //验证论坛是否存在
      if ( !LayoutUtil::isExists($this->db, $bbs_id)) {
         //论坛不存在，则转向首页
         $this->forward('index.php');
      }

      //更新用户在本版的信息
      LayoutUtil::updateOnlineUser($this->db, $bbs_id);

      $bbs_status = LayoutUtil::getLayoutStatus($this->db, $bbs_id);
      if ( $bbs_status == 1 && !isset($_SESSION['user']) ) {
         $this->AlertAndForward(SNT_NEED_LOGIN, 'index.php?module=user&action=showlogin');
         return;
      } else if ( $bbs_status == 2 ) {
         $this->AlertAndForward(SNT_LAYOUT_WAS_CLOSED, 'index.php');
         return;
      } else if ( $bbs_status == 3 ) {
         //等于三不允许发帖
         $this->AlertAndBack(SNT_NOW_ALLOW_NEW_TOPIC);
         return;
      } else if ( LayoutUtil::isClosedByParent($this->db, $bbs_id) ) {
         $this->AlertAndForward(SNT_LAYOUT_WAS_CLOSED, 'index.php');
         return;
      }

      //取得各种参数
      //帖子的表情
      $express = $this->getParameterFromPost('express');
      //上传的帖子标题
      $title = $this->getParameterFromPost('title');
      //上传的内容
      $content = $this->getParameterFromPost('content');

      //记录在Session里
      $_SESSION['temp_title'] = $title;
      $_SESSION['temp_content'] = $content;
      $_SESSION['temp_express'] = $express;




      //看文件是否有文件上传
      if ( $_FILES['attach']['tmp_name'] ) {
         //用户有上传文件
         if ( $_FILES['attach']['type'] != 'image/gif' 
            && $_FILES['attach']['type'] != 'image/jpeg' 
            && $_FILES['attach']['type'] != 'image/jpg' 
            && $_FILES['attach']['type'] != 'image/pjpeg' 
            && $_FILES['attach']['type'] != 'image/png' ) {

               $this->AlertandBack(ST_PHONE_FILE_LIMIT);
         }

            //判断上传的文件大小是否合乎要求
         if ( $_FILES['attach']['size'] > 1048576 ) {
            $this->AlertAndBack(ST_PHONE_FILE_SIZE_LIMIT);
            return;
         }
      }



      //回复标题可以为空
      //如果标题为空，则自动生成一个标题
      if ( !$title || strlen($title)<=0 ) {
         $sql = 'select title from bbs_subject where id=?';
         $sth = $this->db->Prepare($sql);
         $res = $this->db->Execute($sth, array($topic_id));
         $rows = $res->FetchRow();

         $title = "Re:".$rows['title'];
      }


      /*
      if ( strlen($title) > 143 ) {
         $this->AlertAndBack(ST_TITLE_TOO_LONG);
         return;
      }*/



      if ( !$content || strlen($content) <= 0 ) {
         $this->AlertAndBack(ST_CONTENT_IS_EMPTY);
         return;
      }

      //插入新回复
      $ip_temp = getIp();
      $ip = $ip_temp['ip'];

      $user_name = $_SESSION['user']['name'];
      $now = time();

      $sql = 'insert into  bbs_reply ( layout_id, title, author, content, post_ip, '.
         'post_date, express, subject_id ) values (?, ?, ?, ?, ?, ?, ?, ?) ';

      $sth = $this->{'db'}->Prepare($sql);
      $this->{'db'}->Execute($sth, array(
         $bbs_id, $title, $user_name, $content, $ip, $now, $express, $topic_id));

      if ( $this->{'db'}->ErrorNo() ) {
         $this->AlertAndBack($this->{'db'}->ErrorMsg());
         return;
      }

      //得到最后的id
      $insert_id = $this->{'db'}->Insert_id();


      if ( $_FILES['attach']['tmp_name'] ) {
         //取得文件的大小
         list($image_width, $image_height, $image_type, $image_attr ) 
               = getimagesize($_FILES['attach']['tmp_name']);

         //判断文件的类型
         switch ( $image_type ) {
            case 1:
               $image_left_type = '.gif';
               break;
            case 2:
               $image_left_type = '.jpg';
               break;
            case 3:
               $image_left_type = '.png';
               break;
         }


         //存储的文件名
         $file_name = ROOT_PATH.'upload/attach/reply/'.$insert_id.$image_left_type;

         if ( !move_uploaded_file($_FILES['attach']['tmp_name'], $file_name ) ) {
            $sql = 'delete from bbs_reply where id=?';
            $sth = $this->{'db'}->Prepare($sql);
            $this->{'db'}->Execute($sth, array($insert_id));
            $this->AlertAndBack(ST_UPLOAD_ERROR);
            return;
         } else {
            $sql = 'insert into bbs_reply_attach (reply_id, file_type) '.
               ' values (?, ?)';
            $sth = $this->{'db'}->Prepare($sql);
            $this->{'db'}->Execute($sth, array(
               $insert_id, $image_left_type));
         }


      }

      unset($_SESSION['temp_title']);
      unset($_SESSION['temp_content']);
      unset($_SESSION['temp_express']);


      //发送短信，通知各个用户有回复了你的帖子
      //发送邮件，通知各个用户有回复了你的帖子
      $mail_user = array();
      $message_user = array();

      $sql = 'select a.author, b.user_email, b.id from bbs_subject a join '.
         ' base_user_info b on a.author = b.user_name '.
         ' join user_setting c on b.id=c.user_id where a.id=? and	c.user_whether_receive_email=1';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($topic_id));
      $rows = $res->FetchRow();

      if ( $rows['id'] ) {
         $mail_user[] = $rows['user_email'];
      }

      $sql = 'select distinct a.author, b.user_email, b.id from bbs_reply a join base_user_info b '.
         ' on a.author = b.user_name join user_setting c on b.id=c.user_id '.
         ' where a.subject_id=? and c.user_whether_receive_email=1';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($topic_id));

      while ( $rows = $res->FetchRow() ) {
         if ( $rows['id'] ) {
            $mail_user[] = $rows['user_email'];
         }
      }

      $mail_user = array_unique($mail_user);


      //计算发送短信的用户数组
      $sql = 'select a.author, b.id from bbs_subject a join '.
         ' base_user_info b on a.author = b.user_name '.
         ' join user_setting c on b.id=c.user_id where a.id=? and	c.receive_system_message=1';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($topic_id));
      $rows = $res->FetchRow();

      if ( $rows['id'] ) {
         $message_user[] = $rows['id'];
      }

      $sql = 'select distinct a.author,  b.id from bbs_reply a join base_user_info b '.
         ' on a.author = b.user_name join user_setting c on b.id=c.user_id '.
         ' where a.subject_id=? and c.receive_system_message=1';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($topic_id));

      while ( $rows = $res->FetchRow() ) {
         if ( $rows['id'] ) {
            $message_user[] = $rows['id'];
         }
      }

      $message_user = array_unique($message_user);


      //开始发送邮件
      $to_address = implode(',', $mail_user);
      $mail_content = ST_MAIL_CONTENT."\n\n";
      $mail_content .= ROOT_URL.'index.php?module=bbs&action=viewtopic&id='.$topic_id."\n\n";
      $headers = "To:".$to_address."\r\n";
      $headers .= "From:".WEBSITE_EMAIL."\r\n";


      //发送邮件：
      @mail($to_address, ST_MAIL_SUBJECT, $mail_content, $headers);


      //发送短消息
      //发件人
      $sender = '0';
      $message_content = ST_MESSAGE_CONTENT."\n";
      $now = getNoFormateCurrentDate();


      $message_content .= "[url=".
         'index.php?module=bbs&action=viewtopic&id='.$topic_id."][color=red]".ST_CLICK_HERE."[/color]".
         "[/url]";

      $sql = 'insert into message_inbox ( user_id, send_user_id, title, receive_time, content ) '.
         ' values ( ?, ?, ?, ?, ?) ';
      $sth = $this->db->Prepare($sql);
      foreach ( $message_user as $user ) {
         $this->db->Execute($sth, array(
            $user, $sender, ST_MAIL_SUBJECT, $now, $message_content));
      }



      $now = time();
      //更新主题的最后更新时间
      $update_sql = 'update bbs_subject set last_access_date=?,reply_number=reply_number+1  where id=?';
      $sth = $this->db->Prepare($update_sql);
      $this->db->Execute($sth, array($now, $topic_id));

      //求这个回帖的位置所在的位置
      $sort_number = TopicUtil::getSortNumber($this->db, $topic_id, $insert_id);

      $page = ceil ( $sort_number / $this->pre_page );

      //这里还有很多的工作需要做
      
      $this->TipsAndForward(
            ST_SAVE_REPLY_SUCCESS,
            'index.php?module=bbs&action=viewtopic&id='.$topic_id.'&page='.$page.
         '#topic'.$sort_number);

   }


}


?>
