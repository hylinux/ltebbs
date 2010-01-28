<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/DeleteFavor.class.php
 *
 * 删除指定的用户收藏
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
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';


//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/DeleteFavor.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/DeleteFavor.lang.php';
}


class DeleteFavor extends BaseAction {

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
    * 显示用户的控制面板
    */
   public function run() {

      //求得用户的id
      $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);

      $smarty = $this->getSmarty();
      $user_name = $_SESSION['user']['name'];

      //取得用户正在访问的收藏目录
      $dir = $this->getParameterFromPOST('dir');

      if  ( !$dir ) {
          $dir = 0;
      }
      $smarty->assign('dir', $dir);
      //取得用户正在访问的页面
      $page = $this->getParameterFromPOST('page');

      
      //得到用户请求的id
      $id_array = $this->getParameterFromPOST('id');

      if ( !is_array($id_array) ) {
         $this->AlertAndBack(DM_SYSTEM_ERROR);
         return;
      }

      //进行身份的判断
      $sql = 'select count(*) as num from favor where user_id=? and id=?';
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

      $sql = 'delete from favor where id in ('.implode(',', $id_array).')';
      $this->db->Execute($sql);

      //删除成功
      if ( $this->db->ErrorNo() ) {
            $this->AlertAndBack($this->db->ErrorMsg());
      } else {
            $this->TipsAndForward(
                DM_DELETE_SUCCESS,
                'index.php?module=favor&dir='.$dir.'&page='.$page);
      }









   }

}

?>
