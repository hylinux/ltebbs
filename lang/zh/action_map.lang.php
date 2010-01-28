<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   lang/zh/action_map.lang.php
 *
 * 动作配置的语言文件
 *
 * PHP Version 4 or 5
 *
 *  @package:   lang.zh
 *  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 *  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 *  @copyright: http://www.5anet.com
 *  @version:   $Id: action_map.lang.php,v 1.3 2006-09-24 14:38:08 ghw Exp $
 *  @date:      $Date: 2006-09-24 14:38:08 $
 */


#收藏夹模块
define('AC_SHOW_FAVOR_PAGE', '显示添加收藏的界面');
define('AC_ADD_FAVOR', '添加收藏');
define('AC_SHOW_ALL_FAVOR', '查看用户收藏');
define('AC_DELETE_FAVOR', '删除收藏');
define('AC_ADD_FAVOR_DIR', '添加收藏目录');
define('AC_DEL_DIR', '删除收藏目录');


# BBS 模块
define('AC_SHOW_BBS_HOME_PAGE', '查看BBS首页');
define('AC_VIEW_BBS_LAYOUT', '查看论坛');
define('AC_SHOW_NEW_TOPIC', '发表新帖');
define('AC_VIEW_TOPIC', '查看帖子');
define('AC_SHOW_TOPIC_REPLY', '回复帖子');
define('AC_SAVE_REPLY', '保存回复的帖子');
define('AC_SHOW_EDIT', '编辑帖子');
define('AC_SAVE_EDIT', '保存编辑的帖子');
define('AC_CLOSE_TOPIC', '关闭帖子');
define('AC_OPEN_TOPIC', '打开帖子');
define('AC_CHECK_TOPIC', '给主题加上认证');
define('AC_SHOW_DEL_TOPIC', '确认是否删除主题');
define('AC_DEL_TOPIC', '删除主题');
define('AC_SHOW_MOVE_TOPIC', '选择主题移动的目的地');
define('AC_MOVE_TOPIC', '移动主题');
define('AC_SET_BEST_TOPIC', '设置精华主题');
define('AC_UN_SET_BEST_TOPIC', '取消精华设置');
define('AC_SET_TOP_TOPIC', '设置置顶主题');
define('AC_UNSET_TOP_TOPIC', '取消置顶设置');
define('AC_OPEN_REPLY', '解锁用户回复');
define('AC_CLOSE_REPLY', '锁定用户回复');
define('AC_SHOW_SEARCH', '显示搜索界面');
define('AC_BBS_SEARCH_RESULT', '论坛搜索结果');


# 工具模块
define('AC_REQUEST_RECOGONITION_CODE', '请求验证码');

# 用户模块
define('AC_REGISTER_NEW_USER', '注册新用户');
define('AC_VIEW_REGISTER_PAGE', '查看注册页面');
define('AC_LOGIN_SYSTEM', '登录系统');
define('AC_SHOW_LOGIN_PAGE', '查看登录页面');
define('AC_LOGOUT_SYSTEM', '退出系统');
define('AC_VIEW_USER_INFO', '查看用户信息');
define('AC_SHOW_USER_CONTROL', '登录个人控制面板');
define('AC_BASE_INFO', '查看用户个人信息');
define('AC_SAVE_BASE_INFO', '保存个人的用户信息');
define('AC_SHOW_EDIT_MAIL_PASSWD', '显示编辑个人密码和邮件的界面');
define('AC_SAVE_MAIL_AND_PASSWD', '保存编辑后的个人密码和邮箱');
define('AC_SHOW_USER_HEADER', '显示用户的头像');
define('AC_SAVE_USER_HEADER', '保存用户的头像');
define('AC_SHOW_SIGN', '显示个人签名编辑');
define('AC_SAVE_USER_SIGN', '保存个人签名');
define('AC_SHOW_BBS_OPTION', '显示用户的论坛选项');
define('AC_SAVE_BBS_OPTION', '保存用户的个人论坛选项');
define('AC_VIEW_NEW', '查看新帖');
define('AC_LIST_USER', '查看用户列表');
define('AC_CHANGE_USER_THEM', '更改用户风格');
define('AC_CHECK_USERNAME_WHETHER_EXISTS', '检查用户是否存在');
define('AC_CHECK_USEREMAIL_WHETHER_EXISTS', '检查用户邮件是否存在');
define('AC_CHECK_CODE_IS_RIGHT', '检查验证码是否正确');
define('AC_RECOVER_PASSWORD', '取回密码');
define('AC_SHOW_EDIT_INFO', '编辑用户资料');
define('AC_CHECK_USER_PASSWORD', '检验用户密码');
define('AC_SAVE_USER_INFO', '保存用户信息');
define('AC_VIEW_SELF_INFO', '查看用户自己基本资料');
define('AC_LIST_MY_TOPIC', '查看用户自己发表的主题');
define('AC_LIST_MY_REPLY', '查看用户自己参与发表的主题');



#短消息模块
define('AC_SHOW_MSG_INBOX', '显示用户短消息收件箱');
define('AC_SHOW_MSG_CONTENT', '显示用户短消息内容');
define('AC_DEL_MESSAGE', '删除短消息');
define('AC_SHOW_USER_OUTBOX', '显示用户的发件箱');
define('AC_SHOW_SEND_INTERFACE', '发送短消息');
define('AC_SAVE_SEND', '发送短消息');
define('AC_DEL_SEND_MSG', '删除发件箱里保存的短消息');
define('AC_SHOW_SEND_MSG', '查看发件箱里的短消息');

#email模块
define('AC_SHOW_SEND_EMAIL', '写邮件');
define('AC_SEND_EMAIL', '发送邮件');

#帮助模块
define('AC_SHOW_HELP', '查看帮助');


# 上传附件模块
define('AC_VIEW_TEMP_ATTACH', '查看零时上传的附件');
define('AC_SAVE_TOPIC', '保存新主题');

# 图像处理的模块
define('AC_SHOW_IMAGE', '显示图片');

# 系统公告模块
define('AC_VIEW_POST', '查看公告');

?>
