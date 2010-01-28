<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/favor/SaveFavor.class.php
 *
 * 保存用户添加的收藏
 *
 * PHP Version 5
 *
 * @package:   class.email
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: $
 * @date:      $Date: $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';
include_once FUNCTION_PATH.'getCurrentDate.fun.php';


//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/SaveFavor.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/SaveFavor.lang.php';
}


class SaveFavor extends BaseAction {

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
    * 显示用户发送短信的界面
    */
   public function run() {
       $id = $this->getParameterFromPOST('id');
       $type = $this->getParameterFromPOST('type');

       if ( $type != 'topic' ) {
           $type = 'topic';
       }

       //
       //拿到userid
       $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);

       $favor_dir = $this->getParameterFromPOST('favor_dir');
       $add_new_favor_dir = $this->getParameterFromPOST('add_new_favor_dir');

       if ( $add_new_favor_dir ) {
            //我们将添加一个收藏目录
           $sql = 'insert into favor_dir ( user_id, dir_name ) values (?, ?)';
           $stmt = $this->db->prepare($sql);
           $this->db->Execute($stmt, array($user_id, $add_new_favor_dir));
           $favor_dir = $this->db->Insert_ID();
       } else {
           //检查用户选择的收藏目录ID
           $sql = 'select count(*) as num from favor_dir where user_id=? and id=?';
           $stmt = $this->db->Prepare($sql);
           $res = $this->db->Execute($stmt, array($user_id, $favor_dir));
           $rows = $res->FetchRow();

           if ( !$rows['num'] ) {
               $this->AlertAndBack(
                   SF_FAVOR_DIR_NOT_BE_CHOICE);
               return;
           }
       }


       //检查用户传入的topic是否存在
       $sql = 'select count(*) as num from bbs_subject where id=?';
       $stmt = $this->db->prepare($sql);
       $res = $this->db->Execute($stmt, array($id));
       $rows = $res->FetchRow();

       if ( !$rows['num'] ) {
           $this->AlertAndForward(SF_TOPIC_IS_NOT_EXISTS);
           return;
       }

       $back_url = $this->getParameterFromPOST('backurl');

       //检查是否已经添加了该收藏到指定的目录中了
       $sql = 'select count(*) as num from favor where user_id=? and dir_id=? and '.
           ' type=? and favor_id=? ';
       $stmt = $this->db->prepare($sql);
       $res = $this->db->execute($stmt, array(
            $user_id,
            $favor_dir,
            $type,
            $id
        ));

       $rows = $res->FetchRow();


       if ( $rows['num'] ) {
         $this->AlertAndForward(
             SF_FAVOR_HAD_BEEN_ADD,
             base64_decode($back_url));
         return;
       }



       //加入收藏
       $sql = 'insert into favor (user_id, dir_id, type, favor_id, add_date ) '.
           ' values (?, ?, ?, ?, ?) ';
       $stmt = $this->db->prepare($sql);
       $this->db->Execute($stmt, array(
           $user_id,
           $favor_dir, 
           $type, 
           $id, 
           getNoFormateCurrentDate()
       ));


       if ( $this->db->ErrorNo() ) {
            $this->AlertAndForward($this->db->ErrorMsg());
            return;
       } else {
            $this->TipsAndForward(
                SF_ADD_SUCCESS, base64_decode($back_url));
            return;
       }
   }
}

?>
