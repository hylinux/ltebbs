<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/admin/ViewPost.class.php
 *
 * 查看系统公告
 *
 * PHP Version 5
 *
 * @package:   class.admin.
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: ViewPost.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/AdminBaseAction.class.php';

//包含需要用到的函数
include_once FUNCTION_PATH.'utf8_substr.fun.php';
include_once FUNCTION_PATH.'set_locale_time.fun.php';
include_once FUNCTION_PATH.'ConvertString.fun.php';

//包含需要用到的类
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ViewPost.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ViewPost.lang.php';
}



class ViewPost extends AdminBaseAction {
   
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
      $this->db = $this->getCacheDB();
   }

   
   /**
    * 显示版面的情况
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {

      $id = $this->getParameterFromGET('id');

      if ( !$id ) {
         $this->AlertAndBack(POST_IS_EMPTY);
         return;
      }

      $sql = 'select count(*) as num from site_post where id=? and expires>?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($id, time()));
      $rows = $res->FetchRow();

      if ( !$rows['num'] ) {
         $this->AlertAndBack(POST_IS_NOT_EXISTS);
         return;
      }

      $sql = 'select title, content, begin_date from site_post where id=?';
      $sth = $this->db->Prepare($sql);
      $res = $this->db->Execute($sth, array($id));
      $rows = $res->FetchRow();


      $smarty = $this->getSmarty();
      $smarty->assign('clone_title', $rows['title']);
      $smarty->assign('title', $rows['title']);
      $smarty->assign('content', ConvertString($rows['content'], ROOT_URL, IMAGE_URL.'express/'));
      $smarty->assign('begin_date', $rows['begin_date']);


      $smarty->display('adminviewpost.tmpl');


   }
}

?>
