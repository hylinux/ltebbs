<?php
// vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  缩略图生成及处理类
*
*  PHP Version 5
*  
*  @package:   image
*  @author:    黄叶<hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
*  @copyright: 阿叶的小屋 2003-2005 The A_Ye's Little House
*  @version:   $Id: Image.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
*  @date:      $Date: 2006-08-28 13:09:20 $
*  <sample>
*   $my_image = new Image();
*   $my_iamge->setSrcFile($orgi_file);
*   $my_image->setDstFile($dst_file);
*   $my_image->setResizeWidth($width);
*   $my_image->setResizeHeight($height);
*   $my_image->ResizeImage();
*  </sample>
*/

class Image
{
   /**
   *  原图像
   *  这里是全路径名
   *  @var String
   *  @access private
   */
   private $src_name = null;

   /**
   *  目的图像
   *  这里也是全路径名
   *  @var String
   *  @access private
   */
   private $dst_name = null;

   /**
   *  改变后的宽度
   *  @var number;
   *  @access private
   */
   private $require_width = 0;

   /**
   *  改变后的高度
   *  @var number;
   *  @access private
   */
   private $require_height = 0;

   /**
   *  构造函数
   *  @param NULL
   *  @return NULL
   *  @access public
   */
   public function __construct() {/*{{{*/
      //在这里可以做很多的事情
      /**
      *  比如，判断php是否支持gd库， 是否支持jpeg, png, gif等
      *  还可以作其他的是，比如：初始化一些额外的东东。
      *  :), 不过没有时间去更完善它了， *^-^*。
      */
      $this->src_name       = "";
      $this->dst_name       = "";
      $this->require_width  = 0;
      $this->require_height = 0;
   }/*}}}*/


   /**
   *  设置原始图像
   *  @param String 原始图像的路径名
   *  @return boolean
   *  @access public
   */
   public function setSrcFile($filename) {/*{{{*/
      if ( file_exists($filename) ) {
         $this->src_name = $filename;
         return true;
      } else {
         return false;
      }
   }/*}}}*/

   /**
   *  设置目的图像名
   *  @param String  要生成的图像名
   *  @return boolean
   *  @access public
   */
   public function setDstFile($filename) {/*{{{*/
      if ( file_exists($filename) ) {
         return false;
      } else {
         $this->dst_name = $filename;
         return true;
      }
   }/*}}}*/

   /**
   * 设置要求的宽度
   *  @param  number 要求的宽度
   *  @return boolean
   *  @access public
   */
   public function setResizeWidth($width) {/*{{{*/
      if ( $width && is_numeric($width) ) {
         $this->require_width = $width;
         return true;
      } else {
         return false;
      }
   }/*}}}*/

   /**
   *  设置要求的高度
   *  @param number 要求的高度
   *  @return boolean
   *  @access public
   */
   public function setResizeHeight($height) {/*{{{*/
      if ( $height && is_numeric($height) ) {
         $this->require_height = $height;
         return true;
      } else {
         return false;
      }
   }/*}}}*/

   /**
   *  缩放图像
   *  @param NULL
   *  @return NULL
   *  @access public
   */
   public function ResizeImage() {/*{{{*/
      //如果目的文件没有指出，
      //，则是向浏览器输出
      if ( !$this->src_name ) {
         die('请指定要缩放的原文件');
      }

      if ( !$this->require_width ) {
         die('请指定缩放后的宽度');
      }

      if ( !$this->require_height ) {
         die('请指定缩放后的高度');
      }

      //取得原图像的宽度和高度
      list($width, $height, $image_type, $attr) = getimagesize($this->src_name);


      if ( ($this->require_width >= $width) && ($this->require_height >= $height) ) {
         $will_width  = $width;
         $will_height = $height;
      } else if ( ( $this->require_width > $width ) && ( $this->require_height < $height ) ) {
         $will_width  = floor($width * ( $this->require_height / $height ) );
         $will_height = $this->require_height;
      } else if ( ( $this->require_width < $width ) && ( $this->require_height > $height ) ) {
         $will_width  = $width;
         $will_height = floor( $height * ( $this->require_width / $width ) );
      } else if ( ( $this->require_width < $width ) && ( $this->require_height < $height ) ) {
         $rate_width  = $this->require_width / $width;
         $rate_height = $this->require_height / $height;

         //然后对比率进行判断
         if ( $rate_width >= $rate_height ) {
            //这种情况以高度为主
            $will_width  = floor($width * $rate_height);
            $will_height = floor($height * $rate_height);
         } else if ( $rate_width < $rate_height ) {
            $will_width  = floor($width * $rate_width);
            $will_height = floor($height * $rate_width);
         }
      } 

      switch ( $image_type ) {
         case 1:
            $orgi_im = imagecreatefromgif($this->src_name);
            break;
         case 2:
            $orgi_im = imagecreatefromjpeg($this->src_name);
            break;
         case 3:
            $orgi_im = imagecreatefrompng($this->src_name);
            break;
      }

      //创建一个真彩色的图像
      $im    = imagecreatetruecolor($will_width, $will_height);
      $black = imagecolorallocate($im, 255, 255, 255);
      
      //拷贝图像过来
      imagecopyresized($im, $orgi_im, 0, 0, 0, 0, $will_width, $will_height, $width, $height);


      switch ( $image_type ) {
         case 1:
            imagegif($im, $this->dst_name);
            break;
         case 2:
            imagejpeg($im, $this->dst_name);
            break;
         case 3:
            imagepng($im, $this->dst_name);
            break;
      }

      imagedestroy($im);
      imagedestroy($orgi_im);
      return true;
   }
/*}}}*/

   /**
   *  想浏览器输出图像
   *  @param NULL
   *  @return NULL
   *  @access public
   */
   public function ShowImage() {
      //图像的路径
      $image_file = $_GET['file'];

      if ( !$image_file ) {
         echo "<script>";
         echo "alert('图像文件不存在');\n";
         echo "history.back();\n";
         echo "</script>\n";
         exit;
      }

      //如果目的文件没有指出，
      //，则是向浏览器输出
      $this->src_name = $image_file;

      $this->require_width = 320;
      $this->require_height = 300;


      //取得原图像的宽度和高度
      list($width, $height, $image_type, $attr) = getimagesize($this->src_name);

      if ( ($this->require_width >= $width) && ($this->require_height >= $height) ) {
         $will_width  = $width;
         $will_height = $height;
      } else if ( ( $this->require_width > $width ) && ( $this->require_height < $height ) ) {
         $will_width  = floor($width * ( $this->require_height / $height ) );
         $will_height = $this->require_height;
      } else if ( ( $this->require_width < $width ) && ( $this->require_height > $height ) ) {
         $will_width  = $width;
         $will_height = floor( $height * ( $this->require_width / $width ) );
      } else if ( ( $this->require_width < $width ) && ( $this->require_height < $height ) ) {
         $rate_width  = $this->require_width / $width;
         $rate_height = $this->require_height / $height;

         //然后对比率进行判断
         if ( $rate_width >= $rate_height ) {
            //这种情况以高度为主
            $will_width  = floor($width * $rate_height);
            $will_height = floor($height * $rate_height);
         } else if ( $rate_width < $rate_height ) {
            $will_width  = floor($width * $rate_width);
            $will_height = floor($height * $rate_width);
         }
      } 

      switch ( $image_type ) {
         case 1:
            $orgi_im = imagecreatefromgif($this->src_name);
            $mymime = "image/gif";
            break;
         case 2:
            $orgi_im = imagecreatefromjpeg($this->src_name);
            $mymime = "image/jpeg";
            break;
         case 3:
            $orgi_im = imagecreatefrompng($this->src_name);
            $mymime = "image/png";
            break;
      }

      exit;
      //创建一个真彩色的图像
      $im    = imagecreatetruecolor($will_width, $will_height);
      $black = imagecolorallocate($im, 255, 255, 255);
      
      //拷贝图像过来
      imagecopyresized($im, $orgi_im, 0, 0, 0, 0, $will_width, $will_height, $width, $height);

      header("Content-type:".$mymime);

      switch ( $image_type ) {
         case 1:
            imagegif($im, $this->dst_name);
            break;
         case 2:
            imagejpeg($im, $this->dst_name);
            break;
         case 3:
            imagepng($im, $this->dst_name);
            break;
      }

      imagedestroy($im);
      imagedestroy($orgi_im);


   }

}

?>
