<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/user/DelDir.class.php
 *
 * 删除指定的用户收藏目录，并同时删除目录里面的收藏
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
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/DelDir.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/DelDir.lang.php';
}


class DelDir extends BaseAction {

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

      //取得用户要删除的收藏目录
      $dir = $this->getParameterFromGET('dir');

      if  ( !$dir ) {
          $dir = 0;
      }
      

      //进行身份的判断
      $sql = 'select count(*) as num from favor_dir where user_id=? and id=?';
      $sth = $this->db->Prepare($sql);

      $res = $this->db->Execute($sth, array($user_id, $dir));
      $rows = $res->FetchRow();

      if ( !$rows['num'] ) {
        $this->AlertAndBack(DM_ID_IS_NOT_YOUR);
            return;
      }


      //身份判断通过。
      //开始删除收藏
      $sql = 'select id from favor where dir_id=?';
      $stmt = $this->db->prepare($sql);
      $res = $this->db->Execute($stmt, array($dir));

      $id_array = array();

      while ( $rows = $res->FetchRow() ) {
            $id_array[] = $rows['id'];
      }

      $sql = 'delete from favor where id in ('.implode(',', $id_array).')';
      $this->db->Execute($sql);

      $sql = 'delete from favor_dir where id=?';
      $this->db->Execute($sql, array($dir));

      //删除成功
      if ( $this->db->ErrorNo() ) {
            $this->AlertAndBack($this->db->ErrorMsg());
      } else {
            $this->TipsAndForward(
                DM_DELETE_SUCCESS,
                'index.php?module=favor');
      }


   }

}

?>
