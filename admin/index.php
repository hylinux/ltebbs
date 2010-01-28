<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * Project Name:  5anet(BBS)
 * File Name   :  admin/index.php
 *
 * 管理界面的首页，
 * 由这个管理界面接收说有的请求
 * 但是在执行动作之前，必须进行权限的判断。
 * 必须包含在外层定义的配置选项
 *
 * @package:   admin
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: index.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */

//open the cache system
ob_start();
//open the session system
session_set_cookie_params(3000);
session_start();

# include the configure file
if ( file_exists( '../config/config.inc.php' ) ) {
   include_once '../config/config.inc.php';
} else {
   die("Can't open the configure file.\n");
}



# include the configure file
if ( file_exists( '../global.inc.php' ) ) {
   include_once '../global.inc.php';
} else {
   die("Can't open the global file.\n");
}



include_once CLASS_PATH.'main/DB.class.php';

//定义一个管理员动作的对应表
$action_map = array (
   'default' => 'ShowIndex',
   'login' => 'Login',
   'showlogin' => 'ShowLogin',
   'leftmenu' => 'LeftMenu',
   'welcome' => 'Welcome',
   'logout' => 'Logout',
   'site' => 'ShowSite',
   'sitestatus' => 'SetSiteStatus',
   'group' => 'ShowGroup',
   'addgroup' => 'ShowAddGroup',
   'savegroup' => 'SaveGroup',
   'delgroup' => 'DelGroup',
   'editgroup' => 'ShowEditGroup',
   'saveeditgroup' => 'SaveEditGroup',
   'blacklist' => 'ShowBlackList',
   'layout' => 'ShowLayout',
   'addlayout' => 'ShowAddLayout',
   'savelayout' => 'SaveLayout',
   'editlayout' => 'ShowEditLayout',
   'saveeditlayout' => 'SaveEditLayout',
   'dellayout' => 'DelLayout',
   'setlayoutstatus'=>'SetLayoutStatus',
   'adduserblacklist'=>'ShowAddUserBlackList',
   'saveuserblacklist' => 'SaveUserBlackList',
   'deluserblacklist'=>'DelUserBlackList',
   'ipblacklist' => 'ShowIpBlackList',
   'addipblacklist'=>'ShowAddIpBlackList',
   'saveipblacklist'=>'SaveIpBlackList',
   'delipblacklist'=>'DelIpBlackList',
   'showaddmanager' => 'ShowAddManager',
   'savemangager' => 'SaveManager',
   'showdelmanager'=>'ShowDelManager',
   'deltruemanager' => 'DelManager',
   'user' => 'ShowUser',
   'resetpasswd' => 'ResetPasswd',
   'lockuser'=>'LockUser',
   'unlock'=>'UnlockUser',
   'putblacklist'=>'PutUserBlackList',
   'post'=>'ShowPost',
   'addpost'=>'ShowAddPost',
   'savepost' => 'SavePost',
   'delpost'=>'DelPost',
   'viewpost' => 'ViewPost',
   'setgroup' => 'SetGroup',
   'savesetgroup'=>'SaveSetGroup',
   'system' => 'ShowSystem',
   'addsysuser'=>'ShowAddSysUser',
   'savesysuer'=>'SaveSysUser',
   'delsysuser'=>'DelSysUser',
   'editsysuser'=>'EditSysUser',
   'saveeditsysuer'=>'SaveEditSysUser',   

);

$action = $_SERVER['REQUEST_METHOD'] == 'GET'? $_GET['action']:$_POST['action'];

if ( !$action ) {
   $action = 'default';
}


//判断系统管理员是否登录
if ( !$_SESSION['adminuser'] && $action != 'login' ) {
   //则显示登录界面
   if ( file_exists(CLASS_PATH.'admin/ShowLogin.class.php')  ) {
      include_once CLASS_PATH.'admin/ShowLogin.class.php';
      $action = new ShowLogin();
      $action->run();
      ob_end_flush();
      exit; 
   } else {
      die("Can't find out the Login module in admin ");
   }
}

if ( !array_key_exists($action, $action_map) ) {
   die("your requirement can not be response, because the action is not exists!");
}

$class = $action_map["$action"];

if ( file_exists(CLASS_PATH.'admin/'.$class.'.class.php') ) {
   include_once CLASS_PATH.'admin/'.$class.'.class.php';
} else {
   die('The module:admin, the class:'.$class.' define file is not exists');
}

$app = new $class();
$app->run();


//flush the page cache
ob_end_flush();
?>
