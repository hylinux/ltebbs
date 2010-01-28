<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/main/DB.class.php
*
*  define a method get the database
*
*  This is return a ADODB class instance and only defined one static
*  method to get the database connection
*
*  PHP Version 5
*
*  @package:   class/main
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: DB.class.php,v 1.2 2006-09-24 14:38:08 ghw Exp $
*  @date:      @Date:$
*/

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/DB.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/DB.lang.php';
}

class DB {

   /**
   *  this class instance
   *  @static
   *  @var object
   *  @access private
   */
   private static $instance;

   /**
   *  the construct;
   *  because we must use the single step .
   *  so we must set the accessably is private
   */
   private function __construct() {

   }

   /**
   *  get the object instance
   *  @param:  NULL
   *  $db ref
   *  @access: public
   *  @static
   */
   public static function getConnection() {
      if ( !isset(self::$instance) ) {
         $c = __CLASS__;
         self::$instance = new $c;
      }

      return self::$instance->getDataBase();

   }


   /**
   *  get the object instance
   *  @param:  NULL
   *  $db ref
   *  @access: public
   *  @static
   */
   public static function getCacheConnection() {
      if ( !isset(self::$instance) ) {
         $c = __CLASS__;
         self::$instance = new $c;
      }

      return self::$instance->getCacheDataBase();

   }


   /**
    * return the cache databaes connection
    * @param:  NULL
    * @return: $db ref
    * @access: public
    */
   public function &getCacheDataBase() {
      global $db_type, $db_host, $db_name, $db_user, $db_pass, $use_persist;
      global $use_db_debug;

      if ( $use_persist ) {
         $db_dsn = $db_type.'://'.$db_user.':'.$db_pass.'@'.$db_host.'/'.$db_name.'?persist';
      } else {
         $db_dsn = $db_type.'://'.$db_user.':'.$db_pass.'@'.$db_host.'/'.$db_name;
      }

      if ( file_exists(LIB_PATH.'adodb/adodb.inc.php') ) {
         include_once LIB_PATH.'adodb/adodb.inc.php';
      } else {
         die("you have not install ADODB or it is not the correct location.");
      }



      $db = &NewADOConnection($db_dsn);


      if ( !$db ) {
         die("Can't connection the database.");
      }

      if ( $use_db_debug ) {
         $db->debug = true;
      }

      $db->Execute("SET NAMES 'utf8'");

      return $db; 

   }

   /**
   *  return the database connection
   *  @param:  NULL
   *  @return: $db ref
   *  @access: public
   */
   private function &getDataBase() {
      global $db_type, $db_host, $db_name, $db_user, $db_pass, $use_persist;
      global $use_db_debug;

      if ( $use_persist ) {
         $db_dsn = $db_type.'://'.$db_user.':'.$db_pass.'@'.$db_host.'/'.$db_name.'?persist';
      } else {
         $db_dsn = $db_type.'://'.$db_user.':'.$db_pass.'@'.$db_host.'/'.$db_name;
      }

      if ( file_exists(LIB_PATH.'adodb/adodb.inc.php') ) {
         include_once LIB_PATH.'adodb/adodb.inc.php';
      } else {
         die("you have not install ADODB or it is not the correct location.");
      }


      $db = &NewADOConnection($db_dsn);


      if ( !$db ) {
         die("Can't connection the database.");
      }

      if ( $use_db_debug ) {
         $db->debug = true;
      }

      $db->Execute("SET NAMES 'utf8'");

      return $db; 



   }

}

?>
