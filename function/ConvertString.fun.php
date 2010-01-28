<?php
// vim: set expandtab tabstop=3 softtabstop=3 shiftwidth=3 foldcolumn=1 foldmethod=marker:
/**
*  转换字符串
*  
*  PHP Version 4, 5
*  
*  @package:   functions
*  @authro:    黄叶<hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
*  @copyright: 阿叶的小屋 2003-2005 The A_Ye's Little House
*  @version:   $Id: ConvertString.fun.php,v 1.2 2006-09-24 14:38:08 ghw Exp $
*  @date:      $Date: 2006-09-24 14:38:08 $
*/

/**
*  @param NULL
*  @return boolean
*  @access public
*/
//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/ConvertString.fun.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/ConvertString.fun.lang.php';
}


function ConvertString($str1, $http_url, $image_path)
{

   $main = '';
   if ( get_magic_quotes_gpc() ) {
      $main = stripslashes($str1);     
   } else {
      $main = $str1;  
   }


   //使用正则表达式来处理事务
   //转化加粗
   $searcharray[] = "/(\[)(b)(])(.*)(\[\/b\])/siU";
   $replacearray[] = "<b>\\4</b>";

   //转化斜体
   $searcharray[] = "/(\[)(i)(])(.*)(\[\/i\])/siU";
   $replacearray[] = "<i>\\4</i>";

   //转化邮箱
   $searcharray[] = "/(\[)(email)(])(.*)(\[\/email\])/siU";
   $replacearray[] = "<a href=\"mailto:\\4\">\\4</a>";

   $searcharray[] = "/(\[)(email)(=)(['\"]?)([^\"']*)(\\4])(.*)(\[\/email\])/siU";
   $replacearray[] = "<a href=\"mailto:\\5\">\\7</a>";

   //转化文字大小  
   $searcharray[] = "/(\[)(size)(=)(['\"]?)([^\"']*)(\\4])(.*)(\[\/size\])/siU";
   $replacearray[] = "<font size=\\5>\\7</font>";

   //引用文字
   $searcharray[] = "/(\[)(quote)(])(.*)(\[\/quote\])/siU";
   $replacearray[] = "<table width=450  cellpadding=1 cellspacing=1 border=0 bgcolor=\"#336699\"><tr><td align=left valign=top ".
      " bgcolor=\"#f6f7f1\">".
      "<blockquote><smallfont><font color=\"#336699\">引用：</font></smallfont>\\4</blockquote></td></tr></table>";


   //转换代码
   $searcharray[] = "/(\[)(code)(])(.*)(\[\/code\])/siU";
   $replacearray[] = "<table width=450  cellpadding=1 cellspacing=1 border=0 bgcolor=\"#336699\"><tr><td align=left valign=top ".
      " bgcolor=\"#f6f7f1\">".
      "<blockquote>\\4</blockquote></td></tr></table>";

   // 转换下划线
   $searcharray[]="/(\[)(u)(])(.*)(\[\/u\])/siU";
   $replacearray[]="<u>\\4</u>";

   // 转化字体颜色
   $searcharray[]="/(\[)(color)(=)(['\"]?)([^\"']*)(\\4])(.*)(\[\/color\])/siU";
   $replacearray[]="<font color=\"\\5\">\\7</font>";

   // 转化图形
   $searcharray[]="/(\[)(img)(])(\r\n)*([^\"]*)(\[\/img\])/siU";
   $replacearray[]="<table border=0><tr><td>".
      "<a href=\"\\5\" target=_blank title=\"".CLICK_FOR_VIEW_IMAGE."\">".
      "<img src=\"".$http_url."?module=image&action=showimage&width=320&height=300&image=\\5\" ".
      "  border=\"0\" alt=\"".CLICK_FOR_VIEW_IMAGE."\"></a></td></tr>".
      "</table>";

   $searcharray[]="/(\[)(myimg)(])(\r\n)*([^\"]*)(\[\/myimg\])/siU";
   $replacearray[]="<table border=0><tr><td>".
      "<a href=\"\\5\" target=_blank title=\"".CLICK_FOR_VIEW_IMAGE."\">".
      "<img src=\"".$http_url."?module=image&action=showimage&width=320&height=300&image=\\5\" ".
      "  border=\"0\" alt=\"".CLICK_FOR_VIEW_IMAGE."\"></a></td></tr>".
      "</table>";




   // 转化超连接
   $searcharray[]="/(\[)(url)(=)(['\"]?)(www\.)([^\"']*)(\\4)(.*)(\[\/url\])/siU";
   $replacearray[]="<a href=\"http://www.\\6\" target=_blank>\\8</a>";

   $searcharray[]="/(\[)(url)(=)(['\"]?)([^\"']*)(\\4])(.*)(\[\/url\])/siU";
   $replacearray[]="<a href=\"\\5\" target=_blank>\\7</a>";

   $searcharray[]="/(\[)(url)(])(www\.)(.*)(\[\/url\])/siU";
   $replacearray[]="<a href=\"http://www.\\5\" target=_blank>\\5</a>";

   $searcharray[]="/(\[)(url)(])(.*)(\[\/url\])/siU";
   $replacearray[]="<a href=\"\\4\" target=_blank>\\4</a>";

   // 转化字体类型
   $searcharray[]="/(\[)(font)(=)(.*)(])(.*)(\[\/font\])/siU";
   $replacearray[]="<font face=\"\\4\">\\6</font>";

   //转化flash
   $searcharray[] = "/(\[)(flash)(])(.*)(\[\/flash\])/siU";
   $replacearray[] = "<table cellpadding=0 cellspacing=0 align=center width=470 ".
      " height=410 border=0>\n<tr><td valign=top align=center>\n<object height=\"400\" ".
      " width=\"468\"  codebase=\"http://download.macromedia.com/pub/shockwave/cabs".
      "/flash/swflash.cab#version=5,0,0,0\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\">".
      "<param value=\"\\4\" name=\"movie\"><param value=\"high\" name=\"quality\">".
      "<embed height=\"400\" width=\"468\" ".
      " type=\"application/x-shockwave-flash\" ".
      " pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?".
      "P1_Prod_Version=ShockwaveFlash\" quality=\"high\" src=\"\\4\"> ".
      "</object></td></tr><tr><td align=center><a href=\"\\4\">".
      CLICK_FOR_BIG_VIEW."</a></td></tr></table>\n";

   $searcharray[] = "/(\[)(myflash)(])(.*)(\[\/myflash\])/siU";
   $replacearray[] = "<table cellpadding=0 cellspacing=0 align=center width=470 ".
      " height=410 border=0>\n<tr><td valign=top align=center>\n<object height=\"400\" ".
      " width=\"468\"  codebase=\"http://download.macromedia.com/pub/shockwave/cabs".
      "/flash/swflash.cab#version=5,0,0,0\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\">".
      "<param value=\"\\4\" name=\"movie\"><param value=\"high\" name=\"quality\">".
      "<embed height=\"400\" width=\"468\" ".
      " type=\"application/x-shockwave-flash\" ".
      " pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?".
      "P1_Prod_Version=ShockwaveFlash\" quality=\"high\" src=\"\\4\"> ".
      "</object></td></tr><tr><td align=center><a href=\"\\4\">".
      CLICK_FOR_BIG_VIEW."</a></td></tr></table>\n";


   //转换图标
   $searcharray[] = "/:\)/siU";
   $replacearray[] = "<img src=\"".$image_path."smile.gif\" border=\"0\">";

   $searcharray[] = "/:rolleyes:/siU";
   $replacearray[] = "<img src=\"".$image_path."rolleyes.gif\" border=\"0\">";

   $searcharray[] = "/:thank:/siU";
   $replacearray[] = "<img src=\"".$image_path."thank.gif\" border=\"0\">";

   $searcharray[] = "/:comp:/siU";
   $replacearray[] = "<img src=\"".$image_path."comp.gif\" border=\"0\">";

   $searcharray[] = "/:cool:/siU";
   $replacearray[] = "<img src=\"".$image_path."cool.gif\" border=\"0\">";

   $searcharray[] = "/:help:/siU";
   $replacearray[] = "<img src=\"".$image_path."help.gif\" border=\"0\">";

   $searcharray[] = "/:beat:/siU";
   $replacearray[] = "<img src=\"".$image_path."beat.gif\" border=\"0\">";

   $searcharray[] = "/:p/siU";
   $replacearray[] = "<img src=\"".$image_path."tongue.gif\" border=\"0\">";

   $searcharray[] = "/:!!/siU";
   $replacearray[] = "<img src=\"".$image_path."bigscream.gif\" border=\"0\">";

   $searcharray[] = "/:sleep:/siU";
   $replacearray[] = "<img src=\"".$image_path."sleep.gif\" border=\"0\">";

   $searcharray[] = "/;\)/siU";
   $replacearray[] = "<img src=\"".$image_path."wink.gif\" border=\"0\">";

   $searcharray[] = "/:%/siU";
   $replacearray[] = "<img src=\"".$image_path."biglove.gif\" border=\"0\">";

   $searcharray[] = "/:ask:/siU";
   $replacearray[] = "<img src=\"".$image_path."ask.gif\" border=\"0\">";

   $searcharray[] = "/:D/siU";
   $replacearray[] = "<img src=\"".$image_path."biggrin.gif\" border=\"0\">";

   $searcharray[] = "/:confused:/siU";
   $replacearray[] = "<img src=\"".$image_path."confused.gif\" border=\"0\">";

   $searcharray[] = "/:hungry:/siU";
   $replacearray[] = "<img src=\"".$image_path."hungry.gif\" border=\"0\">";

   $searcharray[] = "/:o/siU";
   $replacearray[] = "<img src=\"".$image_path."redface.gif\" border=\"0\">";

   $searcharray[] = "/:eek:/siU";
   $replacearray[] = "<img src=\"".$image_path."eek.gif\" border=\"0\">";

   $searcharray[] = "/:2cool:/siU";
   $replacearray[] = "<img src=\"".$image_path."cool2.gif\" border=\"0\">";

   $searcharray[] = "/:\(/siU";
   $replacearray[] = "<img src=\"".$image_path."frown.gif\" border=\"0\">";

   $searcharray[] = "/:mad:/siU";
   $replacearray[] = "<img src=\"".$image_path."mad.gif\" border=\"0\">";

   $searcharray[] = "/:sorry:/siU";
   $replacearray[] = "<img src=\"".$image_path."sorry.gif\" border=\"0\">";

   //代码高亮
   /**
   $searcharray1="/(\[)(code)(])(\r\n)*(.*)(\[\/code\])/siU";
   $replacearray1="<?php\\5?>";
    */

//   $main = preg_replace($searcharray, $replacearray, $main);

//   $main = preg_replace($searcharray1, $replacearray1, $main);
//   $main = highlight_string($main, true); 


//   $main = nl2br(htmlspecialchars($main));

   $main = nl2br($main);

   $main = preg_replace($searcharray, $replacearray, $main);

   return $main;
}

function ConvertString1($str1, $http_url, $image_path)
{

   $main = $str1;


   //使用正则表达式来处理事务
   //转化加粗
   $searcharray[] = "/(\[)(b)(])(.*)(\[\/b\])/siU";
   $replacearray[] = "<b>\\4</b>";

   //转化斜体
   $searcharray[] = "/(\[)(i)(])(.*)(\[\/i\])/siU";
   $replacearray[] = "<i>\\4</i>";

   //转化邮箱
   $searcharray[] = "/(\[)(email)(])(.*)(\[\/email\])/siU";
   $replacearray[] = "<a href=\"mailto:\\4\">\\4</a>";

   $searcharray[] = "/(\[)(email)(=)(['\"]?)([^\"']*)(\\4])(.*)(\[\/email\])/siU";
   $replacearray[] = "<a href=\"mailto:\\5\">\\7</a>";

   //转化文字大小  
   $searcharray[] = "/(\[)(size)(=)(['\"]?)([^\"']*)(\\4])(.*)(\[\/size\])/siU";
   $replacearray[] = "<font size=\\5>\\7</font>";

   //引用文字
   $searcharray[] = "/(\[)(quote)(])(.*)(\[\/quote\])/siU";
   $replacearray[] = "<table width=450  cellpadding=1 cellspacing=1 border=0 bgcolor=\"#336699\"><tr><td align=left valign=top ".
      " bgcolor=\"#f6f7f1\">".
      "<blockquote><smallfont><font color=\"#336699\">引用：</font></smallfont>\\4</blockquote></td></tr></table>";


   //转换代码
   $searcharray[] = "/(\[)(code)(])(.*)(\[\/code\])/siU";
   $replacearray[] = "<table width=450  cellpadding=1 cellspacing=1 border=0 bgcolor=\"#336699\"><tr><td align=left valign=top ".
      " bgcolor=\"#f6f7f1\">".
      "<blockquote>\\4</blockquote></td></tr></table>";

   // 转换下划线
   $searcharray[]="/(\[)(u)(])(.*)(\[\/u\])/siU";
   $replacearray[]="<u>\\4</u>";

   // 转化字体颜色
   $searcharray[]="/(\[)(color)(=)(['\"]?)([^\"']*)(\\4])(.*)(\[\/color\])/siU";
   $replacearray[]="<font color=\"\\5\">\\7</font>";

   // 转化图形
   $searcharray[]="/(\[)(img)(])(\r\n)*([^\"]*)(\[\/img\])/siU";
   $replacearray[]="<table border=0><tr><td>".
      "<a href=\"\\5\" target=_blank title=\"".CLICK_FOR_VIEW_IMAGE."\">".
      "<img src=\"".$http_url."?module=image&action=showimage&width=320&height=300&image=\\5\" ".
      "  border=\"0\" alt=\"".CLICK_FOR_VIEW_IMAGE."\"></a></td></tr>".
      "</table>";

   $searcharray[]="/(\[)(myimg)(])(\r\n)*([^\"]*)(\[\/myimg\])/siU";
   $replacearray[]="<table border=0><tr><td>".
      "<a href=\"\\5\" target=_blank title=\"".CLICK_FOR_VIEW_IMAGE."\">".
      "<img src=\"".$http_url."?module=image&action=showimage&width=320&height=300&image=\\5\" ".
      "  border=\"0\" alt=\"".CLICK_FOR_VIEW_IMAGE."\"></a></td></tr>".
      "</table>";




   // 转化超连接
   $searcharray[]="/(\[)(url)(=)(['\"]?)(www\.)([^\"']*)(\\4)(.*)(\[\/url\])/siU";
   $replacearray[]="<a href=\"http://www.\\6\" target=_blank>\\8</a>";

   $searcharray[]="/(\[)(url)(=)(['\"]?)([^\"']*)(\\4])(.*)(\[\/url\])/siU";
   $replacearray[]="<a href=\"\\5\" target=_blank>\\7</a>";

   $searcharray[]="/(\[)(url)(])(www\.)(.*)(\[\/url\])/siU";
   $replacearray[]="<a href=\"http://www.\\5\" target=_blank>\\5</a>";

   $searcharray[]="/(\[)(url)(])(.*)(\[\/url\])/siU";
   $replacearray[]="<a href=\"\\4\" target=_blank>\\4</a>";

   // 转化字体类型
   $searcharray[]="/(\[)(font)(=)(.*)(])(.*)(\[\/font\])/siU";
   $replacearray[]="<font face=\"\\4\">\\6</font>";

   //转化flash
   $searcharray[] = "/(\[)(flash)(])(.*)(\[\/flash\])/siU";
   $replacearray[] = "<table cellpadding=0 cellspacing=0 align=center width=470 ".
      " height=410 border=0>\n<tr><td valign=top align=center>\n<object height=\"400\" ".
      " width=\"468\"  codebase=\"http://download.macromedia.com/pub/shockwave/cabs".
      "/flash/swflash.cab#version=5,0,0,0\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\">".
      "<param value=\"\\4\" name=\"movie\"><param value=\"high\" name=\"quality\">".
      "<embed height=\"400\" width=\"468\" ".
      " type=\"application/x-shockwave-flash\" ".
      " pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?".
      "P1_Prod_Version=ShockwaveFlash\" quality=\"high\" src=\"\\4\"> ".
      "</object></td></tr><tr><td align=center><a href=\"\\4\">".
      CLICK_FOR_BIG_VIEW."</a></td></tr></table>\n";

   $searcharray[] = "/(\[)(myflash)(])(.*)(\[\/myflash\])/siU";
   $replacearray[] = "<table cellpadding=0 cellspacing=0 align=center width=470 ".
      " height=410 border=0>\n<tr><td valign=top align=center>\n<object height=\"400\" ".
      " width=\"468\"  codebase=\"http://download.macromedia.com/pub/shockwave/cabs".
      "/flash/swflash.cab#version=5,0,0,0\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\">".
      "<param value=\"\\4\" name=\"movie\"><param value=\"high\" name=\"quality\">".
      "<embed height=\"400\" width=\"468\" ".
      " type=\"application/x-shockwave-flash\" ".
      " pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?".
      "P1_Prod_Version=ShockwaveFlash\" quality=\"high\" src=\"\\4\"> ".
      "</object></td></tr><tr><td align=center><a href=\"\\4\">".
      CLICK_FOR_BIG_VIEW."</a></td></tr></table>\n";


   //转换图标
   $searcharray[] = "/:\)/siU";
   $replacearray[] = "<img src=\"".$image_path."smile.gif\" border=\"0\">";

   $searcharray[] = "/:rolleyes:/siU";
   $replacearray[] = "<img src=\"".$image_path."rolleyes.gif\" border=\"0\">";

   $searcharray[] = "/:thank:/siU";
   $replacearray[] = "<img src=\"".$image_path."thank.gif\" border=\"0\">";

   $searcharray[] = "/:comp:/siU";
   $replacearray[] = "<img src=\"".$image_path."comp.gif\" border=\"0\">";

   $searcharray[] = "/:cool:/siU";
   $replacearray[] = "<img src=\"".$image_path."cool.gif\" border=\"0\">";

   $searcharray[] = "/:help:/siU";
   $replacearray[] = "<img src=\"".$image_path."help.gif\" border=\"0\">";

   $searcharray[] = "/:beat:/siU";
   $replacearray[] = "<img src=\"".$image_path."beat.gif\" border=\"0\">";

   $searcharray[] = "/:p/siU";
   $replacearray[] = "<img src=\"".$image_path."tongue.gif\" border=\"0\">";

   $searcharray[] = "/:!!/siU";
   $replacearray[] = "<img src=\"".$image_path."bigscream.gif\" border=\"0\">";

   $searcharray[] = "/:sleep:/siU";
   $replacearray[] = "<img src=\"".$image_path."sleep.gif\" border=\"0\">";

   $searcharray[] = "/;\)/siU";
   $replacearray[] = "<img src=\"".$image_path."wink.gif\" border=\"0\">";

   $searcharray[] = "/:%/siU";
   $replacearray[] = "<img src=\"".$image_path."biglove.gif\" border=\"0\">";

   $searcharray[] = "/:ask:/siU";
   $replacearray[] = "<img src=\"".$image_path."ask.gif\" border=\"0\">";

   $searcharray[] = "/:D/siU";
   $replacearray[] = "<img src=\"".$image_path."biggrin.gif\" border=\"0\">";

   $searcharray[] = "/:confused:/siU";
   $replacearray[] = "<img src=\"".$image_path."confused.gif\" border=\"0\">";

   $searcharray[] = "/:hungry:/siU";
   $replacearray[] = "<img src=\"".$image_path."hungry.gif\" border=\"0\">";

   $searcharray[] = "/:o/siU";
   $replacearray[] = "<img src=\"".$image_path."redface.gif\" border=\"0\">";

   $searcharray[] = "/:eek:/siU";
   $replacearray[] = "<img src=\"".$image_path."eek.gif\" border=\"0\">";

   $searcharray[] = "/:2cool:/siU";
   $replacearray[] = "<img src=\"".$image_path."cool2.gif\" border=\"0\">";

   $searcharray[] = "/:\(/siU";
   $replacearray[] = "<img src=\"".$image_path."frown.gif\" border=\"0\">";

   $searcharray[] = "/:mad:/siU";
   $replacearray[] = "<img src=\"".$image_path."mad.gif\" border=\"0\">";

   $searcharray[] = "/:sorry:/siU";
   $replacearray[] = "<img src=\"".$image_path."sorry.gif\" border=\"0\">";

   //代码高亮
   $searcharray1="/(\[)(code)(])(\r\n)*(.*)(\[\/code\])/siU";
   $replacearray1="<?php\\5?>";

//   $main = preg_replace($searcharray, $replacearray, $main);
   $main = preg_replace($searcharray1, $replacearray1, $main);

   $main = highlight_string($main, true); 


//   $main = nl2br(htmlspecialchars($main));

//   $main = nl2br($main);

  $main = preg_replace($searcharray, $replacearray, $main);

   return $main;
}





?>
