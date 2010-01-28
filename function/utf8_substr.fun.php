<?php
// vim: set expandtab tabstop=3 softtabstop=3 shiftwidth=3 foldcolumn=1 foldmethod=marker:
/**
 *  截断UTF8编码字符串
 *  
 *  PHP Version 4, 5
 *  
 *  @package:   functions
 *  @author:    Mike.G <¿¿> <hylinux@gmail.com>
 *  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *  @copyright: 
 *  @version:   $Id: utf8_substr.fun.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 *  @date:      $Date: 2006-08-28 13:09:20 $
 */

/**
*  @param NULL
*  @return boolean
*  @access public
*/

function utf8_substr($str,$start=0, $myend=15)
{
   preg_match_all("/./su", $str, $ar);

   if(func_num_args() >= 3) {
      $end = func_get_arg(2);
      return join("",array_slice($ar[0],$start,$end));
   } else {
      return join("",array_slice($ar[0],$start));
   }
}

?>
