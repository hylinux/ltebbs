<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File  Name: class/user/ShowRegister.class.php
*
*  显示注册页面
*  
*  @package:   class.user
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: ShowRegister.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      @Date:$
*/

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once LIB_PATH.'fckeditor/fckeditor.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ShowRegister.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ShowRegister.lang.php';
}

class ShowRegister extends BaseAction {
   /**
   *  显示注册的界面
   *  @param:  NULL
   *  @return: NULL
   *  @access: public
   */
   public function run() {
      /**
      *  现判断用户是否已经登录，
      *  如果已经登录，则不能再次注册
      */
      if ( isset($_SESSION['user']) ) {
         $this->AlertAndForward(SR_USER_HAD_LOGIN);
         return;
      }

      //显示注册的页面
      $smarty = $this->getSmarty();

      //显示默认的头像
      $image_array = "";

      for($i=1; $i<=37; $i++ ) {
         $image_array .= "<option value=".$i.">第".$i."个头像</option>\n";
      }

      $smarty->assign('image_options', $image_array);

      //使用fckeditor
      $fck = new FCKeditor("user_sign");
      $fck->BasePath = FCKEDITOR_BASEPATH;
      $fck->ToolbarSet = 'Basic';
      $fck->Height = '200';
      $fck->Width = '840';
      $smarty->assign('fck', $fck);

      //年月日
      $year_option = '';
      for($i=1959; $i<=1997; $i++ ) {
        $year_option .= "<option value='$i'>$i</option>\n";
      }

      $smarty->assign('birthday_year', $year_option);

      $month_option = '';
      for($i=1; $i<=12; $i++ ) {
        $month_option .= "<option value='$i'>$i</option>\n";
      }
      $smarty->assign('birthday_month', $month_option);

      $day_option = '';
      for($i=1; $i<=31; $i++ ) {
        $day_option .= "<option value='$i'>$i</option>\n";
      }
      $smarty->assign('birthday_day', $day_option);

      $smarty->display('showregister.tmpl');

      return;
   }
}

?>
