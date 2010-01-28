<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * Project: 5anet(BBS)
 * File:    install.php
 *
 * Install 5anet(BBS) in your server
 *
 * after install the 5anet(BBS) in your server, please remove this script
 *
 * PHP Version 5
 *
 * @package:   NULL
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: index.php,v 1.2 2006-09-24 14:38:08 ghw Exp $
 * @date:      $Date: 2006-09-24 14:38:08 $
 */

//we don't limited the run time limition.
set_time_limit(0);
session_start();
include_once 'lib/smarty/Smarty.class.php';


$smarty = new Smarty();
$smarty->template_dir = './install';
    

$smarty->compile_dir =  './cache/smarty/';

$smarty->left_delimiter = '<{';
$smarty->right_delimiter = '}>';



//get the step parameter.

$step = $_SERVER['REQUEST_METHOD'] == 'GET' ? $_GET['step']:$_POST['step'];

$smarty->assign('step', $step);

if ( !$step ) {
    header('Content-type:text/html;charset=UTF-8');
    showDefaultPage();
    exit(0);
} else if ( $step == 1 ) {
    header("Content-type:text/html;charset=UTF-8");
    showLicense();
    exit(0);
} else if ( $step == 2 ) {
    header("Content-type:text/html;charset=UTF-8");
    showFileConfig();
    exit(0);
} else if ( $step == 3 ) {
    header("Content-type:text/html;charset=UTF-8");
    checkPath();
    exit(0);
} else if ( $step == 4 ) {
    header("Content-type:text/html;charset=UTF-8");
    showDatabase();
    exit(0);
} else if ( $step == 5 ) {
    header("Content-type:text/html;charset=UTF-8");
    checkDatabase();
    exit(0);
} else if ( $step == 6 ) {
    header("Content-type:text/html;charset=UTF-8");
    complete();
    exit(0);
} else if ( $step == 7 ) {
    header("Content-type:text/html;charset=UTF-8");
    showJump();
    exit(0);
}



/**
 * jump to check database function
 */
function showJump() {/*{{{*/
    $db_type = $_SESSION['db_type'];
    $db_host = $_SESSION['db_host'];
    $db_name = $_SESSION['db_name'];
    $db_user = $_SESSION['db_user'];
    $db_passwd = $_SESSION['db_passwd'];
    echo <<<SHOWJUMP
    </script><br/><br/></br></br><center><img src="install/images/ajax-loader.gif"></center><br/>
<center><strong>Please wait. We are currently processing your requirement.</strong></center>
<form action="install.php" method="post" name="sform">
<input type="hidden" name="step" value="5">
<input type="hidden" name="confirm" value="1">
<input type="hidden" name="db_type" value="$db_type">
<input type="hidden" name="db_host" value="$db_host">
<input type="hidden" name="db_name" value="$db_name">
<input type="hidden" name="db_user" value="$db_user">
<input type="hidden" name="db_passwd" value="$db_passwd">
<input type="hidden" name="db_persist" value="1">
</form>

<script>
handle = setTimeout("commit_form()",2000);
function commit_form()
{
      clearTimeout(handle);
        document.forms.sform.submit();
}
</script>
SHOWJUMP;
    return;
}
/*}}}*/


/**
 * define the install function 
 */

function showDefaultPage() {/*{{{*/
    global $smarty;
    $smarty->assign('title', '5anet(BBS)安装 － 安装说明');
    $smarty->display('default.tmpl');
    return;
}/*}}}*/


function showLicense() {/*{{{*/
    global $smarty;
    $smarty->assign('title', '5anet(BBS)安装 － 许可协议');
    $smarty->display('license.tmpl');
    return;
}/*}}}*/



function showFileConfig() {/*{{{*/
    
    global $smarty;
    $smarty->assign('title', '5anet(BBS)安装 － 路径和目录设定');


    $temp = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    $temp_array = preg_split("/\//", $temp);

    $domain1 = $temp_array[0];

    $url1 = 'http://';
    for($i=0; $i<count($temp_array)-1; $i++ ) {
       $url1 .= $temp_array[$i]."/"; 
    }

    $domain = $_SESSION['idomain']?$_SESSION['idomain']:$domain1;
    $url = $_SESSION['url']?$_SESSION['url']:$url1;
    $path = $_SESSION['path']?$_SESSION['path']:dirname(__FILE__)."/";
    $cache = $_SESSION['cache']?$_SESSION['cache']:$path.'cache/';
    $adminemail = $_SESSION['adminemail'];
    $adminpassword = $_SESSION['adminpassword'];


    $smarty->assign('domain', $domain);
    $smarty->assign('adminemail', $adminemail);
    $smarty->assign('adminpassword', $adminpassword);

    $smarty->assign('url', $url);
    $smarty->assign('path', $path);
    $smarty->assign('cache', $cache);

    $smarty->display('fileconfig.tmpl');
    
    return;

}
/*}}}*/


function checkPath() {/*{{{*/
    global $smarty;
    $smarty->assign('title', '5anet(BBS)安装 － 路径和目录设定');



    $url = $_POST['url'];
    $cache = $_POST['cache'];
    $path = $_POST['path'];
    $domain = $_POST['domain'];
    $adminemail = $_POST['adminemail'];
    $adminpassword = $_POST['adminpassword'];

    $_SESSION['url'] = $url;
    $_SESSION['cache'] = $cache;
    $_SESSION['path'] = $path;
    $_SESSION['domain'] = $domain;
    $_SESSION['adminemail'] = $adminemail;
    $_SESSION['adminpassword'] = $adminpassword;


    ob_end_flush();
    $have_failed = 0;
    $failed_error = array();

    //检测访问的URL
    $content = file_get_contents($url);

    if ( strlen($content) > 0 ) {
        if ( !preg_match("/\/$/", $url) ) {
            $have_failed = 1;
            $failed_error['url'] = "访问URL应该以\"/\"结尾";
        }
    } else {
        $have_failed = 1;
        $failed_error['url'] = "访问URL没有返回";
    }

    //检测实际安装的路径

    if ( file_exists($path) ) {
        if( !is_dir($path) ) {
            $have_failed = 1;
            $failed_error['path'] = "安装路径不是一个目录";
        }
    } else {
        $have_failed = 1;
        $failed_error['path'] = "安装路径不存在";
    }

    //检测cache的目录
    if ( file_exists($cache) ) {
        if ( is_dir($cache) ) {
            $my_file_perms = substr(sprintf("%o", fileperms($cache)), -3);
            if ( $my_file_perms != 777 && !is_writable($cache) ) {
                $have_failed = 1;
                $failed_error['cache'] = "缓存目录的权限应该是777 或者是web服务器可写";
            }
        } else {
            $have_failed = 1;
            $failed_error['cache'] = "给出的缓存目录不是一个目录";
        }
    } else {
        $have_failed = 1;
        $failed_error['cache'] = "缓存目录不存在";
    }

    //检测upload目录
    if ( file_exists($path.'upload/') ) {
        if ( is_dir($path.'upload/') ) {
            $my_file_perms = substr(sprintf("%o", fileperms($path.'upload/')), -3);
            if ( $my_file_perms != 777 && !is_writable($path.'upload') ) {
                $have_failed = 1;
                $failed_error['upload'] = "上传目录".$path."upload/的权限应该是777 或者web服务器可写";
            }
        } else {
            $have_failed = 1;
            $failed_error['upload']= "给出的上传目录".$path."upload/不是一个目录";
        }
    } else {
        $have_failed = 1;
        $failed_error['upload']= "上传目录".$path."upload/'不存在";
    }


    //检测配置文件是否可以读取
    if ( file_exists('config/config.inc.php') ) {
        if ( !is_writable('config/config.inc.php') ) {
            $have_failed = 1;
            $failed_error['config']= "配置文件config/config.inc.php权限应该是web服务器可写或者是777";
        }
    } else {
        $have_failed = 1;
        $failed_error['config']= "配置文件config/config.inc.php不存在";
    }

    
    $smarty->assign('have_failed', $have_failed);
    $smarty->assign('failed_error', $failed_error);

    $smarty->display('checkpath.tmpl');

}
/*}}}*/

function showDatabase() {/*{{{*/
    global $smarty;
    $smarty->assign('title', '5anet(BBS)安装 － 数据库设定');


    $db_type = $_SESSION['db_type'];
    $db_host = $_SESSION['db_host'];
    $db_user = $_SESSION['db_user'];
    $db_passwd = $_SESSION['db_passwd'];
    $db_persist = $_SESSION['db_persist'];
    $db_name = $_SESSION['db_name'];

    $smarty->assign('db_type', $db_type);
    $smarty->assign('db_host', $db_host);
    $smarty->assign('db_user', $db_user);
    $smarty->assign('db_passwd', $db_passwd);
    $smarty->assign('db_persist', $db_persist);
    $smarty->assign('db_name', $db_name);


    $smarty->display('dbconfig.tmpl');

    return;
}
/*}}}*/

function checkDatabase() {/*{{{*/
   global $smarty;

    $db_type = $_POST['db_type'];
    $db_host = $_POST['db_host'];
    $db_user = $_POST['db_user'];
    $db_passwd = $_POST['db_passwd'];
    $db_persist = $_POST['db_persist'];
    $db_name = $_POST['db_name'];


    $_SESSION['db_type'] = $db_type;
    $_SESSION['db_host'] = $db_host;
    $_SESSION['db_user'] = $db_user;
    $_SESSION['db_passwd'] = $db_passwd;
    $_SESSION['db_persist'] = $db_persist;
    $_SESSION['db_name'] = $db_name;

    $dbh = @mysql_connect($db_host, $db_user, $db_passwd);
    
    $failed_error = null;

    if ( !$dbh ) {
        $failed_error = "数据库不能连接上";
    } 
    
    if ( !$_POST['confirm'] && mysql_select_db($db_name, $dbh)  ) {
        $smarty->assign('title', '数据库检测-数据库已存在');
        $smarty->display('checkdbchoice.tmpl');

        return;
    }

    $smarty->assign('title', '5anet(BBS)安装 － 数据库检测');

   $sql = "CREATE DATABASE $db_name CHARACTER SET 'utf8'; use $db_name; set names 'utf8';";

    $temp_str = file_get_contents("db/5anet.sql");

    $sql .= $temp_str;

    $sql = preg_replace("/\r\n/", "", $sql);
    $sql = preg_replace("/\n/", "", $sql);
    $sql = preg_replace("/\r/", "", $sql);

    $sql_array = preg_split("/;/", $sql);
    $run_script = null;
    for($i=0; $i<count($sql_array)-1; $i++ ) {

        mysql_query($sql_array[$i], $dbh);
        $run_script .= $sql_array[$i]."<hr><br>";
    }


    if ( @mysql_errno($dbh) ) {
        $failed_error = '数据库建立失败<br>'.
            "你可以使用CREATE DATABASE databasename CHARACTER SET 'UTF8'<br>";
    }

    $smarty->assign('failed_error', $failed_error);
    $smarty->assign('run_script', $run_script);

    $smarty->display('checkdatabase.tmpl');
    return;

}/*}}}*/

function complete() {/*{{{*/
    echo <<<COMPLETE
<html>
<head><title>Complete Install</title></head>
<body>
<h1>5anet(BBS)简易安装脚本 － 完成安装</h1>
COMPLETE;

    if ( !file_exists("config/config.inc.php") ) {
       echo "<font color=red>config/config.inc.php配置文件不存在!请手工更改Sample文件</font><br>";
       echo "<input type=\"button\" value=\"上一步\" OnClick=\"javascript:history.back()\"/><br>";
       echo "</body>\n";
       echo "</html>\n";
       exit; 
    } else {
        $my_file_perms = substr(sprintf("%o", fileperms('config/config.inc.php')), -3);

        if ( $my_file_perms != 777 && !is_writable('config/config.inc.php') ) {
            echo "<font color=red>config/config.inc.php不可写，如果是Linux文件系统，请更改为777或者是改为web用户所有者</font><br>";
            echo "<input type=\"button\" value=\"上一步\" OnClick=\"javascript:history.back()\"/><br>";
            echo "</body>\n";
           echo "</html>\n";
           exit; 
        }

    }

    $file = file_get_contents("config/config.inc.php.macro");

    $file = preg_replace("/%DB_TYPE%/", $_SESSION['db_type'], $file);
    $file = preg_replace("/%DB_HOST%/", $_SESSION['db_host'], $file);
    $file = preg_replace("/%DB_NAME%/", $_SESSION['db_name'], $file);
    $file = preg_replace("/%DB_USER%/", $_SESSION['db_user'], $file);
    $file = preg_replace("/%DB_PASS%/", $_SESSION['db_passwd'], $file);
    $file = preg_replace("/%DB_PERSIST%/", $_SESSION['db_persist']?$_SESSION['db_persist']:0, $file);
    $file = preg_replace("/%DB_DEBUG%/", 0, $file);

    $file = preg_replace("/%ACCESS_URL%/", $_SESSION['url'], $file);
    $file = preg_replace("/%ACCESS_PATH%/", $_SESSION['path'], $file);
    $file = preg_replace("/%CACHE_PATH%/", $_SESSION['cache'], $file);
    $file = preg_replace("/%WEB_DOMAIN%/", $_SESSION['domain'], $file);
    $file = preg_replace("/%SYSTEM_ADMIN_EMAIL%/", $_SESSION['adminemail'], $file);

    $temp_url = $_SESSION['url'];
    $temp_url = preg_replace("/http:\/\//", "", $temp_url);

    $temp_array = preg_split("/\//", $temp_url);

    $temp_base = '/';
    for($i=1; $i<count($temp_array)-1; $i++ ) {
        $temp_base .= $temp_array[$i];
    }
    
    if ( $temp_base == '/' ) {
        $fckeditor_base = $temp_base . "lib/fckeditor/";
        $upload_dir = $temp_base ."upload/fckeditor/";
    } else {
        $fckeditor_base = $temp_base . "/lib/fckeditor/";
        $upload_dir = $temp_base ."/upload/fckeditor/";
    }


    $file = preg_replace("/%FCKEDITOR_BASEPATH%/", $fckeditor_base, $file);
    $file = preg_replace("/%FCKEDITOR_UPLOADDIR%/", $upload_dir, $file);

    $fh = fopen("config/config.inc.php", "w");
    fwrite($fh, $file);
    fclose($fh);

    $dbh = mysql_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_passwd']);
    mysql_select_db($_SESSION['db_name']);
    $sql = "update sys_admin set user_passwd='".md5($_SESSION['adminpassword'])."' where user_name='admin' ";
    mysql_query($sql);

    
    $adminpassword = $_SESSION['adminpassword'];

   
    echo <<<WRITEFILE
<h2><font color=red>配置文件已经生成</font></h2>
你也可以将下面的配置文件内容粘贴成config/config.inc.php文件。<br>
<br>
管理控制台的登录入口是:<a href="${_SESSION['url']}admin/">${_SESSION['url']}admin/</a>, <br>
默认管理员帐号是: admin, 管理密码是：$adminpassword
<br>
<br>

WRITEFILE;

    echo highlight_string($file, true), "<br>";

    echo "</body>\n";
    echo "</html>\n";

 session_destroy();


}/*}}}*/

?>
