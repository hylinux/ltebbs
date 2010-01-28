<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:     5anet
*  File Name   :     class/main/Action.class.php
*
*  Action base class
*  
*  The class had defined one parent Class to all action
*  and in this class, we will defined some function
*  to share in all Action
*
*  PHP Version 5
*
*  @package:   class/main
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: Action.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      @Date:$
*/

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/Action.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/Action.lang.php';
}

// include the function defined
if ( file_exists(FUNCTION_PATH.'getCurrentDate.fun.php') ) {
   include_once FUNCTION_PATH.'getCurrentDate.fun.php';
} 

abstract class Action {
   
   /**
   *  the abstract function
   */
   public abstract function run();


   /**
   *  return one Smarty instance
   *  @param:  NULL
   *  @return: $smarty Smarty instance ref
   *  @access: protected
   */
   protected abstract function &getSmarty();

   /**
   *  get the database connection information
   *  @param:  NULL
   *  @return: $db ref
   *  @access: protected
   */
   protected function &getDB() {/*{{{*/
      if ( file_exists(CLASS_PATH.'main/DB.class.php') ) {
         include_once CLASS_PATH.'main/DB.class.php';
      } else {
         die("Can't include the Database connection.\n");
      }

      $db = DB::getConnection();

      return $db;
   }/*}}}*/


   /**
   *  get the cache database connection information
   *  @param:  NULL
   *  @return: $db ref
   *  @access: protected
   */
   protected function &getCacheDB() {/*{{{*/
      if ( file_exists(CLASS_PATH.'main/DB.class.php') ) {
         include_once CLASS_PATH.'main/DB.class.php';
      } else {
         die("Can't include the Database connection.\n");
      }

      $db = DB::getCacheConnection();

      return $db;
   }/*}}}*/



   /**
   *  get the URL Parameter or the Form Parameter
   *  don't worried GET or POST
   *  @param:  $name
   *  @return: $value
   *  @access: protected
   */
   protected function getParameter($name) {/*{{{*/
      if ( !$name ) {
         return null;
      }

      $value = $_SERVER['REQUEST_METHOD'] == 'GET'?$_GET["$name"]:$_POST["$name"];

      if ( get_magic_quotes_gpc() ) {


      } 

      return $value;
   }/*}}}*/

   /**
    * get the user's input from GET
    * @param: $name
    * @return: $value
    * @access: protected
    */
   protected function getParameterFromGET($name) {/*{{{*/
      if ( !$name ) {
         return null;
      }

      return $_GET[$name];
   }/*}}}*/

   /**
    * get the user's input from POST
    * @param:  $name
    * @return: $value
    * @access: protected
    */
   protected function getParameterFromPOST($name) {/*{{{*/
      if ( !$name ) {
         return null;
      }

      return $_POST[$name];
   }/*}}}*/

   /**
   *  Forward one page to the other page
   *  @param:  $url;
   *  @return: NULL
   *  @access: protected
   */
   protected function forward($url) {/*{{{*/
      if ( !$url ) {
         return;
      }

      header('Location:'.$url);
      return;
   }/*}}}*/

   /**
   *  show one page
   *  @param:  $html template file
   *  @return: NULL
   *  @access: protected
   */
   protected function show($html) {/*{{{*/
      if ( !$html || strlen($html) == 0  ) {
         return;
      }

      if ( !file_exists(THEME_PATH.$html) ) {
         die("The Resource file: $html is not exists.");
      }

      $template = $this->getSmarty();
      $template->display($html);
      return;
   }/*}}}*/


   /**
   *  return a Dialog for back and alert.
   *  @param:  $errorMessage
   *  @return: NULL
   *  @access: protected
   */
   protected function AlertAndBack($errorMessage='') {/*{{{*/
      $errorMessage = addslashes($errorMessage);

      $smarty = $this->getSmarty();
      $smarty->assign('errorMessage', $errorMessage);
      $smarty->display('alterandback.tmpl');

      return;
      /*
      header("Content-type:text/html;charset=UTF-8");
      echo "<script language=javascript>\n";
      echo "alert('$errorMessage');\n";
      echo "history.back();\n";
      echo "</script>\n";
      return;
      */


   }/*}}}*/

   /**
   *  return a Dialog for alert and forward
   *  @param:  $errorMessage
   *  @param:  $url
   *  @return: NULL
   *  @access: protected
   */
   protected function AlertAndForward($errorMessage, $url='index.php') {/*{{{*/
      $errorMessage = addslashes($errorMessage);

      $smarty = $this->getSmarty();
      $smarty->assign('errorMessage', $errorMessage);
      $smarty->assign('go_url', $url);
      $smarty->display('alterandforward.tmpl');

      return;

      /*
      header("Content-type:text/html;charset=UTF-8");
      echo "<script language=javascript>\n";
      echo "alert('$errorMessage');\n";
      echo "document.location.href='$url';\n";
      echo "</script>\n";
      return;
       */

   }/*}}}*/

   /**
    * return a Dialog for success and forward
    * @param: $message
    * @param: $url
    * @return:  NULL
    * @access:  protected
    */
   protected function TipsAndForward($message, $url='index.php') {/*{{{*/
      $errorMessage = addslashes($message);

      $smarty = $this->getSmarty();
      $smarty->assign('message', $message);
      $smarty->assign('go_url', $url);
      $smarty->display('tipsandforward.tmpl');

      return;

      /*
      header("Content-type:text/html;charset=UTF-8");
      echo "<script language=javascript>\n";
      echo "alert('$errorMessage');\n";
      echo "document.location.href='$url';\n";
      echo "</script>\n";
      return;
       */

   }/*}}}*/


   
   /**
    * Magic function for unserialize
    * @param:  NULL
    * @return: NULL
    * @access: protected
    */
   public function __wakeup() {
      $this->db = $this->getDB();
   }

}

?>
