
DROP TABLE IF EXISTS `base_user_info`;
CREATE TABLE `base_user_info` (
  `id` int(11) NOT NULL auto_increment,
  `user_name` varchar(40) NOT NULL default '',
  `user_password` varchar(120) NOT NULL default '',
  `user_gender` enum('male','female') default 'male',
  `user_birthday` date default NULL,
  `public_birthday` tinyint(4) default '1',
  `user_email` varchar(85) NOT NULL default '',
  `public_user_email` tinyint(4) default '1',
  `user_website` varchar(60) default NULL,
  `public_website` tinyint(4) default '1',
  `user_icq` varchar(35) default NULL,
  `public_user_icq` tinyint(4) default '1',
  `user_AIM` varchar(35) default NULL,
  `public_user_AIM` tinyint(4) default '1',
  `user_msn` varchar(45) default NULL,
  `public_user_msn` tinyint(4) default '1',
  `user_yahoo` varchar(45) default NULL,
  `public_user_yahoo` tinyint(4) default '1',
  `user_skype` varchar(45) default NULL,
  `public_user_skype` tinyint(4) default '1',
  `user_qq` varchar(35) default NULL,
  `public_user_qq` tinyint(4) default '1',
  `user_hometown` varchar(80) default NULL,
  `user_favor` varchar(150) default NULL,
  `user_sign` varchar(250) default NULL,
  `register_date` datetime default NULL,
  `group_dep` int(11) default '4',
  `user_header` int(11) default '1',
  `status` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_user_name` (`user_name`),
  UNIQUE KEY `idx_user_email` (`user_email`),
  KEY `idx_register_date` (`register_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `base_user_info` WRITE;
UNLOCK TABLES;

DROP TABLE IF EXISTS `bbs_layout`;
CREATE TABLE `bbs_layout` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(50) NOT NULL default '',
  `description` text default '',
  `parent_id` int(11) default '0',
  `status` tinyint(4) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


LOCK TABLES `bbs_layout` WRITE;
UNLOCK TABLES;

DROP TABLE IF EXISTS `bbs_layout_manager`;
CREATE TABLE `bbs_layout_manager` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `layout_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_user_layout_id` (`user_id`,`layout_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



LOCK TABLES `bbs_layout_manager` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `bbs_layout_online_user`;
CREATE TABLE `bbs_layout_online_user` (
  `id` int(11) NOT NULL auto_increment,
  `layout_id` int(11) NOT NULL default '0',
  `user_name` varchar(40) NOT NULL default '',
  `session_id` varchar(55) NOT NULL default '',
  `access_time` int(11) default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_layout_session` (`layout_id`,`session_id`),
  KEY `idx_user_name` (`user_name`),
  KEY `idx_access_time` (`access_time`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;



LOCK TABLES `bbs_layout_online_user` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `bbs_reply`;
CREATE TABLE `bbs_reply` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(80) default NULL,
  `author` varchar(40) NOT NULL default '',
  `content` text NOT NULL,
  `reply_status` tinyint(4) default '0',
  `post_ip` varchar(45) default NULL,
  `is_edit` tinyint(4) default '0',
  `edit_user` varchar(40) default NULL,
  `subject_id` int(11) NOT NULL default '0',
  `layout_id` int(11) NOT NULL default '0',
  `post_date` int(11) default '0',
  `edit_time` datetime default NULL,
  `express` varchar(25) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_author` (`author`),
  KEY `idx_title` (`title`),
  KEY `idx_reply_status` (`reply_status`),
  KEY `idx_subject_id` (`subject_id`),
  KEY `idx_layout_id` (`layout_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


LOCK TABLES `bbs_reply` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `bbs_reply_attach`;
CREATE TABLE `bbs_reply_attach` (
  `id` int(11) NOT NULL auto_increment,
  `reply_id` int(11) NOT NULL default '0',
  `file_type` varchar(15) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_subject_id` (`reply_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `bbs_reply_attach` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `bbs_subject`;
CREATE TABLE `bbs_subject` (
  `id` int(11) NOT NULL auto_increment,
  `layout_id` int(11) NOT NULL default '0',
  `title` varchar(210) NOT NULL default '',
  `author` varchar(40) NOT NULL default '',
  `content` text NOT NULL,
  `is_best` tinyint(4) default '0',
  `is_top` tinyint(4) default '0',
  `subject_status` tinyint(4) default '0',
  `post_ip` varchar(45) default NULL,
  `is_edit` tinyint(4) default '0',
  `edit_user` varchar(40) default NULL,
  `edit_time` datetime default NULL,
  `click_number` int(11) default '0',
  `post_date` int(11) NOT NULL default '0',
  `last_access_date` int(11) NOT NULL default '0',
  `reply_number` int(11) default '0',
  `express` varchar(25) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_title` (`title`),
  KEY `idx_author` (`author`),
  KEY `idx_is_top` (`is_top`),
  KEY `idx_is_best` (`is_best`),
  KEY `idx_status` (`subject_status`),
  KEY `idx_layout_id` (`layout_id`),
  FULLTEXT KEY `idx_content` (`content`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


LOCK TABLES `bbs_subject` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `bbs_subject_attach`;
CREATE TABLE `bbs_subject_attach` (
  `id` int(11) NOT NULL auto_increment,
  `subject_id` int(11) NOT NULL default '0',
  `file_type` varchar(15) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_subject_id` (`subject_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `bbs_subject_attach` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `black_list_by_ip`;
CREATE TABLE `black_list_by_ip` (
  `id` int(11) NOT NULL auto_increment,
  `ip` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `idx_ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



LOCK TABLES `black_list_by_ip` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `black_list_by_user`;
CREATE TABLE `black_list_by_user` (
  `id` int(11) NOT NULL auto_increment,
  `user_name` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `idx_user_name` (`user_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `black_list_by_user` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `max_online_user`;
CREATE TABLE `max_online_user` (
  `id` int(11) NOT NULL auto_increment,
  `online` bigint(20) default '0',
  `online_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `message_inbox`;
CREATE TABLE `message_inbox` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `send_user_id` int(11) NOT NULL default '0',
  `title` varchar(150) NOT NULL default '',
  `receive_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `content` text NOT NULL,
  `is_read` tinyint(4) default '0',
  PRIMARY KEY  (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_send_user_id` (`send_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `message_inbox` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `message_outbox`;
CREATE TABLE `message_outbox` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `receive_user_id` int(11) NOT NULL default '0',
  `title` varchar(150) NOT NULL default '',
  `send_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `content` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_receive_user_id` (`receive_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `message_outbox` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `new_user_group`;
CREATE TABLE `new_user_group` (
  `user_grp` int(11) default '4'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



LOCK TABLES `new_user_group` WRITE;
INSERT INTO `new_user_group` VALUES (4);
UNLOCK TABLES;

DROP TABLE IF EXISTS `online_user`;
CREATE TABLE `online_user` (
  `id` int(11) NOT NULL auto_increment,
  `user_name` varchar(40) default NULL,
  `user_ip` varchar(45) NOT NULL default '',
  `connect_time` int(11) default '0',
  `access_time` int(11) default '0',
  `current_status` varchar(80) default NULL,
  `session_id` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_session_id` (`session_id`),
  KEY `idx_access_time` (`access_time`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `site_post`;
CREATE TABLE `site_post` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(50) NOT NULL default '',
  `content` text NOT NULL default '',
  `begin_date` int(11) NOT NULL default '0',
  `expires` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



LOCK TABLES `site_post` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `sys_actions`;
CREATE TABLE `sys_actions` (
  `id` int(11) NOT NULL auto_increment,
  `module_id` int(11) NOT NULL default '0',
  `action_name` varchar(40) NOT NULL default '',
  `file_name` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_module_action` (`module_id`,`action_name`),
  KEY `idx_action_name` (`action_name`),
  KEY `idx_module_id` (`module_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


LOCK TABLES `sys_actions` WRITE;
INSERT INTO `sys_actions` VALUES (2,1,'viewlayout','ViewLayout.class.php');
UNLOCK TABLES;


DROP TABLE IF EXISTS `sys_admin`;
CREATE TABLE `sys_admin` (
  `id` int(11) NOT NULL auto_increment,
  `user_name` varchar(45) NOT NULL default '',
  `user_passwd` varchar(150) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_user` (`user_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;



LOCK TABLES `sys_admin` WRITE;
INSERT INTO `sys_admin` VALUES (1,'admin','e10adc3949ba59abbe56e057f20f883e');
UNLOCK TABLES;


DROP TABLE IF EXISTS `sys_group`;
CREATE TABLE `sys_group` (
  `id` int(11) NOT NULL auto_increment,
  `group_name` varchar(40) NOT NULL default '',
  `description` varchar(150) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_group_name` (`group_name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

LOCK TABLES `sys_group` WRITE;
INSERT INTO `sys_group` VALUES (1,'系统管理员','系统管理员，可以管理系统中的所有的信息'),(2,'BBS超级版主','BBS超级版主可以管理论坛中一切的版务'),(3,'BBS版主','管理一个或多个版块的版务'),(4,'普通用户','普通注册用户');
UNLOCK TABLES;


DROP TABLE IF EXISTS `sys_group_privileges`;
CREATE TABLE `sys_group_privileges` (
  `id` int(11) NOT NULL auto_increment,
  `groupid` int(11) NOT NULL default '0',
  `actionid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_grp_action` (`groupid`,`actionid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



LOCK TABLES `sys_group_privileges` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `sys_modules`;
CREATE TABLE `sys_modules` (
  `id` int(11) NOT NULL auto_increment,
  `module_name` varchar(40) NOT NULL default '',
  `author` varchar(80) NOT NULL default '',
  `version` varchar(10) default NULL,
  `description` varchar(200) default NULL,
  `status` tinyint(4) default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_module_name` (`module_name`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;


LOCK TABLES `sys_modules` WRITE;
INSERT INTO `sys_modules` VALUES (1,'bbs','Mike.G','0.0.1','one forum module for that',0),(2,'user','Mike.G','0.0.1','manager the user for that',0),(3,'email','Mike.G','0.0.1','show the UI for send email and send email to someboday',0),(4,'image','Mike.G','0.0.1','show and generate one image file',0),(5,'message','Mike.G','0.0.1','site messages',0),(6,'post','Mike.G','0.0.1','site post',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `sys_user_privileges`;
CREATE TABLE `sys_user_privileges` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `actionid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_user_action` (`userid`,`actionid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



LOCK TABLES `sys_user_privileges` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `total_count`;
CREATE TABLE `total_count` (
  `id` int(11) NOT NULL auto_increment,
  `total_count` bigint(20) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


LOCK TABLES `total_count` WRITE;
INSERT INTO `total_count` VALUES (1,1385074);
UNLOCK TABLES;


DROP TABLE IF EXISTS `user_last_time_logout`;
CREATE TABLE `user_last_time_logout` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `last_time` int(11) NOT NULL default '0',
  `last_action` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


LOCK TABLES `user_last_time_logout` WRITE;
UNLOCK TABLES;

DROP TABLE IF EXISTS `user_setting`;
CREATE TABLE `user_setting` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `user_lang` varchar(3) NULL default '',
  `user_theme` varchar(20) NULL default '',
  `user_local_time` varchar(8) default NULL,
  `user_whether_receive_email` tinyint(4) NULL default '1',
  `receive_system_message` tinyint(4) NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_user_name` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `user_setting` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `web_count`;
CREATE TABLE `web_count` (
  `id` int(11) NOT NULL auto_increment,
  `count_date` date NOT NULL default '0000-00-00',
  `access_number` bigint(20) default '1',
  PRIMARY KEY  (`id`),
  KEY `idx_date` (`count_date`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


LOCK TABLES `web_count` WRITE;
UNLOCK TABLES;

ALTER TABLE `base_user_info` CHANGE `user_gender` `user_gender` ENUM( 'keep', 'male', 'female' ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'male';

ALTER TABLE `base_user_info` CHANGE `user_gender` `user_gender` ENUM( 'keep', 'male', 'female' ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'keep';

ALTER TABLE `base_user_info` CHANGE `user_sign` `user_sign` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL  ;


DROP TABLE IF EXISTS `favor`;

CREATE TABLE `favor` (
    `id` INT NOT NULL AUTO_INCREMENT ,
    `user_id` INT NOT NULL ,
    `dir_id`  INT NOT NULL,
    `type` ENUM( 'topic' ) NOT NULL DEFAULT 'topic',
    `favor_id` INT NOT NULL ,
    `add_date` DATETIME NOT NULL ,
    PRIMARY KEY ( `id` ) ,
    INDEX ( `user_id` ),
    unique ( `user_id`,`type`,`favor_id`),
    INDEX (`dir_id`)
) ENGINE = MyISAM COMMENT = '收藏夹表';

DROP TABLE IF EXISTS `favor_dir`;

CREATE TABLE `favor_dir` (
    `id` INT NOT NULL AUTO_INCREMENT ,
    `user_id` INT NOT NULL ,
    `dir_name` VARCHAR( 50 ) NOT NULL ,
    PRIMARY KEY ( `id` ) ,
    INDEX ( `user_id` )
) ENGINE = MyISAM;



