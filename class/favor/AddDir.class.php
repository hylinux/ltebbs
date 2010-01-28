<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/favor/SaveFavor.class.php
 *
 * 保存用户添加的收藏目录
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
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/AddDir.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/AddDir.lang.php';
}


class AddDir extends BaseAction {

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
       //拿到userid
       $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);

       $favor_dir = $this->getParameterFromGET('dirname');
       $favor_dir_id = 0;

       if ( $favor_dir ) {
            //我们将添加一个收藏目录
           $sql = 'insert into favor_dir ( user_id, dir_name ) values (?, ?)';
           $stmt = $this->db->prepare($sql);
           $this->db->Execute($stmt, array($user_id, $favor_dir));
           $favor_dir_id = $this->db->Insert_ID();
       }

       if ( $this->db->ErrorNo() ) {
            $this->AlertAndForward($this->db->ErrorMsg());
            return;
       } else {
            $this->TipsAndForward(
                SF_ADD_SUCCESS,
           'index.php?module=favor&dir='.$favor_dir_id 
            
            );
            return;
       }
   }
}

?>
