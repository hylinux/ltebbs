<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  Project Name:  5anet
*  File Name   :  config/config.inc.php
*
*  The main configure file
*
*  PHP Version 4 and 5
*
*  @package:      config
*  @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
*  @copyright: http://www.5anet.com
*  @version:   $Id: config.inc.php,v 1.4 2006-09-24 14:38:08 ghw Exp $
*  @date:      @Date:$
*/

// the database information
$db_type = '%DB_TYPE%';
$db_host = '%DB_HOST%';
$db_name = '%DB_NAME%';
$db_user = '%DB_USER%';
$db_pass = '%DB_PASS%';
$use_persist = %DB_PERSIST%;
$use_db_debug = %DB_DEBUG%;


//the global url and root path setting
$global_config_web_domain = '%WEB_DOMAIN%';
$global_config_root_url = '%ACCESS_URL%';
$global_config_root_path = '%ACCESS_PATH%';
$global_config_cache_path = '%CACHE_PATH%';

# the system default timezone set
$system_default_timezone_set = 'Asia/Shanghai';

# system admin email
$system_admin_email = '%SYSTEM_ADMIN_EMAIL%';

# fckeditor  setting
$system_fckeditor_basepath = "%FCKEDITOR_BASEPATH%";
$system_fckeditor_uploaddir = "%FCKEDITOR_UPLOADDIR%";


?>
