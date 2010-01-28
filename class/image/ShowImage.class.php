<?php
//vim:set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldcolumn=1 foldmethod=marker:
/**
 * 项目：   5anet(BBS)
 * 文件：   class/image/ShowImage.class.php
 *
 * PHP Version 5
 *
 * @package:   class.image
 * @author:    Mike.G   Chinese Name: 黄叶 <hylinux@gmail.com>
 * @license:   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1 
 * @copyright: http://www.5anet.com
 * @version:   $Id: ShowImage.class.php,v 1.1.1.1 2006-08-28 13:09:20 ghw Exp $
 * @date:      $Date: 2006-08-28 13:09:20 $
 */


class ShowImage {
   /**
    * 显示图片
    * @param:  NULL
    * @return: NULL
    * @access: public
    * @static
    */
   public function run() {

      $imagedir = $_GET['image'];
      $imagedir = urldecode($imagedir);
      $imagedir = trim($imagedir);
      $width = $_GET['width'];
      $height = $_GET['height'];

      //生成缩略图

      $mysize = getimagesize($imagedir);

      $mymime = $mysize["mime"];


      //得出高和宽
      $mywidth = $mysize[0];
      $myheight = $mysize[1];

      //echo $mywidth."<br>".$myheight."<br>".$mymime."<br>";
      //exit;

      //如果图像超出这个范围即采用缩略图的
      //形式

      if ($mywidth > $width || $myheight > $height)
      {
         if ( $mywidth > $width && $myheight > $height ) {
            header("Content-type:".$mymime);
           if ( $mymime == "image/png" ) {
         	  $img = imagecreatefrompng($imagedir);
           } else if ($mymime == "image/jpeg" ) {
         	  $img = imagecreatefromjpeg($imagedir);
           } else if ( $mymime == "image/gif" ) {
               $img = imagecreatefromgif($imagedir);
         }

      if ( $width > $height ) {
         /*
          if ( $mywidth > $myheight ) {
//             $imgheight = $height;
//             $imgwidth = ceil($mywidth * ( $height / $myheight ) );
             $imgwidth = $width;
             $imgheight = ceil($myheight * ( $width / $mywidth ) );

          } else if ( $mywidth < $myheight ) {
//             $imgwidth = $width;
//             $imgheight = ceil($myheight * ( $width / $mywidth ) );
             $imgheight = $height;
             $imgwidth = ceil($mywidth * ( $height / $myheight ) );

          } else if ( $mywidth == $myheight ) {
             $imgheight = $height;
             $imgwidth = ceil($mywidth * ( $height / $myheight ) );
          }
          */
          $bi_width = $width / $mywidth ;
          $bi_height = $height / $myheight;

          if ( $bi_width > $bi_height ) {
             $imgwidth = ceil($bi_height * $mywidth);
             $imgheight = ceil($bi_height * $myheight);
          } else if ( $bi_width < $bi_height ) {
             $imgwidth = ceil($bi_width * $mywidth);
             $imgheight = ceil($bi_width * $myheight);
          } else if ( $bi_width == $bi_height ) {
             $imgwidth = ceil($bi_height * $mywidth);
             $imgheight = ceil($bi_height * $myheight);
          }



//          echo $imgwidth."\t".$imgheight."\t".$mywidth."\t".$myheight."<br>";
//          exit;

          $img2 = imagecreatetruecolor($imgwidth, $imgheight);
          #$bac = imagecolorallocate($img2, 255, 255, 255);
          imagecopyresampled($img2, $img, 0, 0, 0, 0, $imgwidth, $imgheight, $mywidth, $myheight);
      	if ( $mymime == "image/png" ) {
      		imagepng($img2);
      	} else if ( $mymime == "image/jpeg" || $mymime == "image/jpg" ) {
      		imagejpeg($img2);
      	} else if ( $mymime == "image/gif" ) {
              imagegif($img2);
         }
        imagedestroy($img);
        imagedestroy($img2);


      } else if ( $width < $height ) {
         /*
         if ( $mywidth > $myheight ) {
             $imgheight = $height;
             $imgwidth = ceil($mywidth * ( $height / $myheight ) );
         } else if ( $mywidth < $myheight ) {
             $imgheight = $height;
             $imgwidth = ceil($mywidth * ( $height / $myheight ) );

         } else if ( $mywidth == $myheight ) {

             $imgheight = $height;
             $imgwidth = ceil($mywidth * ( $height / $myheight ) );

         }*/

          $bi_width = $width / $mywidth ;
          $bi_height = $height / $myheight;

          if ( $bi_width > $bi_height ) {
             $imgwidth = ceil($bi_height * $mywidth);
             $imgheight = ceil($bi_height * $myheight);
          } else if ( $bi_width < $bi_height ) {
             $imgwidth = ceil($bi_width * $mywidth);
             $imgheight = ceil($bi_width * $myheight);
          } else if ( $bi_width == $bi_height ) {
             $imgwidth = ceil($bi_height * $mywidth);
             $imgheight = ceil($bi_height * $myheight);
          }

//          echo $imgwidth."\t".$imgheight."\t".$mywidth."\t".$myheight."<br>";
//          exit;

       $img2 = imagecreatetruecolor($imgwidth, $imgheight);
       #$bac = imagecolorallocate($img2, 255, 255, 255);
       imagecopyresampled($img2, $img, 0, 0, 0, 0, $imgwidth, $imgheight, $mywidth, $myheight);

   	if ( $mymime == "image/png" ) {
   		imagepng($img2);
   	} else if ( $mymime == "image/jpeg" || $mymime == "image/jpg" ) {
   		imagejpeg($img2);
   	} else if ( $mymime == "image/gif" ) {
           imagegif($img2);
       }


       imagedestroy($img);
       imagedestroy($img2);


      } else if ( $width == $height )  {
       $imgwidth = ceil($width * ( $height / $myheight)) ;
       $imgheight = $imgwidth;

       $img2 = imagecreatetruecolor($imgwidth, $imgheight);
       #$bac = imagecolorallocate($img2, 255, 255, 255);
       imagecopyresampled($img2, $img, 0, 0, 0, 0, $imgwidth, $imgheight, $mywidth, $myheight);

   	if ( $mymime == "image/png" ) {
   		imagepng($img2);
   	} else if ( $mymime == "image/jpeg" || $mymime == "image/jpg" ) {
   		imagejpeg($img2);
   	} else if ( $mymime == "image/gif" ) {
           imagegif($img2);
       }


       imagedestroy($img);
       imagedestroy($img2);


      }

   } else {

     header("Content-type:".$mymime);
     if ( $mymime == "image/png" ) {
   	  $img = imagecreatefrompng($imagedir);
     } else if ($mymime == "image/jpeg" ) {
   	  $img = imagecreatefromjpeg($imagedir);
     } else if ( $mymime == "image/gif" ) {
         $img = imagecreatefromgif($imagedir);
     }

     if ( $mywidth > $myheight ) {
       //以宽度为主
       $imgwidth = $width;
       $imgheight = ceil($myheight * ($width/$mywidth));

       $img2 = imagecreatetruecolor($imgwidth, $imgheight);
       #$bac = imagecolorallocate($img2, 255, 255, 255);
       imagecopyresampled($img2, $img, 0, 0, 0, 0, $imgwidth, $imgheight, $mywidth, $myheight);
   	if ( $mymime == "image/png" ) {
   		imagepng($img2);
   	} else if ( $mymime == "image/jpeg" || $mymime == "image/jpg" ) {
   		imagejpeg($img2);
   	} else if ( $mymime == "image/gif" ) {
           imagegif($img2);
       }


       imagedestroy($img);
       imagedestroy($img2);
     } else if ( $mywidth < $myheight ) {
       //以高度为主

      $imgwidth = ceil($mywidth * ( $height / $myheight)) ;
       $imgheight = $height;


       $img2 = imagecreatetruecolor($imgwidth, $imgheight);
       #$bac = imagecolorallocate($img2, 255, 255, 255);
       imagecopyresampled($img2, $img, 0, 0, 0, 0, $imgwidth, $imgheight, $mywidth, $myheight);

   	if ( $mymime == "image/png" ) {
   		imagepng($img2);
   	} else if ( $mymime == "image/jpeg" || $mymime == "image/jpg" ) {
   		imagejpeg($img2);
   	} else if ( $mymime == "image/gif" ) {
           imagegif($img2);
       }


       imagedestroy($img);
       imagedestroy($img2);
     } else if ( $mywidth == $myheight ) {
       $imgwidth = ceil($mywidth * ( $height / $myheight)) ;
       $imgheight = $imgwidth;

       $img2 = imagecreatetruecolor($imgwidth, $imgheight);
       #$bac = imagecolorallocate($img2, 255, 255, 255);
       imagecopyresampled($img2, $img, 0, 0, 0, 0, $imgwidth, $imgheight, $mywidth, $myheight);

   	if ( $mymime == "image/png" ) {
   		imagepng($img2);
   	} else if ( $mymime == "image/jpeg" || $mymime == "image/jpg" ) {
   		imagejpeg($img2);
   	} else if ( $mymime == "image/gif" ) {
           imagegif($img2);
       }


       imagedestroy($img);
       imagedestroy($img2);
     }
  }
} else {
  header("Content-type:".$mymime);
  if ( $mymime == "image/png" ) {
	  $img = imagecreatefrompng($imagedir);
  } else if ( $mymime == "image/jpeg" ) {
	  $img = imagecreatefromjpeg($imagedir);
  }
  else
 {
	  $img = imagecreatefromgif($imagedir);
  }
  $bac = imagecolorallocate($img, 255, 255, 255);

	if ( $mymime == "image/png" ) {
		imagepng($img);
	} else if ( $mymime == "image/jpeg" || $mymime == "image/jpg" ) {
		imagejpeg($img);
	} else if ( $mymime == "image/gif" ) {
        imagegif($img);
    }

  imagedestroy($img);

}






   }

}



?>
