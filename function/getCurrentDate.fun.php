<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:     5anet
*  File Name   :     function/getCurrentDate.fun.php
*
*  get the current Date
*
*  PHP Version 5
*
*  @package:   function
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: getCurrentDate.fun.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      @Date:$
*/

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/getCurrentDate.fun.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/getCurrentDate.fun.lang.php';
}

function getCurrentDate() {
   // get the current date
   $current_array_date = getdate();

   // get the current year
   $current_year  = $current_array_date['year'];

   // get the current month
   $current_month = $current_array_date['mon'];

   // get the current day
   $current_day   = $current_array_date['mday'];

   // get the current week
   $current_week  = $current_array_date['wday'];

   switch ( $current_week ) {
      case 0:
         $string_week = FUN_SUNDAY;
         break;
      case 1:
         $string_week = FUN_MONDAY;
         break;
      case 2:
         $string_week = FUN_TUESDAY;
         break;
      case 3:
         $string_week = FUN_WEDNESDAY;
         break;
      case 4:
         $string_week = FUN_THURSDAY;
         break;
      case 5:
         $string_week = FUN_FRIDAY;
         break;
      case 6:
         $string_week = FUN_SATURDAY;
         break;
   }

   $return_string = $current_year.'/';
   $return_string .= $current_month.'/';
   $return_string .= $current_day.' ';
   $return_string .= $string_week;

   return $return_string;

}


function getNoFormateCurrentDate() {
   // get the current date
   $current_array_date = getdate();

   // get the current year
   $current_year  = $current_array_date['year'];

   // get the current month
   $current_month = $current_array_date['mon'];

   // get the current day
   $current_day   = $current_array_date['mday'];

   //get the hour
   $current_hour = $current_array_date['hours'];

   //get the minutes
   $current_minutes = $current_array_date['minutes'];

   //get the seonds
   $current_seconds = $current_array_date['seconds'];


   $return_string = $current_year.'-';
   $return_string .= $current_month.'-';
   $return_string .= $current_day.' ';
   $return_string .= $current_hour.":".$current_minutes.":".$current_seconds;

   return $return_string;
}

?>
