<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  class/user/RecoverEmail.class.php
*
*  Show the Recover password interface by email.
*  
*  PHP Version 5
*  
*  @package:   class/user
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: $
*  @date:      @Date:$
*/

include_once CLASS_PATH.'main/BaseAction.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/RecoverEmail.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/RecoverEmail.lang.php';
}

class RecoverEmail extends BaseAction {

    private $db;

    public function __construct() {
        $this->db = $this->getDB();
    }

   
   /**
   *  run this action
   *  @param:  NULL
   *  @return: NULL
   *  @access: public
   */
   public function run() {
        
       //get the email from PoST
       $email = $this->getParameterFromPost('recover_email');

       if ( !$email ) {
           $this->AlertAndBack(RE_EMAIL_IS_NULL);
           return;
       }

       $sql = 'select count(*) as num from base_user_info where user_email=?';
       $stmt = $this->db->Prepare($sql);
       $res = $this->db->Execute($stmt, array($email));
        
       $rows = $res->FetchRow();

       if ( !$rows['num'] ) {
            $this->AlertAndBack(RE_EMAIL_IS_NOT_EXISTS);
            return;
       }


    //send email to user
      $to_address = $rows['user_email'];

      $headers = "To:".$email."\r\n";
      $headers .= "From:".$system_admin_email."\r\n";
      $password_array = array();

      for($i=1; $i<7; $i++ ) {
        $password_array[$i] = rand(1,10);
      }

      $password = implode('', $password_array);

      //设置email 内容
      $content = '您注册使用的电子邮件是:'.$email."\r\n".
          "您的密码重新初始化为:".$password."\r\n";


      mail($email, RE_RECOVER_PASSWORD, $content, $headers);

      $this->TipsAndForward(
          RE_RECOVER_PASSWORD_SUCCESS);
   }
}

?>
