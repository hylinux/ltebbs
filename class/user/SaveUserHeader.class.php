<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/SaveUserHeader.class.php
 *
 * 保存用户的自定义头像
 *
 * PHP Version 5
 *
 * @package:   class.user
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: SaveUserHeader.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';
include_once CLASS_PATH.'image/Image.class.php';


//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/SaveUserHeader.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/SaveUserHeader.lang.php';
}


class SaveUserHeader extends BaseAction {

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
    * 更改用户个人头像
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {
      //求得用户的id
      $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);
      
      if ( $_FILES['user_define_header']['tmp_name'] ) {
         //如果用选择了自定义图像。
                  //用户有上传文件
         if ( $_FILES['user_define_header']['type'] != 'image/gif' 
            && $_FILES['user_define_header']['type'] != 'image/jpeg' 
            && $_FILES['user_define_header']['type'] != 'image/jpg' 
            && $_FILES['user_define_header']['type'] != 'image/pjpeg' 
            && $_FILES['user_define_header']['type'] != 'image/png' ) {

               $this->AlertandBack(SU_PHONE_FILE_LIMIT);
         }

            //判断上传的文件大小是否合乎要求
         if ( $_FILES['user_define_header']['size'] > 1048576 ) {
            $this->AlertAndBack(SU_PHONE_FILE_SIZE_LIMIT);
            return;
         }

         //取得文件的大小
         list($image_width, $image_height, $image_type, $image_attr ) 
               = getimagesize($_FILES['user_define_header']['tmp_name']);

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
         $file_name = ROOT_PATH.'upload/userhead/'.$user_id.$image_left_type;

         if ( file_exists($file_name) ) {
            unlink($file_name);
         }

         //缩放用户头像标准为200 * 200
         $image = new Image();
         $image->setSrcFile($_FILES['user_define_header']['tmp_name']);
         $image->setDstFile($file_name);
         $image->setResizeWidth(200);
         $image->setResizeHeight(200);
         $image->ResizeImage();


         //更换用户的头像
         $sql = 'update base_user_info set user_header=? where id=?';
         $sth = $this->db->Prepare($sql);
         $this->db->Execute($sth, array(
            -1, $user_id));

      } else  {
         //如果用户没有选择自定义图像
         //取得用户选定的图像
         $user_header = $this->getParameterFromPOST('userheader');

         if ( !$user_header ) {
            $this->AlertAndBack(SU_USER_HEADER_IS_NULL);
            return;
         }

         //判断用户选择的头像的范围
         if ( $user_header < 1 || $user_header > 37 ) {
            $this->AlertAndBack(SU_USER_HEADER_GET_RANGE);
            return;
         }

         //更换用户的头像
         $sql = 'update base_user_info set user_header=? where id=?';
         $sth = $this->db->Prepare($sql);
         $this->db->Execute($sth, array(
            $user_header, $user_id));
      }

      $this->forward('index.php?module=user&action=userhead');

   }

}

?>
