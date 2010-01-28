<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/bbs/ShowNewTopic.class.php
 *
 * 这个类是用来像用户显示创建新帖的界面
 * 在创建新帖的时候我们需要考虑到几个因素
 * 1、用户是必须登录的。
 * 2、在Session里保留一些需要保存的数据
 * 3、附件的处理
 *
 * PHP Version 5
 *
 * @package:   class.bbs
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: ShowNewTopic.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';

//包含必要的类
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';
include_once LIB_PATH.'fckeditor/fckeditor.php';

//包含需要用到的函数
include_once FUNCTION_PATH.'utf8_substr.fun.php';
//
//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ShowNewTopic.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ShowNewTopic.lang.php';
}

class ShowNewTopic extends BaseAction {


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
    * 运行本类
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {
      $smarty = $this->getSmarty();

      $smarty->assign('clone_title', CREATE_NEW_TOPIC);

      
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

      //返回论坛上面的导行栏。
      $nav_array = LayoutUtil::getParentLayoutInfo($this->db, $bbs_id);
      //导航栏
      $smarty->assign('nav_array', $nav_array);


      //先删除已经不存在的用户
      LayoutUtil::delNotExistsUser($this->db);


      //从Session里读出数据
      $temp_title = $_SESSION['temp_title'];
      $smarty->assign('temp_title', $temp_title);

      $temp_express = $_SESSION['temp_express'];
      $smarty->assign('temp_express', $temp_express);


      //附件临时
      $smarty->assign('is_new_topic', 1);

      $smarty->assign('bbsid',  $bbs_id);

      $temp_content = $_SESSION['temp_content'];
      $fck = new FCKeditor("content");
      $fck->BasePath = FCKEDITOR_BASEPATH;
      $fck->Value = $temp_content;
      $smarty->assign('fck', $fck);



      $smarty->display('topic.tmpl');

   }



}

?>
