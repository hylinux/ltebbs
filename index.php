<?php
//vim:set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet(BBS)
*  File Name   :  index.php
*
*  The main input file
*
*  This file will received all user's request. and parse user's action
*  and resend the user's action to a action component. and response the result
*  by the action component.
*  
*  PHP Version 5
*  
*  @package:   NULL
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: index.php,v 1.2 2006-09-24 14:38:08 ghw Exp $
*  @date:      $Date: 2006-09-24 14:38:08 $
 */

/**
 * Check the install.php
 * if the install.php eixts. we will exit
 */

if ( file_exists("install.php") or file_exists("install") ) {
    die("After we had installed the 5anet(BBS), please remove the install.php<br>
        当安装完5anet(BBS)后，请删除install.php，然后刷新页面<br>");
}

//open the cache system
ob_start();
//open the session system
session_start();

# include the configure file
if ( file_exists( './config/config.inc.php' ) ) {
   include_once './config/config.inc.php';
} else {
   die("Can't open the configure file.\n");
}



if ( file_exists( './global.inc.php' ) ) {
   include_once './global.inc.php';
} else {
   die("Can't open the global file.\n");
}

if ( get_magic_quotes_runtime() ) {
    set_magic_quotes_runtime(false);
}



# include the application defined file
if ( file_exists(CLASS_PATH.'/main/Application.class.php') ) {
   include_once CLASS_PATH.'/main/Application.class.php';
} else {
   die("Can't include the Application defined file.\n");
}

# defined a Application instance, and run this Application
$app = Application::getInstance();
$app->run();

//flush the page cache
ob_end_flush();
?>
