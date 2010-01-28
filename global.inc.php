<?php
//vim:set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldcolumn=1 foldmethod=marker:
/**
 * Project: 5anet(BBS)
 * File:    global.inc.php
 *
 * this is a configure because my fauiler.
 *
 *  PHP Version 5
 *  
 *  @package:   NULL
 *  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 *  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 *  @copyright: http://www.5anet.com
 *  @version:   $Rev:$
 *  @date:      $Date:$
 *  @Id:        $Id:$
 *
 */

// the smarty template engineer setting
$global_config_smarty_left_delimiter = '<{';
$global_config_smarty_right_delimiter = '}>';

//define the abs access URL
if ( !defined('ROOT_URL') ) {
   define('ROOT_URL',  $global_config_root_url); //你的URL存取位置，注意最后的 "/"
}


// define the abs path information
// the root path
if ( !defined('ROOT_PATH') ) {
   define('ROOT_PATH',  $global_config_root_path); //你的安装路径, 注意最后的 "/"
}

// the config path
if ( !defined('CONFIG_PATH') ) {
   define('CONFIG_PATH', ROOT_PATH.'config/');
}

// the lib path
if ( !defined('LIB_PATH') ) {
   define('LIB_PATH', ROOT_PATH.'lib/');
}

// the function path
if ( !defined('FUNCTION_PATH') ) {
   define('FUNCTION_PATH', ROOT_PATH.'function/');
}

// the class path
if ( !defined('CLASS_PATH') ) {
   define('CLASS_PATH', ROOT_PATH.'class/');
}

// the lang path
if ( !defined('LANG_PATH') ) {
   define('LANG_PATH', ROOT_PATH.'lang/');
}


// the theme config path
if ( !defined('THEME_CONFIG_PATH') ) {
   define('THEME_CONFIG_PATH', ROOT_PATH.'lang/res/');
}

// the theme compile path
if ( !defined('DATA_CACHE_PATH') ) {
   define('DATA_CACHE_PATH', ROOT_PATH.'/cache/');
}

// define the system language
if ( !defined('SYSTEM_LANG') ) {
   define('SYSTEM_LANG', 'zh');
}


// the object cache path
if( !defined('OBJECT_CACHE_PATH') ) {
   define("OBJECT_CACHE_PATH", DATA_CACHE_PATH.'object/');
}

// the theme system delimiter 
if ( !defined('THEME_LEFT_DELIMITER') ) {
   define('THEME_LEFT_DELIMITER',  $global_config_smarty_left_delimiter);
}

if ( !defined('THEME_RIGHT_DELIMITER') ) {
   define('THEME_RIGHT_DELIMITER', $global_config_smarty_right_delimiter);
}


#the database cache directory
$ADODB_CACHE_DIR = DATA_CACHE_PATH.'adodb/';


date_default_timezone_set($system_default_timezone_set);

//设置错误处理
if ( file_exists(FUNCTION_PATH.'errorhandle.fun.php') ) {
    include_once FUNCTION_PATH.'errorhandle.fun.php';
} else {
    die('we were losted the error handle function define file');
}

set_error_handler("m5abb_error_handle");




//如果用户已经登录，则可以设定用户的一些参数。例如用户的语言
//用户喜欢的桌面，
$user_lang = '';
$user_theme = '';
if ( isset($_SESSION['user']) ) {
   $user_lang = $_SESSION['user']['lang'];
   $user_theme = $_SESSION['user']['theme'];
   $user_time  = $_SESSION['user']['localtime'];
} else {

    if ( isset($_COOKIE['user']) ) {
      //确认Session里复制一份
        //      $_SESSION['user'] = unserialize(stripslashes($_COOKIE['user']));
        $temp_user_info = get_magic_quotes_gpc()?unserialize(stripslashes($_COOKIE['user'])):
            unserialize($_COOKIE['user']);


      include_once CLASS_PATH.'main/DB.class.php';
      $dbh = DB::getConnection();
      //验证一下这个cookie是否是真实的。
      $temp_user_name = $temp_user_info['name'];
      $temp_user_check = $temp_user_info['check'];

      $sql = 'select user_password from base_user_info where user_name=? '.
         ' and status=0 ';
      $sth = $dbh->Prepare($sql);
      $res = $dbh->Execute($sth, array(strtolower($temp_user_name)));
      $rows = $res->FetchRow();

      $temp_user_password = $rows['user_password'];
      if ( $temp_user_password && $temp_user_check == md5($temp_user_password) ) {
         //这里应该模拟用户登录，然后将登录后的情况放入Session

        $sql = "select id, user_header from base_user_info where user_name=?";
         $sth = $dbh->Prepare($sql);
        $res = $dbh->Execute($sth, array(strtolower($temp_user_name)));
        $rows = $res->FetchRow();

        $temp_userhead = $rows['user_header'];
        $temp_user_id = $rows['id'];

        // do a query for user's lang and theme
        $sql = 'select user_lang, user_theme, user_local_time from user_setting where '
         .' user_id = ? ';
        $sth = $dbh->Prepare($sql);
        $res = $dbh->Execute($sth, $temp_user_id);
        $rows = $res->FetchRow();

        $temp_userlang = $rows['user_lang'];
        $temp_usertheme = $rows['user_theme'];
        $temp_usertime = $rows['user_local_time'];

        // the password is corrent
        $temp1_user_info = array(
         'name' => $temp_user_name,
         'userhead'=>$temp_userhead,
         'lang'   => $temp_userlang,
         'theme'  => $temp_usertheme,
         'localtime' => $temp_usertime,
         'check'=>$temp_user_check
        );

        $_SESSION['user'] = $temp1_user_info;

        $temp_user_info = $temp1_user_info;
        $str_user_info = serialize($temp_user_info);
        setcookie('user', $str_user_info, time() + 60*60*24*365, '/', $global_config_web_domain);

         $user_lang = $_SESSION['user']['lang'];
         $user_theme = $_SESSION['user']['theme'];
         $user_time  = $_SESSION['user']['localtime'];

        if ( $_COOKIE['5abb_cookie_lang'] ) {
            $_COOKIE['5abb_cookie_lang'] = $user_lang;
        }

        if ( $_COOKIE['5abb_cookie_theme'] ) {
            $_COOKIE['5abb_cookie_theme'] = $user_theme;
        }
         
      }
      } else {
        if ( $_COOKIE['5abb_cookie_lang'] ) {
            $user_lang = $_COOKIE['5abb_cookie_lang'];
        }

        if ( $_COOKIE['5abb_cookie_theme'] ) {
            $user_theme = $_COOKIE['5abb_cookie_theme'];
        }
      }
}

if ( !$user_lang ) {
    $user_lang = 'zh';
}

if ( !$user_theme ) {
   $user_theme = 'new';
} else {
    if ( !file_exists(ROOT_PATH.'theme/'.$user_theme) ) {
        $user_theme = 'new';
    }
}


$_SESSION['system_request_theme'] = $user_theme;
$_SESSION['system_request_lang'] = $user_lang;


// the image access URL
if ( !defined('IMAGE_URL') ) {
   define('IMAGE_URL', ROOT_URL.'theme/'.$user_theme.'/images/');
}

// the theme access URL
if ( !defined('THEME_URL') ) {
   define('THEME_URL', ROOT_URL.'theme/'.$user_theme.'/');
}



// the theme path
if ( !defined('THEME_PATH') ) {
   define('THEME_PATH', ROOT_PATH.'theme/'.$user_theme.'/');
}

define('SYSTEM_LANG', $user_lang);



// website email
define('WEBSITE_EMAIL', $system_admin_email);

//定义可视化web编辑器的参数
define('FCKEDITOR_BASEPATH', $system_fckeditor_basepath);
define('FCKEDITOR_UPLOADDIR', $system_fckeditor_uploaddir);








?>
