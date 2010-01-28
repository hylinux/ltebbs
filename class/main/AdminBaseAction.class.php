<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet.com
*  File Name   :  class/main/AdminBaseAction.class.php
*
*  inhirt the base class Action as the administrator class
*
*  PHP Version 5
*
*  @package:   class.main
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: $
*  @date:      @Date:$
*/


include_once CLASS_PATH.'post/PostUtil.class.php';

abstract class AdminBaseAction  {
   
   /**
   *  override the base class method getSmarty
   *  @param:  NULL
   *  @return: object $smarty
   *  @access: public
   */
   public function &getSmarty() {
    global $use_db_debug;
      //输出头
      header("Content-Type:text/html;charset=UTF-8");
      if ( file_exists(LIB_PATH.'smarty/Smarty.class.php') ) {
         include_once LIB_PATH.'smarty/Smarty.class.php';
      } else {
         die("you have not install SMARTY or it is not the corrent location");
      }

      include_once FUNCTION_PATH.'getCurrentDate.fun.php';


      $smarty = new Smarty();
      $smarty->template_dir = ROOT_PATH.'theme/admin/';


      //增加根据用户theme的数据
      //进行设置Smarty的编译目录
      if ( !file_exists(DATA_CACHE_PATH.'smarty/admin/')) {
          mkdir(DATA_CACHE_PATH.'smarty/admin/') or
              die("Can't create the Smarty compile directly, please check the privilege is 777");
      }

      $smarty->compile_dir =  DATA_CACHE_PATH.'smarty/admin/';

      $smarty->config_dir  = THEME_CONFIG_PATH.SYSTEM_LANG.'/';


      $smarty->left_delimiter = THEME_LEFT_DELIMITER;
      $smarty->right_delimiter = THEME_RIGHT_DELIMITER;

      if ( $use_db_debug ) {
        $smarty->compile_check = false;
      };


      // assign the images and css file path
      $smarty->assign('image_url', IMAGE_URL);
      $smarty->assign('root_url', ROOT_URL);
      $smarty->assign('css_url', ROOT_URL.'theme/admin/');
      $smarty->assign('now_date', getCurrentDate());
      $smarty->assign('user_name', $_SESSION['user']['name']);
      $smarty->assign('module', $_SESSION['module']);
      $smarty->assign('action', $_SESSION['action']);
      $smarty->assign('showbest', $_GET['showbest']);

      return $smarty;

   }

   /**
   *  the abstract function
   */
   public abstract function run();

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

      if ( !file_exists(ROOT_PATH.'theme/admin/'.$html) ) {
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

      header("Content-type:text/html;charset=UTF-8");
      echo "<script language=javascript>\n";
      echo "alert('$errorMessage');\n";
      echo "history.back();\n";
      echo "</script>\n";
      return;

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


      header("Content-type:text/html;charset=UTF-8");
      echo "<script language=javascript>\n";
      echo "alert('$errorMessage');\n";
      echo "document.location.href='$url';\n";
      echo "</script>\n";
      return;

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

      header("Content-type:text/html;charset=UTF-8");
      echo "<script language=javascript>\n";
      echo "alert('$errorMessage');\n";
      echo "document.location.href='$url';\n";
      echo "</script>\n";
      return;

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
