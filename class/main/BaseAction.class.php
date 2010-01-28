<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet.com
*  File Name   :  class/main/BaseAction.class.php
*
*  inhirt the base class Action
*
*  PHP Version 5
*
*  @package:   class.main
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: BaseAction.class.php,v 1.2 2006-09-24 14:38:08 ghw Exp $
*  @date:      @Date:$
*/

include_once CLASS_PATH.'main/Action.class.php';

include_once CLASS_PATH.'post/PostUtil.class.php';

abstract class BaseAction extends Action {
   
   /**
   *  override the base class method getSmarty
   *  @param:  NULL
   *  @return: object $smarty
   *  @access: public
   */
   public function &getSmarty() {
    global $use_db_debug;
      //输出头
      header("Content-Type:text/html;charset=UTF-8");
      if ( file_exists(LIB_PATH.'smarty/Smarty.class.php') ) {
         include_once LIB_PATH.'smarty/Smarty.class.php';
      } else {
         die("you have not install SMARTY or it is not the corrent location");
      }

      include_once FUNCTION_PATH.'getCurrentDate.fun.php';


      $smarty = new Smarty();
      $smarty->template_dir = THEME_PATH;
    
      //增加根据用户theme的数据
      //进行设置Smarty的编译目录
      if ( !file_exists(DATA_CACHE_PATH.'smarty/'.$_SESSION['system_request_theme'])) {
          mkdir(DATA_CACHE_PATH.'smarty/'.$_SESSION['system_request_theme']) or
              die("Can't create the Smarty compile directly, please check the privilege is 777");
      }

      $smarty->compile_dir =  DATA_CACHE_PATH.'smarty/'.$_SESSION['system_request_theme'].'/';

      $smarty->config_dir  = THEME_CONFIG_PATH.SYSTEM_LANG.'/';


      $smarty->left_delimiter = THEME_LEFT_DELIMITER;
      $smarty->right_delimiter = THEME_RIGHT_DELIMITER;

      if ( $use_db_debug ) {
        $smarty->compile_check = false;
      };



      // assign the images and css file path
      $smarty->assign('image_url', IMAGE_URL);
      $smarty->assign('root_url', ROOT_URL);
      $smarty->assign('css_url', THEME_URL);
      $smarty->assign('now_date', getCurrentDate());
      $smarty->assign('user_name', $_SESSION['user']['name']);
      $smarty->assign('module', $_SESSION['module']);
      $smarty->assign('action', $_SESSION['action']);
      $smarty->assign('showbest', $_GET['showbest']);






      return $smarty;

   }


}




?>
