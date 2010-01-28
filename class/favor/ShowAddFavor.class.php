<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/favor/ShowAddFavor.class.php
 *
 * 显示添加收藏的界面，让用户选择要添加到哪个收藏目录
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


//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ShowAddFavor.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ShowAddFavor.lang.php';
}


class ShowAddFavor extends BaseAction {

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
       $id = $this->getParameterFromGET('id');
       $type = $this->getParameterFromGET('type');

       if ( $type != 'topic' ) {
           $type = 'topic';
       }

       //拿到userid
       $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);

       $sql = 'select id, dir_name from favor_dir where user_id=?';
       $stmt = $this->db->prepare($sql);
       $res = $this->db->Execute($stmt, array($user_id));
       $rows = $res->GetAll();



       $smarty = $this->getSmarty();

       $smarty->assign('favor_type', $type);
       $smarty->assign('id', $id);
       $smarty->assign('favor', $rows);

      $smarty->assign('backurl', $this->getParameter('backurl'));

      $smarty->display('showaddfavor.tmpl');

   }

}

?>
