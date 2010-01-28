<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  config/action_map.inc.php
*  
*  The action map file
*
*  this File include a array defined, and it include all user action
*
*  PHP Version 4 and 5
*
*  @package:   config
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: action_map.inc.php,v 1.3 2006-09-24 14:38:08 ghw Exp $
*  @date:      $Date: 2006-09-24 14:38:08 $
*/

/**
*  The action defined module
*  array (
*   'module name' => array(
*      'action name' => array('class'=>'Class Name', 'validate'=>'whether need check',
*      'onlychecklogin'=>1,
*      'description'=>'action description')
*   )
*);
 */

include_once LANG_PATH.SYSTEM_LANG.'/action_map.lang.php';


$action_map = array(

    /**
     * 用户收藏夹表
     */
    'favor'=>array(
        /**
         * 删除收藏夹目录，并删除收藏夹里的
         * 收藏
         */
        'deldir'=>array(
            'class'=>'DelDir',
            'validate'=>1,
            'onlychecklogin'=>1,
            'description'=>AC_DEL_DIR

        ),

        /**
         * 新添加收藏目录
         */
        'adddir'=>array(
            'class'=>'AddDir',
            'validate'=>1,
            'onlychecklogin'=>1,
            'description'=>AC_ADD_FAVOR_DIR
        ),

        /**
         * 删除收藏夹里的收藏/
         */
        'delete'=>array(
            'class'=>'DeleteFavor', 'validate'=>1,
            'onlychecklogin'=>1,
            'description'=>AC_DELETE_FAVOR
        ),

        /**
         * 显示我的收藏
         */
        'default'=>array(
            'class'=>'ShowFavor', 'validate'=>1,
            'onlychecklogin'=>1,
            'description'=>AC_SHOW_ALL_FAVOR

        ),

        /*
         * 保存添加的收藏
         */
        'save'=>array(
            'class'=>'SaveFavor', 'validate'=>1,
            'onlychecklogin'=>1,
            'description'=>AC_ADD_FAVOR
        ),


        'add'=>array(
            'class'=>'ShowAddFavor', 'validate'=>1,
            'onlychecklogin'=>1,
            'description'=>AC_SHOW_FAVOR_PAGE
     ),


    ),

   /**
   *  用户管理模块
   */
    'user' => array (
        /**
         * 用户参与发表的帖子
         */
        'myreply'=>array(
            'class'=>'ListMyReply', 'validate'=>1,
            'onlychecklogin'=>1,
            'description'=>AC_LIST_MY_REPLY
        ),


        /**
         * 用户发表的主题
         */
        'mytopic'=>array(
            'class'=>'ListMyTopic', 'validate'=>1,
            'onlychecklogin'=>1,
            'description'=>AC_LIST_MY_TOPIC
        ),

        /**
         * 用户查看自己的资料
         */
        'viewself'=>array(
            'class'=>'ViewSelf', 'validate'=>1,
            'onlychecklogin'=>1,
            'description'=>AC_VIEW_SELF_INFO
        ),

        /**
         * 保存用户编辑后的信息
         */
        'saveuserinfo'=>array(
            'class'=>'SaveUserInfo', 'validate'=>1,
            'onlychecklogin'=>1,
            'description'=>AC_SAVE_USER_INFO
        ),

        /**
         * 在用户登录的情况下检验用户的密码
         * 要求来自于用户想更改自己的密码
         */
        'checkpassword' => array(
            'class'=>'CheckPassword', 'validate'=>1,
            'onlychecklogin'=>1,
            'description'=>AC_CHECK_USER_PASSWORD
        ),


        /**
         * 编辑用户资料
         */
        'editinfo'=>array(
            'class'=>'ShowEditInfo', 'validate'=>1,
                'onlychecklogin'=>1,
                'description'=>AC_SHOW_EDIT_INFO,
        ),


        /**
         * 发送密码
         */
        'recoveremail'=>array(
            'class'=>'RecoverEmail', 'validate'=>0,
            'onlychecklogin'=>1,
            'description'=>AC_RECOVER_PASSWORD
        ),

        /**
         * 找回密码
         */
        'recover'=>array(
            'class'=>'RecoverPassword', 'validate'=>0,
            'onlychecklogin'=>1,
            'description'=>AC_RECOVER_PASSWORD


        ),

        /**
         * 验证码是否正确
         */
        'checkcode' => array(
            'class'=>'CheckCode', 'validate'=>0,
            'onlychecklogin'=>1,
            'description'=>AC_CHECK_CODE_IS_RIGHT


        ),

        /**
         * 检查用户邮件是否已经存在
         */
        'checkemail'=>array(
            'class'=>'CheckEmail', 'validate'=>0,
            'onlychecklogin'=>1,
            'description'=>AC_CHECK_USEREMAIL_WHETHER_EXISTS
        ),


        /**
         * 检查用户是否存在
         */
        'checkuser'=> array(
            'class'=>'CheckUser', 'validate'=>0,
            'onlychecklogin'=>1,
            'description'=>AC_CHECK_USERNAME_WHETHER_EXISTS


        ),

        /**
         * 更改网站外观
         */
        'changetheme'=>array(
            'class'=>'ChangeTheme', 'validate'=>1,
            'onlychecklogin'=>1,
            'description'=>AC_CHANGE_USER_THEM
        ),

      /**
       * 显示控制面板的界面
       */
      'default' => array(
         'class'=>'ShowControl', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SHOW_USER_CONTROL
      ),



      /**
       * 注册一个新用户
       */
      'register' => array(
         'class'=>'ShowRegister', 'validate'=>0, 
         'description'=>AC_VIEW_REGISTER_PAGE
      ),

      /**
       * 检查是否已经注册
       */
      'checkregiste' => array('class'=>'Register', 'validate'=> 0,
      'description'=>AC_REGISTER_NEW_USER ),

      /**
       * 登录系统
       */
      'login'  => array('class'=>'Login', 'validate'=>0, 
      'description'=>AC_LOGIN_SYSTEM ),

      /**
       * 显示登录界面
       */
      'showlogin' => array('class'=>'ShowLogin', 'validate'=>0, 
      'description'=>AC_SHOW_LOGIN_PAGE ),

      /**
       * 注销系统
       */
      'logout' => array('class'=>'Logout', 'validate'=>0, 
            'description'=>AC_LOGOUT_SYSTEM),

      /**
       * 查看用户的个人信息
       */
      'view' => array (
         'class'=>'ViewUser', 'validate'=>1,
         'onlychecklogin'=>1, 
         'description'=>AC_VIEW_USER_INFO
      ),

      /**
       * 修改个人的基本信息
       */
      'baseinfo' => array (
         'class' => 'BaseInfo', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_BASE_INFO
      ),

      /**
       * 保存个人的信息
       */
      'savebaseinfo' => array (
         'class' => 'SaveBaseInfo', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SAVE_BASE_INFO
      ),

      /**
       * 显示编辑邮件和密码的界面
       */
      'mailandpasswd' => array (
         'class' => 'ShowEditMailAndPasswd', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SHOW_EDIT_MAIL_PASSWD
      ),

      /**
       * 保存编辑后的邮件和密码
       */
      'savemail' => array (
         'class'=> 'SaveMailAndPasswd', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SAVE_MAIL_AND_PASSWD
      ),

      /**
       * 显示编辑用户图像的界面
       */
      'userhead' => array (
         'class'=>'ShowUserHeader', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SHOW_USER_HEADER
      ),

      /**
       * 保存用户自定义的头像
       */
      'saveuserheader' => array (
         'class'=>'SaveUserHeader', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SAVE_USER_HEADER
      ),

      /**
       * 显示编辑个人签名的interface
       */
      'sign' => array (
         'class'=>'ShowSign', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SHOW_SIGN
      ),

      /**
       * 保存用户的个人签名
       */
      'saveusersign' => array (
         'class'=>'SaveUserSign', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SAVE_USER_SIGN
      ),

      /**
       * 显示用户的论坛选项
       */
      'bbsoption' => array (
         'class' => 'ShowBBSOption', 'validate'=>1,
         'onlychecklogin'=>1,
         'description' => AC_SHOW_BBS_OPTION
      ),

      /**
       * 保存用户的BBS选项
       */
      'savebbsoption' => array (
         'class' => 'SaveBBSOption', 'validate'=>1,
         'onlychecklogin'=>1,
         'description' => AC_SAVE_BBS_OPTION
      ),

      /**
       * 列出用户的列表
       */
      'listuser' => array (
         'class'=>'ListUser', 'validate'=>1,
         'onlychecklogin'=>1,
         'description' => AC_LIST_USER
      ),
   ), 


   /**
    * 站内短消息模块
    */

   'message'=> array (
      /**
       * 显示用户的收件箱
       */
      'receive' => array (
         'class' => 'InBox', 'validate'=>1,
         'onlychecklogin' => 1,
         'description' => AC_SHOW_MSG_INBOX
      ),

      /**
       * 查看用户的短消息
       */
      'show' => array (
         'class'=>'ShowReceiveMsg', 'validate'=>1,
         'onlychecklogin' => 1,
         'description'=>AC_SHOW_MSG_CONTENT
      ),

      /**
       * 删除用户的短消息
       */
      'delmsg' => array (
         'class'=>'DelMsg', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_DEL_MESSAGE
      ),

      /**
       * 显示用户的短消息发件箱
       */
      'send' => array (
         'class' => 'OutBox', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SHOW_USER_OUTBOX
      ),

      /**
       * 显示发送短消息的界面
       */
      'showsend' => array (
         'class'=> 'ShowSend', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SHOW_SEND_INTERFACE
      ),

      /**
       * 发送短消息
       */
      'savesend' => array (
         'class'=>'SaveSend', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SAVE_SEND
      ),

      /**
       * 删除发件箱里保留的短消息
       */
      'delsendmsg' => array (
         'class' => 'DelSendMsg', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_DEL_SEND_MSG
      ),

      /**
       * 显示发件箱里的短消息
       */
      'showsendmsg' => array (
         'class'=>'ShowSendMsg', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SHOW_SEND_MSG
      ),





   ),


   /**
    * 公告模块
    */

   'post' => array (
      'view'=>array(
         'class'=>'ViewPost', 'validate'=>0, 
         'description'=>AC_VIEW_POST
      ),

   ),

   /**
   *  工具模块
   */
   'util' => array (
      'showcheck' => array('class'=>'ShowCheck', 'validate'=>0, 'description'=>
         AC_REQUEST_RECOGONITION_CODE ),
   ),

   /**
    * 图像处理模块
    */
   'image' => array (
      'showimage' => array(
         'class'=>'ShowImage',
         'validate' =>0,
         'description' => AC_SHOW_IMAGE ),

   ),

   /**
    * BBS模块
    */
   'bbs' => array (
      'default' => array('class'=>'ShowBBSIndex', 'validate'=>0, 'description'=>AC_SHOW_BBS_HOME_PAGE),

      /**
       * 查看论坛的版面
       */
      'viewlayout' => array(
         'class'=>'ViewLayout', 'validate'=>0, 
         'description'=>AC_VIEW_BBS_LAYOUT),
      /**
       * 显示新增贴子的界面
       */
      'newtopic' => array(
         'class'=>'ShowNewTopic', 'validate'=>1, 
         'onlychecklogin'=>1, 
         'description'=>AC_SHOW_NEW_TOPIC),

      /**
       * 保存贴子
       */
      'savetopic' => array(
         'class'=>'SaveTopic', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SAVE_TOPIC
      ),

      /**
       * 查看贴子
       */
      'viewtopic' => array(
         'class' => 'ViewTopic', 'validate'=>0,
         'onlychecklogin'=>0,
         'description'=>AC_VIEW_TOPIC
      ),

      /**
       * 显示回复的界面
       */
      'reply' => array(
         'class' => 'ShowReply', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SHOW_TOPIC_REPLY
      ),

      /**
       * 保存新的回复
       */
      'savereply' => array(
         'class' => 'SaveReply', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SAVE_REPLY
      ),

      /**
       * 显示编辑帖子的界面
       */
      'edit' => array (
         'class' => 'ShowEdit', 'validate'=>1,
         'onlychecklogin'=>1,
         'description' => AC_SHOW_EDIT
      ),

      /**
       * 保存编辑的帖子
       */
      'saveedit' => array (
         'class' => 'SaveEdit', 'validate'=>1,
         'onlychecklogin' => 1,
         'description' => AC_SAVE_EDIT
      ),

      /**
       * 关闭当前正在显示的帖子
       */
      'closetopic' => array(
         'class' => 'CloseTopic', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_CLOSE_TOPIC
      ),

      /**
       * 打开当前被关闭的帖子
       */
      'opentopic' => array(
         'class' => 'OpenTopic', 'validate'=>1,
         'onlychecklogin'=>1,
         'description' => AC_OPEN_TOPIC
      ),

      /**
       * 给主题加上认证
       */
      'checktopic' => array (
         'class'=>'CheckTopic', 'validate'=>1,
         'onlychecklogin'=>1,
         'description' => AC_CHECK_TOPIC
      ),


      /**
       * 显示删除的确认界面
       */
      'deltopic' => array(
         'class'=>'ShowDelTopic', 'validate'=>1,
         'onlychecklogin'=>1,
         'description' => AC_SHOW_DEL_TOPIC
      ),

      /**
       * 删除主题
       */
      'suredeltopic' => array(
         'class'=>'DelTopic', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_DEL_TOPIC
      ),

      /**
       * 显示移动主题的界面
       */
      'movetopic' => array (
         'class'=>'ShowMoveTopic', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SHOW_MOVE_TOPIC
      ),

      /**
       * 移动主题到其他的版块
       */
      'suremovetopic' => array(
         'class'=>'MoveTopic', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_MOVE_TOPIC
      ),

      /**
       * 设置主题为精华
       */
      'setbest' => array(
         'class'=>'SetBest', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SET_BEST_TOPIC
      ),

      /**
       * 取消精华设置
       */
      'unsetbest' => array(
         'class' => 'UnSetBest', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_UN_SET_BEST_TOPIC
      ),

      /**
       * 设置主题为置顶
       */
      'settop' => array (
         'class' => 'SetTop', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SET_TOP_TOPIC
      ),

      /**
       * 取消置顶设置
       */
      'unsettop' => array (
         'class' => 'UnSetTop', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_UNSET_TOP_TOPIC
      ),

      /**
       * 解锁用户的回复
       */
      'openreply' => array(
         'class' => 'OpenReply', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_OPEN_REPLY
      ),

      /**
       * 锁定用户的回复
       */
      'closereply' => array(
         'class'=>'CloseReply', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_CLOSE_REPLY
      ),

      /**
       * 查看新帖
       */
      'viewnew' => array (
         'class'=>'ViewNew', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_VIEW_NEW
      ),

      /**
       * 显示论坛的搜索界面
       */
      'search' => array (
         'class'=>'ShowSearch', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SHOW_SEARCH
      ),

      /**
       * 显示搜索结果
       */
      'searchresult' => array (
         'class'=>'SearchResult', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_BBS_SEARCH_RESULT

      ),
   ),



   /**
    * email 接口模块
    * 此模块需要用户在安装和编译php的时候加入对
    * mail函数的支持
    */
   'email' => array (
      
      /*
       * 显示用户发送邮件的界面
       */
      'showsend'=> array (
         'class'=>'ShowSendEmail', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SHOW_SEND_EMAIL
      ), 

      /*
       * 发送邮件
       */
      'send' => array (
         'class'=>'SendEmail', 'validate'=>1,
         'onlychecklogin'=>1,
         'description'=>AC_SEND_EMAIL
      ),


   ),

   /**
    * 帮助接口
    */
   'help' => array (
      'default' => array(
         'class'=>'ShowHelp', 'validate'=>0,
         'description'=>AC_SHOW_HELP
      ),

      'showabout' => array (
         'class'=>'ShowAbout', 'validate'=>0,
         'description'=>AC_SHOW_ABOUT
      ),

      'lawyer' => array (
         'class'=>'ShowLawyer', 'validate'=>0,
         'description'=>AC_SHOW_LAWYER
      )
   ),

);

?>
