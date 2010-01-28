<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 按用户的设定的区域，显示用户的时间
 *
 * PHP Version 5
 *
 *  @package:   functions
 *  @authro:    Mike.G <黄叶> <hylinux@gmail.com> 
 *  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *  @copyright: 
 *  @version:   $Id: set_locale_time.fun.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 *  @date:      $Date: 2006-08-28 13:09:20 $
 */

/**
 * 根据用户的设定进行日期和时间的转换
 * @param:  $time integer
 * @return: $str_time String
 * @access: public
 */

function set_locale_time($time) {
   /*
   if ( isset($_SESSION['user']['localtime'] ) {

   }*/

   if ( $time ) {
      return strftime("%G-%m-%d %H:%M:%S", $time);
   }

}

?>
