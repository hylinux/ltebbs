<?php
// vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
*  ����ͼ���ɼ�������
*
*  PHP Version 5
*  
*  @package:   image
*  @author:    ��Ҷ<hylinux@gmail.com>
*  @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
*  @copyright: ��Ҷ��С�� 2003-2005 The A_Ye's Little House
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
   *  ԭͼ��
   *  ������ȫ·����
   *  @var String
   *  @access private
   */
   private $src_name = null;

   /**
   *  Ŀ��ͼ��
   *  ����Ҳ��ȫ·����
   *  @var String
   *  @access private
   */
   private $dst_name = null;

   /**
   *  �ı��Ŀ��
   *  @var number;
   *  @access private
   */
   private $require_width = 0;

   /**
   *  �ı��ĸ߶�
   *  @var number;
   *  @access private
   */
   private $require_height = 0;

   /**
   *  ���캯��
   *  @param NULL
   *  @return NULL
   *  @access public
   */
   public function __construct() {/*{{{*/
      //������������ܶ������
      /**
      *  ���磬�ж�php�Ƿ�֧��gd�⣬ �Ƿ�֧��jpeg, png, gif��
      *  ���������������ǣ����磺��ʼ��һЩ����Ķ�����
      *  :), ����û��ʱ��ȥ���������ˣ� *^-^*��
      */
      $this->src_name       = "";
      $this->dst_name       = "";
      $this->require_width  = 0;
      $this->require_height = 0;
   }/*}}}*/


   /**
   *  ����ԭʼͼ��
   *  @param String ԭʼͼ���·����
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
   *  ����Ŀ��ͼ����
   *  @param String  Ҫ���ɵ�ͼ����
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
   * ����Ҫ��Ŀ��
   *  @param  number Ҫ��Ŀ��
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
   *  ����Ҫ��ĸ߶�
   *  @param number Ҫ��ĸ߶�
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
   *  ����ͼ��
   *  @param NULL
   *  @return NULL
   *  @access public
   */
   public function ResizeImage() {/*{{{*/
      //���Ŀ���ļ�û��ָ����
      //����������������
      if ( !$this->src_name ) {
         die('��ָ��Ҫ���ŵ�ԭ�ļ�');
      }

      if ( !$this->require_width ) {
         die('��ָ�����ź�Ŀ��');
      }

      if ( !$this->require_height ) {
         die('��ָ�����ź�ĸ߶�');
      }

      //ȡ��ԭͼ��Ŀ�Ⱥ͸߶�
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

         //Ȼ��Ա��ʽ����ж�
         if ( $rate_width >= $rate_height ) {
            //��������Ը߶�Ϊ��
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

      //����һ�����ɫ��ͼ��
      $im    = imagecreatetruecolor($will_width, $will_height);
      $black = imagecolorallocate($im, 255, 255, 255);
      
      //����ͼ�����
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
   *  ����������ͼ��
   *  @param NULL
   *  @return NULL
   *  @access public
   */
   public function ShowImage() {
      //ͼ���·��
      $image_file = $_GET['file'];

      if ( !$image_file ) {
         echo "<script>";
         echo "alert('ͼ���ļ�������');\n";
         echo "history.back();\n";
         echo "</script>\n";
         exit;
      }

      //���Ŀ���ļ�û��ָ����
      //����������������
      $this->src_name = $image_file;

      $this->require_width = 320;
      $this->require_height = 300;


      //ȡ��ԭͼ��Ŀ�Ⱥ͸߶�
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

         //Ȼ��Ա��ʽ����ж�
         if ( $rate_width >= $rate_height ) {
            //��������Ը߶�Ϊ��
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
      //����һ�����ɫ��ͼ��
      $im    = imagecreatetruecolor($will_width, $will_height);
      $black = imagecolorallocate($im, 255, 255, 255);
      
      //����ͼ�����
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
