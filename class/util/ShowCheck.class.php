<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  项目：   5anet
*  文件：   class/util/ShowCheck.class.php
*
*  显示随机注册码
*
*  PHP Version 5
*
*  @package:   class.util
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: ShowCheck.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*/

include_once CLASS_PATH.'main/BaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ShowCheck.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ShowCheck.lang.php';
}

class ShowCheck extends BaseAction {
   /**
   *  显示需要的注册码，并将注册码存入session里。
   *  @param:  NULL
   *  @return: NULL
   *  @access: public
   */
   public function run() {/*{{{*/
      /**
      *  定义可用的字符集
      */
      $check_char = array (
         0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9,
         10=>'A', 11=>'B', 12=>'C', 13=>'D', 14=>'E', 15=>'F', 16=>'G',
         17=>'H', 18=>'I', 19=>'J', 20=>'K', 21=>'L', 22=>'M', 23=>'N',
         24=>'O', 25=>'P', 26=>'Q', 27=>'R', 28=>'S', 29=>'T', 30=>'W',
         31=>'V', 32=>'Y', 33=>'X', 34=>'Z'
      );

      $check_stirng = "";

      for($i=1; $i<=4; $i++ ) {
         $j = rand(1, 34);
         $check_string .= $check_char[$j];
      } 

      //向Session里写入校验码变量
      unset($_SESSION['register_check_code']);

      $_SESSION['register_check_code'] = $check_string;

      header("Content-type: image/png");
      $im = @imagecreate(80, 20) or die("Can't create a new image\n");
      $background = imagecolorallocate($im, 143, 143, 142);
      $black = imagecolorallocate($im, 0, 0, 0);
      
      //写出注册码
      imagestring($im, 5, 8, 2, $check_string, $black);
      imagepng($im);
      imagedestroy($im);

   }/*}}}*/


}

?>
