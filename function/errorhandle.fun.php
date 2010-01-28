<?php
// vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldcolumn=1 foldmethod=marker:
/**
 *  设置自定义的错误处理程序
*  
*  PHP Version 4, 5
*  
*  @package:   functions
*  @authro:    黄叶<hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
*  @copyright: 阿叶的小屋 2003-2005 The A_Ye's Little House
*  @version:   $Id: $
*  @date:      $Date: $
*/

/**
*  @param NULL
*  @return array() 'ip'=>'真实ip', 'proxy'=>'代理IP'
*  @access public
*/

function m5abb_error_handle($errno, $errstr, $errfile, $errline) {
    global $system_admin_email;
    switch($errno) {
    case E_WARNING:
        mail($system_admin_email, 'There is a error', $errstr."<br>\r\n".$errfile."<br>\r\n".$errline);
    case E_USER_ERROR:
        mail($system_admin_email, 'There is a error', $errstr."<br>\r\n".$errfile."<br>\r\n".$errline);
        break;
    case E_USER_WARNING:
        mail($system_admin_email, 'There is a error', $errstr."<br>\r\n".$errfile."<br>\r\n".$errline);
        break;
    default:

    }
    return false;

}

?>
