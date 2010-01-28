<?php
// vim: set expandtab tabstop=3 softtabstop=3 shiftwidth=3 foldcolumn=1 foldmethod=marker:
/**
*  取得用户的真实IP和代理IP
*  
*  PHP Version 4, 5
*  
*  @package:   functions
*  @authro:    黄叶<hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
*  @copyright: 阿叶的小屋 2003-2005 The A_Ye's Little House
*  @version:   $Id: getIp.fun.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

/**
*  @param NULL
*  @return array() 'ip'=>'真实ip', 'proxy'=>'代理IP'
*  @access public
*/

function getIp() {

   //取得真实ip和代理ip
   if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
      if ($_SERVER["HTTP_CLIENT_IP"]) {
      $proxy = $_SERVER["HTTP_CLIENT_IP"];
     } else {
      $proxy = $_SERVER["REMOTE_ADDR"];
     }
     $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
   } else {
     if ($_SERVER["HTTP_CLIENT_IP"]) {
         $ip = $_SERVER["HTTP_CLIENT_IP"];
     } else {
         $ip = $_SERVER["REMOTE_ADDR"];
     }
   }

   if ( !$proxy ) {
      $proxy = $ip;
   }

   return array('ip'=>$ip, 'proxy'=>$proxy);
}
?>
