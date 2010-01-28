<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/bbs/SearchResult.class.php
 *
 * 查看论坛搜索的结果
 *
 * PHP Version 5
 *
 * @package:   class.bbs
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: SearchResult.class.php,v 1.2 2006-09-24 14:38:08 ghw Exp $
 * @date:      $Date: 2006-09-24 14:38:08 $
 */

include_once CLASS_PATH.'main/BaseAction.class.php';

//包含需要用到的函数
include_once FUNCTION_PATH.'utf8_substr.fun.php';
include_once FUNCTION_PATH.'set_locale_time.fun.php';

//包含需要用到的类
include_once CLASS_PATH.'bbs/LayoutUtil.class.php';
include_once CLASS_PATH.'user/UserUtil.class.php';

//include the language file
if ( file_exists(LANG_PATH.SYSTEM_LANG.'/SearchResult.lang.php') ) {
   include_once LANG_PATH.SYSTEM_LANG.'/SearchResult.lang.php';
}



class SearchResult extends BaseAction {
   
   /**
    * 数据库的连接
    */
   public $db;


   /**
    * 每一页显示帖子数
    */
   private $page_number = 25;


   /**
    * 构造函数
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function __construct() {
      $this->db = $this->getCacheDB();
   }

   
   /**
    * 显示版面的情况
    * @param:  NULL
    * @return: NULL
    * @access: public
    */
   public function run() {
      //取得用户的id
      $user_id = UserUtil::getUserId($this->db, $_SESSION['user']['name']);

      $smarty = $this->getSmarty();

      //取得站点的公告，并显示在页面上
      $is_have_post = false;
      $post_str = '';
      if ( PostUtil::haveNotExpirePost($this->getDB()) ) {
         $is_have_post = true;
         $post_array = PostUtil::getPost($this->getDB(), 3);

         foreach ( $post_array as $post_rows ) {
            $post_str .= '<a href=\'index.php?module=post&action=view&id='.
               $post_rows['id'].'\' title=\''.$post_rows['title'].'\'>'.
               utf8_substr($post_rows['title'], 0, 35).'</a>'.
               '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
         }
      }

      $smarty->assign('have_system_post', $is_have_post);
      $smarty->assign('post_str', $post_str);

      //公告显示结束

      $q = $this->getParameterFromGET('q');
      $encode_q = urlencode($q);
      //取得查询字符串
      if ( !$q ) {
         $where_sql = '';

         //收集查询的变量
         //按关键字查询
         $word = $this->getParameter('word');
         //按用户名来查询
         $find_user = $this->getParameter('user');

         if ( !$find_user && !$word ) {
            $this->AlertAndBack(SR_NO_FIND_KEYWORD);
            return;
         }


         $word = ltrim($word);
         $word = rtrim($word);
         //按空格拆分关键字
         
         $word_array = preg_split("/[\s,]+/", $word);

         //看用户的搜索是按关键字，还是按帖子的内容
         $is_topic = $this->getParameter('wordtarget');

         if ( $is_topic != 1 && $is_topic != 2 ) {
            $is_topic = 1;
         }

         $i = 0;

         if ( count($word_array) > 1 ) {
            $where_sql .= " and ( ";
            foreach ( $word_array as $value ) {
               if ( $is_topic == 1 ) {
                  $where_sql .= " title like '%".$value."%' ";

                  if ( $i < count($word_array) - 1 ) {
                     $where_sql .= " or ";
                  }

               } else if ( $is_topic == 2 ) {
                  $where_sql .= " MATCH(content) AGAINST('".$value."') ";
                  if ( $i < count($word_array) - 1 ) {
                     $where_sql .= " or ";
                  }
               }
               $i = $i + 1; 

            }
            $where_sql .= " )  ";
         } else if ( count($word_array)== 1 && $word ) {
            if ( $is_topic == 1 ) {
               $where_sql .= " and title like '%".$word."%' ";
            } else if ( $is_topic == 2 ) {
               $where_sql .= " and match(content) against('".$word."')";
            }
         }

         //是按用户名来搜索的
         //收集用户名
         $find_user = ltrim($find_user);
         $find_user = rtrim($find_user);

         $find_user_array = preg_split("/[\s,]+/", $find_user);

         $is_match = $this->getParameter('usermatch');

         $i = 0;
         if ( count($find_user_array) > 1 ) {
            $where_sql .= " and ( ";
            foreach ( $find_user_array as $value ) {
               if ( $is_match ) {
                  $where_sql .= " author='".$value."' ";

                  if ( $i < count($find_user_array) - 1 ) {
                     $where_sql .= " or ";
                  }

               } else {
                  $where_sql .= " author like '%".$value."%' ";
                  if ( $i < count($find_user_array) - 1 ) {
                     $where_sql .= " or ";
                  }
               }

               $i = $i + 1; 
            }
            $where_sql .= " )  ";
         } else if ( count($find_user_array)==1 && $find_user ) {
            if ( $is_match ) {
               $where_sql .= " and author ='".$find_user."' ";
            } else {
               $where_sql .= " and author like '%".$find_user."%' ";
            }
         }

         //再收集用户是否选择了论坛进行搜索。
         $layout = $this->getParameterFromPOST('layout');
         if ( $layout && !is_array($layout)  ) {
            $this->AlertAndBack(SR_SYSTEM_REQUEST_ERROR);
            return;
         }

         $i = 0;
         if ( count ( $layout ) > 1 ) {
            $where_sql .= " and ( ";
            foreach ( $layout as $value ) {
               $where_sql .= " layout_id='".$value."' ";

               if ( $i < count($layout) - 1 ) {
                  $where_sql .= " or ";
               }
               $i = $i + 1;
            }
            $where_sql .= " ) ";
         } else if ( count($layout)==1 ) {
            $where_sql .= " and layout_id='".$layout."' ";
         }

         $q = ' where 1 '.$where_sql;
         $encode_q = base64_encode($q);
         $encode_q = urlencode($encode_q);

      } else {
//         $q = urldecode($q);
         $q = base64_decode($q);
      }



      //求总的total number
      $smarty->assign('encode_q', $encode_q);

      //生成所有的记录数
      $sql = 'select count(*) as num from bbs_subject '.$q;
      $res = $this->db->Execute($sql);
      $rows = $res->FetchRow();

      $total_number = $rows['num'];

      //求总公的页面
      $total_page = ceil($total_number / $this->page_number );
      
      //取得当前的页面
      $page = $this->getParameter('page');

      if ( !$page || $page < 0 ) {
         $page = 1;
      }

      if ( $page > $total_page && $total_page > 0 ) {
         $page = $total_page;
      }

      $begin_page = 1;
      $end_page = $total_page;

      if ( $page <= 10 && $total_page >=10  ) {
         $end_page = 10; 
      } else if ( $page > 10  ) {
         if ( $page % 10 == 0 ) {
         //向前翻
            $end_page = $page; 

            $begin_page = $end_page - 9;
   
         } else if ( $page % 10 == 1 ) {
            //向后翻
            //确定开始的页数
            $begin_page = $page; 
   
            if ( $begin_page > $total_page ) {
               $begin_page = $page - 9;
             }
   
            if ( ( $begin_page + 9 ) > $total_page ) {
               $end_page = $total_page;
             } else {
               $end_page = $begin_page + 9;
             } 
   
         } else {
               $num = $page % 10;
               $pre_num = floor($page / 10 );
               $begin_page = $pre_num * 10 + 1;
               $end_page = $begin_page + 9;
            }
         }

         if ( $end_page > $total_page ) {
            $end_page = $total_page;
         }

         $nav_page_array = array();
         for( $i = $begin_page; $i<=$end_page; $i++ ) {
            array_push($nav_page_array, $i);
         }
         //帖子导航栏
         $smarty->assign('nav_page', $nav_page_array);
         //当前的页面
         $smarty->assign('now_page', $page);
         //共有的页面
         $smarty->assign('total_page', $total_page);
         
         //显示搜索结果
          //求出偏移
         $offset_number = ( $page - 1 ) * $this->page_number;

         $subject_array = LayoutUtil::getCacheSubjectInfo($this->db, $this->page_number, 
            $offset_number, $q);

         if ( $total_page > 0 ) {
            $smarty->assign('subject', $subject_array);
            $smarty->assign('have_subject', 1);
         }


      
      $smarty->display('bbssearchresult.tmpl');


   }

}

?>
