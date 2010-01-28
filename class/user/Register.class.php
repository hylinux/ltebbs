<?php
//vim:set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldcolumn=1 foldmethod=marker:
/**
*  项目：   5anet
*  文件：   class/user/Register.class.php
*
*  用户注册请求处理
*
*  PHP Version 5
*
*  @package:   class.user
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: Register.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      @Date:$
*/

include_once CLASS_PATH.'main/BaseAction.class.php';
include_once FUNCTION_PATH.'getCurrentDate.fun.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/Register.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/Register.lang.php';
}


class Register extends BaseAction {
   /**
   *  处理用户的注册请求
   *  @param:  NULL
   *  @return: NULL
   *  @access; public
   */
   public function run() {
      /**
      *  现判断用户是否已经登录，
      *  如果已经登录，则不能再次注册
      */
      if ( isset($_SESSION['user']) ) {
         $this->AlertAndForward(RE_USER_HAD_LOGIN);
         return;
      }

      /**
      *  收集变量
      */
      $username = $this->getParameterFromPost('username'); //用户名
      $useremail = $this->getParameterFromPost('useremail');  //用户邮件
      $userpass = $this->getParameterFromPost('userpass'); //用户密码
      $userpasscheck = $this->getParameterFromPost('userpasscheck');   //校验密码
      $check_code = strtolower($this->getParameterFromPost('checkcode')); //注册校验密码
      $userhead = $this->getParameterFromPost('persionimage');   //用户选择的头像
      $public_email = $this->getParameterFromPost('public_email');   //是否公开用户的邮件


      if ( $public_email != 1 ) {
         $public_email = 0;
      }

      $username = strtolower($username);


      //校验用户名
      if ( !$username || strlen($username) <= 0 ) {/*{{{*/
         $this->AlertAndForward(RE_USER_NAME_NOT_EMPTY,
            'index.php?module=user&action=register');
         return;
      }

      if ( strlen($username) > 30 ) {
         $this->AlertAndForward(RE_USER_NAME_TOO_LONG,
         'index.php?module=user&action=register');
         return;
      }/*}}}*/

      //注意保留用户名
      if ( strtolower($username) == 'system' ) {/*{{{*/
         $this->AlertAndForward(RE_USER_NAME_IS_KEEP,
            'index.php?module=user&action=register');
         return;
      }
/*}}}*/

      //验证用户名是否已经存在
      $db = $this->getDB();/*{{{*/

      $sql = 'select count(*) as num from base_user_info where lower(user_name)=';
      $sql .= $db->qstr(strtolower($username), get_magic_quotes_gpc());
      $res = $db->Execute($sql);
      $rows = $res->FetchRow();
      $num = $rows['num'];

      if ( $num ) {
         $this->AlertAndForward(RE_USER_NAME_EXISTS,
         'index.php?module=user&action=register');
         return;
      }/*}}}*/

      //校验用户的邮件的合法性
      if ( !$useremail || strlen($useremail) <= 0 ) {/*{{{*/
         $this->AlertAndForward(RE_USER_EMAIL_NOT_EMPTY,
         'index.php?module=user&action=register');
         return;
      }/*}}}*/

      //校验用户邮件格式的合法性
      if ( !preg_match("/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/i", $useremail) ){/*{{{*/
         $this->AlertAndForward(RE_USER_EMAIL_FORMAT_ERROR,
         'index.php?module=user&action=register');
         return;
      }/*}}}*/

      //验证用户邮件是否存在
      $sql = 'select count(*) as num from base_user_info where lower(user_email)=';/*{{{*/
      $sql .= $db->qstr(strtolower($useremail), get_magic_quotes_gpc());
      $res = $db->Execute($sql);
      $rows = $res->FetchRow();
      $num = $rows['num'];

      if ( $num ) {
         $this->AlertAndForward(RE_USER_EMAIL_EXISTS,
            'index.php?module=user&action=register');
         return;
      }/*}}}*/

      //校验密码和验证密码
      if ( strlen($userpass) <= 0 || strlen($userpasscheck) <= 0 ) {/*{{{*/
         $this->AlertAndForward(RE_USER_PASS_NOT_EMPTY,
            'index.php?module=user&action=register');
         return;
      }

      if ( $userpass != $userpasscheck ) {
         $this->AlertAndForward(RE_PASS_NOT_CHECK,
            'index.php?module=user&action=register');
         return;
      }/*}}}*/

      //校验用户的头像
      if ( !$userhead ) {/*{{{*/
         $userhead = 1;
      }

      if ( !$userhead ) {
         $userhead = 1;
      }

      if ( $userhead > 37 ) {
         $userhead = 37;
      }/*}}}*/

 
      
      //从session得到已经存储的校验密码
      $register_check_code = strtolower($_SESSION['register_check_code']);

      //校验如果是验证码不对，则提示。
      if ( $check_code != $register_check_code ) {/*{{{*/
         $this->AlertAndForward(RE_CHECK_CODE_NOT_VALIDATE, 
            'index.php?module=user&action=register');
         return;
      }/*}}}*/

 


      //注册性别
      $register_gender = $this->getParameterFromPost('register_gender');
      if ( $register_gender != 'keep' &&
          $register_gender != 'male' &&
          $register_gender != 'female' ) {
            
              $register_gender = 'keep';
      }

        
      //来自哪里
      $user_hometown = $this->getParameterFromPost('user_hometown');
      if ( empty($user_hometown) ) {
            $user_hometown = '';
      }

      //生日
      $birthday_year = $this->getParameterFromPost('birthday_year');
      $birthday_month = $this->getParameterFromPost('birthday_month');
      $birthday_day = $this->getParameterFromPost('birthday_day');
      //检查日期的合法性
      $check_time = mktime(0, 0, 0, $birthday_month, $birthday_day, $birthday_year);

      if ( !$check_time || $check_time == -1 ) {
        $this->AlertAndForward(RE_CHECK_BIRTHDAY_NOT_VALIDATE,
            'index.php?module=user&action=register');
        return;
      }

      $birthday_date = $birthday_year.'-'.$birthday_month.'-'.$birthday_day;


      //个人网站
      $user_website = $this->getParameterFromPost('user_website');

      if ( empty($user_website) ) {
        $user_website = '';
      }

      //默认语言
      //目前只支持一种语言。所以写死在这儿了
      $user_lang = $this->getParameterFromPost('user_lang');
      if ( $user_lang != 'zh' ) {
          $user_lang = 'zh';
      }

      //界面风格
      $user_theme = $this->getParameterFromPost('user_theme');
      if ( $user_theme != 'new' || $user_theme != 'newll' ) {
            $user_theme = 'new';
      }

      //是否接收新邮件
      //
      $receive_system_email = $this->getParameterFromPost('receive_system_email');
      if ( $receive_system_email != 1 ||
          $receive_system_email != 0 ) {
        $receive_system_email = 1;
          }

      //是否接收系统消息
      $receive_system_message = $this->getParameterFromPost('receive_system_message');
      if ( $receive_system_message != 1 ||
          $receive_system_message != 0 ) {
        $receive_system_message = 1;
          }


      //个性化签名
      $user_sign = $this->getParameterFromPost('user_sign');
      if ( empty ( $user_sign ) ) {
        $user_sign = '';
      }

      if ( !get_magic_quotes_gpc() ) {

      }











    //通过所有的验证，开始进行真正的注册动作
      
      //查询新用户默认所属于的组
      /*
      $sql = 'select user_grp from new_user_group';
      $res = $db->SelectLimit($sql, 1, 0);
      $rows = $res->FetchRow();
      $user_grp = $rows['user_grp'];
       */

      if ( !$user_grp ) {
         $user_grp = 4;
      }


      $sql = 'insert into base_user_info (
          user_name, 
          user_password, 
          user_email, 
          user_header, 
          public_user_email, 
          group_dep, 
          register_date,
          user_gender,
          user_hometown,
          user_birthday,
          user_website,
          user_sign
          ) values 
          (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?) ';


      $now = getNoFormateCurrentDate();
      $stmt = $db->prepare($sql);
      //插入数据库
      $db->Execute($stmt,
          array($username, md5($userpass), $useremail, $userhead, $public_email, $user_grp, $now,
      
          $register_gender,
          $user_hometown,
          $birthday_date,
          $user_website,
          $user_sign 
      ) );
      
      if ( $db->ErrorNo() ) {
         $this->AlertAndForward($db->ErrorMsg(),
            'index.php?module=user&action=register');
         return;
      } 

      $temp_user_id = $db->Insert_ID();

      $sql = 'insert into user_setting (user_lang, user_theme, user_whether_receive_email, '.
            'receive_system_message, user_id ) values (?, ?, ?, ?, ? ) ';
         $sth = $db->Prepare($sql);
         $db->Execute($sth, array($user_lang, $user_theme, $receive_system_email,
            $receive_system_message, $temp_user_id));

      unset($_SESSION['register_check_code']);

      //注册成功
      $this->TipsAndForward(RE_REGISTER_SUCCESS,
         'index.php?module=user&action=showlogin');
      return;
   }

}

?>
