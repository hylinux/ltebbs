<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/post/PostUtil.class.php
 *
 * 本类提供站点内系统通告的工具集
 *
 * PHP Version 5
 * @package:   class.post
 * @author:    Mike.G 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: www.5anet.com
 * @version:   $Id: PostUtil.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @Date:      $Date: 2006-08-28 13:09:20 $
 */

class PostUtil {
   
   /**
    * 判断系统是否有有效的通告
    * @param:  &$db database references
    * @return: NULL
    * @access: public
    * @static
    */
   public static function haveNotExpirePost(&$db) {/*{{{*/
      $sql = 'select count(*) as num from site_post where expires>?';
      $stmt = $db->Prepare($sql);
      $res = $db->Execute($stmt, array(time()));
      $rows = $res->FetchRow();

      if ( $rows['num']) {
         return TRUE;
      } else {
         return FALSE;
      }
   }/*}}}*/

   /**
    * 返回系统的通告
    * @param:  &$db databaes reference
    * @param:  $number  返回的数目
    * @return: $post_array array
    * @access: public
    * @static
    */
   public static function getPost(&$db, $number=1) {/*{{{*/
      $sql = 'select id, title from  site_post  where expires > ? '.
         ' order by  id desc ';
      $res = $db->SelectLimit($sql, $number, 0, array(time()));

      $post_array = array();

      while ( $rows = $res->FetchRow() ) {
         $post_array[] = array(
            'id'=>$rows['id'],
            'title'=>$rows['title']
         );
      }

      return $post_array;
   } /*}}}*/






}

?>
