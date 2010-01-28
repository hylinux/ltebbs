<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/bbs/SaveTopic.class.php
 *
 * 保存新帖
 *
 * PHP Version 5
 *
 * @package:   class.bbs
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: SaveTopic.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';


//包含需要用到的函数
include_once FUNCTION_PATH.'getIp.fun.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/SaveTopic.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/SaveTopic.lang.php';
}

class SaveTopic extends BaseAction {
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
    * 保存新帖
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {
        //取得版块的id
      $bbs_id = $this->getParameter('id');

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



      if ( !$title || strlen($title)<=0 ) {
         $this->AlertAndBack(ST_TITLE_IS_EMPTY);
         return;
      }

      /*
      if ( strlen($title) > 140 ) {
         $this->AlertAndBack(ST_TITLE_TOO_LONG);
         return;
      }*/



      if ( !$content || strlen($content) <= 0 ) {
         $this->AlertAndBack(ST_CONTENT_IS_EMPTY);
         return;
      }

      //插入新帖子
      $ip_temp = getIp();
      $ip = $ip_temp['ip'];

      $user_name = $_SESSION['user']['name'];
      $now = time();

      $sql = 'insert into  bbs_subject ( layout_id, title, author, content, post_ip, '.
         'post_date, express, last_access_date ) values (?, ?, ?, ?, ?, ?, ?, ?) ';

      $sth = $this->{'db'}->Prepare($sql);
      $this->{'db'}->Execute($sth, array(
         $bbs_id, $title, $user_name, $content, $ip, $now, $express, $now));

      if ( $this->{'db'}->ErrorNo() ) {
         $this->AerltAndBack($this->{'db'}->ErrorMsg());
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
         $file_name = ROOT_PATH.'upload/attach/'.$insert_id.$image_left_type;

         if ( !move_uploaded_file($_FILES['attach']['tmp_name'], $file_name ) ) {
            $sql = 'delete from bbs_subject where id=?';
            $sth = $this->{'db'}->Prepare($sql);
            $this->{'db'}->Execute($sth, array($insert_id));
            $this->AlertAndBack(ST_UPLOAD_ERROR);
            return;
         } else {
            $sql = 'insert into bbs_subject_attach (subject_id, file_type) '.
               ' values (?, ?)';
            $sth = $this->{'db'}->Prepare($sql);
            $this->{'db'}->Execute($sth, array(
               $insert_id, $image_left_type));
         }


      }

      unset($_SESSION['temp_title']);
      unset($_SESSION['temp_content']);
      unset($_SESSION['temp_express']);

      $this->TipsAndForward(
          ST_SEND_TOPIC_SUCCESS,
            'index.php?module=bbs&action=viewtopic&id='.$insert_id
          );

   }

}

?>
